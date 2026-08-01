<?php
defined('ABSPATH') || exit;
$progress = function_exists('ruwah_shipping_progress') ? ruwah_shipping_progress() : ['remaining'=>0,'percent'=>0];
$cart_count = function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <?php wp_head(); ?>
    <style id="ruwah-reference-header-v3">
        [hidden]{display:none!important}
        .rb-header{position:sticky;top:0;z-index:100;background:#fff!important;border-bottom:1px solid #e4e4e4!important;backdrop-filter:none!important}
        .rb-header-main{position:relative;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;min-height:104px;padding:0 28px;border-bottom:1px solid #e4e4e4;background:#fff}
        .rb-header-search{justify-self:start}
        .rb-header-brand{justify-self:center;display:flex;align-items:center;justify-content:center;width:250px;height:96px;overflow:hidden;text-decoration:none}
        .rb-header-brand svg{display:block;width:235px;height:94px;overflow:visible}
        .rb-header-tools{justify-self:end;display:flex;align-items:center;gap:27px;color:#202020;font-size:14px;font-weight:500}
        .rb-header-tools a,.rb-header-tools button,.rb-header-search button{display:inline-flex;align-items:center;justify-content:center;margin:0;padding:0;border:0!important;border-radius:0!important;background:transparent!important;color:#202020!important;box-shadow:none!important;cursor:pointer;text-decoration:none}
        .rb-header-tools svg,.rb-header-search svg{width:25px;height:25px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
        .rb-header-cart{position:relative}.rb-header-cart .rb-cart-count{position:absolute;right:-11px;top:-9px;display:grid;width:18px;height:18px;place-items:center;border-radius:50%;background:#5b1624;color:#fff;font-size:10px;font-weight:700}
        .rb-header-nav{min-height:58px;display:flex;align-items:center;justify-content:center;padding:0 24px;background:#fff}.rb-header-nav ul{display:flex;align-items:center;justify-content:center;gap:37px;margin:0;padding:0;list-style:none!important}.rb-header-nav li{margin:0;padding:0;list-style:none!important}.rb-header-nav li::marker,.rb-header-nav li::before{display:none!important;content:none!important}.rb-header-nav a{display:block;padding:21px 0;color:#181818;font-size:13px;font-weight:800;line-height:1;text-transform:uppercase;white-space:nowrap}.rb-header-nav a::after{display:none!important}.rb-menu-btn{display:none!important}body.admin-bar .rb-header{top:32px}
        @media(max-width:1180px){.rb-header-nav ul{gap:23px}.rb-header-nav a{font-size:12px}.rb-header-main{padding:0 22px}.rb-header-tools{gap:18px}}
        @media(max-width:1050px){.rb-header-main{min-height:76px;grid-template-columns:auto 1fr auto;padding:0 16px}.rb-menu-btn{display:inline-flex!important;justify-self:start}.rb-header-search{display:none}.rb-header-brand{justify-self:center;width:165px;height:70px}.rb-header-brand svg{width:155px;height:68px}.rb-header-tools{gap:14px}.rb-track-order{display:none!important}.rb-header-nav{display:none;position:absolute;left:0;right:0;top:76px;min-height:0;padding:14px 20px;border-top:1px solid #eee;box-shadow:0 14px 30px rgba(0,0,0,.08)}.rb-header-nav.is-open{display:block}.rb-header-nav ul{display:flex;flex-direction:column;align-items:stretch;gap:0}.rb-header-nav a{padding:12px 4px}}
        @media(max-width:760px){body.admin-bar .rb-header{top:0}.rb-header-tools .rb-account-link{display:none}.rb-header-main{min-height:70px}.rb-header-nav{top:70px}.rb-header-brand{width:138px;height:64px}.rb-header-brand svg{width:130px;height:62px}}
    </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="rb-skip" href="#main-content">Skip to content</a>
<div class="rb-announcement"><span>Free delivery above PKR 5,000</span><i></i><span>Cash on delivery across Pakistan</span><i></i><span>Secure checkout</span></div>
<header class="rb-header">
    <div class="rb-header-main">
        <button class="rb-icon-btn rb-menu-btn" type="button" data-menu-toggle aria-label="Open menu"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg></button>
        <div class="rb-header-search"><button type="button" data-search-open aria-label="Search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg></button></div>
        <a class="rb-header-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="Ruwah Beauty home">
            <svg viewBox="0 0 640 300" role="img" aria-labelledby="rb-logo-title rb-logo-desc">
                <title id="rb-logo-title">Ruwah Beauty</title><desc id="rb-logo-desc">Purple Ruwah Beauty monogram with vertical Ruwah Beauty lettering.</desc>
                <g fill="none" stroke="#8f35c5" stroke-width="17" stroke-linecap="round" stroke-linejoin="round"><path d="M265 84h88c43 0 67 18 67 50 0 28-22 47-54 47h-65"/><path d="M301 181h74c42 0 67 20 67 52 0 30-23 49-62 49h-115c-42 0-68-20-68-51 0-27 18-47 45-56"/><path d="M420 134V55"/><circle cx="469" cy="142" r="51"/><path d="M197 231c-13 10-15 25-3 37 12 11 27 8 36-5 10-14 3-26-9-33-9-5-17-5-24 1Z"/></g>
                <text x="0" y="0" transform="translate(95 267) rotate(-90)" fill="#24191d" font-family="Georgia, 'Times New Roman', serif" font-size="42" letter-spacing="1">Ruwah Beauty</text>
            </svg>
        </a>
        <div class="rb-header-tools"><a class="rb-track-order" href="<?php echo esc_url(home_url('/track-order/')); ?>">Track Order</a><a class="rb-account-link" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/')); ?>" aria-label="Account"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21c0-4 3-7 7-7s7 3 7 7"/></svg></a><button class="rb-header-cart" type="button" data-cart-open aria-label="Cart"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg><span class="rb-cart-count"><?php echo esc_html($cart_count); ?></span></button></div>
    </div>
    <nav class="rb-header-nav" data-menu aria-label="Primary navigation"><?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'menu_class'=>'rb-nav-list','fallback_cb'=>false]); ?></nav>
</header>
<div class="rb-overlay" data-overlay hidden></div>
<section class="rb-search" data-search hidden aria-hidden="true"><div class="rb-search-card"><button class="rb-close" type="button" data-search-close aria-label="Close search">×</button><span class="rb-kicker">Search Ruwah Beauty</span><h2>What are you looking for?</h2><form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><input type="search" name="s" placeholder="Search products, ingredients or concerns"><input type="hidden" name="post_type" value="product"><button class="rb-button" type="submit">Search</button></form></div></section>
<aside class="rb-cart-drawer" data-cart-drawer aria-hidden="true"><header><h2>Your shopping bag</h2><button class="rb-close" type="button" data-cart-close aria-label="Close cart">×</button></header><div class="rb-cart-body widget_shopping_cart_content"><?php if (function_exists('woocommerce_mini_cart')) { woocommerce_mini_cart(); } ?></div><footer><p><?php echo esc_html($progress['remaining'] > 0 ? 'Add more for free shipping' : 'Free shipping unlocked'); ?></p><div class="rb-progress"><i style="width:<?php echo esc_attr($progress['percent']); ?>%"></i></div><a class="rb-button" href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')); ?>">View bag &amp; checkout</a></footer></aside>
<main id="main-content">
