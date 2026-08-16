<?php
/**
 * Plugin Name: Ruwah Fresh Commerce Design
 * Description: Reference-led editorial homepage experience for Ruwah Beauty using live WooCommerce products, pricing, stock and reviews.
 * Version: 5.0.0
 * Author: Ruwah Beauty
 * Requires PHP: 8.1
 */

defined('ABSPATH') || exit;

final class Ruwah_Fresh_Commerce_Design {
    private const VERSION = '5.0.0';

    public static function boot(): void {
        add_filter('template_include', [self::class, 'front_page_template'], 99);
        add_action('wp_enqueue_scripts', [self::class, 'assets'], 999);
        add_filter('body_class', [self::class, 'body_class']);
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
        wp_enqueue_style('ruwah-reference-home', plugins_url('assets/home.css', __FILE__), ['rwb-theme'], self::VERSION);
        wp_enqueue_script('wc-add-to-cart');
        wp_enqueue_script('ruwah-reference-home', plugins_url('assets/home.js', __FILE__), [], self::VERSION, true);
        wp_script_add_data('ruwah-reference-home', 'strategy', 'defer');
    }

    public static function body_class(array $classes): array {
        if (is_front_page()) {
            $classes[] = 'rwb-reference-home-v5';
        }
        return $classes;
    }
}

Ruwah_Fresh_Commerce_Design::boot();
