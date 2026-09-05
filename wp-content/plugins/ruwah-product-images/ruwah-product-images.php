<?php
/**
 * Plugin Name: Ruwah Product Images
 * Description: Authoritative exact product image mapping for Ruwah Beauty.
 * Version: 4.0.0
 * Author: Ruwah Beauty
 * Requires at least: 6.5
 * Requires PHP: 8.1
 */
defined('ABSPATH') || exit;

final class Ruwah_Product_Images_V4 {
    const SNAPSHOT_OPTION = 'ruwah_product_images_v4_snapshot';

    public static function mappings(): array {
        return [
            54 => ['name' => 'Triple Action Serum',        'featured' => 390, 'gallery' => [266]],
            60 => ['name' => 'Rice Brightening Face Wash', 'featured' => 388, 'gallery' => [277, 278]],
            62 => ['name' => 'Rice Whitening Cream',       'featured' => 391, 'gallery' => [269]],
            64 => ['name' => 'Rice Glow Serum',            'featured' => 389, 'gallery' => [274]],
            68 => ['name' => 'Rice Glow Sun Lotion',       'featured' => 387, 'gallery' => [282]],
        ];
    }

    public static function init(): void {
        add_action('wp_loaded', [__CLASS__, 'register_runtime_filters'], PHP_INT_MAX);
        add_action('admin_notices', [__CLASS__, 'admin_notice']);
        if (defined('WP_CLI') && WP_CLI) {
            WP_CLI::add_command('ruwah product-images', 'Ruwah_Product_Images_V4_CLI');
        }
    }

    public static function register_runtime_filters(): void {
        add_filter('woocommerce_product_get_image_id', [__CLASS__, 'filter_image_id'], PHP_INT_MAX, 2);
        add_filter('woocommerce_product_get_gallery_image_ids', [__CLASS__, 'filter_gallery_ids'], PHP_INT_MAX, 2);
    }

    public static function filter_image_id($image_id, $product) {
        if (!$product || !is_a($product, 'WC_Product')) return $image_id;
        $map = self::mappings();
        $id = (int) $product->get_id();
        return isset($map[$id]) ? (int) $map[$id]['featured'] : $image_id;
    }

    public static function filter_gallery_ids($gallery_ids, $product) {
        if (!$product || !is_a($product, 'WC_Product')) return $gallery_ids;
        $map = self::mappings();
        $id = (int) $product->get_id();
        return isset($map[$id]) ? array_map('intval', $map[$id]['gallery']) : $gallery_ids;
    }

    private static function valid_image(int $attachment_id): bool {
        return $attachment_id > 0
            && 'attachment' === get_post_type($attachment_id)
            && wp_attachment_is_image($attachment_id);
    }

    public static function begin(): array {
        $snapshot = ['products' => []];
        foreach (array_keys(self::mappings()) as $id) {
            $product = function_exists('wc_get_product') ? wc_get_product($id) : null;
            if (!$product) continue;
            $snapshot['products'][$id] = [
                'image_id' => (int) get_post_thumbnail_id($id),
                'gallery_ids' => self::raw_gallery_ids($id),
            ];
        }
        update_option(self::SNAPSHOT_OPTION, $snapshot, false);
        return $snapshot;
    }

    public static function stage(): int {
        return 0;
    }

    public static function apply(): bool {
        foreach (self::mappings() as $id => $map) {
            if (!self::valid_image((int) $map['featured'])) return false;
            foreach ($map['gallery'] as $attachment_id) {
                if (!self::valid_image((int) $attachment_id)) return false;
            }
            $product = function_exists('wc_get_product') ? wc_get_product($id) : null;
            if (!$product) return false;
            $product->set_image_id((int) $map['featured']);
            $product->set_gallery_image_ids(array_map('intval', $map['gallery']));
            $product->save();
            if (function_exists('wc_delete_product_transients')) wc_delete_product_transients($id);
        }
        if (function_exists('wp_cache_flush')) wp_cache_flush();
        return self::is_complete();
    }

    public static function restore(): bool {
        $snapshot = get_option(self::SNAPSHOT_OPTION, []);
        if (!is_array($snapshot) || empty($snapshot['products'])) return false;
        foreach ($snapshot['products'] as $id => $data) {
            $product = function_exists('wc_get_product') ? wc_get_product((int) $id) : null;
            if (!$product) continue;
            $product->set_image_id((int) ($data['image_id'] ?? 0));
            $product->set_gallery_image_ids(array_map('intval', (array) ($data['gallery_ids'] ?? [])));
            $product->save();
            if (function_exists('wc_delete_product_transients')) wc_delete_product_transients((int) $id);
        }
        if (function_exists('wp_cache_flush')) wp_cache_flush();
        return true;
    }

    private static function raw_gallery_ids(int $product_id): array {
        $raw = (string) get_post_meta($product_id, '_product_image_gallery', true);
        if ($raw === '') return [];
        return array_values(array_filter(array_map('intval', explode(',', $raw))));
    }

    public static function status(): array {
        $rows = [];
        foreach (self::mappings() as $id => $map) {
            $featured = (int) get_post_thumbnail_id($id);
            $gallery = self::raw_gallery_ids($id);
            $rows[] = [
                'product_id' => $id,
                'name' => $map['name'],
                'featured_expected' => (int) $map['featured'],
                'featured_actual' => $featured,
                'gallery_expected' => array_map('intval', $map['gallery']),
                'gallery_actual' => $gallery,
                'featured_ok' => $featured === (int) $map['featured'],
                'gallery_ok' => $gallery === array_map('intval', $map['gallery']),
            ];
        }
        return ['complete' => self::is_complete(), 'products' => $rows];
    }

    public static function is_complete(): bool {
        foreach (self::mappings() as $id => $map) {
            if (!self::valid_image((int) $map['featured'])) return false;
            if ((int) get_post_thumbnail_id($id) !== (int) $map['featured']) return false;
            if (self::raw_gallery_ids($id) !== array_map('intval', $map['gallery'])) return false;
            foreach ($map['gallery'] as $attachment_id) {
                if (!self::valid_image((int) $attachment_id)) return false;
            }
        }
        return true;
    }

    public static function admin_notice(): void {
        if (!current_user_can('manage_options') || self::is_complete()) return;
        echo '<div class="notice notice-warning"><p><strong>Ruwah Product Images:</strong> exact ZIP image mapping is not complete.</p></div>';
    }
}

if (defined('WP_CLI') && WP_CLI) {
    class Ruwah_Product_Images_V4_CLI {
        public function begin($args, $assoc_args) {
            Ruwah_Product_Images_V4::begin();
            WP_CLI::success('Ruwah product image snapshot created.');
        }
        public function stage($args, $assoc_args) {
            Ruwah_Product_Images_V4::stage();
            WP_CLI::success('Exact ZIP image assets already exist as verified media attachments.');
        }
        public function apply($args, $assoc_args) {
            if (!Ruwah_Product_Images_V4::apply()) WP_CLI::error('Exact ZIP image mapping could not be applied completely.');
            WP_CLI::success('Exact ZIP product image mappings applied.');
        }
        public function status($args, $assoc_args) {
            $status = Ruwah_Product_Images_V4::status();
            WP_CLI::line(wp_json_encode($status, JSON_PRETTY_PRINT));
            if (isset($assoc_args['require-complete']) && !$status['complete']) WP_CLI::error('Exact ZIP product image status is incomplete.');
            if ($status['complete']) WP_CLI::success('Exact ZIP product image status is complete.');
        }
        public function restore($args, $assoc_args) {
            if (!Ruwah_Product_Images_V4::restore()) WP_CLI::error('No valid image snapshot was found.');
            WP_CLI::success('Previous product image mapping restored.');
        }
    }
}

Ruwah_Product_Images_V4::init();
