<?php
/**
 * Plugin Name: Ruwah Real Transparent Product Images
 * Description: Generates validated transparent PNG media attachments and assigns them as WooCommerce featured images.
 * Version: 2.3.0
 * Author: Ruwah Beauty
 * Requires PHP: 8.1
 */
defined('ABSPATH') || exit;

final class Ruwah_Product_Images {
    private const VERSION = '2.3.0';
    private const MAP = 'ruwah_rpi_map_v23';
    private const STATUS = 'ruwah_rpi_status_v23';
    private const ENGINE = 'ruwah_rpi_engine';
    private const CONFLICTS = [
        'nub-product-images-deep-purple-v4/nub-product-images-deep-purple-v4.php',
        'ruwah-fresh-commerce-design/ruwah-fresh-commerce-design.php',
    ];

    public static function boot(): void {
        add_action('plugins_loaded', [self::class, 'migrate'], 40);
        add_action('admin_menu', [self::class, 'menu']);
        add_action('admin_post_ruwah_rpi_regenerate', [self::class, 'regen']);
        add_action('admin_post_ruwah_rpi_restore', [self::class, 'restore_action']);
    }

    public static function activate(): void {
        self::disable_conflicts();
        if (class_exists('WooCommerce')) {
            self::process(true);
            update_option(self::ENGINE, self::VERSION, false);
        }
    }

    public static function migrate(): void {
        if (!class_exists('WooCommerce') || get_option(self::ENGINE) === self::VERSION) {
            return;
        }
        self::disable_conflicts();
        $status = self::process(true);
        if (empty($status['errors']) && (int) ($status['processed'] ?? 0) === count(self::product_ids())) {
            update_option(self::ENGINE, self::VERSION, false);
        }
    }

    private static function disable_conflicts(): void {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
        foreach (self::CONFLICTS as $plugin) {
            if (is_plugin_active($plugin)) {
                deactivate_plugins($plugin, true, false);
            }
        }
    }

    private static function product_ids(): array {
        return array_map('intval', get_posts([
            'post_type' => 'product',
            'post_status' => ['publish', 'private', 'draft'],
            'numberposts' => -1,
            'fields' => 'ids',
            'orderby' => 'ID',
            'order' => 'ASC',
        ]));
    }

    private static function previous_map(): array {
        foreach ([self::MAP, 'ruwah_rpi_map_v22', 'ruwah_rpi_map_v21'] as $option) {
            $map = (array) get_option($option, []);
            if ($map) {
                return $map;
            }
        }
        return [];
    }

    public static function process(bool $force = true): array {
        @set_time_limit(600);
        @ini_set('memory_limit', '1024M');
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        self::disable_conflicts();
        $existing = self::previous_map();
        $map = (array) get_option(self::MAP, []);
        $done = [];
        $errors = [];
        $details = [];

        foreach (self::product_ids() as $product_id) {
            $product = wc_get_product($product_id);
            if (!$product) {
                $errors[] = $product_id . ':product unavailable';
                continue;
            }

            $current_id = (int) $product->get_image_id();
            $source_id = (int) (
                $map[$product_id]['original_id']
                ?? $existing[$product_id]['original_id']
                ?? $current_id
            );
            if (!$source_id) {
                $errors[] = $product_id . ':no source image';
                continue;
            }

            $source = wp_get_original_image_path($source_id) ?: get_attached_file($source_id);
            if (!$source || !is_readable($source)) {
                $errors[] = $product_id . ':source unreadable';
                continue;
            }
            if (!$force && !empty($map[$product_id]['new_id'])) {
                continue;
            }

            $result = self::make_png($product_id, $source_id, $source);
            if (is_wp_error($result)) {
                $errors[] = $product_id . ':' . $result->get_error_message();
                continue;
            }

            $new_id = (int) $result['attachment_id'];
            $product->set_image_id($new_id);
            $product->save();
            update_post_meta($new_id, '_wp_attachment_image_alt', get_the_title($product_id));

            $map[$product_id] = [
                'original_id' => $source_id,
                'new_id' => $new_id,
                'opaque_fraction' => $result['opaque_fraction'],
                'generated_at' => gmdate('c'),
            ];
            $done[] = $product_id;
            $details[$product_id] = $map[$product_id];
        }

        update_option(self::MAP, $map, false);
        $status = [
            'version' => self::VERSION,
            'total_products' => count(self::product_ids()),
            'processed' => count($done),
            'mapped' => count($map),
            'products' => $done,
            'details' => $details,
            'errors' => $errors,
            'conflicts_active' => array_values(array_filter(self::CONFLICTS, 'is_plugin_active')),
            'updated_at' => gmdate('c'),
        ];
        update_option(self::STATUS, $status, false);
        return $status;
    }

    private static function make_png(int $product_id, int $source_id, string $source) {
        if (!class_exists('Imagick')) {
            return new WP_Error('imagick', 'Imagick unavailable');
        }
        $uploads = wp_upload_dir();
        if (!empty($uploads['error'])) {
            return new WP_Error('upload', $uploads['error']);
        }
        $directory = trailingslashit($uploads['basedir']) . 'ruwah-product-png';
        wp_mkdir_p($directory);
        $filename = 'product-' . $product_id . '-transparent-v23-' . time() . '.png';
        $destination = trailingslashit($directory) . $filename;

        try {
            $base = new Imagick($source);
            $base->setIteratorIndex(0);
            $base->setImageColorspace(Imagick::COLORSPACE_SRGB);
            $base->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);
            $base->thumbnailImage(1800, 1800, true, true);

            $best = null;
            foreach ([0.025, 0.045, 0.07, 0.10] as $fuzz_ratio) {
                $candidate = clone $base;
                $width = $candidate->getImageWidth();
                $height = $candidate->getImageHeight();
                $range = Imagick::getQuantumRange();
                $fuzz = (float) $range['quantumRangeLong'] * $fuzz_ratio;
                $points = self::edge_points($width, $height);
                foreach ($points as [$x, $y]) {
                    $target = $candidate->getImagePixelColor($x, $y);
                    $candidate->floodFillPaintImage(new ImagickPixel('rgba(0,0,0,0)'), $fuzz, $target, $x, $y, false);
                }
                $candidate->trimImage(0);
                $candidate->setImagePage(0, 0, 0, 0);
                $opaque = self::opaque_fraction($candidate);
                if ($candidate->getImageWidth() >= 240 && $candidate->getImageHeight() >= 240 && $opaque >= 0.05 && $opaque <= 0.90) {
                    if ($best) {
                        $best->clear();
                    }
                    $best = $candidate;
                    if ($opaque <= 0.72) {
                        break;
                    }
                } else {
                    $candidate->clear();
                }
            }

            if (!$best) {
                $base->clear();
                throw new RuntimeException('No valid transparent subject could be isolated');
            }

            $best->thumbnailImage(1060, 1060, true, true);
            $canvas = new Imagick();
            $canvas->newImage(1200, 1200, new ImagickPixel('rgba(0,0,0,0)'), 'png32');
            $canvas->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);
            $canvas->compositeImage(
                $best,
                Imagick::COMPOSITE_OVER,
                (int) ((1200 - $best->getImageWidth()) / 2),
                (int) ((1200 - $best->getImageHeight()) / 2)
            );
            $canvas->setImageFormat('png32');
            $canvas->setImageCompressionQuality(98);
            if (!$canvas->writeImage($destination)) {
                throw new RuntimeException('PNG write failed');
            }
            $base->clear();
            $best->clear();
            $canvas->clear();
        } catch (Throwable $error) {
            @unlink($destination);
            return new WP_Error('generation', $error->getMessage());
        }

        $validation = self::validate_png($destination);
        if (is_wp_error($validation)) {
            @unlink($destination);
            return $validation;
        }

        $attachment_id = wp_insert_attachment([
            'post_mime_type' => 'image/png',
            'post_title' => get_the_title($product_id) . ' transparent PNG',
            'post_status' => 'inherit',
            'post_parent' => $product_id,
        ], $destination, $product_id, true);
        if (is_wp_error($attachment_id)) {
            @unlink($destination);
            return $attachment_id;
        }

        $metadata = wp_generate_attachment_metadata((int) $attachment_id, $destination);
        wp_update_attachment_metadata((int) $attachment_id, $metadata);
        update_post_meta((int) $attachment_id, '_wp_attachment_image_alt', get_the_title($product_id));

        return [
            'attachment_id' => (int) $attachment_id,
            'opaque_fraction' => $validation['opaque_fraction'],
        ];
    }

    private static function edge_points(int $width, int $height): array {
        $points = [];
        foreach ([0.0, 0.2, 0.4, 0.6, 0.8, 1.0] as $ratio) {
            $x = min($width - 1, max(0, (int) round(($width - 1) * $ratio)));
            $y = min($height - 1, max(0, (int) round(($height - 1) * $ratio)));
            $points[] = [$x, 0];
            $points[] = [$x, $height - 1];
            $points[] = [0, $y];
            $points[] = [$width - 1, $y];
        }
        return array_values(array_unique($points, SORT_REGULAR));
    }

    private static function opaque_fraction(Imagick $image): float {
        $range = Imagick::getQuantumRange();
        $stats = $image->getImageChannelMean(Imagick::CHANNEL_ALPHA);
        $mean = is_array($stats) ? (float) ($stats['mean'] ?? 0.0) : 0.0;
        return $range['quantumRangeLong'] > 0 ? $mean / (float) $range['quantumRangeLong'] : 0.0;
    }

    private static function validate_png(string $file) {
        $info = @getimagesize($file);
        if (!$info || ($info['mime'] ?? '') !== 'image/png' || ($info[0] ?? 0) !== 1200 || ($info[1] ?? 0) !== 1200) {
            return new WP_Error('invalid', 'PNG dimensions or MIME are invalid');
        }
        if ((int) @filesize($file) < 25000) {
            return new WP_Error('invalid', 'PNG is blank or too small');
        }
        try {
            $image = new Imagick($file);
            $image->setImageAlphaChannel(Imagick::ALPHACHANNEL_SET);
            $opaque = self::opaque_fraction($image);
            $image->clear();
        } catch (Throwable $error) {
            return new WP_Error('alpha', $error->getMessage());
        }
        if ($opaque < 0.05) {
            return new WP_Error('blank', 'PNG contains too little visible product content');
        }
        if ($opaque > 0.92) {
            return new WP_Error('opaque', 'PNG does not contain meaningful transparency');
        }
        return ['opaque_fraction' => round($opaque, 4)];
    }

    public static function restore(): array {
        $map = (array) get_option(self::MAP, self::previous_map());
        $restored = 0;
        foreach ($map as $product_id => $row) {
            $product = wc_get_product((int) $product_id);
            $original_id = (int) ($row['original_id'] ?? 0);
            if ($product && $original_id && get_post($original_id)) {
                $product->set_image_id($original_id);
                $product->save();
                $restored++;
            }
        }
        $result = ['restored' => $restored, 'updated_at' => gmdate('c')];
        update_option(self::STATUS, $result, false);
        return $result;
    }

    public static function menu(): void {
        add_submenu_page('woocommerce', 'Ruwah Product PNGs', 'Product PNGs', 'manage_woocommerce', 'ruwah-product-pngs', [self::class, 'page']);
    }

    public static function page(): void {
        if (!current_user_can('manage_woocommerce')) {
            return;
        }
        $status = (array) get_option(self::STATUS, []);
        $map = (array) get_option(self::MAP, []);
        echo '<div class="wrap"><h1>Ruwah Product PNGs</h1><p>Mapped: <strong>' . esc_html((string) count($map)) . '</strong></p><pre>' . esc_html(wp_json_encode($status, JSON_PRETTY_PRINT)) . '</pre><p><a class="button button-primary" href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=ruwah_rpi_regenerate'), 'ruwah_rpi_regenerate')) . '">Regenerate & Apply</a> <a class="button" href="' . esc_url(wp_nonce_url(admin_url('admin-post.php?action=ruwah_rpi_restore'), 'ruwah_rpi_restore')) . '">Restore Originals</a></p></div>';
    }

    public static function regen(): void {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Not allowed');
        }
        check_admin_referer('ruwah_rpi_regenerate');
        self::process(true);
        wp_safe_redirect(admin_url('admin.php?page=ruwah-product-pngs'));
        exit;
    }

    public static function restore_action(): void {
        if (!current_user_can('manage_woocommerce')) {
            wp_die('Not allowed');
        }
        check_admin_referer('ruwah_rpi_restore');
        self::restore();
        wp_safe_redirect(admin_url('admin.php?page=ruwah-product-pngs'));
        exit;
    }
}

register_activation_hook(__FILE__, [Ruwah_Product_Images::class, 'activate']);
Ruwah_Product_Images::boot();
