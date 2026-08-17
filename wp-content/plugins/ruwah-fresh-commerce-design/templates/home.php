<?php
defined('ABSPATH') || exit;

$products = function_exists('rwb_products') ? rwb_products() : [];
$hero = $products[0] ?? null;
$hero_info = $hero && function_exists('rwb_info') ? rwb_info($hero) : null;
$reviews = function_exists('rwb_reviews')
    ? rwb_reviews(5)
    : get_comments([
        'post_type' => 'product',
        'status' => 'approve',
        'number' => 5,
    ]);
$count = function_exists('WC') && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
$sun_product = function_exists('rwb_product')
    ? rwb_product(68)
    : (function_exists('wc_get_product') ? wc_get_product(68) : null);
$shop_url = function_exists('rwb_shop_url') ? rwb_shop_url() : home_url('/shop/');
$account_url = function_exists('rwb_account_url') ? rwb_account_url() : home_url('/my-account/');
$cart_url = function_exists('rwb_cart_url') ? rwb_cart_url() : home_url('/cart/');
$newsletter = isset($_GET['newsletter']) ? sanitize_key(wp_unslash($_GET['newsletter'])) : '';

$hero_image_id = 0;
if ($hero) {
    $gallery = $hero->get_gallery_image_ids();
    $hero_image_id = (int) ($gallery[1] ?? $gallery[0] ?? $hero->get_image_id());
}

$hero_descriptor = '';
$hero_ingredient_line = '';
if (is_array($hero_info)) {
    $tagline = trim((string) ($hero_info['tagline'] ?? ''));
    if ($tagline && preg_match('/^A\s+(.+?)\s+with\b/i', $tagline, $descriptor_match)) {
        $descriptor = trim((string) $descriptor_match[1]);
        $descriptor = preg_replace('/\s+and\s+/i', ' + ', $descriptor);
        $hero_descriptor = ucwords((string) $descriptor);
    }
    if (! $hero_descriptor && $tagline) {
        $hero_descriptor = wp_trim_words($tagline, 7, '');
    }
    $ingredient_labels = [];
    foreach ((array) ($hero_info['benefits'] ?? []) as $benefit) {
        $parts = preg_split('/\s+for\s+/i', trim((string) $benefit), 2);
        $label = trim((string) ($parts[0] ?? ''));
        if ($label && ! in_array($label, $ingredient_labels, true)) {
            $ingredient_labels[] = $label;
        }
    }
    $hero_ingredient_line = implode(' · ', array_slice($ingredient_labels, 0, 3));
}
if (! $hero_descriptor) {
    $hero_descriptor = 'Luxury Care For Everyday Skin';
}
if (! $hero_ingredient_line && is_array($hero_info) && ! empty($hero_info['tagline'])) {
    $hero_ingredient_line = (string) $hero_info['tagline'];
}

$total_reviews = 0;
$best_sales = -1;
$best_id = 0;
foreach ($products as $product) {
    $total_reviews += (int) $product->get_review_count();
    $sales = (int) $product->get_total_sales();
    if ($sales > $best_sales) {
        $best_sales = $sales;
        $best_id = $sales > 0 ? (int) $product->get_id() : 0;
    }
}
$community_heading = $total_reviews > 0 ? 'Community Favorites' : 'Ruwah Favorites';

$render_card = static function ($product, $best_id) {
    if (! $product) {
        return;
    }
    $info = function_exists('rwb_info') ? rwb_info($product) : null;
    $rating = (float) $product->get_average_rating();
    $review_count = (int) $product->get_review_count();
    $badge = (int) $product->get_id() === (int) $best_id
        ? 'Bestseller'
        : ($product->is_on_sale() ? 'Offer' : '');
    ?>
    <article class="rwb-ref-card" data-reveal>
        <a class="rwb-ref-card-media" href="<?php echo esc_url($product->get_permalink()); ?>">
            <?php if ($badge) : ?>
                <span class="rwb-ref-badge"><?php echo esc_html($badge); ?></span>
            <?php endif; ?>
            <?php echo wp_kses_post($product->get_image('woocommerce_single', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
        </a>
        <div class="rwb-ref-card-body">
            <h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
            <?php if ($info) : ?>
                <p class="rwb-ref-card-tagline"><?php echo esc_html($info['tagline']); ?></p>
            <?php endif; ?>
            <div class="rwb-ref-rating">
                <?php if ($review_count > 0) : ?>
                    <span aria-hidden="true"><?php echo esc_html(str_repeat('★', max(1, min(5, (int) round($rating))))); ?></span>
                    <small><?php echo esc_html((string) $review_count); ?></small>
                <?php else : ?>
                    <span class="rwb-ref-rating-neutral">Ruwah formula</span>
                <?php endif; ?>
            </div>
            <?php if ($info && ! empty($info['size'])) : ?>
                <div class="rwb-ref-size-control"><span><?php echo esc_html($info['size']); ?></span><b aria-hidden="true">⌄</b></div>
            <?php endif; ?>
            <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                <a rel="nofollow" class="rwb-ref-cart add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($product->add_to_cart_url()); ?>"><span>Add to Cart</span><span><?php echo wp_kses_post(wc_price((float) $product->get_price())); ?></span></a>
            <?php else : ?>
                <a class="rwb-ref-cart" href="<?php echo esc_url($product->get_permalink()); ?>"><span>View Product</span><span>↗</span></a>
            <?php endif; ?>
        </div>
    </article>
    <?php
};
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
<style id="rwb-dieux-home-hero-menu-v65">
.rwb-reference-home-v5 .rwb-ref-utility{position:relative;height:40px;min-height:40px;background:#2d2d2d;color:#fff}
.rwb-reference-home-v5 .rwb-ref-utility a{height:40px;min-height:40px;padding:0 70px;color:#fff;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:14px;font-weight:500;line-height:1;letter-spacing:.035em;text-transform:uppercase}
.rwb-reference-home-v5 .rwb-ref-utility-pause{position:absolute;right:24px;top:50%;transform:translateY(-50%);font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:13px;letter-spacing:-.15em}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact){position:absolute;left:0;right:0;top:40px;border:0;background:transparent;color:#fff;backdrop-filter:none}
.admin-bar.rwb-reference-home-v5 .rwb-ref-header:not(.compact){top:72px}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-shell{width:calc(100% - 112px);max-width:none}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-header-row{min-height:112px;grid-template-columns:1fr auto 1fr}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-nav{gap:56px}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-nav a,.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-account-link,.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-cart-link{color:#fff;font-size:16px;font-weight:500;line-height:1;letter-spacing:.015em;text-transform:uppercase;text-shadow:0 1px 12px rgba(0,0,0,.18)}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-account-link{margin:0}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-tools{gap:30px}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-brand .custom-logo{width:auto;max-width:205px;max-height:92px;filter:brightness(0) invert(1);object-fit:contain}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-icon{width:52px;height:52px;color:#fff}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-icon svg{width:30px;height:30px;stroke-width:1.7}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-cart-link{display:flex;align-items:center;gap:0;min-height:52px;padding:0;border:0;background:transparent}
.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-cart-link .rwb-cart-count{position:static;width:auto;height:auto;display:inline;background:transparent;color:inherit;font-size:16px;font-weight:500}
.rwb-reference-home-v5 .rwb-ref-header.compact .rwb-header-row{min-height:64px}
.rwb-reference-home-v5 .rwb-ref-header.compact .rwb-brand .custom-logo{max-width:118px;max-height:50px}
.rwb-reference-home-v5 .rwb-ref-hero{height:calc(100vh - 40px);min-height:720px;display:grid;place-items:center;background:#514645;color:#fff}
.rwb-reference-home-v5 .rwb-ref-hero-media img{width:100%;height:100%;object-fit:cover;object-position:center center;filter:saturate(.92) contrast(1.04) brightness(.78);transform:scale(1.012)}
.rwb-reference-home-v5 .rwb-ref-hero-wash{background:linear-gradient(90deg,rgba(0,0,0,.10),rgba(0,0,0,.03) 42%,rgba(0,0,0,.12)),linear-gradient(0deg,rgba(0,0,0,.18),transparent 48%,rgba(0,0,0,.04))}
.rwb-reference-home-v5 .rwb-ref-hero-copy{width:940px;max-width:90vw;padding:122px 24px 0;text-align:center;text-shadow:0 2px 18px rgba(0,0,0,.22)}
.rwb-reference-home-v5 .rwb-ref-hero-copy .rwb-ref-kicker{margin:0;color:#fff;font-size:16px;font-weight:500;line-height:1;letter-spacing:.015em;text-transform:uppercase}
.rwb-reference-home-v5 .rwb-ref-hero-copy h1{margin:20px 0 0;color:#fff;font-family:var(--sans,Inter,Arial,sans-serif);font-size:clamp(62px,4vw,78px);font-weight:600;line-height:.95;letter-spacing:-.04em}
.rwb-reference-home-v5 .rwb-ref-hero-copy h2{max-width:900px;margin:12px auto 0;color:#fff;font-family:var(--sans,Inter,Arial,sans-serif);font-size:clamp(36px,2.8vw,52px);font-weight:500;line-height:1.02;letter-spacing:-.035em}
.rwb-reference-home-v5 .rwb-ref-hero-copy>p:not(.rwb-ref-kicker){margin:22px auto 0;color:#fff;font-size:23px;font-weight:400;line-height:1.25;letter-spacing:-.015em}
.rwb-reference-home-v5 .rwb-ref-hero-btn{width:228px;min-width:228px;height:64px;min-height:64px;margin-top:34px;padding:0 24px;border:0;background:#f8f5ee;color:#2c2a28;font-family:ui-monospace,SFMono-Regular,Menlo,Consolas,monospace;font-size:18px;font-weight:400;line-height:1;text-shadow:none;text-transform:none}
.rwb-reference-home-v5 .rwb-ref-hero-btn:hover{background:#fff;color:#111}
@media(max-width:1200px) and (min-width:783px){.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-shell{width:calc(100% - 64px)}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-nav{gap:30px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-nav a,.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-account-link,.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-cart-link,.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-cart-link .rwb-cart-count{font-size:12px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-brand .custom-logo{max-width:160px;max-height:78px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-tools{gap:16px}.rwb-reference-home-v5 .rwb-ref-hero-copy{padding-top:105px}.rwb-reference-home-v5 .rwb-ref-hero-copy>p:not(.rwb-ref-kicker){font-size:18px}}
@media(max-width:782px){.rwb-reference-home-v5 .rwb-ref-utility{height:30px;min-height:30px}.rwb-reference-home-v5 .rwb-ref-utility a{height:30px;min-height:30px;padding:0 38px 0 12px;font-size:8px;letter-spacing:.05em}.rwb-reference-home-v5 .rwb-ref-utility-pause{right:10px;font-size:9px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact){top:30px}.admin-bar.rwb-reference-home-v5 .rwb-ref-header:not(.compact){top:76px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-shell{width:calc(100% - 24px)}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-header-row{min-height:68px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-brand .custom-logo{max-width:108px;max-height:56px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-icon{width:42px;height:42px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-icon svg{width:23px;height:23px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-cart-link{font-size:0}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-cart-link:before{content:'BAG (';font-size:9px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-cart-link:after{content:')';font-size:9px}.rwb-reference-home-v5 .rwb-ref-header:not(.compact) .rwb-ref-cart-link .rwb-cart-count{font-size:9px}.rwb-reference-home-v5 .rwb-ref-hero{height:auto;min-height:calc(100svh - 30px)}.rwb-reference-home-v5 .rwb-ref-hero-copy{width:100%;max-width:94vw;padding:94px 16px 30px}.rwb-reference-home-v5 .rwb-ref-hero-copy .rwb-ref-kicker{font-size:11px}.rwb-reference-home-v5 .rwb-ref-hero-copy h1{margin-top:14px;font-size:clamp(46px,12vw,62px)}.rwb-reference-home-v5 .rwb-ref-hero-copy h2{margin-top:10px;font-size:clamp(26px,7.6vw,38px)}.rwb-reference-home-v5 .rwb-ref-hero-copy>p:not(.rwb-ref-kicker){margin-top:17px;font-size:14px}.rwb-reference-home-v5 .rwb-ref-hero-btn{width:168px;min-width:168px;height:50px;min-height:50px;margin-top:25px;font-size:14px}}
</style>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#main">Skip to content</a>
<div class="rwb-announcement rwb-ref-utility">
    <a href="<?php echo esc_url($shop_url); ?>"><span class="rwb-ref-utility-copy">PAKISTAN-WIDE DELIVERY · SECURE CHECKOUT</span></a>
    <span class="rwb-ref-utility-pause" aria-hidden="true">Ⅱ</span>
</div>
<header class="rwb-header rwb-ref-header" data-header>
    <div class="rwb-shell rwb-header-row">
        <div class="rwb-nav-side">
            <button class="rwb-icon rwb-menu-btn" data-menu-open aria-label="Menu"><?php echo function_exists('rwb_icon') ? rwb_icon('menu') : '☰'; ?></button>
            <nav class="rwb-desktop-nav rwb-ref-nav" aria-label="Primary">
                <a href="<?php echo esc_url($shop_url); ?>">Shop</a>
                <a href="#rwb-genesis">Learn</a>
                <a href="<?php echo esc_url($sun_product ? $sun_product->get_permalink() : $shop_url); ?>">Sunscreen Decoder</a>
            </nav>
        </div>
        <div class="rwb-brand">
            <?php if (has_custom_logo()) { the_custom_logo(); } else { ?><a href="<?php echo esc_url(home_url('/')); ?>">RUWAH</a><?php } ?>
        </div>
        <div class="rwb-tools">
            <button class="rwb-icon" data-search-open aria-label="Search"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.7"></circle><path d="m15.5 15.5 5 5"></path></svg></button>
            <a class="rwb-ref-account-link" href="<?php echo esc_url($account_url); ?>">My Account</a>
            <button class="rwb-ref-cart-link" data-cart-open aria-label="Cart">CART (<span class="rwb-cart-count"><?php echo esc_html((string) $count); ?></span>)</button>
        </div>
    </div>
</header>
<aside class="rwb-mobile-menu" data-menu hidden>
    <div class="rwb-panel-head"><b>RUWAH BEAUTY</b><button class="rwb-icon" data-menu-close aria-label="Close menu"><?php echo function_exists('rwb_icon') ? rwb_icon('close') : '×'; ?></button></div>
    <nav>
        <a href="<?php echo esc_url($shop_url); ?>">Shop all</a>
        <a href="#rwb-genesis">Learn</a>
        <a href="<?php echo esc_url($sun_product ? $sun_product->get_permalink() : $shop_url); ?>">Sunscreen Decoder</a>
        <?php foreach ($products as $product) : ?><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a><?php endforeach; ?>
        <a href="<?php echo esc_url($account_url); ?>">My account</a>
    </nav>
</aside>
<div class="rwb-layer" data-search hidden>
    <button class="rwb-backdrop" data-search-close aria-label="Close search"></button>
    <div class="rwb-search-panel"><div class="rwb-panel-head"><b>Search Ruwah</b><button class="rwb-icon" data-search-close aria-label="Close search"><?php echo function_exists('rwb_icon') ? rwb_icon('close') : '×'; ?></button></div><form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><input type="search" name="s" placeholder="Search products" required><input type="hidden" name="post_type" value="product"><button>Search</button></form></div>
</div>
<div class="rwb-layer" data-cart hidden>
    <button class="rwb-backdrop" data-cart-close aria-label="Close cart"></button>
    <aside class="rwb-cart-panel rwb-shop-cart-drawer"><?php if (function_exists('rwb_render_cart_drawer_content')) { rwb_render_cart_drawer_content(); } ?></aside>
</div>
<main id="main" class="rwb-ref-home">
<section class="rwb-ref-hero" aria-label="Featured formula">
    <div class="rwb-ref-hero-media" aria-hidden="true"><?php if ($hero_image_id) { echo wp_kses_post(wp_get_attachment_image($hero_image_id, 'full', false, ['loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async'])); } elseif ($hero) { echo wp_kses_post($hero->get_image('full', ['loading' => 'eager', 'fetchpriority' => 'high'])); } ?></div>
    <div class="rwb-ref-hero-wash"></div>
    <div class="rwb-ref-hero-copy" data-reveal><p class="rwb-ref-kicker">NEW</p><h1><?php echo esc_html($hero ? $hero->get_name() : 'Ruwah Beauty'); ?></h1><h2><?php echo esc_html($hero_descriptor); ?></h2><?php if ($hero_ingredient_line) : ?><p><?php echo esc_html($hero_ingredient_line); ?></p><?php endif; ?><a class="rwb-ref-hero-btn" href="<?php echo esc_url($hero ? $hero->get_permalink() : $shop_url); ?>">Shop Now</a></div>
</section>
<?php if ($products) : ?>
<section class="rwb-ref-community" id="community-favorites"><div class="rwb-ref-wrap"><h2 data-reveal><?php echo esc_html($community_heading); ?></h2><div class="rwb-ref-card-grid"><?php foreach (array_slice($products, 0, 4) as $rank => $product) : ?><article class="rwb-commerce-card" data-reveal><?php Ruwah_Fresh_Commerce_Design::render_card($product, (int) $rank); ?></article><?php endforeach; ?></div><a class="rwb-ref-text-link" href="<?php echo esc_url($shop_url); ?>">Shop All</a></div></section>
<?php endif; ?>
<section class="rwb-ref-proof" aria-label="Ruwah product notes">
    <div class="rwb-ref-proof-shell" data-proof-slider>
        <button class="rwb-ref-proof-arrow prev" type="button" data-proof-prev aria-label="Previous note">←</button>
        <div class="rwb-ref-proof-viewport"><div class="rwb-ref-proof-track" data-proof-track>
        <?php if ($reviews) : ?>
            <?php foreach ($reviews as $review) : ?>
                <?php
                $product = function_exists('rwb_product') ? rwb_product($review->comment_post_ID) : wc_get_product($review->comment_post_ID);
                if (! $product) { continue; }
                $gallery = $product->get_gallery_image_ids();
                $image_id = (int) ($gallery[0] ?? $product->get_image_id());
                $rating = (int) get_comment_meta($review->comment_ID, 'rating', true);
                ?>
                <article class="rwb-ref-proof-slide"><div class="rwb-ref-proof-image"><?php echo wp_kses_post(wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy', 'decoding' => 'async'])); ?></div><div class="rwb-ref-proof-copy"><?php if ($rating > 0) : ?><div class="rwb-ref-proof-stars"><?php echo esc_html(str_repeat('★', min(5, $rating))); ?></div><?php endif; ?><blockquote>“<?php echo esc_html(wp_trim_words(wp_strip_all_tags($review->comment_content), 44, '…')); ?>”</blockquote><p>— <?php echo esc_html($review->comment_author); ?></p><a href="<?php echo esc_url($product->get_permalink()); ?>">Shop <?php echo esc_html($product->get_name()); ?></a></div></article>
            <?php endforeach; ?>
        <?php else : ?>
            <?php foreach (array_slice($products, 0, 4) as $product) : ?>
                <?php $info = function_exists('rwb_info') ? rwb_info($product) : null; $gallery = $product->get_gallery_image_ids(); $image_id = (int) ($gallery[0] ?? $product->get_image_id()); ?>
                <article class="rwb-ref-proof-slide"><div class="rwb-ref-proof-image"><?php echo wp_kses_post(wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy', 'decoding' => 'async'])); ?></div><div class="rwb-ref-proof-copy"><p class="rwb-ref-kicker">PRODUCT NOTE</p><blockquote><?php echo esc_html($product->get_name()); ?></blockquote><?php if ($info) : ?><p><?php echo esc_html($info['tagline']); ?> · <?php echo esc_html(implode(' · ', $info['benefits'])); ?></p><?php endif; ?><a href="<?php echo esc_url($product->get_permalink()); ?>">Shop Product</a></div></article>
            <?php endforeach; ?>
        <?php endif; ?>
        </div></div>
        <button class="rwb-ref-proof-arrow next" type="button" data-proof-next aria-label="Next note">→</button>
        <div class="rwb-ref-proof-dots" data-proof-dots></div>
    </div>
</section>
<section class="rwb-ref-genesis" id="rwb-genesis"><div class="rwb-ref-wrap"><p class="rwb-ref-kicker">OUR GENESIS</p><div class="rwb-ref-genesis-grid"><article><span>I.</span><h3>PACK-FIRST DETAILS</h3><p>Names, ingredients, size and benefits are kept aligned with the supplied RWB packaging.</p></article><article><span>II.</span><h3>FOCUSED FORMULAS</h3><p>Five formulas, each with a clear role in cleansing, brightening, moisture or daily sun care.</p></article><article><span>III.</span><h3>REAL PRODUCT MEDIA</h3><p>Each mapped formula uses its own optimized product photography across featured and gallery views.</p></article><article><span>IV.</span><h3>LIVE PRICE &amp; STOCK</h3><p>WooCommerce remains the source of truth for price, sale state, availability and purchase flow.</p></article><article><span>V.</span><h3>NO FILLER CLAIMS</h3><p>When a claim, test or review is not available, the storefront does not invent one.</p></article></div></div></section>
<section class="rwb-ref-trust" id="rwb-standard"><div class="rwb-ref-wrap"><p class="rwb-ref-kicker">THE RUWAH STANDARD</p><div class="rwb-ref-trust-words" aria-label="Ruwah store standards"><span>5 FORMULAS</span><span>20 PRODUCT SHOTS</span><span>LIVE COMMERCE</span></div><p class="rwb-ref-trust-quote">“Every product page should make the formula, size, benefits, price and purchase decision easier to understand.”</p></div></section>
<?php if ($products) : ?>
<section class="rwb-ref-rituals" id="rituals"><div class="rwb-ref-wrap rwb-ref-rituals-head"><h2>Rituals, not clutter.</h2><a href="<?php echo esc_url($shop_url); ?>">SHOP RUWAH</a></div><div class="rwb-ref-ritual-grid"><?php foreach ($products as $product) : ?><?php $gallery = $product->get_gallery_image_ids(); $image_id = (int) ($gallery[1] ?? $gallery[0] ?? $product->get_image_id()); ?><a href="<?php echo esc_url($product->get_permalink()); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>"><?php echo wp_kses_post(wp_get_attachment_image($image_id, 'large', false, ['loading' => 'lazy', 'decoding' => 'async'])); ?></a><?php endforeach; ?></div></section>
<?php endif; ?>
</main>
<footer class="rwb-ref-footer">
<section class="rwb-ref-footer-signup"><div class="rwb-ref-footer-signup-copy"><p class="rwb-ref-kicker">GET RUWAH</p><h2>Ruwah notes, new drops and ritual edits.</h2></div><div><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="rwb_newsletter"><?php wp_nonce_field('rwb_newsletter', 'rwb_nonce'); ?><label class="screen-reader-text" for="rwb-footer-email">Email address</label><input id="rwb-footer-email" type="email" name="email" required placeholder="My email address is"><button type="submit">Initiate Me</button></form><?php if ('success' === $newsletter) : ?><p class="rwb-form-ok">Thank you — your request was sent.</p><?php elseif ('invalid' === $newsletter) : ?><p class="rwb-form-error">Please enter a valid email.</p><?php elseif ('error' === $newsletter) : ?><p class="rwb-form-error">We could not send the signup request. Please try again.</p><?php endif; ?></div></section>
<div class="rwb-ref-footer-grid"><div class="rwb-ref-footer-brand"><?php if (has_custom_logo()) { the_custom_logo(); } else { ?><b>RUWAH BEAUTY</b><?php } ?><p>Luxury care for everyday skin.</p><small>Pakistan-wide delivery.</small></div><div><h3>Shop</h3><a href="<?php echo esc_url($shop_url); ?>">Shop all</a><?php foreach ($products as $product) : ?><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a><?php endforeach; ?></div><div><h3>Learn</h3><a href="#rwb-genesis">Our Genesis</a><a href="#rwb-standard">The Ruwah Standard</a><a href="#rituals">Rituals</a></div><div><h3>Account</h3><a href="<?php echo esc_url($account_url); ?>">My account</a><a href="<?php echo esc_url($cart_url); ?>">Shopping bag</a><a href="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/')); ?>">Checkout</a></div><div><h3>Our Promise</h3><p>Exact pack details.</p><p>Live price &amp; stock.</p><p>No filler claims.</p></div></div>
<div class="rwb-ref-footer-bottom"><span>© <?php echo esc_html(wp_date('Y')); ?> Ruwah Beauty</span><div><?php $privacy = get_privacy_policy_url(); if ($privacy) : ?><a href="<?php echo esc_url($privacy); ?>">Privacy Policy</a><?php endif; ?><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></div></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>