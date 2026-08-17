<?php
/**
 * Plugin Name: Ruwah Product Images
 * Description: Authoritative lightweight transparent RWB product image and logo mapping for Ruwah Beauty.
 * Version: 3.2.0
 * Author: Ruwah Beauty
 * Requires at least: 6.5
 * Requires PHP: 8.1
 */
defined('ABSPATH') || exit;

final class Ruwah_Product_Images_V3 {
    const VERSION = '3.2.0';
    const STATE_OPTION = 'ruwah_product_images_v3_state';
    const SNAPSHOT_OPTION = 'ruwah_product_images_v3_snapshot';

    public static function init() {
        add_action('admin_notices', [__CLASS__, 'admin_notice']);
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::add_command('ruwah product-images', 'Ruwah_Product_Images_V3_CLI');
        }
    }

    public static function activate() { self::deactivate_conflicts(); }

    public static function mappings() {
        return [
            54 => ['name' => 'Triple Action Serum', 'featured' => 2, 'gallery' => [1, 3, 4]],
            62 => ['name' => 'Rice Whitening Cream', 'featured' => 8, 'gallery' => [5, 6, 7]],
            64 => ['name' => 'Rice Glow Serum', 'featured' => 10, 'gallery' => [9, 11, 12]],
            60 => ['name' => 'Rice Brightening Face Wash', 'featured' => 14, 'gallery' => [13, 15, 16]],
            68 => ['name' => 'Rice Glow Sun Lotion', 'featured' => 18, 'gallery' => [17, 19, 20]],
        ];
    }

    public static function asset_numbers() { return range(1, 20); }
    public static function state() { $s = get_option(self::STATE_OPTION, []); return is_array($s) ? $s : []; }
    public static function save_state($s) { update_option(self::STATE_OPTION, $s, false); }

    public static function begin() {
        self::deactivate_conflicts();
        $snapshot = ['custom_logo' => (int) get_theme_mod('custom_logo', 0), 'products' => []];
        foreach (self::mappings() as $id => $map) {
            $p = function_exists('wc_get_product') ? wc_get_product($id) : null;
            if (!$p) continue;
            $snapshot['products'][$id] = [
                'image_id' => (int) $p->get_image_id(),
                'gallery_ids' => array_map('intval', $p->get_gallery_image_ids()),
            ];
        }
        update_option(self::SNAPSHOT_OPTION, $snapshot, false);
        $s = self::state();
        $s['started_at'] = current_time('mysql');
        $s['last_error'] = '';
        self::save_state($s);
        return $snapshot;
    }

    public static function stage($batch_size = 2, $retry_failed = false) {
        $batch_size = max(1, (int) $batch_size);
        $s = self::state();
        if (!isset($s['assets']) || !is_array($s['assets'])) $s['assets'] = [];
        if (!isset($s['normalized']) || !is_array($s['normalized'])) $s['normalized'] = [];
        $processed = 0;
        $queue = ['logo'];
        foreach (self::asset_numbers() as $n) $queue[] = 'product-' . $n;

        foreach ($queue as $key) {
            if ($processed >= $batch_size) break;
            $existing = self::attachment_for_key($key);
            if ($existing) {
                $s['assets'][$key] = $existing;
                if (0 === strpos($key, 'product-') && !self::is_normalized($key)) {
                    if (self::refresh_key($key)) {
                        $s = self::state();
                    } else {
                        $s['failed'][$key] = current_time('mysql');
                        self::save_state($s);
                    }
                    $processed++;
                }
                continue;
            }
            if (!$retry_failed && !empty($s['failed'][$key])) continue;
            $aid = self::import_key($key);
            if ($aid) {
                $s = self::state();
                $s['assets'][$key] = $aid;
                unset($s['failed'][$key]);
            } else {
                $s['failed'][$key] = current_time('mysql');
            }
            $processed++;
            self::save_state($s);
        }
        $s = self::state();
        $s['staged_at'] = current_time('mysql');
        self::save_state($s);
        return $processed;
    }

    public static function apply() {
        self::deactivate_conflicts();
        if (!self::attachment_for_key('logo')) self::import_key('logo');
        foreach (self::asset_numbers() as $n) {
            $key = 'product-' . $n;
            if (!self::attachment_for_key($key)) self::import_key($key);
            if (!self::is_normalized($key)) self::refresh_key($key);
        }

        $logo = self::attachment_for_key('logo');
        if ($logo) set_theme_mod('custom_logo', $logo);
        foreach (self::mappings() as $id => $map) {
            $p = function_exists('wc_get_product') ? wc_get_product($id) : null;
            if (!$p) continue;
            $featured = self::attachment_for_key('product-' . $map['featured']);
            $gallery = [];
            foreach ($map['gallery'] as $n) {
                $a = self::attachment_for_key('product-' . $n);
                if ($a) $gallery[] = $a;
            }
            if ($featured) $p->set_image_id($featured);
            $p->set_gallery_image_ids($gallery);
            $p->save();
            if (function_exists('wc_delete_product_transients')) wc_delete_product_transients($id);
        }
        $s = self::state();
        $s['applied_at'] = current_time('mysql');
        $s['complete'] = self::is_complete();
        self::save_state($s);
        if (function_exists('wp_cache_flush')) wp_cache_flush();
        return $s['complete'];
    }

    public static function restore() {
        $snapshot = get_option(self::SNAPSHOT_OPTION, []);
        if (!is_array($snapshot)) return false;
        if (array_key_exists('custom_logo', $snapshot)) set_theme_mod('custom_logo', (int) $snapshot['custom_logo']);
        foreach ((array) ($snapshot['products'] ?? []) as $id => $data) {
            $p = function_exists('wc_get_product') ? wc_get_product((int) $id) : null;
            if (!$p) continue;
            $p->set_image_id((int) ($data['image_id'] ?? 0));
            $p->set_gallery_image_ids(array_map('intval', (array) ($data['gallery_ids'] ?? [])));
            $p->save();
            if (function_exists('wc_delete_product_transients')) wc_delete_product_transients((int) $id);
        }
        if (function_exists('wp_cache_flush')) wp_cache_flush();
        return true;
    }

    public static function status() {
        $rows = [];
        $normalized = 0;
        foreach (self::asset_numbers() as $n) if (self::is_normalized('product-' . $n)) $normalized++;
        foreach (self::mappings() as $id => $map) {
            $p = function_exists('wc_get_product') ? wc_get_product($id) : null;
            $ef = self::attachment_for_key('product-' . $map['featured']);
            $eg = array_values(array_filter(array_map(function($n) { return self::attachment_for_key('product-' . $n); }, $map['gallery'])));
            $rows[] = [
                'product_id' => $id,
                'name' => $map['name'],
                'exists' => (bool) $p,
                'featured_ok' => $p && $ef && (int) $p->get_image_id() === (int) $ef,
                'gallery_ok' => $p && array_map('intval', $p->get_gallery_image_ids()) === array_map('intval', $eg),
            ];
        }
        return [
            'complete' => self::is_complete(),
            'logo_id' => self::attachment_for_key('logo'),
            'asset_count' => count(array_filter(array_map(function($n) { return self::attachment_for_key('product-' . $n); }, self::asset_numbers()))),
            'normalized_count' => $normalized,
            'products' => $rows,
            'conflicts_active' => self::active_conflicts(),
        ];
    }

    public static function is_complete() {
        if (!self::attachment_for_key('logo')) return false;
        foreach (self::asset_numbers() as $n) {
            $key = 'product-' . $n;
            if (!self::attachment_for_key($key) || !self::is_normalized($key)) return false;
        }
        foreach (self::mappings() as $id => $map) {
            $p = function_exists('wc_get_product') ? wc_get_product($id) : null;
            if (!$p) return false;
            if ((int) $p->get_image_id() !== (int) self::attachment_for_key('product-' . $map['featured'])) return false;
            $expected = [];
            foreach ($map['gallery'] as $n) $expected[] = (int) self::attachment_for_key('product-' . $n);
            if (array_map('intval', $p->get_gallery_image_ids()) !== $expected) return false;
        }
        return empty(self::active_conflicts());
    }

    private static function import_key($key) {
        $existing = self::attachment_for_key($key);
        if ($existing) return $existing;
        $source = self::source_path($key);
        if (!$source || !is_readable($source)) return 0;
        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        $u = wp_upload_dir();
        if (!empty($u['error'])) return 0;
        $dir = trailingslashit($u['basedir']) . 'ruwah-rwb-v3';
        wp_mkdir_p($dir);
        $filename = sanitize_file_name($key . '.webp');
        $target = trailingslashit($dir) . $filename;
        $ok = 0 === strpos($key, 'product-') ? self::normalize_to_transparent($source, $target) : copy($source, $target);
        if (!$ok) return 0;
        $aid = wp_insert_attachment([
            'post_mime_type' => 'image/webp',
            'post_title' => 'Ruwah ' . ucwords(str_replace('-', ' ', $key)),
            'post_status' => 'inherit',
            'post_content' => '',
        ], $target);
        if (is_wp_error($aid) || !$aid) return 0;
        $meta = wp_generate_attachment_metadata($aid, $target);
        if (is_array($meta)) wp_update_attachment_metadata($aid, $meta);
        $s = self::state();
        if (!isset($s['assets']) || !is_array($s['assets'])) $s['assets'] = [];
        $s['assets'][$key] = (int) $aid;
        if (0 === strpos($key, 'product-')) {
            if (!isset($s['normalized']) || !is_array($s['normalized'])) $s['normalized'] = [];
            $s['normalized'][$key] = self::VERSION;
        }
        self::save_state($s);
        return (int) $aid;
    }

    private static function refresh_key($key) {
        $aid = self::attachment_for_key($key);
        $source = self::source_path($key);
        $target = $aid ? get_attached_file($aid) : '';
        if (!$aid || !$source || !$target || !is_readable($source)) return false;
        require_once ABSPATH . 'wp-admin/includes/image.php';
        if (!self::normalize_to_transparent($source, $target)) return false;
        $meta = wp_generate_attachment_metadata($aid, $target);
        if (is_array($meta)) wp_update_attachment_metadata($aid, $meta);
        $s = self::state();
        if (!isset($s['normalized']) || !is_array($s['normalized'])) $s['normalized'] = [];
        $s['normalized'][$key] = self::VERSION;
        unset($s['failed'][$key]);
        self::save_state($s);
        return true;
    }

    private static function normalize_to_transparent($source, $target) {
        if (!function_exists('imagecreatefromwebp') || !function_exists('imagewebp')) {
            return copy($source, $target);
        }
        $im = @imagecreatefromwebp($source);
        if (!$im) return copy($source, $target);

        $w = imagesx($im);
        $h = imagesy($im);
        $out = imagecreatetruecolor($w, $h);
        if (!$out) {
            imagedestroy($im);
            return false;
        }
        imagealphablending($out, false);
        imagesavealpha($out, true);
        $transparent = imagecolorallocatealpha($out, 0, 0, 0, 127);
        imagefill($out, 0, 0, $transparent);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $rgba = imagecolorat($im, $x, $y);
                $r = ($rgba >> 16) & 0xFF;
                $g = ($rgba >> 8) & 0xFF;
                $b = $rgba & 0xFF;
                $is_cyan_canvas = $g >= 185 && $b >= 195 && $r <= 230 && ($g - $r) >= 10 && ($b - $r) >= 14 && abs($b - $g) <= 45;
                if ($is_cyan_canvas) {
                    imagesetpixel($out, $x, $y, $transparent);
                } else {
                    imagesetpixel($out, $x, $y, $rgba);
                }
            }
        }

        $saved = imagewebp($out, $target, 90);
        imagedestroy($out);
        imagedestroy($im);
        return (bool) $saved;
    }

    private static function is_normalized($key) {
        $s = self::state();
        return isset($s['normalized'][$key]) && self::VERSION === (string) $s['normalized'][$key];
    }

    private static function source_path($key) {
        $c = [];
        if ('logo' === $key) {
            $c[] = plugin_dir_path(__FILE__) . 'assets/logo/rwb-logo.webp';
            $c[] = WP_PLUGIN_DIR . '/ruwah-product-images-lite/assets/logo/rwb-logo.webp';
        } elseif (preg_match('/^product-(\d+)$/', $key, $m)) {
            $n = (int) $m[1];
            $c[] = plugin_dir_path(__FILE__) . 'assets/products/' . $n . '.webp';
            $c[] = WP_PLUGIN_DIR . '/ruwah-product-images-lite/assets/products/' . $n . '.webp';
        }
        foreach ($c as $candidate) if (is_readable($candidate)) return $candidate;
        return '';
    }

    public static function attachment_for_key($key) {
        $s = self::state();
        $id = isset($s['assets'][$key]) ? (int) $s['assets'][$key] : 0;
        if ($id && 'attachment' === get_post_type($id)) return $id;
        return 0;
    }

    private static function active_conflicts() {
        if (!function_exists('get_plugins')) require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $active = (array) get_option('active_plugins', []);
        $out = [];
        foreach ($active as $plugin) {
            if (false !== strpos($plugin, 'ruwah-product-images-lite/') || false !== strpos($plugin, 'nub-product-images-deep-purple-v4/')) $out[] = $plugin;
        }
        return $out;
    }

    private static function deactivate_conflicts() {
        if (!function_exists('deactivate_plugins')) require_once ABSPATH . 'wp-admin/includes/plugin.php';
        $c = self::active_conflicts();
        if ($c) deactivate_plugins($c, true, false);
    }

    public static function admin_notice() {
        if (!current_user_can('manage_options')) return;
        $s = self::status();
        if ($s['complete']) return;
        echo '<div class="notice notice-warning"><p><strong>Ruwah Product Images:</strong> media mapping is not complete. Run the configured Ruwah product-image deployment/apply command.</p></div>';
    }
}

if (defined('WP_CLI') && WP_CLI) {
    class Ruwah_Product_Images_V3_CLI {
        public function begin($a, $b) { Ruwah_Product_Images_V3::begin(); WP_CLI::success('Ruwah product image snapshot created and conflicting image plugins deactivated.'); }
        public function stage($a, $b) { $batch = isset($b['batch-size']) ? (int) $b['batch-size'] : 2; $retry = isset($b['retry-failed']); $count = Ruwah_Product_Images_V3::stage($batch, $retry); WP_CLI::success('Staged/refreshed ' . $count . ' asset(s).'); }
        public function apply($a, $b) { $complete = Ruwah_Product_Images_V3::apply(); if (!$complete) WP_CLI::error('Ruwah product image apply finished but verification is incomplete.'); WP_CLI::success('All 20 RWB images are transparent, optimized and mapped, and the RWB logo is applied.'); }
        public function restore($a, $b) { if (!Ruwah_Product_Images_V3::restore()) WP_CLI::error('No valid snapshot was found.'); WP_CLI::success('Previous product images and logo restored.'); }
        public function status($a, $b) { $s = Ruwah_Product_Images_V3::status(); WP_CLI::line(wp_json_encode($s, JSON_PRETTY_PRINT)); if (isset($b['require-complete']) && !$s['complete']) WP_CLI::error('Ruwah product image status is incomplete.'); if ($s['complete']) WP_CLI::success('Ruwah product image status is complete.'); }
    }
}

register_activation_hook(__FILE__, ['Ruwah_Product_Images_V3', 'activate']);
Ruwah_Product_Images_V3::init();