<?php
/**
 * Plugin Name: Ruwah Fresh Commerce Design
 * Description: Reference-led editorial homepage experience for Ruwah Beauty using live WooCommerce products, pricing, stock and reviews.
 * Version: 5.0.2
 * Author: Ruwah Beauty
 * Requires PHP: 8.1
 */

defined('ABSPATH') || exit;

final class Ruwah_Fresh_Commerce_Design {
    private const VERSION = '5.0.2';

    public static function boot(): void {
        add_filter('template_include', [self::class, 'front_page_template'], 99);
        add_action('wp_enqueue_scripts', [self::class, 'assets'], 999);
        add_filter('body_class', [self::class, 'body_class']);
        add_filter('wc_price_args', [self::class, 'price_args'], 20);
    }

    public static function front_page_template(string $template): string {
        if (! is_front_page() || ! class_exists('WooCommerce')) {
            return $template;
        }
        $custom = __DIR__ . '/templates/home.php';
        return is_readable($custom) ? $custom : $template;
    }

    public static function assets(): void {
        if (! is_front_page()) {
            return;
        }

        /*
         * Some production hosts block direct requests to newly deployed custom
         * plugin asset files. Keep the files as the maintainable source of truth,
         * but inject their contents inline so the homepage cannot render unstyled
         * when those static URLs are denied by the web server.
         */
        $css_path = __DIR__ . '/assets/home.css';
        if (is_readable($css_path)) {
            $css = file_get_contents($css_path);
            if (false !== $css && '' !== trim($css)) {
                wp_register_style('ruwah-reference-home', false, ['rwb-theme'], self::VERSION);
                wp_enqueue_style('ruwah-reference-home');
                wp_add_inline_style('ruwah-reference-home', $css);
            }
        }

        wp_enqueue_script('wc-add-to-cart');

        $js_path = __DIR__ . '/assets/home.js';
        if (is_readable($js_path)) {
            $js = file_get_contents($js_path);
            if (false !== $js && '' !== trim($js)) {
                wp_add_inline_script('wc-add-to-cart', $js, 'after');
            }
        }
    }

    public static function body_class(array $classes): array {
        if (is_front_page()) {
            $classes[] = 'rwb-reference-home-v5';
        }
        return $classes;
    }

    public static function price_args(array $args): array {
        if (is_front_page()) {
            $args['decimals'] = 0;
        }
        return $args;
    }
}

Ruwah_Fresh_Commerce_Design::boot();
