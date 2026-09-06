<?php
defined('ABSPATH') || exit;
$cart_count = function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$shop_url = function_exists('ruwah_shop_url') ? ruwah_shop_url() : home_url('/shop/');
$learn_url = home_url('/beauty-guide/');
$hide_learn_on_shop = function_exists('is_shop') && is_shop();
$account_url = function_exists('ruwah_account_url') ? ruwah_account_url() : home_url('/my-account/');
$cart_url = function_exists('ruwah_cart_url') ? ruwah_cart_url() : home_url('/cart/');
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<script>document.documentElement.classList.add('rwb-js');</script>
<?php
/* Replace the legacy WordPress site icon with the active Ruwah brand logo. */
remove_action('wp_head', 'wp_site_icon', 99);
wp_head();

$rwb_logo_id = (int) get_theme_mod('custom_logo', 0);
$rwb_logo_url = $rwb_logo_id ? wp_get_attachment_url($rwb_logo_id) : '';
if ($rwb_logo_url) {
    $rwb_favicon_url = add_query_arg('rwb-favicon', '20260828-1', $rwb_logo_url);
    echo '<link rel="icon" type="image/png" href="' . esc_url($rwb_favicon_url) . '">';
    echo '<link rel="shortcut icon" href="' . esc_url($rwb_favicon_url) . '">';
    echo '<link rel="apple-touch-icon" href="' . esc_url($rwb_favicon_url) . '">';
}

/* Host security blocks direct requests to this page-specific CSS asset.
 * Inline the trusted local stylesheet so Privacy Policy styling is guaranteed. */
if (is_page('privacy-policy')) {
    $rwb_privacy_css = get_template_directory() . '/assets/privacy-policy.css';
    if (is_readable($rwb_privacy_css)) {
        $rwb_privacy_rules = file_get_contents($rwb_privacy_css);
        if (false !== $rwb_privacy_rules && '' !== trim($rwb_privacy_rules)) {
            echo '<style id="rwb-privacy-policy-inline">' . $rwb_privacy_rules . '</style>';
        }
    }
}

/* Order-received page: scoped premium spacing/presentation only. */
if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received')) {
    ?>
    <style id="rwb-order-received-inline">
    body.woocommerce-order-received{background:#f7f3e9;color:#111}
    body.woocommerce-order-received main#main-content{background:#f7f3e9}
    body.woocommerce-order-received .rb-content{width:100%;padding:48px 0 72px!important;background:#f7f3e9}
    body.woocommerce-order-received .rb-content>.rb-shell{width:calc(100% - 64px)!important;max-width:1180px!important;margin:0 auto!important;padding:0!important}
    body.woocommerce-order-received .rb-content article{width:100%;margin:0!important;padding:0!important}
    body.woocommerce-order-received .woocommerce-order{width:100%;margin:0!important}
    body.woocommerce-order-received .woocommerce-thankyou-order-received{margin:0 0 28px!important;font-family:var(--serif,'DM Serif Display',Georgia,serif);font-size:38px!important;line-height:1.08!important;font-weight:400!important;color:#171417}
    body.woocommerce-order-received .woocommerce-order>p{margin:0 0 24px!important;font-size:14px!important;line-height:1.6!important;color:#4e474e}
    body.woocommerce-order-received ul.woocommerce-order-overview{display:flex!important;flex-wrap:nowrap!important;align-items:stretch!important;gap:12px!important;width:100%!important;margin:0 0 34px!important;padding:0!important;list-style:none!important;box-sizing:border-box!important}
    body.woocommerce-order-received ul.woocommerce-order-overview:before,body.woocommerce-order-received ul.woocommerce-order-overview:after{display:none!important;content:none!important}
    body.woocommerce-order-received ul.woocommerce-order-overview li{float:none!important;clear:none!important;display:block!important;flex:1 1 0!important;width:0!important;max-width:none!important;min-width:0!important;margin:0!important;padding:16px 18px!important;border:1px solid #d8cedb!important;border-right:1px solid #d8cedb!important;background:#fffdfa!important;color:#6f6670!important;font-size:10px!important;line-height:1.35!important;text-transform:uppercase!important;box-sizing:border-box!important}
    body.woocommerce-order-received ul.woocommerce-order-overview li:before{display:none!important}
    body.woocommerce-order-received ul.woocommerce-order-overview li strong{display:block!important;margin-top:7px!important;color:#171417!important;font-size:17px!important;line-height:1.25!important;font-weight:700!important;text-transform:none!important;white-space:normal!important}
    body.woocommerce-order-received .woocommerce-order-details,body.woocommerce-order-received .woocommerce-customer-details{width:100%!important;box-sizing:border-box!important;margin:30px 0 0!important;padding:26px 28px!important;border:1px solid #d8cedb!important;background:#fffdfa!important}
    body.woocommerce-order-received .woocommerce-order-details__title,body.woocommerce-order-received .woocommerce-column__title{margin:0 0 18px!important;font-family:var(--serif,'DM Serif Display',Georgia,serif)!important;font-size:28px!important;line-height:1.1!important;font-weight:400!important;color:#171417}
    body.woocommerce-order-received table.shop_table{width:100%!important;margin:0!important;border:0!important;border-collapse:collapse!important;background:#fff!important}
    body.woocommerce-order-received table.shop_table th,body.woocommerce-order-received table.shop_table td{padding:14px 16px!important;border:1px solid #ded6e0!important;background:#fff!important;color:#171417!important;font-size:13px!important;line-height:1.4!important}
    body.woocommerce-order-received table.shop_table thead th{background:#f1edf3!important;font-weight:700!important}
    body.woocommerce-order-received table.shop_table tfoot th,body.woocommerce-order-received table.shop_table tfoot td{font-weight:700!important}
    body.woocommerce-order-received .woocommerce-customer-details .woocommerce-columns{display:block!important;margin:0!important}
    body.woocommerce-order-received .woocommerce-customer-details .woocommerce-column{float:none!important;width:100%!important;max-width:none!important;margin:0!important;padding:0!important}
    body.woocommerce-order-received .woocommerce-customer-details address{margin:0!important;padding:18px 20px!important;border:1px solid #ded6e0!important;border-radius:0!important;background:#fff!important;color:#2c272c!important;font-size:13px!important;font-style:normal!important;line-height:1.7!important}
    body.woocommerce-order-received .woocommerce-customer-details address p{margin:10px 0 0!important}
    @media(max-width:900px){body.woocommerce-order-received .rb-content{padding:36px 0 56px!important}body.woocommerce-order-received .rb-content>.rb-shell{width:calc(100% - 40px)!important}body.woocommerce-order-received ul.woocommerce-order-overview{flex-wrap:wrap!important}body.woocommerce-order-received ul.woocommerce-order-overview li{flex:0 0 calc(50% - 6px)!important;width:calc(50% - 6px)!important}body.woocommerce-order-received .woocommerce-thankyou-order-received{font-size:32px!important}}
    @media(max-width:560px){body.woocommerce-order-received .rb-content{padding:28px 0 42px!important}body.woocommerce-order-received .rb-content>.rb-shell{width:calc(100% - 24px)!important}body.woocommerce-order-received ul.woocommerce-order-overview{display:block!important}body.woocommerce-order-received ul.woocommerce-order-overview li{width:100%!important;max-width:100%!important;margin:0 0 8px!important}body.woocommerce-order-received .woocommerce-thankyou-order-received{font-size:27px!important;margin-bottom:20px!important}body.woocommerce-order-received .woocommerce-order-details,body.woocommerce-order-received .woocommerce-customer-details{padding:18px 14px!important;margin-top:22px!important}body.woocommerce-order-received .woocommerce-order-details__title,body.woocommerce-order-received .woocommerce-column__title{font-size:23px!important}body.woocommerce-order-received table.shop_table th,body.woocommerce-order-received table.shop_table td{padding:11px 9px!important;font-size:12px!important}body.woocommerce-order-received .woocommerce-customer-details address{padding:14px!important;font-size:12px!important}}
    </style>
    <?php
}
?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#main-content"><?php esc_html_e('Skip to content', 'ruwah'); ?></a>
<div class="rwb-announcement"><span>RUWAH BEAUTY · SECURE CHECKOUT · PAKISTAN-WIDE DELIVERY</span></div>
<header class="rwb-header" data-header>
    <div class="rwb-shell rwb-header-row">
        <div class="rwb-nav-side">
            <button class="rwb-icon rwb-menu-btn" type="button" data-menu-open aria-label="Menu"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
            <nav class="rwb-desktop-nav" aria-label="Primary"><a href="<?php echo esc_url($shop_url); ?>">Shop</a><?php if (! $hide_learn_on_shop) : ?><a href="<?php echo esc_url($learn_url); ?>">Learn</a><?php endif; ?></nav>
        </div>
        <div class="rwb-brand"><?php if (has_custom_logo()) { the_custom_logo(); } else { ?><a href="<?php echo esc_url(home_url('/')); ?>">RUWAH</a><?php } ?></div>
        <div class="rwb-tools">
            <button class="rwb-icon" type="button" data-search-open aria-label="Search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg></button>
            <a class="rwb-icon" href="<?php echo esc_url($account_url); ?>" aria-label="My Account"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M5 21c0-4 3-7 7-7s7 3 7 7"/></svg></a>
            <button class="rwb-icon" type="button" data-cart-open aria-label="Cart"><svg viewBox="0 0 24 24"><path d="M6 8h12l-1 13H7L6 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg><span class="rwb-cart-count"><?php echo esc_html((string) $cart_count); ?></span></button>
        </div>
    </div>
</header>
<aside class="rwb-mobile-menu" data-menu hidden><div class="rwb-panel-head"><b>RUWAH BEAUTY</b><button class="rwb-icon" type="button" data-menu-close aria-label="Close menu">×</button></div><nav><a href="<?php echo esc_url($shop_url); ?>">Shop all</a><?php if (! $hide_learn_on_shop) : ?><a href="<?php echo esc_url($learn_url); ?>">Learn</a><?php endif; ?><a href="<?php echo esc_url($account_url); ?>">My account</a></nav></aside>
<div class="rwb-layer" data-search hidden><button class="rwb-backdrop" type="button" data-search-close aria-label="Close search"></button><div class="rwb-search-panel"><div class="rwb-panel-head"><b>Search Ruwah</b><button class="rwb-icon" data-search-close aria-label="Close search">×</button></div><form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><input type="search" name="s" placeholder="Search products" required><input type="hidden" name="post_type" value="product"><button type="submit">Search</button></form></div></div>
<div class="rwb-layer" data-cart hidden><button class="rwb-backdrop" type="button" data-cart-close aria-label="Close cart"></button><aside class="rwb-cart-panel rwb-shop-cart-drawer"><?php if (function_exists('rwb_render_cart_drawer_content')) { rwb_render_cart_drawer_content(); } ?></aside></div>
<main id="main-content">