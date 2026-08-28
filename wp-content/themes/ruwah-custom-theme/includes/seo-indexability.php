<?php
defined('ABSPATH') || exit;

/**
 * Public-store SEO/indexability hardening.
 * Applies only to customer-facing catalogue surfaces; cart, checkout, account
 * and other transactional endpoints remain under WordPress/WooCommerce rules.
 */
add_filter('wp_robots', static function (array $robots): array {
    if ('1' !== (string) get_option('blog_public')) {
        return $robots;
    }

    $public_surface = is_front_page()
        || (function_exists('is_shop') && is_shop())
        || (function_exists('is_product') && is_product())
        || (function_exists('is_product_taxonomy') && is_product_taxonomy());

    if (! $public_surface) {
        return $robots;
    }

    unset($robots['noindex'], $robots['nofollow']);
    $robots['index'] = true;
    $robots['follow'] = true;
    $robots['max-image-preview'] = 'large';
    return $robots;
}, 20000);

add_filter('robots_txt', static function (string $output, bool $public): string {
    if (! $public) {
        return $output;
    }

    if (false === stripos($output, 'User-agent:')) {
        $output = "User-agent: *\nAllow: /\n" . ltrim($output);
    }

    if (false === stripos($output, 'Sitemap:')) {
        $output = rtrim($output) . "\nSitemap: " . esc_url_raw(home_url('/sitemap_index.xml')) . "\n";
    }

    return $output;
}, 20000, 2);
