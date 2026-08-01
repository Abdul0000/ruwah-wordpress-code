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
    <style id="ruwah-top-section-fix">
        .rb-nav,
        .rb-nav-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .rb-nav-list {
            display: flex;
            align-items: center;
            gap: 22px;
        }
        .rb-nav-list > li {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .rb-nav-list > li::marker {
            content: '';
        }
        .rb-header-inner {
            grid-template-columns: minmax(0,1fr) auto minmax(0,1fr);
        }
        .rb-nav {
            min-width: 0;
        }
        .rb-tools {
            white-space: nowrap;
        }
        .rb-hero {
            min-height: 640px;
            grid-template-columns: minmax(0,1fr) minmax(420px,46%);
            align-items: stretch;
            background: linear-gradient(90deg,#f6e8e8 0 56%,#ead4d4 56%);
        }
        .rb-hero-copy {
            width: min(100%,1240px);
            max-width: 1240px;
            margin: 0 auto;
            padding: 82px max(24px,calc((100vw - 1240px)/2)) 82px max(24px,calc((100vw - 1240px)/2));
            padding-right: min(52vw,650px);
            box-sizing: border-box;
        }
        .rb-hero-copy h1 {
            max-width: 680px;
            font-size: clamp(58px,5.6vw,88px);
            line-height: .96;
            letter-spacing: -.035em;
            overflow-wrap: normal;
        }
        .rb-hero-copy p,
        .rb-hero-copy .rb-actions,
        .rb-hero-copy .rb-proof,
        .rb-hero-copy .rb-kicker {
            max-width: 600px;
        }
        .rb-hero-media {
            inset: 0 0 0 auto;
            width: 46%;
            padding: 54px 54px 54px 24px;
        }
        .rb-hero-media::before {
            width: min(520px,80%);
            height: min(520px,80%);
        }
        .rb-hero-media img {
            width: min(620px,100%);
            height: min(520px,78vh);
            object-fit: contain;
        }
        @media (max-width: 1180px) {
            .rb-nav-list { gap: 14px; }
            .rb-nav-list a { font-size: 12px; }
            .rb-hero-copy h1 { font-size: clamp(52px,5.5vw,72px); }
        }
        @media (max-width: 1050px) {
            .rb-nav-list {
                flex-direction: column;
                align-items: stretch;
                gap: 0;
            }
            .rb-nav-list a {
                display: block;
                padding: 10px;
                font-size: 14px;
            }
            .rb-hero {
                grid-template-columns: 1fr;
            }
            .rb-hero-copy {
                padding-left: max(24px,calc((100vw - 900px)/2));
                padding-right: 48%;
            }
            .rb-hero-media { width: 48%; }
        }
        @media (max-width: 760px) {
            .rb-hero {
                min-height: 760px;
                display: block;
            }
            .rb-hero-copy {
                padding: 55px 20px 330px;
            }
            .rb-hero-copy h1 {
                max-width: 100%;
                font-size: clamp(44px,13vw,58px);
                line-height: 1;
            }
            .rb-hero-media {
                inset: auto 0 0;
                width: 100%;
                height: 330px;
                padding: 20px;
            }
            .rb-hero-media img {
                width: 100%;
                height: 100%;
            }
        }
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
    <div class="rb-shell rb-header-inner">
        <button class="rb-icon-btn rb-menu-btn" type="button" data-menu-toggle aria-label="Open menu">☰</button>
        <nav class="rb-nav" data-menu aria-label="Primary navigation">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'items_wrap'     => '<ul class="rb-nav-list">%3$s</ul>',
                    'fallback_cb'    => false,
                ]);
            } else {
                ?>
                <ul class="rb-nav-list">
                    <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
                    <li><a href="<?php echo esc_url(ruwah_shop_url()); ?>">Shop All</a></li>
                    <li><a href="<?php echo esc_url(add_query_arg('orderby','date',ruwah_shop_url())); ?>">New Arrivals</a></li>
                    <li><a href="<?php echo esc_url(ruwah_page_url('shop-by-concern')); ?>">Concerns</a></li>
                    <li><a href="<?php echo esc_url(ruwah_page_url('bundles')); ?>">Bundles</a></li>
                </ul>
                <?php
            }
            ?>
        </nav>
        <a class="rb-brand" href="<?php echo esc_url(home_url('/')); ?>">
            <svg viewBox="0 0 40 40"><path d="M20 4c7 8 11 14 11 21a11 11 0 1 1-22 0c0-7 4-13 11-21Z"/><path d="M13 27c5-2 10-6 14-12"/></svg>
            <strong>RUWAH BEAUTY</strong>
        </a>
        <div class="rb-tools">
            <button class="rb-icon-btn" data-search-open aria-label="Search">⌕</button>
            <a href="<?php echo esc_url(ruwah_account_url()); ?>" aria-label="Account">◎</a>
            <a href="<?php echo esc_url(ruwah_page_url('wishlist')); ?>" aria-label="Wishlist">♡</a>
            <button class="rb-icon-btn rb-cart-btn" data-cart-open><span>Bag</span><span class="rb-cart-count"><?php echo esc_html((string)ruwah_cart_count()); ?></span></button>
        </div>
    </div>
</header>
<div class="rb-overlay" data-overlay hidden></div>
<section class="rb-search" data-search hidden>
    <div class="rb-search-card">
        <button class="rb-close" data-search-close>×</button>
        <span class="rb-kicker">SEARCH RUWAH BEAUTY</span>
        <h2>What are you looking for?</h2>
        <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
            <input type="search" name="s" placeholder="Search products, ingredients or concerns">
            <input type="hidden" name="post_type" value="product">
            <button class="rb-button" type="submit">Search</button>
        </form>
    </div>
</section>
<aside class="rb-cart-drawer" data-cart-drawer aria-hidden="true">
    <header><h2>Your shopping bag</h2><button class="rb-close" data-cart-close>×</button></header>
    <div class="rb-cart-body widget_shopping_cart_content"><?php if(function_exists('woocommerce_mini_cart'))woocommerce_mini_cart(); ?></div>
    <footer>
        <p><?php echo $progress['remaining']>0?wp_kses_post(sprintf(__('Add %s more for free shipping','ruwah'),wc_price($progress['remaining']))):esc_html__('You unlocked free shipping','ruwah'); ?></p>
        <div class="rb-progress"><i style="width:<?php echo esc_attr((string)$progress['percent']); ?>%"></i></div>
        <a class="rb-button" href="<?php echo esc_url(ruwah_cart_url()); ?>">View bag & checkout</a>
    </footer>
</aside>
<main id="main-content">
