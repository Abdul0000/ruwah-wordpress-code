<?php
defined('ABSPATH') || exit;

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    register_nav_menus(['primary' => 'Primary Menu']);
});

add_action('wp_enqueue_scripts', function () {
    $style = get_stylesheet_directory() . '/style.css';
    $script = get_stylesheet_directory() . '/theme.js';
    wp_enqueue_style('astra-parent', get_template_directory_uri() . '/style.css', [], wp_get_theme('astra')->get('Version'));
    wp_enqueue_style('ruwa-v30', get_stylesheet_directory_uri() . '/style.css', ['astra-parent'], is_readable($style) ? (string) filemtime($style) : '30.0.0');
    wp_enqueue_script('ruwa-v30', get_stylesheet_directory_uri() . '/theme.js', [], is_readable($script) ? (string) filemtime($script) : '30.0.0', true);
}, 40);

add_filter('body_class', function ($classes) {
    $classes[] = 'ruwa-v30';
    return $classes;
});

function ruwa_page_url(string $slug): string {
    $page = get_page_by_path($slug);
    return $page ? get_permalink($page) : home_url('/' . trim($slug, '/') . '/');
}

function ruwa_shop_url(): string {
    return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
}

function ruwa_cart_count(): int {
    return function_exists('WC') && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

function ruwa_products(int $limit = 12): array {
    if (!function_exists('wc_get_products')) return [];
    return wc_get_products(['status' => 'publish', 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC']);
}

function ruwa_product_card($product, string $badge = ''): void {
    if (!$product) return;
    $id = $product->get_id();
    $classes = implode(' ', array_map('sanitize_html_class', wc_get_product_class('ruwa-product-card', $product)));
    echo '<article class="' . esc_attr($classes) . '">';
    if ($badge) echo '<span class="ruwa-badge">' . esc_html($badge) . '</span>';
    echo '<a class="ruwa-product-media" href="' . esc_url($product->get_permalink()) . '">' . wp_kses_post($product->get_image('woocommerce_thumbnail', ['loading' => 'lazy'])) . '</a>';
    echo '<div class="ruwa-product-copy"><small>' . esc_html(wc_get_product_category_list($id, ', ', '', '')) . '</small><h3><a href="' . esc_url($product->get_permalink()) . '">' . esc_html($product->get_name()) . '</a></h3><div class="ruwa-price">' . wp_kses_post($product->get_price_html()) . '</div></div>';
    echo '<div class="ruwa-product-actions"><a class="ruwa-text-link" href="' . esc_url($product->get_permalink()) . '">View product</a>';
    if ($product->is_purchasable() && $product->is_in_stock()) {
        echo '<a rel="nofollow" data-product_id="' . esc_attr((string) $id) . '" data-quantity="1" class="button add_to_cart_button ajax_add_to_cart ruwa-mini-add" href="' . esc_url($product->add_to_cart_url()) . '">Add to cart</a>';
    }
    echo '</div></article>';
}

add_filter('woocommerce_output_related_products_args', function ($args) {
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;
    return $args;
});

add_filter('woocommerce_product_loop_start', function () {
    return '<ul class="products columns-4">';
});
