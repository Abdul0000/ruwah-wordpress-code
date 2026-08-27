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
