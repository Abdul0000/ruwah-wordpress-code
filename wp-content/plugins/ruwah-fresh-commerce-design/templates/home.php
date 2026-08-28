<?php
defined('ABSPATH') || exit;

$products = function_exists('rwb_products') ? array_values(array_filter(rwb_products(), static fn($item) => $item instanceof WC_Product && $item->is_visible())) : [];
$by_id = [];
foreach ($products as $item) $by_id[(int) $item->get_id()] = $item;
$get_product = static function (int $id) use ($by_id) {
    if (isset($by_id[$id])) return $by_id[$id];
    if (function_exists('rwb_product')) { $p = rwb_product($id); if ($p instanceof WC_Product) return $p; }
    return function_exists('wc_get_product') ? wc_get_product($id) : null;
};

$hero = $get_product(54) ?: ($products[0] ?? null);
$hero_info = $hero ? Ruwah_Fresh_Commerce_Design::display_copy($hero) : ['name' => 'Ruwah Beauty', 'tagline' => '', 'benefits' => [], 'size' => ''];
$shop_url = function_exists('rwb_shop_url') ? rwb_shop_url() : home_url('/shop/');
$account_url = function_exists('rwb_account_url') ? rwb_account_url() : home_url('/my-account/');
$cart_url = function_exists('rwb_cart_url') ? rwb_cart_url() : home_url('/cart/');
$checkout_url = function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/');
$shipping_url = home_url('/shipping-delivery/');
$refund_url = home_url('/returns-refunds/');
$privacy_url = get_privacy_policy_url();
$contact_url = home_url('/contact-us/');
$learn_url = home_url('/beauty-guide/');
$quality_url = home_url('/quality-safety/');
$count = function_exists('WC') && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
$whatsapp_number = '923713923279';
$whatsapp_url = 'https://wa.me/' . $whatsapp_number;
$admin_email = sanitize_email((string) get_option('admin_email'));
$branded_email = str_ends_with(strtolower($admin_email), '@ruwahbeauty.com') ? $admin_email : '';

$hero_image_id = 0;
if ($hero) {
    $gallery = $hero->get_gallery_image_ids();
    $hero_image_id = (int) ($gallery[1] ?? $gallery[0] ?? $hero->get_image_id());
}
$hero_image = $hero_image_id ? wp_get_attachment_image($hero_image_id, 'full', false, ['loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async', 'sizes' => '100vw']) : ($hero ? $hero->get_image('full', ['loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async']) : '');

$best = $products;
usort($best, static fn($a, $b) => (int) $b->get_total_sales() <=> (int) $a->get_total_sales());
$best = array_slice($best, 0, 4);

$verified_reviews = [];
$raw_reviews = function_exists('rwb_reviews') ? rwb_reviews(30) : get_comments(['post_type' => 'product', 'status' => 'approve', 'number' => 30]);
foreach ((array) $raw_reviews as $review) {
    if (! $review instanceof WP_Comment) continue;
    if (function_exists('wc_review_is_from_verified_owner') && ! wc_review_is_from_verified_owner($review->comment_ID)) continue;
    $review_product = $get_product((int) $review->comment_post_ID);
    if (! $review_product) continue;
    $rating = (int) get_comment_meta($review->comment_ID, 'rating', true);
    if ($rating < 1 || $rating > 5) continue;
    $verified_reviews[] = [$review, $review_product, $rating];
}
$review_count = count($verified_reviews);
$review_avg = 0.0;
$review_dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
if ($review_count) {
    $sum = 0;
    foreach ($verified_reviews as $row) { $sum += $row[2]; $review_dist[$row[2]]++; }
    $review_avg = $sum / $review_count;
}

$routine = [
    ['step' => '01', 'label' => 'Cleanse', 'product' => $get_product(60)],
    ['step' => '02', 'label' => 'Treat', 'product' => $get_product(54)],
    ['step' => '03', 'label' => 'Moisturize', 'product' => $get_product(62)],
    ['step' => '04', 'label' => 'Protect', 'product' => $get_product(68)],
];
$concerns = [
    ['title' => 'Dullness & uneven-looking tone', 'copy' => 'Explore products positioned around visible radiance and brighter-looking skin.', 'product' => $get_product(54)],
    ['title' => 'Dehydration', 'copy' => 'Find lightweight hydration-focused serum options for everyday routines.', 'product' => $get_product(64) ?: $get_product(54)],
    ['title' => 'Daily cleansing', 'copy' => 'A straightforward daily cleansing step designed to remove everyday buildup.', 'product' => $get_product(60)],
    ['title' => 'Everyday sun care', 'copy' => 'A dedicated daily sun-care step with product directions kept on-pack.', 'product' => $get_product(68)],
];

$usage = '';
$verified_fields = [];
if ($hero) {
    foreach (['pa_how-to-use', 'how-to-use', 'how_to_use', 'pa_usage', 'usage', 'directions'] as $attribute) {
        $value = trim(wp_strip_all_tags((string) $hero->get_attribute($attribute)));
        if ($value !== '') { $usage = $value; break; }
    }
    if ($usage === '') {
        foreach (['_how_to_use', 'how_to_use', '_usage', 'usage', '_directions', 'directions'] as $meta_key) {
            $value = trim(wp_strip_all_tags((string) get_post_meta($hero->get_id(), $meta_key, true)));
            if ($value !== '') { $usage = $value; break; }
        }
    }
    $field_sources = [
        'Complete ingredients / INCI' => ['pa_ingredients', 'ingredients', 'inci'],
        'Skin type' => ['pa_skin-type', 'skin-type', 'skin_type'],
        'Net quantity' => ['pa_size', 'size', 'pa_volume', 'volume'],
    ];
    foreach ($field_sources as $label => $keys) {
        foreach ($keys as $key) {
            $value = trim(wp_strip_all_tags((string) $hero->get_attribute($key)));
            if ($value === '') $value = trim(wp_strip_all_tags((string) get_post_meta($hero->get_id(), $key, true)));
            if ($value !== '') { $verified_fields[$label] = $value; break; }
        }
    }
    if (! empty($hero_info['size']) && empty($verified_fields['Net quantity'])) $verified_fields['Net quantity'] = trim((string) $hero_info['size']);
}
if ($usage === '') $usage = 'Follow the directions printed on the product packaging.';

$logo_id = (int) get_theme_mod('custom_logo', 0);
$logo_url = $logo_id ? wp_get_attachment_url($logo_id) : '';
$site_url = home_url('/');
$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Organization',
            '@id' => $site_url . '#organization',
            'name' => 'Ruwah Beauty',
            'url' => $site_url,
            'logo' => $logo_url ?: null,
            'sameAs' => [
                'https://www.facebook.com/share/1BNAdjWpYW/',
                'https://www.instagram.com/rawah.beauty',
                'https://www.tiktok.com/',
            ],
        ],
        [
            '@type' => 'WebSite',
            '@id' => $site_url . '#website',
            'url' => $site_url,
            'name' => 'Ruwah Beauty',
            'publisher' => ['@id' => $site_url . '#organization'],
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => home_url('/?s={search_term_string}&post_type=product'),
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ],
];
$schema['@graph'][0] = array_filter($schema['@graph'][0], static fn($value) => null !== $value && [] !== $value);

add_filter('pre_get_document_title', static fn() => 'Ruwah Beauty Pakistan | Brightening & Hydrating Skincare', 999);
$premium_css_path = dirname(__DIR__) . '/assets/home-premium.css';
$premium_css = is_readable($premium_css_path) ? (string) file_get_contents($premium_css_path) : '';
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="description" content="Shop Ruwah Beauty skincare in Pakistan. Discover brightening serums, rice skincare, face wash and daily sun care with cash on delivery.">
<?php wp_head(); ?>
<script type="application/ld+json"><?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
<?php if ($premium_css !== '') : ?><style id="rwb-home-premium-v1"><?php echo wp_strip_all_tags($premium_css); ?></style><?php endif; ?>
</head>
<body <?php body_class('rwb-home-premium'); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#main">Skip to content</a>

<div class="rhp-announcement" role="region" aria-label="Delivery and payment notice">
    <a href="<?php echo esc_url($shipping_url); ?>"><span>Pakistan-wide delivery</span><span aria-hidden="true">·</span><span>Cash on delivery</span><span aria-hidden="true">·</span><span>Shipping details</span></a>
</div>

<header class="rhp-header" data-premium-header>
    <div class="rhp-header-inner">
        <button class="rhp-icon-button rhp-menu-toggle" type="button" data-premium-menu-open aria-label="Open navigation" aria-expanded="false" aria-controls="rhp-mobile-menu">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h16"/></svg>
        </button>
        <nav class="rhp-desktop-nav" aria-label="Primary navigation">
            <a href="<?php echo esc_url($shop_url); ?>">Shop</a>
            <a href="#shop-by-concern">Shop by concern</a>
            <a href="#routine-builder">Routine guide</a>
            <a href="<?php echo esc_url($learn_url); ?>">Learn</a>
            <a href="<?php echo esc_url($contact_url); ?>">Contact</a>
        </nav>
        <div class="rhp-logo">
            <?php if (has_custom_logo()) { the_custom_logo(); } else { ?><a href="<?php echo esc_url($site_url); ?>" class="rhp-wordmark">Ruwah</a><?php } ?>
        </div>
        <div class="rhp-tools">
            <button class="rhp-icon-button" type="button" data-search-open aria-label="Search products"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.5" cy="10.5" r="6.4"/><path d="m15.4 15.4 4.8 4.8"/></svg></button>
            <a class="rhp-icon-button rhp-account" href="<?php echo esc_url($account_url); ?>" aria-label="My account"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="3.2"/><path d="M5.7 20c.7-4 2.8-6 6.3-6s5.6 2 6.3 6"/></svg></a>
            <button class="rhp-icon-button rhp-cart" type="button" data-cart-open aria-label="Open cart, <?php echo esc_attr((string) $count); ?> items"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 8h12l-1 12H7L6 8Z"/><path d="M9 9V6a3 3 0 0 1 6 0v3"/></svg><span class="rwb-cart-count rhp-cart-count"><?php echo esc_html((string) $count); ?></span></button>
        </div>
    </div>
</header>

<aside class="rhp-mobile-menu" id="rhp-mobile-menu" data-premium-menu role="dialog" aria-modal="true" aria-label="Navigation" hidden>
    <div class="rhp-mobile-head"><span>Menu</span><button class="rhp-icon-button" type="button" data-premium-menu-close aria-label="Close navigation"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 5 14 14M19 5 5 19"/></svg></button></div>
    <nav aria-label="Mobile navigation">
        <a href="<?php echo esc_url($shop_url); ?>">Shop <span>All products</span></a>
        <a href="#shop-by-concern">Shop by concern <span>Choose by routine goal</span></a>
        <a href="#routine-builder">Routine guide <span>Cleanse · Treat · Moisturize · Protect</span></a>
        <a href="<?php echo esc_url($learn_url); ?>">Learn <span>Skincare guidance</span></a>
        <a href="<?php echo esc_url($contact_url); ?>">Contact <span>Order and product support</span></a>
        <a href="<?php echo esc_url($account_url); ?>">My account <span>Orders and account details</span></a>
    </nav>
    <div class="rhp-mobile-support"><a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener">WhatsApp support</a><small>Response hours are not currently published.</small></div>
</aside>
<div class="rhp-menu-backdrop" data-premium-menu-backdrop hidden></div>

<div class="rwb-layer" data-search hidden><button class="rwb-backdrop" data-search-close aria-label="Close search"></button><div class="rwb-search-panel"><div class="rwb-panel-head"><b>Search Ruwah</b><button class="rwb-icon" data-search-close aria-label="Close search">×</button></div><form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>"><label class="screen-reader-text" for="rhp-search">Search products</label><input id="rhp-search" type="search" name="s" placeholder="Search products" required><input type="hidden" name="post_type" value="product"><button>Search</button></form></div></div>
<div class="rwb-layer" data-cart hidden><button class="rwb-backdrop" data-cart-close aria-label="Close cart"></button><aside class="rwb-cart-panel rwb-shop-cart-drawer"><?php if (function_exists('rwb_render_cart_drawer_content')) rwb_render_cart_drawer_content(); ?></aside></div>

<main id="main" class="rhp-main">
    <section class="rhp-hero">
        <div class="rhp-hero-media"><?php echo wp_kses_post($hero_image); ?></div>
        <div class="rhp-hero-overlay" aria-hidden="true"></div>
        <div class="rhp-hero-content">
            <p class="rhp-eyebrow">Focused skincare · Pakistan</p>
            <h1>Brighter-looking, hydrated skin — without the noise.</h1>
            <p class="rhp-hero-lead"><?php echo esc_html($hero_info['name']); ?> combines Vitamin C, Niacinamide and Hyaluronic Acid in a brightening and hydration-focused serum.</p>
            <div class="rhp-hero-actions">
                <a class="rhp-button rhp-button-primary" href="<?php echo esc_url($hero ? $hero->get_permalink() : $shop_url); ?>">Shop Triple Action Serum</a>
                <a class="rhp-button rhp-button-ghost" href="#routine-builder">Find your routine</a>
            </div>
            <p class="rhp-hero-trust">Cosmetic benefits are described in measured language; current price and stock come directly from WooCommerce.</p>
        </div>
    </section>

    <section class="rhp-trust-strip" aria-label="Shopping reassurance">
        <a href="<?php echo esc_url($checkout_url); ?>"><span class="rhp-trust-icon" aria-hidden="true">₨</span><strong>Cash on delivery</strong><small>Available for current checkout orders.</small></a>
        <a href="<?php echo esc_url($shipping_url); ?>"><span class="rhp-trust-icon" aria-hidden="true">↗</span><strong>Pakistan-wide delivery</strong><small>Availability and charges are confirmed for your address.</small></a>
        <a href="<?php echo esc_url($quality_url); ?>"><span class="rhp-trust-icon" aria-hidden="true">◎</span><strong>Measured skincare claims</strong><small>Cosmetic benefits without unsupported treatment promises.</small></a>
        <a href="<?php echo esc_url($contact_url); ?>"><span class="rhp-trust-icon" aria-hidden="true">↘</span><strong>Order support</strong><small>Use your order number when contacting support.</small></a>
    </section>

    <section class="rhp-section rhp-concerns" id="shop-by-concern">
        <div class="rhp-section-head"><div><p class="rhp-eyebrow">Shop by concern</p><h2>Start with what your routine needs.</h2></div><p>Four clear entry points into the current Ruwah range — without diagnostic or medical claims.</p></div>
        <div class="rhp-concern-grid">
            <?php foreach ($concerns as $index => $concern) : $p = $concern['product']; if (! $p) continue; $info = Ruwah_Fresh_Commerce_Design::display_copy($p); ?>
                <a class="rhp-concern-card" href="<?php echo esc_url($p->get_permalink()); ?>">
                    <span class="rhp-concern-number">0<?php echo esc_html((string) ($index + 1)); ?></span>
                    <div><h3><?php echo esc_html($concern['title']); ?></h3><p><?php echo esc_html($concern['copy']); ?></p><span class="rhp-text-link">Explore <?php echo esc_html($info['name']); ?> →</span></div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if ($best) : ?>
    <section class="rhp-section rhp-bestsellers" id="bestsellers">
        <div class="rhp-section-head"><div><p class="rhp-eyebrow">Product discovery</p><h2>Ruwah essentials.</h2></div><p>Current product data, price, stock and genuine review counts — no placeholder ratings.</p></div>
        <div class="rhp-product-grid">
            <?php foreach ($best as $rank => $product) :
                $info = Ruwah_Fresh_Commerce_Design::display_copy($product);
                $regular = (float) $product->get_regular_price(); $price = (float) $product->get_price(); $saving = ($regular > $price && $price > 0) ? $regular - $price : 0;
                $reviews = (int) $product->get_review_count(); $rating = (float) $product->get_average_rating();
                $can_cart = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
                $image_url = wp_get_attachment_image_url((int) $product->get_image_id(), 'woocommerce_single') ?: '';
                $benefit = ! empty($info['benefits'][0]) ? (string) $info['benefits'][0] : (string) $info['tagline'];
                $stock_text = $product->is_in_stock() ? 'In stock' : 'Out of stock';
            ?>
            <article class="rhp-product-card">
                <a class="rhp-product-image" href="<?php echo esc_url($product->get_permalink()); ?>" aria-label="View <?php echo esc_attr($info['name']); ?>">
                    <?php echo wp_kses_post($product->get_image('woocommerce_single', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
                    <?php if ($saving > 0) : ?><span class="rhp-product-badge">Save <?php echo wp_kses_post(wc_price($saving, ['decimals' => 0])); ?></span><?php elseif (0 === $rank) : ?><span class="rhp-product-badge">Popular pick</span><?php endif; ?>
                </a>
                <div class="rhp-product-copy">
                    <div class="rhp-product-meta"><span><?php echo esc_html($stock_text); ?></span><?php if (! empty($info['size'])) : ?><span><?php echo esc_html($info['size']); ?></span><?php endif; ?></div>
                    <h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($info['name']); ?></a></h3>
                    <p><?php echo esc_html($benefit); ?></p>
                    <?php if ($reviews > 0) : ?><div class="rhp-rating" aria-label="<?php echo esc_attr(number_format_i18n($rating, 1) . ' out of 5 from ' . $reviews . ' reviews'); ?>"><span aria-hidden="true">★ <?php echo esc_html(number_format_i18n($rating, 1)); ?></span><small><?php echo esc_html((string) $reviews); ?> reviews</small></div><?php endif; ?>
                    <div class="rhp-price"><?php if ($saving > 0) : ?><del><?php echo wp_kses_post(wc_price($regular, ['decimals' => 0])); ?></del><?php endif; ?><strong><?php echo wp_kses_post(wc_price($price, ['decimals' => 0])); ?></strong><?php if ($saving > 0) : ?><small>You save <?php echo wp_kses_post(wc_price($saving, ['decimals' => 0])); ?></small><?php endif; ?></div>
                    <div class="rhp-card-actions">
                        <?php if ($can_cart) : ?><a class="rhp-add add_to_cart_button ajax_add_to_cart" rel="nofollow" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($product->add_to_cart_url()); ?>">Add to cart</a><?php else : ?><a class="rhp-add" href="<?php echo esc_url($product->get_permalink()); ?>">View product</a><?php endif; ?>
                        <button class="rhp-quick" type="button" data-quick-view data-qv-name="<?php echo esc_attr($info['name']); ?>" data-qv-image="<?php echo esc_url($image_url); ?>" data-qv-copy="<?php echo esc_attr($benefit); ?>" data-qv-price="<?php echo esc_attr(wp_strip_all_tags(wc_price($price, ['decimals' => 0]))); ?>" data-qv-stock="<?php echo esc_attr($stock_text); ?>" data-qv-url="<?php echo esc_url($product->get_permalink()); ?>" data-qv-add="<?php echo esc_url($can_cart ? $product->add_to_cart_url() : $product->get_permalink()); ?>" data-qv-can-cart="<?php echo $can_cart ? '1' : '0'; ?>">Quick view</button>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="rhp-centered"><a class="rhp-text-link" href="<?php echo esc_url($shop_url); ?>">Shop all skincare →</a></div>
    </section>
    <?php endif; ?>

    <section class="rhp-section rhp-routine" id="routine-builder">
        <div class="rhp-section-head"><div><p class="rhp-eyebrow">Routine builder</p><h2>Four steps. Use only what fits.</h2></div><p>Existing products mapped to a simple routine position; product directions remain on the product page and packaging.</p></div>
        <div class="rhp-routine-grid">
            <?php foreach ($routine as $row) : $p = $row['product']; if (! $p) continue; $info = Ruwah_Fresh_Commerce_Design::display_copy($p); ?>
            <article><span class="rhp-routine-step">Step <?php echo esc_html($row['step']); ?></span><h3><?php echo esc_html($row['label']); ?></h3><a href="<?php echo esc_url($p->get_permalink()); ?>"><?php echo esc_html($info['name']); ?></a><p><?php echo esc_html((string) $info['tagline']); ?></p></article>
            <?php endforeach; ?>
        </div>
        <a class="rhp-button rhp-button-dark" href="<?php echo esc_url($shop_url); ?>">Build your routine</a>
    </section>

    <?php if ($hero) : ?>
    <section class="rhp-story">
        <div class="rhp-story-media"><?php echo wp_kses_post($hero->get_image('woocommerce_single', ['loading' => 'lazy', 'decoding' => 'async'])); ?></div>
        <div class="rhp-story-copy">
            <p class="rhp-eyebrow">Featured formula</p>
            <h2><?php echo esc_html($hero_info['name']); ?></h2>
            <p class="rhp-story-intro">For routines focused on brighter-looking, hydrated skin. Its verified product copy highlights Vitamin C, Niacinamide and Hyaluronic Acid.</p>
            <dl class="rhp-story-facts">
                <div><dt>Routine position</dt><dd>Treat</dd></div>
                <div><dt>How to use</dt><dd><?php echo esc_html($usage); ?></dd></div>
                <?php foreach ($verified_fields as $label => $value) : ?><div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($value); ?></dd></div><?php endforeach; ?>
                <div><dt>Delivery</dt><dd>No delivery-time range is currently published; availability and charges are confirmed for the checkout address.</dd></div>
                <div><dt>Returns</dt><dd>Eligibility and damaged/incorrect item guidance are available in the published returns policy.</dd></div>
            </dl>
            <a class="rhp-button rhp-button-dark" href="<?php echo esc_url($hero->get_permalink()); ?>">View product</a>
        </div>
    </section>
    <?php endif; ?>

    <section class="rhp-section rhp-why">
        <div class="rhp-section-head"><div><p class="rhp-eyebrow">Why Ruwah</p><h2>A focused range, not an endless shelf.</h2></div><p>Verifiable differences drawn from the current store — not manufacturing or clinical claims we cannot substantiate.</p></div>
        <div class="rhp-why-grid">
            <article><span>01</span><h3>Routine-focused collection</h3><p>The current range centers on cleansing, brightening, hydration and everyday sun care.</p></article>
            <article><span>02</span><h3>Live commerce details</h3><p>Price, sale state and stock are pulled from WooCommerce at the moment you browse.</p></article>
            <article><span>03</span><h3>Product-specific photography</h3><p>Existing product media is reused so shoppers can assess the actual item and packaging.</p></article>
            <article><span>04</span><h3>Pakistan-first checkout</h3><p>Prices are shown in PKR and current checkout supports cash on delivery.</p></article>
        </div>
    </section>

    <?php if ($review_count > 0) : ?>
    <section class="rhp-section rhp-reviews" aria-labelledby="rhp-reviews-title">
        <div class="rhp-section-head"><div><p class="rhp-eyebrow">Verified customer reviews</p><h2 id="rhp-reviews-title">What verified buyers said.</h2></div><p>Only reviews associated with verified WooCommerce owners are shown here.</p></div>
        <?php if ($review_count >= 5) : ?><div class="rhp-review-summary"><strong><?php echo esc_html(number_format_i18n($review_avg, 1)); ?> / 5</strong><span><?php echo esc_html((string) $review_count); ?> verified reviews</span><div class="rhp-rating-bars"><?php foreach ($review_dist as $stars => $n) : $pct = $review_count ? round(($n / $review_count) * 100) : 0; ?><div><span><?php echo esc_html((string) $stars); ?>★</span><i><b style="width:<?php echo esc_attr((string) $pct); ?>%"></b></i><small><?php echo esc_html((string) $n); ?></small></div><?php endforeach; ?></div></div><?php endif; ?>
        <div class="rhp-review-grid">
            <?php foreach (array_slice($verified_reviews, 0, 6) as [$review, $review_product, $rating]) : $info = Ruwah_Fresh_Commerce_Design::display_copy($review_product); ?>
            <article><div class="rhp-review-stars" aria-label="<?php echo esc_attr($rating . ' out of 5 stars'); ?>"><?php echo esc_html(str_repeat('★', $rating)); ?></div><blockquote>“<?php echo esc_html(wp_trim_words(wp_strip_all_tags($review->comment_content), 38, '…')); ?>”</blockquote><p><strong><?php echo esc_html($review->comment_author); ?></strong><span>Verified purchase · <?php echo esc_html($info['name']); ?></span><time datetime="<?php echo esc_attr(get_comment_date('c', $review)); ?>"><?php echo esc_html(get_comment_date(get_option('date_format'), $review)); ?></time></p></article>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>

    <section class="rhp-section rhp-ingredients" id="ingredient-guide">
        <div class="rhp-section-head"><div><p class="rhp-eyebrow">Ingredient education</p><h2>Know the role, not the hype.</h2></div><p>Simple cosmetic context for ingredients already named in current Ruwah product information.</p></div>
        <div class="rhp-ingredient-grid">
            <article><h3>Vitamin C</h3><p>Used in cosmetic formulas to support a brighter-looking, more radiant appearance.</p></article>
            <article><h3>Niacinamide</h3><p>Commonly used to support an even-looking tone and the skin barrier’s comfortable appearance.</p></article>
            <article><h3>Hyaluronic Acid</h3><p>A humectant used to help skin feel hydrated and look more comfortably plumped.</p></article>
            <article><h3>Rice Extract</h3><p>Used in Ruwah’s rice-focused products as part of their brightening and conditioning positioning.</p></article>
            <article><h3>Alpha Arbutin</h3><p>Included in the current rice cream product information for even-looking tone and radiance-focused care.</p></article>
        </div>
        <a class="rhp-text-link" href="<?php echo esc_url($learn_url); ?>">Formula guide →</a>
    </section>

    <section class="rhp-support">
        <div><p class="rhp-eyebrow">Delivery & support</p><h2>Know what happens after you order.</h2></div>
        <div class="rhp-support-grid">
            <article><h3>Delivery estimate</h3><p>A delivery-time range is not currently published. Delivery availability and charge are confirmed for your address.</p><a href="<?php echo esc_url($shipping_url); ?>">Shipping details →</a></article>
            <article><h3>Cash on delivery</h3><p>Place the order through checkout and use the current Cash on Delivery method.</p><a href="<?php echo esc_url($checkout_url); ?>">Go to checkout →</a></article>
            <article><h3>Returns</h3><p>Return/refund eligibility depends on the published policy and the condition of the order.</p><a href="<?php echo esc_url($refund_url); ?>">Read returns policy →</a></article>
            <article><h3>Tracking & WhatsApp</h3><p>Use your order number when requesting status help. Response hours are not currently published.</p><a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener">WhatsApp support →</a></article>
        </div>
    </section>
</main>

<dialog class="rhp-quick-view" data-quick-dialog aria-labelledby="rhp-qv-title">
    <button class="rhp-qv-close" type="button" data-quick-close aria-label="Close quick view">×</button>
    <div class="rhp-qv-media"><img data-qv-image alt=""></div>
    <div class="rhp-qv-copy"><span data-qv-stock></span><h2 id="rhp-qv-title" data-qv-name></h2><p data-qv-copy></p><strong data-qv-price></strong><div><a class="rhp-button rhp-button-dark" data-qv-add href="#">Add to cart</a><a class="rhp-text-link" data-qv-link href="#">View full product →</a></div></div>
</dialog>

<footer class="rhp-footer">
    <div class="rhp-footer-top">
        <div class="rhp-footer-brand"><div class="rhp-footer-wordmark">Ruwah Beauty</div><p>Focused skincare for brighter-looking, hydrated and comfortable everyday skin.</p><div class="rhp-footer-social"><a href="https://www.facebook.com/share/1BNAdjWpYW/" target="_blank" rel="noopener" aria-label="Ruwah Beauty on Facebook">Fb</a><a href="https://www.instagram.com/rawah.beauty" target="_blank" rel="noopener" aria-label="Ruwah Beauty on Instagram">Ig</a><a href="https://vt.tiktok.com/ZSX6WqwS2/" target="_blank" rel="noopener" aria-label="Ruwah Beauty on TikTok">Tk</a></div></div>
        <nav><h2>Shop</h2><a href="<?php echo esc_url($shop_url); ?>">Shop all</a><a href="#shop-by-concern">Shop by concern</a><a href="#routine-builder">Routine guide</a></nav>
        <nav><h2>Learn</h2><a href="<?php echo esc_url($learn_url); ?>">Formula guide</a><a href="<?php echo esc_url($quality_url); ?>">Quality & safety</a><a href="<?php echo esc_url($contact_url); ?>">Contact</a></nav>
        <nav><h2>Support</h2><a href="<?php echo esc_url($shipping_url); ?>">Shipping</a><a href="<?php echo esc_url($refund_url); ?>">Refunds</a><?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>">Privacy</a><?php endif; ?><a href="<?php echo esc_url($account_url); ?>">Account / track orders</a></nav>
        <div class="rhp-footer-support"><h2>Pakistan support</h2><?php if ($branded_email) : ?><a href="mailto:<?php echo esc_attr($branded_email); ?>"><?php echo esc_html($branded_email); ?></a><?php endif; ?><a href="<?php echo esc_url($whatsapp_url); ?>" target="_blank" rel="noopener">WhatsApp +92 371 3923279</a><p>Response hours are not currently published.</p><p>Pakistan · Online skincare</p></div>
    </div>
    <div class="rhp-footer-bottom"><span>© <?php echo esc_html(wp_date('Y')); ?> Ruwah Beauty</span><span>PKR pricing · Cash on Delivery</span></div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
