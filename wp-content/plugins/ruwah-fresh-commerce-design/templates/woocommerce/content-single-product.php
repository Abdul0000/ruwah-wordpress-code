<?php
defined('ABSPATH') || exit;
global $product;
if (! $product || ! is_a($product, 'WC_Product')) return;
do_action('woocommerce_before_single_product');
if (post_password_required()) { echo get_the_password_form(); return; }

/*
 * Keep PDP copy aligned with the same packaging-first display source used by
 * the shop/home cards. WooCommerce remains authoritative for transactional
 * data (price, stock, media, reviews, SKU and add-to-cart).
 */
$info = Ruwah_Fresh_Commerce_Design::display_copy($product);
$gallery_ids = Ruwah_Fresh_Commerce_Design::gallery_ids($product);
$related = Ruwah_Fresh_Commerce_Design::related_products($product);
$reviews = (int) $product->get_review_count();
$description = trim((string) ($info['tagline'] ?? ''));
$sku = trim((string) $product->get_sku());

/* Never treat the marketing short description as usage instructions. */
$usage = '';
foreach (['pa_how-to-use', 'how-to-use', 'how_to_use', 'pa_usage', 'usage'] as $attribute) {
    $value = trim(wp_strip_all_tags((string) $product->get_attribute($attribute)));
    if ('' !== $value) {
        $usage = $value;
        break;
    }
}
if ('' === $usage) {
    foreach (['_how_to_use', 'how_to_use', '_usage', 'usage'] as $meta_key) {
        $value = trim(wp_strip_all_tags((string) get_post_meta($product->get_id(), $meta_key, true)));
        if ('' !== $value) {
            $usage = $value;
            break;
        }
    }
}
if ('' === $usage) {
    $usage = 'Follow the directions printed on the product packaging.';
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('rwb-commerce-pdp rwb-dieux-pdp', $product); ?>>
    <section class="rwb-commerce-pdp-top rwb-dieux-pdp-top">
        <div class="rwb-commerce-pdp-gallery rwb-dieux-pdp-gallery"><?php do_action('woocommerce_before_single_product_summary'); ?></div>
        <div class="rwb-commerce-pdp-summary rwb-dieux-pdp-summary">
            <h1 class="product_title entry-title"><?php echo esc_html((string) ($info['name'] ?? $product->get_name())); ?></h1>

            <div class="rwb-dieux-pdp-rating">
                <?php if ($reviews > 0) : ?>
                    <?php woocommerce_template_single_rating(); ?>
                <?php else : ?>
                    <span class="rwb-dieux-pdp-proof">RUWAH FORMULA</span>
                <?php endif; ?>
            </div>

            <?php if (! empty($info['benefits'])) : ?>
                <p class="rwb-dieux-pdp-benefits"><?php echo esc_html(implode(', ', array_map('strtoupper', $info['benefits']))); ?></p>
            <?php endif; ?>

            <?php if ('' !== $description) : ?><p class="rwb-dieux-pdp-description"><?php echo esc_html($description); ?></p><?php endif; ?>

            <div class="rwb-dieux-pdp-buy" data-live-price="<?php echo esc_attr(wp_strip_all_tags($product->get_price_html())); ?>">
                <?php woocommerce_template_single_add_to_cart(); ?>
                <span class="rwb-dieux-pdp-buy-price" aria-hidden="true"><?php echo wp_kses_post($product->get_price_html()); ?></span>
            </div>

            <div class="rwb-commerce-pdp-accordions rwb-dieux-pdp-accordions">
                <details><summary><span>DETAILS:</span><b>+</b></summary><div><ul><?php if (! empty($info['benefits'])) : ?><?php foreach ($info['benefits'] as $benefit) : ?><li><?php echo esc_html((string) $benefit); ?></li><?php endforeach; ?><?php endif; ?><?php if ('' !== $sku) : ?><li>SKU: <?php echo esc_html($sku); ?></li><?php endif; ?><?php if (! empty($info['size'])) : ?><li><?php echo esc_html($info['size']); ?></li><?php endif; ?><li><?php echo esc_html($product->is_in_stock() ? 'In stock' : 'Out of stock'); ?></li></ul></div></details>
                <details><summary><span>HOW TO USE:</span><b>+</b></summary><div><p><?php echo esc_html($usage); ?></p></div></details>
            </div>

            <?php if ($gallery_ids) : ?>
                <div class="rwb-dieux-pdp-action" aria-label="Product gallery quick views">
                    <p>SEE IT IN ACTION:</p>
                    <div class="rwb-dieux-pdp-action-strip">
                        <?php foreach (array_slice($gallery_ids, 0, 8) as $index => $image_id) : ?>
                            <button type="button" data-rwb-gallery-index="<?php echo esc_attr((string) $index); ?>" aria-label="View product image <?php echo esc_attr((string) ($index + 1)); ?>">
                                <?php echo wp_kses_post(wp_get_attachment_image((int) $image_id, 'woocommerce_thumbnail', false, ['loading' => 'lazy', 'decoding' => 'async'])); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($gallery_ids) : ?>
        <section class="rwb-commerce-pdp-views"><div class="rwb-shell"><div class="rwb-commerce-section-head"><p class="rwb-commerce-kicker">THE PRODUCT, UP CLOSE</p><h2>The ritual, in four views.</h2><p>Real mapped RWB media for the exact product you are viewing.</p></div><div class="rwb-commerce-pdp-views-grid"><?php foreach (array_slice($gallery_ids, 0, 4) as $image_id) : ?><figure><?php echo wp_kses_post(wp_get_attachment_image((int) $image_id, 'large', false, ['loading' => 'lazy', 'decoding' => 'async'])); ?></figure><?php endforeach; ?></div></div></section>
    <?php endif; ?>
    <section class="rwb-commerce-standard"><div class="rwb-shell"><p class="rwb-commerce-kicker">THE RUWAH STANDARD</p><h2>Exact pack details. Clear purchase decisions.</h2><div class="rwb-commerce-standard-grid"><article><b>FORMULA</b><p>Key ingredients shown from the RWB pack.</p></article><article><b>BENEFITS</b><p>Product-specific benefits, not generic claims.</p></article><article><b>PRICE & STOCK</b><p>Live WooCommerce price and availability.</p></article><article><b>REAL MEDIA</b><p>Four optimized RWB product shots per mapped product.</p></article></div></div></section>
    <?php if ($related) : ?>
        <section class="rwb-commerce-pair"><div class="rwb-shell"><div class="rwb-commerce-section-head"><p class="rwb-commerce-kicker">PAIR WITH</p><h2>Complete the ritual.</h2></div><div class="rwb-commerce-pair-grid"><?php foreach ($related as $rank => $candidate) : ?><article class="rwb-commerce-card"><?php Ruwah_Fresh_Commerce_Design::render_card($candidate, $rank); ?></article><?php endforeach; ?></div></div></section>
    <?php endif; ?>
</div>
<?php do_action('woocommerce_after_single_product'); ?>