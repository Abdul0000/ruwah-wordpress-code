<?php
/**
 * Plugin Name: Ruwah Transparent Product Images
 * Description: Generates transparent PNG product renders and swaps them on the storefront without changing WooCommerce product records.
 * Version: 1.1.0
 * Author: Ruwah Beauty
 * Requires PHP: 8.1
 */

defined('ABSPATH') || exit;

final class Ruwah_Transparent_Product_Images {
    private const MAP_OPTION = 'ruwah_tpi_image_map';
    private const STATUS_OPTION = 'ruwah_tpi_status';
    private const ENABLED_OPTION = 'ruwah_tpi_enabled';

    public static function boot(): void {
        add_filter('wp_get_attachment_image_src', [self::class, 'filter_image_src'], 999, 4);
        add_filter('wp_get_attachment_url', [self::class, 'filter_attachment_url'], 999, 2);
        add_filter('wp_get_attachment_image_attributes', [self::class, 'filter_attributes'], 999, 3);
        add_filter('wp_calculate_image_srcset', [self::class, 'filter_srcset'], 999, 5);
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_post_ruwah_tpi_regenerate', [self::class, 'handle_regenerate']);
        add_action('admin_post_ruwah_tpi_toggle', [self::class, 'handle_toggle']);
        add_action('admin_notices', [self::class, 'notice']);
    }

    public static function activate(): void {
        update_option(self::ENABLED_OPTION, 'yes', false);
        self::generate_all();
    }

    private static function enabled(): bool {
        return get_option(self::ENABLED_OPTION, 'yes') === 'yes';
    }

    private static function product_attachment_ids(): array {
        if (!class_exists('WooCommerce')) {
            return [];
        }
        $ids = [];
        $product_ids = get_posts([
            'post_type' => 'product',
            'post_status' => ['publish', 'private', 'draft'],
            'numberposts' => -1,
            'fields' => 'ids',
        ]);
        foreach ($product_ids as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) continue;
            $main = (int) $product->get_image_id();
            if ($main) $ids[] = $main;
            foreach ($product->get_gallery_image_ids() as $gallery_id) {
                if ($gallery_id) $ids[] = (int) $gallery_id;
            }
        }
        return array_values(array_unique(array_filter($ids)));
    }

    public static function generate_all(): array {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');
        $upload = wp_upload_dir();
        $folder = trailingslashit($upload['basedir']) . 'ruwah-transparent-products';
        $base_url = trailingslashit($upload['baseurl']) . 'ruwah-transparent-products';
        wp_mkdir_p($folder);

        $map = [];
        $errors = [];
        foreach (self::product_attachment_ids() as $attachment_id) {
            $source = wp_get_original_image_path($attachment_id) ?: get_attached_file($attachment_id);
            if (!$source || !is_readable($source)) {
                $errors[] = 'Unreadable attachment ' . $attachment_id;
                continue;
            }
            $filename = 'product-' . $attachment_id . '-transparent.png';
            $destination = trailingslashit($folder) . $filename;
            $created = class_exists('Imagick')
                ? self::create_with_imagick($source, $destination)
                : self::create_with_gd($source, $destination);
            if (!$created) {
                $errors[] = 'Could not process attachment ' . $attachment_id;
                continue;
            }
            $size = @getimagesize($destination);
            $map[$attachment_id] = [
                'url' => trailingslashit($base_url) . $filename . '?v=' . filemtime($destination),
                'width' => (int) ($size[0] ?? 1200),
                'height' => (int) ($size[1] ?? 1200),
            ];
        }

        update_option(self::MAP_OPTION, $map, false);
        $status = [
            'state' => $errors ? 'warning' : 'success',
            'count' => count($map),
            'errors' => $errors,
            'message' => sprintf('Generated %d transparent product PNG images%s.', count($map), $errors ? ' with ' . count($errors) . ' skipped' : ''),
        ];
        update_option(self::STATUS_OPTION, $status, false);
        return $status;
    }

    private static function mapped(int $attachment_id): ?array {
        if (!self::enabled()) return null;
        $map = (array) get_option(self::MAP_OPTION, []);
        return isset($map[$attachment_id]) && is_array($map[$attachment_id]) ? $map[$attachment_id] : null;
    }

    public static function filter_image_src($image, int $attachment_id, $size, bool $icon) {
        $mapped = self::mapped($attachment_id);
        if (!$mapped || !is_array($image)) return $image;
        return [$mapped['url'], $mapped['width'], $mapped['height'], false];
    }

    public static function filter_attachment_url(string $url, int $attachment_id): string {
        $mapped = self::mapped($attachment_id);
        return $mapped ? (string) $mapped['url'] : $url;
    }

    public static function filter_attributes(array $attr, $attachment, $size): array {
        $mapped = self::mapped((int) $attachment->ID);
        if (!$mapped) return $attr;
        $attr['src'] = $mapped['url'];
        $attr['srcset'] = '';
        $attr['data-src'] = $mapped['url'];
        $attr['width'] = (string) $mapped['width'];
        $attr['height'] = (string) $mapped['height'];
        return $attr;
    }

    public static function filter_srcset($sources, array $size_array, string $image_src, array $image_meta, int $attachment_id) {
        return self::mapped($attachment_id) ? false : $sources;
    }

    private static function create_with_imagick(string $source, string $destination): bool {
        try {
            $image = new Imagick($source);
            $image->setIteratorIndex(0);
            $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_ACTIVATE);
            $image->setImageBackgroundColor(new ImagickPixel('transparent'));
            $image->thumbnailImage(1200, 1200, true, true);
            $width = $image->getImageWidth();
            $height = $image->getImageHeight();
            $range = Imagick::getQuantumRange();
            $fuzz = (float) $range['quantumRangeLong'] * 0.15;
            $points = [[0,0],[$width-1,0],[0,$height-1],[$width-1,$height-1],[intdiv($width,2),0],[intdiv($width,2),$height-1],[0,intdiv($height,2)],[$width-1,intdiv($height,2)]];
            foreach ($points as [$x,$y]) {
                $target = $image->getImagePixelColor($x, $y);
                $image->floodFillPaintImage(new ImagickPixel('transparent'), $fuzz, $target, $x, $y, false);
            }
            $image->trimImage(0);
            $image->setImagePage(0, 0, 0, 0);
            $image->thumbnailImage(1030, 1030, true, true);
            $canvas = new Imagick();
            $canvas->newImage(1200, 1200, new ImagickPixel('transparent'), 'png');
            $canvas->compositeImage($image, Imagick::COMPOSITE_OVER, (int) ((1200-$image->getImageWidth())/2), (int) ((1200-$image->getImageHeight())/2));
            $canvas->setImageFormat('png');
            $canvas->setImageCompressionQuality(96);
            $ok = $canvas->writeImage($destination);
            $image->clear();
            $canvas->clear();
            return (bool) $ok;
        } catch (Throwable $error) {
            return false;
        }
    }

    private static function create_with_gd(string $source, string $destination): bool {
        $data = @file_get_contents($source);
        $src = $data ? @imagecreatefromstring($data) : false;
        if (!$src) return false;
        $w = imagesx($src); $h = imagesy($src);
        $scale = min(1, 900/max($w,$h));
        $rw = max(1,(int)round($w*$scale)); $rh = max(1,(int)round($h*$scale));
        $work = imagecreatetruecolor($rw,$rh);
        imagealphablending($work,false); imagesavealpha($work,true);
        $clear = imagecolorallocatealpha($work,0,0,0,127); imagefill($work,0,0,$clear);
        imagecopyresampled($work,$src,0,0,0,0,$rw,$rh,$w,$h); imagedestroy($src);
        $samples=[];
        foreach([[0,0],[$rw-1,0],[0,$rh-1],[$rw-1,$rh-1],[intdiv($rw,2),0],[intdiv($rw,2),$rh-1]] as [$x,$y]){
            $c=imagecolorsforindex($work,imagecolorat($work,$x,$y)); $samples[]=[$c['red'],$c['green'],$c['blue']];
        }
        for($y=0;$y<$rh;$y++) for($x=0;$x<$rw;$x++){
            $c=imagecolorsforindex($work,imagecolorat($work,$x,$y)); $best=PHP_INT_MAX;
            foreach($samples as [$r,$g,$b]) $best=min($best,($c['red']-$r)**2+($c['green']-$g)**2+($c['blue']-$b)**2);
            if($best<3600&&($x<$rw*.25||$x>$rw*.75||$y<$rh*.18||$y>$rh*.82)) imagesetpixel($work,$x,$y,$clear);
        }
        $canvas=imagecreatetruecolor(1200,1200); imagealphablending($canvas,false); imagesavealpha($canvas,true);
        $transparent=imagecolorallocatealpha($canvas,0,0,0,127); imagefill($canvas,0,0,$transparent);
        $fit=min(1030/$rw,1030/$rh); $fw=(int)round($rw*$fit); $fh=(int)round($rh*$fit);
        imagecopyresampled($canvas,$work,(int)((1200-$fw)/2),(int)((1200-$fh)/2),0,0,$fw,$fh,$rw,$rh);
        imagedestroy($work); $ok=imagepng($canvas,$destination,2); imagedestroy($canvas); return $ok;
    }

    public static function menu(): void {
        add_submenu_page('woocommerce','Transparent Product Images','Transparent Images','manage_woocommerce','ruwah-transparent-images',[self::class,'page']);
    }

    public static function page(): void {
        if (!current_user_can('manage_woocommerce')) return;
        $status=(array)get_option(self::STATUS_OPTION,[]); $enabled=self::enabled();
        echo '<div class="wrap"><h1>Ruwah Transparent Product Images</h1><p>'.esc_html($status['message']??'Ready.').'</p><p>Status: <strong>'.($enabled?'Enabled':'Disabled').'</strong></p>';
        echo '<p><a class="button button-primary" href="'.esc_url(wp_nonce_url(admin_url('admin-post.php?action=ruwah_tpi_regenerate'),'ruwah_tpi_regenerate')).'">Regenerate PNGs</a> ';
        echo '<a class="button" href="'.esc_url(wp_nonce_url(admin_url('admin-post.php?action=ruwah_tpi_toggle'),'ruwah_tpi_toggle')).'">'.($enabled?'Disable transparent images':'Enable transparent images').'</a></p></div>';
    }

    public static function handle_regenerate(): void {
        if(!current_user_can('manage_woocommerce')) wp_die('Not allowed.'); check_admin_referer('ruwah_tpi_regenerate');
        self::generate_all(); wp_safe_redirect(admin_url('admin.php?page=ruwah-transparent-images')); exit;
    }

    public static function handle_toggle(): void {
        if(!current_user_can('manage_woocommerce')) wp_die('Not allowed.'); check_admin_referer('ruwah_tpi_toggle');
        update_option(self::ENABLED_OPTION,self::enabled()?'no':'yes',false); wp_safe_redirect(admin_url('admin.php?page=ruwah-transparent-images')); exit;
    }

    public static function notice(): void {
        if(!current_user_can('manage_woocommerce')) return; $status=(array)get_option(self::STATUS_OPTION,[]); if(empty($status['message'])) return;
        $class=($status['state']??'')==='warning'?'notice-warning':'notice-success';
        echo '<div class="notice '.esc_attr($class).' is-dismissible"><p>'.esc_html($status['message']).'</p></div>';
    }
}

register_activation_hook(__FILE__,[Ruwah_Transparent_Product_Images::class,'activate']);
Ruwah_Transparent_Product_Images::boot();
