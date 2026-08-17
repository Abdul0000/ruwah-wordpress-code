<?php
defined('ABSPATH') || exit;

define('RUWAH_THEME_VERSION', '4.0.0');

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
    wp_enqueue_style('rwb-theme', get_stylesheet_uri(), ['ruwah-fonts'], RUWAH_THEME_VERSION);
    wp_add_inline_style('rwb-theme', '.rwb-layer[hidden],.rwb-mobile-menu[hidden]{display:none!important}');

    wp_enqueue_script('rwb-theme', get_template_directory_uri() . '/theme.js', [], RUWAH_THEME_VERSION, true);
    wp_script_add_data('rwb-theme', 'strategy', 'defer');

    if (function_exists('WC') && (is_front_page() || (function_exists('is_woocommerce') && is_woocommerce()) || is_cart() || is_checkout())) {
        wp_enqueue_script('wc-add-to-cart');
    }
}, 30);

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
    echo '<article class="rwb-card" data-reveal>';
    if ($badge) echo '<span class="rwb-badge">' . esc_html($badge) . '</span>';
    echo '<a class="rwb-card-media" href="' . esc_url($product->get_permalink()) . '">' . wp_kses_post($product->get_image('woocommerce_thumbnail', ['loading' => 'lazy', 'decoding' => 'async'])) . '</a>';
    echo '<div class="rwb-card-copy"><div class="rwb-card-head"><h3><a href="' . esc_url($product->get_permalink()) . '">' . esc_html($product->get_name()) . '</a></h3><div class="rwb-price">' . wp_kses_post($product->get_price_html()) . '</div></div>';
    echo '<div class="rwb-rating"><span aria-hidden="true">' . esc_html($stars) . '</span><small>' . esc_html($count ? number_format_i18n($rating, 1) . ' · ' . $count : __('New', 'ruwah')) . '</small></div></div><div class="rwb-card-action">';
    if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) {
        echo '<a rel="nofollow" href="' . esc_url($product->add_to_cart_url()) . '" data-quantity="1" data-product_id="' . esc_attr((string) $id) . '" data-product_sku="' . esc_attr($product->get_sku()) . '" class="rwb-add button product_type_simple add_to_cart_button ajax_add_to_cart"><span>' . esc_html__('Add to Bag', 'ruwah') . '</span><span>+</span></a>';
    } else {
        echo '<a class="rwb-add button" href="' . esc_url($product->get_permalink()) . '"><span>' . esc_html__('View Product', 'ruwah') . '</span><span>↗</span></a>';
    }
    echo '</div></article>';
}

/* Compatibility layer used by the reference commerce templates. */
if (!function_exists('rwb_product')) {
    function rwb_product($product_id) {
        if (!function_exists('wc_get_product')) return null;
        $product = wc_get_product((int) $product_id);
        return $product instanceof WC_Product ? $product : null;
    }
}

if (!function_exists('rwb_products')) {
    function rwb_products($limit = 5) {
        if (!function_exists('wc_get_product')) return [];
        $ids = [54, 62, 64, 60, 68];
        $products = [];
        foreach ($ids as $id) {
            $product = wc_get_product($id);
            if (!$product instanceof WC_Product) continue;
            if ('publish' !== get_post_status($id) || !$product->is_visible()) continue;
            $products[] = $product;
        }
        $limit = (int) $limit;
        return $limit > 0 ? array_slice($products, 0, $limit) : $products;
    }
}

if (!function_exists('rwb_info')) {
    function rwb_info($product) {
        if (!$product instanceof WC_Product) return null;
        $tagline = trim(wp_strip_all_tags($product->get_short_description()));
        if ('' === $tagline) {
            $tagline = wp_trim_words(wp_strip_all_tags($product->get_description()), 24, '…');
        }

        $benefits = [];
        $description = (string) $product->get_description();
        if ($description && preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $description, $matches)) {
            foreach ($matches[1] as $item) {
                $item = trim(wp_strip_all_tags($item));
                if ('' !== $item) $benefits[] = $item;
            }
        }
        $benefits = array_values(array_unique(array_slice($benefits, 0, 4)));

        $size = '';
        foreach (['pa_size', 'size', 'pa_volume', 'volume'] as $attribute) {
            $value = trim((string) $product->get_attribute($attribute));
            if ('' !== $value) {
                $size = $value;
                break;
            }
        }

        return [
            'tagline' => $tagline,
            'benefits' => $benefits,
            'size' => $size,
            'facts' => [],
        ];
    }
}

if (!function_exists('rwb_shop_url')) {
    function rwb_shop_url() { return ruwah_shop_url(); }
}
if (!function_exists('rwb_cart_url')) {
    function rwb_cart_url() { return ruwah_cart_url(); }
}
if (!function_exists('rwb_account_url')) {
    function rwb_account_url() { return ruwah_account_url(); }
}

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    $count = function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
    $fragments['.rwb-cart-count'] = '<span class="rwb-cart-count">' . esc_html((string) $count) . '</span>';
    if (function_exists('woocommerce_mini_cart')) {
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();
        $fragments['div.widget_shopping_cart_content'] = '<div class="rwb-mini-cart widget_shopping_cart_content">' . $mini_cart . '</div>';
    }
    return $fragments;
});

require_once __DIR__ . '/includes/cart-drawer.php';

/* Keep the cached homepage cart UI in sync with the live WooCommerce session. */
add_action('wp_enqueue_scripts', function () {
    if (!is_front_page() || !function_exists('WC')) {
        return;
    }

    $endpoint = class_exists('WC_AJAX')
        ? WC_AJAX::get_endpoint('get_refreshed_fragments')
        : add_query_arg('wc-ajax', 'get_refreshed_fragments', home_url('/'));

    $js = <<<'JS'
(()=>{'use strict';const endpoint='RWB_FRAGMENT_ENDPOINT';const allowed=['.rwb-cart-count','.rwb-cart-drawer-content'];const swap=(selector,html)=>{document.querySelectorAll(selector).forEach(el=>{const template=document.createElement('template');template.innerHTML=String(html||'').trim();const node=template.content.firstElementChild;if(node)el.replaceWith(node.cloneNode(true))})};const sync=()=>fetch(endpoint,{method:'POST',credentials:'same-origin',cache:'no-store',headers:{'X-Requested-With':'XMLHttpRequest'}}).then(res=>{if(!res.ok)throw new Error('fragment refresh failed');return res.json()}).then(data=>{const fragments=data&&data.fragments?data.fragments:{};allowed.forEach(selector=>{if(fragments[selector])swap(selector,fragments[selector])});if(window.jQuery)window.jQuery(document.body).trigger('wc_fragments_refreshed')}).catch(()=>{});if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',sync,{once:true})}else{sync()}window.addEventListener('pageshow',event=>{if(event.persisted)sync()})})();
JS;
    $js = str_replace('RWB_FRAGMENT_ENDPOINT', esc_js($endpoint), $js);
    wp_add_inline_script('rwb-theme', $js, 'after');
}, 80);
