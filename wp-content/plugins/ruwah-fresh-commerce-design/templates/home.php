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
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#main">Skip to content</a>
<div class="rwb-announcement rwb-ref-utility">
    <?php if ($hero) : ?>
        <a href="<?php echo esc_url($hero->get_permalink()); ?>"><span>NEW</span><span><?php echo esc_html($hero->get_name()); ?></span><span>SHOP</span></a>
    <?php else : ?>
        <a href="<?php echo esc_url($shop_url); ?>"><span>RUWAH BEAUTY</span><span>FIVE FOCUSED FORMULAS</span><span>SHOP</span></a>
    <?php endif; ?>
</div>
<header class="rwb-header rwb-ref-header" data-header>
    <div class="rwb-shell rwb-header-row">
        <div class="rwb-nav-side">
            <button class="rwb-icon rwb-menu-btn" data-menu-open aria-label="Menu"><?php echo function_exists('rwb_icon') ? rwb_icon('menu') : '☰'; ?></button>
            <nav class="rwb-desktop-nav rwb-ref-nav" aria-label="Primary">
                <a href="<?php echo esc_url($shop_url); ?>">Shop</a>
                <a href="#rwb-genesis">Learn</a>
                <a href="<?php echo esc_url($sun_product ? $sun_product->get_permalink() : $shop_url); ?>">Sun Care</a>
            </nav>
        </div>
        <div class="rwb-brand">
            <?php if (has_custom_logo()) { the_custom_logo(); } else { ?><a href="<?php echo esc_url(home_url('/')); ?>">RUWAH</a><?php } ?>
        </div>
        <div class="rwb-tools">
            <button class="rwb-icon" data-search-open aria-label="Search"><?php echo function_exists('rwb_icon') ? rwb_icon('search') : '⌕'; ?></button>
            <a class="rwb-ref-account-link" href="<?php echo esc_url($account_url); ?>">My Account</a>
            <button class="rwb-ref-cart-link" data-cart-open aria-label="Cart">Cart <span class="rwb-cart-count"><?php echo esc_html((string) $count); ?></span></button>
        </div>
    </div>
</header>
<aside class="rwb-mobile-menu" data-menu hidden>
    <div class="rwb-panel-head"><b>RUWAH BEAUTY</b><button class="rwb-icon" data-menu-close aria-label="Close menu"><?php echo function_exists('rwb_icon') ? rwb_icon('close') : '×'; ?></button></div>
    <nav>
        <a href="<?php echo esc_url($shop_url); ?>">Shop all</a>
        <a href="#rwb-genesis">Learn</a>
        <a href="<?php echo esc_url($sun_product ? $sun_product->get_permalink() : $shop_url); ?>">Sun Care</a>
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
    <div class="rwb-ref-hero-copy" data-reveal><p class="rwb-ref-kicker">FEATURED FORMULA</p><h1><?php echo esc_html($hero ? $hero->get_name() : 'Ruwah Beauty'); ?></h1><?php if ($hero_info) : ?><h2><?php echo esc_html(implode(' · ', $hero_info['benefits'])); ?></h2><p><?php echo esc_html($hero_info['tagline']); ?></p><?php else : ?><p>Luxury care for everyday skin.</p><?php endif; ?><a class="rwb-ref-hero-btn" href="<?php echo esc_url($hero ? $hero->get_permalink() : $shop_url); ?>">Shop Now</a></div>
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