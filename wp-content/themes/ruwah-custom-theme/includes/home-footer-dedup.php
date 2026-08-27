<?php
defined('ABSPATH') || exit;

/*
 * The standalone Ruwah homepage template already renders its own launch-ready
 * footer. Prevent the legacy commerce plugin footer from being appended again
 * by wp_footer(), while leaving every other wp_footer callback intact.
 */
add_action('wp', static function (): void {
    if (! is_front_page() || ! class_exists('Ruwah_Fresh_Commerce_Design')) {
        return;
    }
    remove_action('wp_footer', [Ruwah_Fresh_Commerce_Design::class, 'reference_footer'], 5);
}, 1000);

/* Advertise the canonical WordPress core sitemap to compliant crawlers. */
add_filter('robots_txt', static function (string $output, bool $public): string {
    if (! $public) {
        return $output;
    }
    $sitemap = home_url('/wp-sitemap.xml');
    if (false === stripos($output, 'Sitemap:')) {
        $output = rtrim($output) . "\nSitemap: " . esc_url_raw($sitemap) . "\n";
    }
    return $output;
}, 20, 2);

/*
 * Some audit/SEO tools probe Yoast-style /sitemap_index.xml. Ruwah uses the
 * WordPress core sitemap, so expose a permanent compatibility redirect rather
 * than maintaining a second sitemap implementation.
 */
add_action('template_redirect', static function (): void {
    $path = isset($_SERVER['REQUEST_URI']) ? (string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';
    if ('/sitemap_index.xml' !== rtrim($path, '/') && '/sitemap_index.xml' !== $path) {
        return;
    }
    wp_safe_redirect(home_url('/wp-sitemap.xml'), 301, 'Ruwah Sitemap Compatibility');
    exit;
}, 1);
