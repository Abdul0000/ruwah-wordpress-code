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
    <style id="ruwah-top-section-fix-v2">
        [hidden] { display: none !important; }

        .rb-nav,
        .rb-nav-list {
            list-style: none !important;
            margin: 0;
            padding: 0;
        }
        .rb-nav-list {
            display: flex;
            align-items: center;
            gap: 22px;
        }
        .rb-nav-list > li {
            list-style: none !important;
            margin: 0;
            padding: 0;
        }
        .rb-nav-list > li::marker { content: ""; }
        .rb-nav-list > li::before { display: none !important; content: none !important; }

        .rb-header-inner {
            grid-template-columns: minmax(0,1fr) auto minmax(0,1fr);
        }
        .rb-nav { min-width: 0; }
        .rb-tools { white-space: nowrap; }

        .rb-hero {
            position: relative;
            display: grid !important;
            grid-template-columns: minmax(0,56%) minmax(0,44%) !important;
            grid-template-rows: minmax(620px,auto);
            min-height: 620px;
            overflow: hidden;
            background: linear-gradient(90deg,#f7eaea 0 56%,#ead3d3 56% 100%);
        }
        .rb-hero-copy {
            position: relative !important;
            inset: auto !important;
            z-index: 2;
            grid-column: 1 !important;
            grid-row: 1 !important;
            align-self: center;
            width: 100% !important;
            max-width: none !important;
            margin: 0 !important;
            padding: 76px 7vw 76px max(34px,calc((100vw - 1360px)/2)) !important;
            box-sizing: border-box;
        }
        .rb-hero-copy .rb-kicker {
            display: block;
            max-width: 650px;
            margin-bottom: 26px;
            line-height: 1.35;
        }
        .rb-hero-copy h1 {
            max-width: 760px !important;
            margin: 0 0 28px !important;
            font-size: clamp(56px,5.2vw,88px) !important;
            line-height: .96 !important;
            letter-spacing: -.04em;
            overflow-wrap: normal !important;
            word-break: normal !important;
        }
        .rb-hero-copy p {
            max-width: 620px !important;
            margin-bottom: 28px;
            font-size: clamp(16px,1.25vw,20px);
            line-height: 1.7;
        }
        .rb-hero-copy .rb-actions,
        .rb-hero-copy .rb-proof {
            max-width: 650px;
        }

        .rb-hero-media {
            position: relative !important;
            inset: auto !important;
            z-index: 1;
            grid-column: 2 !important;
            grid-row: 1 !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: auto !important;
            height: auto !important;
            min-height: 620px;
            padding: 56px 5vw 56px 28px !important;
            box-sizing: border-box;
            overflow: hidden;
        }
        .rb-hero-media::before {
            content: "";
            position: absolute;
            width: min(540px,82%);
            aspect-ratio: 1;
            border-radius: 50%;
            background: rgba(255,246,241,.78);
            z-index: 0;
        }
        .rb-hero-media img {
            position: relative;
            z-index: 1;
            display: block !important;
            width: min(650px,100%) !important;
            height: auto !important;
            max-height: 530px !important;
            object-fit: contain !important;
            object-position: center !important;
            margin: 0 !important;
        }

        @media (max-width: 1180px) {
            .rb-nav-list { gap: 14px; }
            .rb-nav-list a { font-size: 12px; }
            .rb-hero {
                grid-template-columns: minmax(0,54%) minmax(0,46%) !important;
            }
            .rb-hero-copy {
                padding-right: 4vw !important;
            }
            .rb-hero-copy h1 {
                font-size: clamp(50px,5.2vw,70px) !important;
            }
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
                grid-template-columns: minmax(0,52%) minmax(0,48%) !important;
            }
            .rb-hero-copy {
                padding: 60px 28px !important;
            }
            .rb-hero-copy h1 {
                font-size: clamp(46px,5.7vw,62px) !important;
            }
            .rb-hero-media {
                padding: 40px 24px !important;
            }
        }

        @media (max-width: 760px) {
            .rb-hero {
                display: flex !important;
                flex-direction: column;
                min-height: 0;
                background: #f7eaea;
            }
            .rb-hero-copy {
                order: 1;
                padding: 52px 20px 34px !important;
            }
            .rb-hero-copy h1 {
                max-width: 100% !important;
                font-size: clamp(42px,13vw,58px) !important;
                line-height: 1 !important;
            }
            .rb-hero-media {
                order: 2;
                width: 100% !important;
                min-height: 360px;
                padding: 24px 20px 44px !important;
                background: #ead3d3;
            }
            .rb-hero-media img {
                width: min(520px,100%) !important;
                max-height: 330px !important;
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
    <div class="rb-cart-body widget_shopping_cart_content"><?php if (function_exists('woocommerce_mini_cart')) { woocommerce_mini_cart(); } ?></div>
    <footer>
        <p><?php echo $progress['remaining'] > 0 ? wp_kses_post(sprintf(__('Add %s more for free shipping','ruwah'),wc_price($progress['remaining']))) : esc_html__('You unlocked free shipping','ruwah'); ?></p>
        <div class="rb-progress"><i style="width:<?php echo esc_attr((string)$progress['percent']); ?>%"></i></div>
        <a class="rb-button" href="<?php echo esc_url(ruwah_cart_url()); ?>">View bag & checkout</a>
    </footer>
</aside>
<main id="main-content">
