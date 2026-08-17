<?php
defined('ABSPATH') || exit;
$cart_count = function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$shop_url = function_exists('ruwah_shop_url') ? ruwah_shop_url() : home_url('/shop/');
$account_url = function_exists('ruwah_account_url') ? ruwah_account_url() : home_url('/my-account/');
$cart_url = function_exists('ruwah_cart_url') ? ruwah_cart_url() : home_url('/cart/');
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<script>document.documentElement.classList.add('rwb-js');</script>
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#main-content"><?php esc_html_e('Skip to content', 'ruwah'); ?></a>
<div class="rwb-announcement"><span>RUWAH BEAUTY · SECURE CHECKOUT · PAKISTAN-WIDE DELIVERY</span></div>
<header class="rwb-header" data-header>
    <div class="rwb-shell rwb-header-row">
        <div class="rwb-nav-side">
            <button class="rwb-icon rwb-menu-btn" type="button" data-menu-open aria-label="Menu"><svg viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
            <nav class="rwb-desktop-nav" aria-label="Primary"><a href="<?php echo esc_url($shop_url); ?>">Shop</a><a href="<?php echo esc_url($shop_url); ?>">Products</a></nav>
        </div>
        <div class="rwb-brand"><?php if (has_custom_logo()) { the_custom_logo(); } else { ?><a href="<?php echo esc_url(home_url('/')); ?>">RUWAH</a><?php } ?></div>
        <div class="rwb-tools">
            <button class="rwb-icon" type="button" data-search-open aria-label="Search"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg></button>
            <a class="rwb-icon" href="<?php echo esc_url($account_url); ?>" aria-label="My Account"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"/><path d="M5 21c0-4 3-7 7-7s7 3 7 7"/></svg></a>
            <button class="rwb-icon" type="button" data-cart-open aria-label="Cart"><svg viewBox="0 0 24 24"><path d="M6 8h12l-1 13H7L6 8Z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg><span class="rwb-cart-count"><?php echo esc_html((string) $cart_count); ?></span></button>
        </div>
    </div>
</header>
<aside class="rwb-mobile-menu" data-menu hidden><div class="rwb-panel-head"><b>RUWAH BEAUTY</b><button class="rwb-icon" type="button" data-menu-close aria-label="Close menu">×</button></div><nav><a href="<?php echo esc_url($shop_url); ?>">Shop all</a><a href="<?php echo esc_url($shop_url); ?>">Products</a><a href="<?php echo esc_url($account_url); ?>">My account</a></nav></aside>
<div class="rwb-layer" data-search hidden><button class="rwb-backdrop" type="button" data-search-close aria-label="Close search"></button><div class="rwb-search-panel"><div class="rwb-panel-head"><b>Search Ruwah</b><button class="rwb-icon" type="button" data-search-close aria-label="Close search">×</button></div><form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><input type="search" name="s" placeholder="Search products" required><input type="hidden" name="post_type" value="product"><button type="submit">Search</button></form></div></div>
<div class="rwb-layer" data-cart hidden><button class="rwb-backdrop" type="button" data-cart-close aria-label="Close cart"></button><aside class="rwb-cart-panel"><div class="rwb-panel-head"><b>Your bag</b><button class="rwb-icon" type="button" data-cart-close aria-label="Close cart">×</button></div><div class="rwb-mini-cart widget_shopping_cart_content"><?php if (function_exists('woocommerce_mini_cart')) { woocommerce_mini_cart(); } ?></div><a class="rwb-cart-checkout" href="<?php echo esc_url($cart_url); ?>">View bag &amp; checkout</a></aside></div>
<main id="main-content">
