<?php
defined('ABSPATH') || exit;

define('RUWAH_THEME_VERSION', '2.0.1');

add_action('after_setup_theme', function () {
    load_theme_textdomain('ruwah', get_template_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    register_nav_menus([
        'primary' => __('Primary Menu', 'ruwah'),
        'footer' => __('Footer Menu', 'ruwah'),
    ]);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('ruwah-fonts', 'https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700;800&display=swap', [], null);
    wp_enqueue_style('ruwah-style', get_stylesheet_uri(), ['ruwah-fonts'], RUWAH_THEME_VERSION);
    wp_enqueue_script('ruwah-theme', get_template_directory_uri() . '/theme.js', [], RUWAH_THEME_VERSION, true);
});

function ruwah_page_url($slug) {
    $page = get_page_by_path(trim($slug, '/'));
    return $page ? get_permalink($page) : home_url('/' . trim($slug, '/') . '/');
}
function ruwah_shop_url() { return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : ruwah_page_url('shop'); }
function ruwah_cart_url() { return function_exists('wc_get_cart_url') ? wc_get_cart_url() : ruwah_page_url('cart'); }
function ruwah_account_url() { return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : ruwah_page_url('my-account'); }
function ruwah_products($limit = 8, $args = []) {
    if (!function_exists('wc_get_products')) return [];
    return wc_get_products(wp_parse_args($args, ['status' => 'publish', 'limit' => $limit, 'orderby' => 'date', 'order' => 'DESC']));
}
function ruwah_featured_product() {
    if (!function_exists('wc_get_product')) return null;
    $ids = get_posts(['post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => 1, 'fields' => 'ids', 'meta_key' => 'total_sales', 'orderby' => 'meta_value_num', 'order' => 'DESC', 'no_found_rows' => true]);
    if ($ids) {
        $product = wc_get_product((int) $ids[0]);
        if ($product) return $product;
    }
    $fallback = ruwah_products(1);
    return $fallback[0] ?? null;
}
function ruwah_shipping_progress() {
    $threshold = (float) apply_filters('ruwah_free_shipping_threshold', 5000);
    $subtotal = function_exists('WC') && WC()->cart ? (float) WC()->cart->get_subtotal() : 0;
    $remaining = max(0, $threshold - $subtotal);
    $percent = $threshold > 0 ? min(100, ($subtotal / $threshold) * 100) : 100;
    return compact('threshold', 'subtotal', 'remaining', 'percent');
}
function ruwah_product_card($product, $badge = '') {
    if (!$product || !is_a($product, 'WC_Product')) return;
    $id = $product->get_id();
    $rating = (float) $product->get_average_rating();
    $count = (int) $product->get_review_count();
    $filled = max(0, min(5, (int) round($rating)));
    $stars = str_repeat('★', $filled) . str_repeat('☆', 5 - $filled);
    echo '<article class="rb-product-card rb-reveal">';
    if ($badge) echo '<span class="rb-badge">' . esc_html($badge) . '</span>';
    echo '<a class="rb-product-media" href="' . esc_url($product->get_permalink()) . '">' . wp_kses_post($product->get_image('woocommerce_thumbnail', ['loading' => 'lazy', 'decoding' => 'async'])) . '</a>';
    echo '<div class="rb-product-copy"><h3><a href="' . esc_url($product->get_permalink()) . '">' . esc_html($product->get_name()) . '</a></h3>';
    echo '<div class="rb-card-rating" role="img" aria-label="' . esc_attr($count ? sprintf(__('%1$s out of 5 based on %2$d reviews', 'ruwah'), number_format_i18n($rating, 1), $count) : __('No reviews yet', 'ruwah')) . '"><span aria-hidden="true">' . esc_html($stars) . '</span><span class="rb-rating-count">' . esc_html($count ? number_format_i18n($rating, 1) . ' · ' . $count . ' ' . ($count === 1 ? __('review', 'ruwah') : __('reviews', 'ruwah')) : __('New', 'ruwah')) . '</span></div>';
    echo '<div class="rb-price">' . wp_kses_post($product->get_price_html()) . '</div></div><div class="rb-product-actions">';
    if ($product->is_purchasable() && $product->is_in_stock()) echo '<a rel="nofollow" data-product_id="' . esc_attr((string) $id) . '" data-quantity="1" class="button add_to_cart_button ajax_add_to_cart" href="' . esc_url($product->add_to_cart_url()) . '">' . esc_html__('Add to Bag', 'ruwah') . '</a>';
    else echo '<a class="button" href="' . esc_url($product->get_permalink()) . '">' . esc_html__('View Product', 'ruwah') . '</a>';
    echo '</div></article>';
}
add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    ob_start(); ?><span class="rb-cart-count"><?php echo esc_html(function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0); ?></span><?php
    $fragments['.rb-cart-count'] = ob_get_clean();
    return $fragments;
});