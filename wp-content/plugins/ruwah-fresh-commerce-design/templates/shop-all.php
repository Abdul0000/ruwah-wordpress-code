<?php
defined('ABSPATH') || exit;

$products = function_exists('rwb_products') ? array_values(array_filter(rwb_products(), static fn($item) => $item instanceof WC_Product && $item->is_visible())) : [];

/*
 * Packaging-first catalogue copy.
 * Keep the WooCommerce records untouched and only align the storefront card
 * name/tagline with the product that is actually shown in the mapped image.
 */
$pack_copy = [
    54 => [
        'name' => 'Triple Action Serum',
        'tagline' => 'A brightening and hydrating serum with Vitamin C, Niacinamide and Hyaluronic Acid.',
    ],
    62 => [
        'name' => 'Rice Whitening Cream',
        'tagline' => 'A brightening rice cream with Rice Extract, Glutathione, Vitamin C, Niacinamide and Alpha Arbutin.',
    ],
    64 => [
        'name' => 'Rice Glow Serum',
        'tagline' => 'A lightweight glow serum with Vitamin C, Niacinamide and Hyaluronic Acid for brighter-looking, hydrated skin.',
    ],
    60 => [
        'name' => 'Rice Brightening Face Wash',
        'tagline' => 'A gentle rice-based face wash for daily cleansing and a brighter, refreshed-looking complexion.',
    ],
    68 => [
        'name' => 'Rice Glow Sun Lotion',
        'tagline' => 'A lightweight daily sun lotion designed to protect while keeping skin comfortable and radiant-looking.',
    ],
];

$priority = [54 => 10, 62 => 20, 64 => 30, 60 => 40, 68 => 50];
usort($products, static function (WC_Product $a, WC_Product $b) use ($priority): int {
    return ($priority[(int) $a->get_id()] ?? 999) <=> ($priority[(int) $b->get_id()] ?? 999);
});
$products = array_slice($products, 0, 5);
$editorial_product = $products[0] ?? null;
$editorial_id = 0;
if ($editorial_product) {
    $editorial_ids = Ruwah_Fresh_Commerce_Design::gallery_ids($editorial_product);
    $editorial_id = (int) (end($editorial_ids) ?: $editorial_product->get_image_id());
}

$plugin_file = dirname(__DIR__) . '/ruwah-fresh-commerce-design.php';
wp_enqueue_style(
    'ruwah-reference-card-parity-shop',
    plugins_url('assets/card-parity.css', $plugin_file),
    [],
    '6.5.0'
);

get_header();
?>
<main id="main-content" class="rwb-dieux-shop" aria-labelledby="rwb-shop-all-title">
    <header class="rwb-dieux-shop-head">
        <h1 id="rwb-shop-all-title">SHOP ALL</h1>
        <nav class="rwb-dieux-shop-nav" aria-label="Shop categories">
            <a href="#skincare">Skincare</a>
            <a href="#serums">Serums</a>
            <a href="#brightening">Brightening</a>
            <a href="#cleanse">Cleanse</a>
            <a href="#sun-care">Sun Care</a>
        </nav>
    </header>

    <?php woocommerce_output_all_notices(); ?>

    <section id="skincare" class="rwb-dieux-catalog" aria-label="Ruwah skincare catalogue">
        <article class="rwb-dieux-category-tile">
            <?php if ($editorial_id) : ?>
                <?php echo wp_kses_post(wp_get_attachment_image($editorial_id, 'large', false, ['loading' => 'eager', 'decoding' => 'async'])); ?>
            <?php endif; ?>
            <span>Skincare</span>
        </article>

        <?php foreach ($products as $index => $product) :
            $product_id = (int) $product->get_id();
            $copy = $pack_copy[$product_id] ?? [
                'name' => $product->get_name(),
                'tagline' => wp_strip_all_tags($product->get_short_description()),
            ];
            $name_key = strtolower($copy['name']);
            $anchor = '';
            if (str_contains($name_key, 'triple action')) $anchor = 'serums';
            elseif (str_contains($name_key, 'whitening cream')) $anchor = 'brightening';
            elseif (str_contains($name_key, 'face wash')) $anchor = 'cleanse';
            elseif (str_contains($name_key, 'sun lotion')) $anchor = 'sun-care';

            $reviews = (int) $product->get_review_count();
            $rating = (float) $product->get_average_rating();
            $badge = Ruwah_Fresh_Commerce_Design::product_badge($product, (int) $index);
            $regular = (float) $product->get_regular_price();
            $current = (float) $product->get_price();
            ?>
            <article class="rwb-dieux-product-card rwb-commerce-card"<?php if ($anchor) : ?> id="<?php echo esc_attr($anchor); ?>"<?php endif; ?>>
                <a class="rwb-commerce-card-media" href="<?php echo esc_url($product->get_permalink()); ?>" aria-label="<?php echo esc_attr($copy['name']); ?>">
                    <?php if ($badge) : ?><span class="rwb-commerce-badge"><?php echo esc_html($badge); ?></span><?php endif; ?>
                    <?php echo wp_kses_post($product->get_image('woocommerce_single', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
                </a>
                <div class="rwb-commerce-card-copy">
                    <h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($copy['name']); ?></a></h3>
                    <?php if (! empty($copy['tagline'])) : ?><p class="rwb-commerce-card-tagline"><?php echo esc_html($copy['tagline']); ?></p><?php endif; ?>
                    <?php if ($reviews > 0) : ?><div class="rwb-commerce-card-rating" aria-label="<?php echo esc_attr(number_format_i18n($rating, 1) . ' out of 5'); ?>"><span><?php echo esc_html(str_repeat('★', max(1, min(5, (int) round($rating))))); ?></span><small><?php echo esc_html((string) $reviews); ?></small></div><?php else : ?><div class="rwb-commerce-card-proof">RUWAH FORMULA</div><?php endif; ?>
                    <?php if ($product->is_on_sale() && $regular > 0 && $current < $regular) : ?><div class="rwb-commerce-card-sale"><del><?php echo wp_kses_post(wc_price($regular, ['decimals' => 0])); ?></del><ins><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></ins></div><?php endif; ?>
                    <div class="rwb-commerce-card-action">
                        <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?><a rel="nofollow" class="rwb-commerce-add add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr((string) $product_id); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($product->add_to_cart_url()); ?>"><span>Add to Cart</span><span><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></span></a><?php else : ?><a class="rwb-commerce-add" href="<?php echo esc_url($product->get_permalink()); ?>"><span>View Product</span><span><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></span></a><?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?php get_footer();