<?php
defined('ABSPATH') || exit;
$progress = function_exists('ruwah_shipping_progress') ? ruwah_shipping_progress() : ['remaining' => 0, 'percent' => 0];
$cart_count = function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$logo_path = get_template_directory() . '/assets/ruwah-monogram.svg';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <?php wp_head(); ?>
    <style id="ruwah-reference-header-v4">
        [hidden]{display:none!important}
        .rb-header{position:sticky;top:0;z-index:100;background:#fff!important;border-bottom:1px solid #e4e4e4!important;backdrop-filter:none!important}
        .rb-header-main{position:relative;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;min-height:104px;padding:0 28px;border-bottom:1px solid #e4e4e4;background:#fff}
        .rb-header-search{justify-self:start}
        .rb-header-brand{justify-self:center;display:flex;align-items:center;justify-content:center;width:250px;height:96px;overflow:hidden;text-decoration:none}
        .rb-header-brand svg{display:block;width:220px;height:auto;max-height:82px;overflow:visible}
        .rb-header-tools{justify-self:end;display:flex;align-items:center;gap:27px;color:#202020;font-size:14px;font-weight:500}
        .rb-header-tools a,.rb-header-tools button,.rb-header-search button{display:inline-flex;align-items:center;justify-content:center;margin:0;padding:0;border:0!important;border-radius:0!important;background:transparent!important;color:#202020!important;box-shadow:none!important;cursor:pointer;text-decoration:none}
        .rb-header-tools svg,.rb-header-search svg{width:25px;height:25px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
        .rb-header-cart{position:relative}.rb-header-cart .rb-cart-count{position:absolute;right:-11px;top:-9px;display:grid;width:18px;height:18px;place-items:center;border-radius:50%;background:#9638d5;color:#fff;font-size:10px;font-weight:700}
        .rb-header-nav{min-height:58px;display:flex;align-items:center;justify-content:center;padding:0 24px;background:#fff}.rb-header-nav ul{display:flex;align-items:center;justify-content:center;gap:37px;margin:0;padding:0;list-style:none!important}.rb-header-nav li{margin:0;padding:0;list-style:none!important}.rb-header-nav li::marker,.rb-header-nav li::before{display:none!important;content:none!important}.rb-header-nav a{display:block;padding:21px 0;color:#181818;font-size:13px;font-weight:800;line-height:1;text-transform:uppercase;white-space:nowrap}.rb-header-nav a::after{display:none!important}.rb-menu-btn{display:none!important}body.admin-bar .rb-header{top:32px}
        @media(max-width:1180px){.rb-header-nav ul{gap:23px}.rb-header-nav a{font-size:12px}.rb-header-main{padding:0 22px}.rb-header-tools{gap:18px}}
        @media(max-width:1050px){.rb-header-main{min-height:76px;grid-template-columns:auto 1fr auto;padding:0 16px}.rb-menu-btn{display:inline-flex!important;justify-self:start}.rb-header-search{display:none}.rb-header-brand{justify-self:center;width:165px;height:70px}.rb-header-brand svg{width:150px;max-height:58px}.rb-header-tools{gap:14px}.rb-track-order{display:none!important}.rb-header-nav{display:none;position:absolute;left:0;right:0;top:76px;min-height:0;padding:14px 20px;border-top:1px solid #eee;box-shadow:0 14px 30px rgba(0,0,0,.08)}.rb-header-nav.is-open{display:block}.rb-header-nav ul{display:flex;flex-direction:column;align-items:stretch;gap:0}.rb-header-nav a{padding:12px 4px}}
        @media(max-width:760px){body.admin-bar .rb-header{top:0}.rb-header-tools .rb-account-link{display:none}.rb-header-main{min-height:70px}.rb-header-nav{top:70px}.rb-header-brand{width:138px;height:64px}.rb-header-brand svg{width:126px;max-height:50px}}
    </style>
    <style id="ruwah-final-purple-system">
      :root{--rb-burgundy:#9638d5!important;--rb-burgundy-2:#7f2cc0!important;--rb-rose:#6f2dbd!important;--rb-blush:#f6f1fb!important;--rb-cream:#fcfafd!important;--rb-ink:#151218!important;--rb-muted:#625c66!important;--rb-border:#e8e0ed!important;--rb-gold:#9638d5!important;--rb-shadow:0 14px 40px rgba(42,22,56,.10)!important}
      html,body{background:#fcfafd!important;color:#151218!important}
      .rb-announcement{background:#2a1638!important;color:#fff!important}
      .rb-header,.rb-header-main,.rb-header-nav{background:#fff!important;border-color:#e8e0ed!important}
      .rb-header-tools,.rb-header-tools a,.rb-header-tools button,.rb-header-search button,.rb-header-nav a{color:#151218!important}
      .rb-header-tools a:hover,.rb-header-tools button:hover,.rb-header-search button:hover,.rb-header-nav a:hover{color:#9638d5!important}
      .rb-header-cart .rb-cart-count,.rb-cart-count{background:#9638d5!important;color:#fff!important}
      .rb-kicker,.rb-text-link{color:#9638d5!important}
      .rb-section h2,.rb-page-title,.woocommerce h1,.woocommerce h2,.woocommerce h3,.rb-product-copy h3,.rb-concern-card strong,.rb-ingredient-card strong{color:#151218!important}
      .rb-section--blush,.rb-page-hero{background:#f6f1fb!important}
      body.home .rb-hero{background:linear-gradient(90deg,#f6f1fb 0 56%,#eee4f7 56%)!important}
      body.home .rb-hero h1{color:#151218!important}
      body.home .rb-hero p{color:#625c66!important}
      body.home .rb-hero-media:before{background:#fff!important}
      body.home .rb-hero-media img{filter:drop-shadow(0 26px 34px rgba(42,22,56,.20))!important}
      .rb-button,.woocommerce a.button,.woocommerce button.button,.woocommerce input.button,.woocommerce #respond input#submit,.wp-element-button,.wc-block-components-button{background:#9638d5!important;color:#fff!important;border:1px solid transparent!important;border-radius:999px!important;box-shadow:none!important}
      .rb-button:hover,.woocommerce a.button:hover,.woocommerce button.button:hover,.woocommerce input.button:hover,.wp-element-button:hover,.wc-block-components-button:hover{background:#7f2cc0!important;color:#fff!important;transform:translateY(-1px)!important}
      .rb-button--light{background:#fff!important;color:#2a1638!important;border-color:#e8e0ed!important}
      .rb-product-actions .button{background:#151218!important;color:#fff!important}
      .rb-product-actions .button:hover{background:#9638d5!important}
      body.home .rb-category-slider-section{background:#fff!important;border-color:#e8e0ed!important}
      body.home .rb-category-slider .rb-category-card span{background:linear-gradient(180deg,#fff 0 22%,#f6f1fb 22% 100%)!important;border-color:#e8e0ed!important;box-shadow:0 8px 22px rgba(42,22,56,.06)!important}
      body.home .rb-category-slider .rb-category-card b{color:#151218!important}
      body.home .rb-category-arrow{background:#fff!important;color:#9638d5!important;border-color:#e8e0ed!important}
      body.home .rb-category-arrow:hover{background:#9638d5!important;color:#fff!important;border-color:#9638d5!important}
      .rb-product-card,.woocommerce ul.products li.product,.rb-review,.rb-post-card,.rb-quality-card,.rb-empty,.woocommerce table.shop_table,.woocommerce-cart-form,.cart_totals,#customer_details,#order_review,.woocommerce-MyAccount-navigation,.woocommerce-MyAccount-content{background:#fff!important;border-color:#e8e0ed!important;box-shadow:0 6px 22px rgba(42,22,56,.05)!important}
      .rb-product-media,.woocommerce ul.products li.product img{background:#f6f1fb!important}
      .rb-card-rating,.woocommerce .star-rating,.comment-form-rating .stars a{color:#9638d5!important}
      .rb-price,.woocommerce ul.products li.product .price,.woocommerce div.product p.price{color:#151218!important}
      .rb-badge,.woocommerce span.onsale{background:#9638d5!important;color:#fff!important}
      .rb-promo{background:linear-gradient(145deg,#21142b,#432653)!important;color:#fff!important}
      .rb-promo:nth-child(2){background:linear-gradient(145deg,#fff,#f6f1fb)!important;color:#151218!important;border:1px solid #e8e0ed!important}
      .rb-promo:nth-child(2) h3{color:#151218!important}
      .rb-concern-card,.rb-ingredient-card{background:linear-gradient(145deg,#fff,#f6f1fb)!important;border-color:#e8e0ed!important}
      .rb-feature-media{background:#f6f1fb!important}
      .rb-trust{background:#e8e0ed!important;border-color:#e8e0ed!important}
      .rb-trust b{color:#6f2dbd!important}
      .rb-newsletter{background:linear-gradient(135deg,#2a1638,#432653)!important}
      .rb-footer{background:#1d1025!important;color:#eee4f7!important}
      .rb-footer a{color:#d8c9e5!important}.rb-footer a:hover{color:#fff!important}
      .rb-search-card,.rb-cart-drawer{background:#fcfafd!important}
      .rb-close,.rb-progress i{background:#9638d5!important}
      input,select,textarea,.woocommerce form .form-row input.input-text,.woocommerce form .form-row textarea,.woocommerce select,.woocommerce .quantity .qty{background:#fff!important;color:#151218!important;border-color:#e8e0ed!important}
      .woocommerce-message,.woocommerce-info,.woocommerce-error{background:#fff!important;border-color:#e8e0ed!important;color:#151218!important}
      .woocommerce div.product div.images img{background:#f6f1fb!important}
      .woocommerce div.product form.cart .single_add_to_cart_button{background:#9638d5!important}
      .woocommerce-cart .cart_totals,.woocommerce-checkout #order_review{background:#f6f1fb!important}
      .woocommerce-MyAccount-navigation .is-active a,.woocommerce-MyAccount-navigation a:hover{background:#f6f1fb!important;color:#6f2dbd!important}
      @media(max-width:760px){body.home .rb-hero{background:linear-gradient(#f6f1fb 0 59%,#eee4f7 59%)!important}}
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
            <?php if (is_readable($logo_path)) { echo file_get_contents($logo_path); } ?>
        </a>
        <div class="rb-header-tools">
            <a class="rb-track-order" href="<?php echo esc_url(home_url('/track-order/')); ?>">Track Order</a>
            <a class="rb-account-link" href="<?php echo esc_url(home_url('/my-account/')); ?>" aria-label="Account"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21c0-4 3-7 7-7s7 3 7 7"/></svg></a>
            <button class="rb-header-cart" type="button" data-cart-open aria-label="Cart"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg><span class="rb-cart-count"><?php echo esc_html($cart_count); ?></span></button>
        </div>
    </div>
    <nav class="rb-header-nav" data-menu aria-label="Primary navigation">
        <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false, 'menu_class' => 'rb-nav-list', 'fallback_cb' => false]); ?>
    </nav>
</header>
<div class="rb-overlay" data-overlay hidden></div>
<section class="rb-search" data-search hidden aria-hidden="true"><div class="rb-search-card"><button class="rb-close" type="button" data-search-close aria-label="Close search">×</button><span class="rb-kicker">Search Ruwah Beauty</span><h2>What are you looking for?</h2><form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><input type="search" name="s" placeholder="Search products, ingredients or concerns"><input type="hidden" name="post_type" value="product"><button class="rb-button" type="submit">Search</button></form></div></section>
<aside class="rb-cart-drawer" data-cart-drawer aria-hidden="true"><header><h2>Your shopping bag</h2><button class="rb-close" type="button" data-cart-close aria-label="Close cart">×</button></header><div class="rb-cart-body widget_shopping_cart_content"><?php if (function_exists('woocommerce_mini_cart')) { woocommerce_mini_cart(); } ?></div><footer><p><?php echo $progress['remaining'] > 0 ? esc_html('Add PKR ' . number_format_i18n($progress['remaining']) . ' for free shipping') : esc_html('You qualify for free shipping'); ?></p><div class="rb-progress"><i style="width:<?php echo esc_attr($progress['percent']); ?>%"></i></div><a class="rb-button" href="<?php echo esc_url(wc_get_cart_url()); ?>">View bag &amp; checkout</a></footer></aside>
<main id="main-content">