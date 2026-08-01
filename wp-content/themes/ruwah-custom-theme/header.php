<?php
defined('ABSPATH') || exit;
$progress = ruwah_shipping_progress();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <?php wp_head(); ?>
    <style id="ruwah-reference-header-v1">
        [hidden]{display:none!important}
        .rb-header{position:sticky;top:0;z-index:100;background:#fff!important;border-bottom:1px solid #e4e4e4!important;backdrop-filter:none!important}
        .rb-header-main{position:relative;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;min-height:104px;padding:0 28px;border-bottom:1px solid #e4e4e4;background:#fff}
        .rb-header-search{justify-self:start}
        .rb-header-brand{justify-self:center;display:flex;align-items:center;gap:12px;color:#5b1624;font-family:'DM Serif Display',Georgia,serif;font-size:31px;letter-spacing:.035em;white-space:nowrap}
        .rb-header-brand svg{width:36px;height:36px;fill:none;stroke:currentColor;stroke-width:1.7}
        .rb-header-tools{justify-self:end;display:flex;align-items:center;gap:27px;color:#202020;font-size:14px;font-weight:500}
        .rb-header-tools a,.rb-header-tools button,.rb-header-search button{display:inline-flex;align-items:center;justify-content:center;margin:0;padding:0;border:0!important;border-radius:0!important;background:transparent!important;color:#202020!important;box-shadow:none!important;cursor:pointer;text-decoration:none}
        .rb-header-tools svg,.rb-header-search svg{width:25px;height:25px;fill:none;stroke:currentColor;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round}
        .rb-header-cart{position:relative}
        .rb-header-cart .rb-cart-count{position:absolute;right:-11px;top:-9px;display:grid;width:18px;height:18px;place-items:center;border-radius:50%;background:#5b1624;color:#fff;font-size:10px;font-weight:700}
        .rb-header-nav{min-height:58px;display:flex;align-items:center;justify-content:center;padding:0 24px;background:#fff}
        .rb-header-nav ul{display:flex;align-items:center;justify-content:center;gap:37px;margin:0;padding:0;list-style:none!important}
        .rb-header-nav li{margin:0;padding:0;list-style:none!important}
        .rb-header-nav li::marker,.rb-header-nav li::before{display:none!important;content:none!important}
        .rb-header-nav a{display:block;padding:21px 0;color:#181818;font-size:13px;font-weight:800;line-height:1;text-transform:uppercase;white-space:nowrap}
        .rb-header-nav a::after{display:none!important}
        .rb-menu-btn{display:none!important}
        body.admin-bar .rb-header{top:32px}
        .rb-hero{position:relative;display:grid!important;grid-template-columns:minmax(0,56%) minmax(0,44%)!important;grid-template-rows:minmax(620px,auto);min-height:620px;overflow:hidden;background:linear-gradient(90deg,#f7eaea 0 56%,#ead3d3 56% 100%)}
        .rb-hero-copy{position:relative!important;inset:auto!important;z-index:2;grid-column:1!important;grid-row:1!important;align-self:center;width:100%!important;max-width:none!important;margin:0!important;padding:76px 7vw 76px max(34px,calc((100vw - 1360px)/2))!important}
        .rb-hero-copy h1{max-width:760px!important;margin:0 0 28px!important;font-size:clamp(56px,5.2vw,88px)!important;line-height:.96!important;letter-spacing:-.04em;overflow-wrap:normal!important;word-break:normal!important}
        .rb-hero-copy p{max-width:620px!important}
        .rb-hero-media{position:relative!important;inset:auto!important;z-index:1;grid-column:2!important;grid-row:1!important;display:flex!important;align-items:center;justify-content:center;width:auto!important;height:auto!important;min-height:620px;padding:56px 5vw 56px 28px!important;overflow:hidden}
        .rb-hero-media::before{content:"";position:absolute;width:min(540px,82%);aspect-ratio:1;border-radius:50%;background:rgba(255,246,241,.78)}
        .rb-hero-media img{position:relative;z-index:1;display:block!important;width:min(650px,100%)!important;height:auto!important;max-height:530px!important;object-fit:contain!important;margin:0!important}
        @media(max-width:1180px){.rb-header-nav ul{gap:23px}.rb-header-nav a{font-size:12px}.rb-header-main{padding:0 22px}.rb-header-tools{gap:18px}.rb-hero-copy h1{font-size:clamp(50px,5.2vw,70px)!important}}
        @media(max-width:1050px){.rb-header-main{min-height:76px;grid-template-columns:auto 1fr auto;padding:0 16px}.rb-menu-btn{display:inline-flex!important;justify-self:start}.rb-header-search{display:none}.rb-header-brand{justify-self:center;font-size:21px}.rb-header-brand svg{width:29px;height:29px}.rb-header-tools{gap:14px}.rb-track-order{display:none!important}.rb-header-nav{display:none;position:absolute;left:0;right:0;top:76px;min-height:0;padding:14px 20px;border-top:1px solid #eee;box-shadow:0 14px 30px rgba(0,0,0,.08)}.rb-header-nav.is-open{display:block}.rb-header-nav ul{display:flex;flex-direction:column;align-items:stretch;gap:0}.rb-header-nav a{padding:12px 4px}.rb-hero{grid-template-columns:minmax(0,52%) minmax(0,48%)!important}.rb-hero-copy{padding:60px 28px!important}.rb-hero-copy h1{font-size:clamp(46px,5.7vw,62px)!important}.rb-hero-media{padding:40px 24px!important}}
        @media(max-width:760px){body.admin-bar .rb-header{top:0}.rb-header-tools .rb-account-link{display:none}.rb-header-main{min-height:70px}.rb-header-nav{top:70px}.rb-hero{display:flex!important;flex-direction:column;min-height:0;background:#f7eaea}.rb-hero-copy{order:1;padding:52px 20px 34px!important}.rb-hero-copy h1{font-size:clamp(42px,13vw,58px)!important;line-height:1!important}.rb-hero-media{order:2;width:100%!important;min-height:360px;padding:24px 20px 44px!important;background:#ead3d3}.rb-hero-media img{width:min(520px,100%)!important;max-height:330px!important}}
    </style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="rb-skip" href="#main-content"><?php esc_html_e('Skip to content','ruwah'); ?></a>
<div class="rb-announcement">
    <span><?php esc_html_e('Free delivery above PKR 5,000','ruwah'); ?></span><i></i>
    <span><?php esc_html_e('Cash on delivery across Pakistan','ruwah'); ?></span><i></i>
    <span><?php esc_html_e('Secure checkout','ruwah'); ?></span>
</div>
<header class="rb-header">
    <div class="rb-header-main">
        <button class="rb-icon-btn rb-menu-btn" type="button" data-menu-toggle aria-label="<?php esc_attr_e('Open menu','ruwah'); ?>">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
        <div class="rb-header-search">
            <button type="button" data-search-open aria-label="<?php esc_attr_e('Search','ruwah'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></svg></button>
        </div>
        <a class="rb-header-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <svg viewBox="0 0 40 40" aria-hidden="true"><path d="M20 4c7 8 11 14 11 21a11 11 0 1 1-22 0c0-7 4-13 11-21Z"/><path d="M13 27c5-2 10-6 14-12"/></svg>
            <strong><?php echo esc_html(get_bloginfo('name') ?: 'RUWAH BEAUTY'); ?></strong>
        </a>
        <div class="rb-header-tools">
            <a class="rb-track-order" href="<?php echo esc_url(home_url('/track-order/')); ?>"><?php esc_html_e('Track Order','ruwah'); ?></a>
            <a class="rb-account-link" href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/')); ?>" aria-label="<?php esc_attr_e('Account','ruwah'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M5 21c0-4 3-7 7-7s7 3 7 7"/></svg></a>
            <button class="rb-header-cart" type="button" data-cart-open aria-label="<?php esc_attr_e('Cart','ruwah'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 4h2l2.2 10.2a2 2 0 0 0 2 1.6h7.9a2 2 0 0 0 2-1.6L21 7H6"/><circle cx="10" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg><span class="rb-cart-count"><?php echo esc_html(function_exists('WC') && WC()->cart ? WC()->cart->get_cart_contents_count() : 0); ?></span></button>
        </div>
    </div>
    <nav class="rb-header-nav" data-menu aria-label="<?php esc_attr_e('Primary navigation','ruwah'); ?>">
        <?php wp_nav_menu(['theme_location'=>'primary','container'=>false,'menu_class'=>'rb-nav-list','fallback_cb'=>false]); ?>
    </nav>
</header>
<div class="rb-overlay" data-overlay hidden></div>
<section class="rb-search" data-search hidden aria-hidden="true">
    <div class="rb-search-card">
        <button class="rb-close" type="button" data-search-close aria-label="<?php esc_attr_e('Close search','ruwah'); ?>">×</button>
        <span class="rb-kicker"><?php esc_html_e('Search Ruwah Beauty','ruwah'); ?></span>
        <h2><?php esc_html_e('What are you looking for?','ruwah'); ?></h2>
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" name="s" placeholder="<?php esc_attr_e('Search products, ingredients or concerns','ruwah'); ?>">
            <input type="hidden" name="post_type" value="product">
            <button class="rb-button" type="submit"><?php esc_html_e('Search','ruwah'); ?></button>
        </form>
    </div>
</section>
<aside class="rb-cart-drawer" data-cart-drawer aria-hidden="true">
    <header><h2><?php esc_html_e('Your shopping bag','ruwah'); ?></h2><button class="rb-close" type="button" data-cart-close aria-label="<?php esc_attr_e('Close cart','ruwah'); ?>">×</button></header>
    <div class="rb-cart-body widget_shopping_cart_content"><?php if(function_exists('woocommerce_mini_cart')) woocommerce_mini_cart(); ?></div>
    <footer>
        <p><?php echo wp_kses_post(sprintf(__('Add %s more for free shipping','ruwah'),wc_price($progress['remaining']))); ?></p>
        <div class="rb-progress"><i style="width:<?php echo esc_attr($progress['percent']); ?>%"></i></div>
        <a class="rb-button" href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')); ?>"><?php esc_html_e('View bag & checkout','ruwah'); ?></a>
    </footer>
</aside>
