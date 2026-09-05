<?php
defined('ABSPATH') || exit;

define('RUWAH_THEME_VERSION', '4.0.1');

/**
 * Authoritative exact PNGs for the five approved products.
 * These filters affect every WooCommerce surface that reads the product image:
 * cards, product pages, related products, search, cart and mini-cart.
 */
function ruwah_exact_product_png_map(): array {
    return [
        54 => 390,
        60 => 388,
        62 => 391,
        64 => 389,
        68 => 387,
    ];
}

add_filter('woocommerce_product_get_image_id', function ($image_id, $product) {
    if (! $product instanceof WC_Product) return $image_id;
    $map = ruwah_exact_product_png_map();
    $product_id = (int) $product->get_id();
    return isset($map[$product_id]) ? (int) $map[$product_id] : $image_id;
}, 9999, 2);

add_filter('woocommerce_product_get_gallery_image_ids', function ($gallery_ids, $product) {
    if (! $product instanceof WC_Product) return $gallery_ids;
    return isset(ruwah_exact_product_png_map()[(int) $product->get_id()]) ? [] : $gallery_ids;
}, 9999, 2);

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

if (!function_exists('rwb_product')) {
    function rwb_product($product_id) {
        if (!function_exists('wc_get_product')) return null;
        $product = wc_get_product((int) $product_id);
        return $product instanceof WC_Product ? $product : null;
    }
}

if (!function_exists('rwb_products')) {
    function rwb_products($limit = 5) {
        if (!function_exists('wc_get_products')) return [];
        $limit = (int) $limit;
        $products = wc_get_products([
            'status' => 'publish',
            'limit' => $limit > 0 ? $limit : -1,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ]);
        return array_values(array_filter($products, static fn($product) => $product instanceof WC_Product && $product->is_visible()));
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
require_once __DIR__ . '/includes/reference-checkout.php';
require_once __DIR__ . '/includes/home-footer-dedup.php';

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

add_action('wp_head', function () {
    $logo_url = wp_get_attachment_image_url(262, 'full');
    if (!$logo_url) return;
    echo '<link rel="icon" href="' . esc_url($logo_url) . '">' . "\n";
    echo '<link rel="shortcut icon" href="' . esc_url($logo_url) . '">' . "\n";
}, 100);

add_action('wp_head', function () {
    if (! is_front_page()) return;
    ?>
    <style id="rwb-home-hero-logo-contrast-v1">
    body.rwb-reference-home-v5.home .rwb-ref-header:not(.compact) .rwb-brand{position:relative;isolation:isolate;padding:0 12px}
    body.rwb-reference-home-v5.home .rwb-ref-header:not(.compact) .rwb-brand:before{content:"";position:absolute;z-index:-1;left:50%;top:50%;width:235px;height:104px;transform:translate(-50%,-50%);border-radius:50%;background:radial-gradient(ellipse at center,rgba(247,243,233,.64) 0%,rgba(247,243,233,.34) 48%,rgba(247,243,233,.10) 66%,rgba(247,243,233,0) 79%);filter:blur(5px);pointer-events:none}
    body.rwb-reference-home-v5.home .rwb-ref-header:not(.compact) .rwb-brand .custom-logo-link{filter:none!important}
    body.rwb-reference-home-v5.home .rwb-ref-header:not(.compact) .rwb-brand .custom-logo{opacity:1!important;filter:drop-shadow(0 1px 0 rgba(255,255,255,.42)) drop-shadow(0 4px 11px rgba(15,10,18,.34))!important}
    body.rwb-reference-home-v5.home .rwb-ref-header.compact .rwb-brand:before{display:none!important}
    body.rwb-reference-home-v5.home .rwb-ref-header.compact .rwb-brand .custom-logo{filter:none!important}
    @media(max-width:782px){body.rwb-reference-home-v5.home .rwb-ref-header:not(.compact) .rwb-brand{padding:0 7px}body.rwb-reference-home-v5.home .rwb-ref-header:not(.compact) .rwb-brand:before{width:154px;height:72px;filter:blur(3px)}}
    </style>
    <?php
}, 999);

add_action('wp_footer', function () {
    if (! is_front_page()) return;
    ?>
    <style id="rwb-home-contact-dock-server-styles">
    .rwb-contact-dock{position:fixed;right:22px;bottom:max(22px,env(safe-area-inset-bottom));z-index:125;display:flex;flex-direction:column;align-items:flex-end;gap:10px;font-family:Inter,Arial,sans-serif}
    .rwb-contact-dock-item{position:relative;width:56px;height:56px;display:grid;place-items:center;border-radius:50%;box-shadow:0 7px 24px rgba(0,0,0,.18);transition:transform .18s ease,box-shadow .18s ease;text-decoration:none!important}
    .rwb-contact-dock-item:hover,.rwb-contact-dock-item:focus-visible{transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,0,0,.24)}
    .rwb-contact-dock-item--whatsapp{background:#25D366;color:#fff!important}.rwb-contact-dock-item--gmail{border:1px solid rgba(17,17,17,.12);background:#fff;color:#EA4335!important}
    .rwb-contact-dock-item svg{width:28px;height:28px;display:block}.rwb-contact-dock-item--gmail svg{width:27px;height:27px}
    .rwb-contact-dock-label{position:absolute;right:66px;top:50%;min-width:max-content;padding:7px 10px;border-radius:4px;background:#111;color:#fff;font-size:11px;font-weight:600;line-height:1;opacity:0;pointer-events:none;transform:translate(7px,-50%);transition:.18s ease}
    .rwb-contact-dock-item:hover .rwb-contact-dock-label,.rwb-contact-dock-item:focus-visible .rwb-contact-dock-label{opacity:1;transform:translate(0,-50%)}
    body.rwb-lock .rwb-contact-dock{opacity:0;pointer-events:none}
    @media(max-width:820px){.rwb-contact-dock{right:14px;bottom:max(14px,env(safe-area-inset-bottom));gap:8px}.rwb-contact-dock-item{width:50px;height:50px}.rwb-contact-dock-item svg{width:25px;height:25px}.rwb-contact-dock-label{display:none}}
    </style>
    <nav id="rwb-contact-dock" class="rwb-contact-dock" aria-label="Contact Ruwah Beauty">
      <a class="rwb-contact-dock-item rwb-contact-dock-item--whatsapp" href="https://wa.me/923713923279" target="_blank" rel="noopener noreferrer" aria-label="Chat with Ruwah Beauty on WhatsApp at +92 371 3923279" title="WhatsApp: +92 371 3923279"><span class="rwb-contact-dock-label">WhatsApp</span><svg viewBox="0 0 16 16" aria-hidden="true"><path fill="currentColor" d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.93 7.93 0 0 0 3.79.965h.004c4.366 0 7.926-3.558 7.93-7.93a7.9 7.9 0 0 0-2.327-5.607m-5.607 12.2a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.25a6.56 6.56 0 0 1-1.007-3.505c0-3.64 2.963-6.601 6.591-6.601a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.003 3.64-2.963 6.605-6.592 6.608m3.615-4.943c-.197-.099-1.17-.578-1.352-.643-.182-.065-.315-.099-.445.099-.133.197-.513.643-.629.775-.116.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.984-.59-.525-.985-1.174-1.101-1.372-.116-.197-.013-.304.086-.402.089-.088.197-.232.296-.348.099-.116.132-.197.197-.33.066-.132.033-.247-.016-.346-.05-.099-.445-1.074-.61-1.47-.16-.389-.323-.336-.445-.342l-.378-.007a.72.72 0 0 0-.527.247c-.182.197-.692.676-.692 1.65s.708 1.916.807 2.049c.099.132 1.394 2.128 3.377 2.984.471.203.839.324 1.125.414.472.15.902.129 1.242.078.379-.057 1.17-.478 1.335-.94.164-.462.164-.858.115-.94-.049-.083-.182-.132-.38-.231"/></svg></a>
      <a class="rwb-contact-dock-item rwb-contact-dock-item--gmail" href="https://mail.google.com/mail/?view=cm&amp;fs=1&amp;to=rawahbeauty783@gmail.com" target="_blank" rel="noopener noreferrer" aria-label="Email Ruwah Beauty at rawahbeauty783@gmail.com using Gmail" title="Gmail: rawahbeauty783@gmail.com"><span class="rwb-contact-dock-label">Gmail</span><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#EA4335" d="M3.2 5.1 12 11.7l8.8-6.6A2.7 2.7 0 0 0 18.6 4H5.4c-.9 0-1.7.4-2.2 1.1Z"/><path fill="#4285F4" d="M2.7 6.8V18c0 1.1.9 2 2 2h1.7V9.6L2.7 6.8Z"/><path fill="#34A853" d="M17.6 9.6V20h1.7c1.1 0 2-.9 2-2V6.8l-3.7 2.8Z"/><path fill="#FBBC04" d="m6.4 9.6 5.6 4.2 5.6-4.2V20H6.4V9.6Z"/><path fill="#C5221F" d="M3.2 5.1 12 11.7l8.8-6.6c.3.5.5 1.1.5 1.7v.1L12 13.9 2.7 6.9v-.1c0-.6.2-1.2.5-1.7Z"/></svg></a>
    </nav>
    <script id="rwb-home-footer-social-links-server">(()=>{'use strict';const links={Facebook:'https://www.facebook.com/share/1BNAdjWpYW/',Instagram:'https://www.instagram.com/rawah.beauty?utm_source=qr&igsh=ZjMzazdrNmk1aTVt',TikTok:'https://vt.tiktok.com/ZSX6WqwS2/'};document.querySelectorAll('.rwb-dieux-footer-socials span[aria-label]').forEach(span=>{if(span.closest('a'))return;const label=span.getAttribute('aria-label')||'';const href=links[label];if(!href)return;const a=document.createElement('a');a.href=href;a.target='_blank';a.rel='noopener noreferrer';a.className='rwb-dieux-social-link';a.setAttribute('aria-label',label+' — Ruwah Beauty');span.parentNode.insertBefore(a,span);a.appendChild(span);});})();</script>
    <?php
}, 8);

add_filter('pre_option_woocommerce_cod_settings', function ($pre_option) {
    return [
        'enabled' => 'yes',
        'title' => 'Cash on Delivery',
        'description' => 'Pay with cash when your order is delivered.',
        'instructions' => 'Please keep the order amount ready at delivery.',
        'enable_for_methods' => [],
        'enable_for_virtual' => 'yes',
    ];
}, 20, 1);

add_filter('woocommerce_available_payment_gateways', function ($gateways) {
    if (is_admin() && !wp_doing_ajax()) {
        return $gateways;
    }
    if (isset($gateways['cod'])) {
        return ['cod' => $gateways['cod']];
    }
    return [];
}, 9999);

add_action('woocommerce_review_order_before_payment', function () {
    if (function_exists('is_checkout') && !is_checkout()) return;
    echo '<div class="rwb-online-coming-soon" role="note"><strong>Online Payment</strong><span>Coming Soon</span><small>For now, orders are confirmed with Cash on Delivery.</small></div>';
}, 5);

add_action('wp_enqueue_scripts', function () {
    if (!function_exists('is_checkout') || !is_checkout()) return;
    wp_add_inline_style('rwb-theme', '.rwb-online-coming-soon{display:grid;grid-template-columns:1fr auto;gap:4px 12px;align-items:center;margin:18px 0 12px;padding:14px 16px;border:1px solid #d8cedb;background:#fffdfa;color:#282328}.rwb-online-coming-soon strong{font-size:12px}.rwb-online-coming-soon span{padding:5px 8px;background:#f1edf3;color:#705591;font-size:9px;font-weight:700;letter-spacing:.06em;text-transform:uppercase}.rwb-online-coming-soon small{grid-column:1/-1;color:#706870;font-size:10px;line-height:1.45}');
}, 10000);