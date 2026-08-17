<?php
defined('ABSPATH') || exit;

$products = function_exists('rwb_products') ? array_values(array_filter(rwb_products(), static fn($item) => $item instanceof WC_Product && $item->is_visible())) : [];
$priority = [
    'triple action serum' => 10,
    'rice whitening cream' => 20,
    'rice glow serum' => 30,
    'rice brightening face wash' => 40,
    'rice glow sun lotion' => 50,
];
usort($products, static function (WC_Product $a, WC_Product $b) use ($priority): int {
    $ak = strtolower(trim($a->get_name()));
    $bk = strtolower(trim($b->get_name()));
    return ($priority[$ak] ?? 999) <=> ($priority[$bk] ?? 999);
});
$products = array_slice($products, 0, 5);
$editorial_product = $products[0] ?? null;
$editorial_id = 0;
if ($editorial_product) {
    $editorial_ids = Ruwah_Fresh_Commerce_Design::gallery_ids($editorial_product);
    $editorial_id = (int) (end($editorial_ids) ?: $editorial_product->get_image_id());
}

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
            $info = Ruwah_Fresh_Commerce_Design::product_info($product);
            $reviews = (int) $product->get_review_count();
            $rating = (float) $product->get_average_rating();
            $badge = Ruwah_Fresh_Commerce_Design::product_badge($product, $index);
            $current = (float) $product->get_price();
            $name_key = strtolower($product->get_name());
            $anchor = '';
            if (str_contains($name_key, 'triple action')) $anchor = 'serums';
            elseif (str_contains($name_key, 'whitening cream')) $anchor = 'brightening';
            elseif (str_contains($name_key, 'face wash')) $anchor = 'cleanse';
            elseif (str_contains($name_key, 'sun lotion')) $anchor = 'sun-care';
            ?>
            <article class="rwb-dieux-product-card"<?php if ($anchor) : ?> id="<?php echo esc_attr($anchor); ?>"<?php endif; ?>>
                <a class="rwb-dieux-product-media" href="<?php echo esc_url($product->get_permalink()); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>">
                    <?php if ($badge) : ?><span class="rwb-dieux-badge"><?php echo esc_html($badge); ?></span><?php endif; ?>
                    <?php echo wp_kses_post($product->get_image('woocommerce_single', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
                </a>
                <div class="rwb-dieux-product-copy">
                    <h2><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a></h2>
                    <?php if (! empty($info['tagline'])) : ?><p class="rwb-dieux-product-tagline"><?php echo esc_html($info['tagline']); ?></p><?php endif; ?>
                    <?php if ($reviews > 0) : ?>
                        <div class="rwb-dieux-proof" aria-label="<?php echo esc_attr(number_format_i18n($rating, 1) . ' out of 5'); ?>"><span><?php echo esc_html(str_repeat('★', max(1, min(5, (int) round($rating))))); ?></span><small><?php echo esc_html((string) $reviews); ?></small></div>
                    <?php else : ?>
                        <div class="rwb-dieux-proof"><span>RUWAH FORMULA</span></div>
                    <?php endif; ?>
                    <?php if (! empty($info['size'])) : ?>
                        <label class="rwb-dieux-size"><span class="screen-reader-text">Pack size</span><select aria-label="Pack size for <?php echo esc_attr($product->get_name()); ?>"><option><?php echo esc_html(strtoupper($info['size'])); ?></option></select></label>
                    <?php endif; ?>
                    <div class="rwb-dieux-action">
                        <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                            <a rel="nofollow" class="add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($product->add_to_cart_url()); ?>"><span>Add to Cart</span><span><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></span></a>
                        <?php else : ?>
                            <a href="<?php echo esc_url($product->get_permalink()); ?>"><span>View Product</span><span><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></span></a>
                        <?php endif; ?>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>
<?php get_footer();
