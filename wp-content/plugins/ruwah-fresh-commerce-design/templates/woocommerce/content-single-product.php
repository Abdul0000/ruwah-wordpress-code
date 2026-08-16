<?php
defined('ABSPATH') || exit;
global $product;
if (! $product || ! is_a($product, 'WC_Product')) return;
do_action('woocommerce_before_single_product');
if (post_password_required()) { echo get_the_password_form(); return; }
$info = Ruwah_Fresh_Commerce_Design::product_info($product);
$gallery_ids = Ruwah_Fresh_Commerce_Design::gallery_ids($product);
$related = Ruwah_Fresh_Commerce_Design::related_products($product);
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('rwb-commerce-pdp', $product); ?>>
    <section class="rwb-commerce-pdp-top">
        <div class="rwb-commerce-pdp-gallery"><?php do_action('woocommerce_before_single_product_summary'); ?></div>
        <div class="rwb-commerce-pdp-summary">
            <p class="rwb-commerce-kicker">RUWAH BEAUTY</p>
            <h1 class="product_title entry-title"><?php echo esc_html($product->get_name()); ?></h1>
            <?php if (! empty($info['tagline'])) : ?><p class="rwb-commerce-pdp-tagline"><?php echo esc_html($info['tagline']); ?></p><?php endif; ?>
            <?php if ($product->get_review_count() > 0) woocommerce_template_single_rating(); ?>
            <div class="rwb-commerce-pdp-price"><?php echo wp_kses_post($product->get_price_html()); ?></div>
            <?php if (! empty($info['benefits'])) : ?><p class="rwb-commerce-pdp-intro">A focused Ruwah formula for <?php echo esc_html(strtolower(implode(', ', $info['benefits']))); ?>.</p><?php endif; ?>
            <div class="rwb-commerce-pdp-chips"><?php foreach (($info['benefits'] ?? []) as $benefit) : ?><span><?php echo esc_html($benefit); ?></span><?php endforeach; ?><?php if (! empty($info['size'])) : ?><span><?php echo esc_html($info['size']); ?></span><?php endif; ?></div>
            <?php woocommerce_template_single_add_to_cart(); ?>
            <div class="rwb-commerce-pdp-accordions">
                <details open><summary><span>Details</span><b>+</b></summary><div><ul><?php if (! empty($info['size'])) : ?><li><?php echo esc_html($info['size']); ?></li><?php endif; ?><?php if (! empty($info['benefits'])) : ?><li><?php echo esc_html(implode(' • ', $info['benefits'])); ?></li><?php endif; ?></ul></div></details>
                <?php if (! empty($info['benefits'])) : ?><details><summary><span>What it's good for</span><b>+</b></summary><div><ul><?php foreach ($info['benefits'] as $benefit) : ?><li><?php echo esc_html($benefit); ?></li><?php endforeach; ?></ul></div></details><?php endif; ?>
                <?php if (! empty($info['tagline'])) : ?><details><summary><span>Ingredients</span><b>+</b></summary><div><p><?php echo esc_html($info['tagline']); ?></p></div></details><?php endif; ?>
                <?php if (! empty($info['facts'])) : ?><details><summary><span>Product facts</span><b>+</b></summary><div><ul><?php foreach ($info['facts'] as $fact) : ?><li><?php echo esc_html($fact); ?></li><?php endforeach; ?></ul></div></details><?php endif; ?>
            </div>
        </div>
    </section>
    <?php if ($gallery_ids) : ?>
        <section class="rwb-commerce-pdp-views"><div class="rwb-shell"><div class="rwb-commerce-section-head"><p class="rwb-commerce-kicker">THE PRODUCT, UP CLOSE</p><h2>The ritual, in four views.</h2><p>Real mapped RWB media for the exact product you are viewing.</p></div><div class="rwb-commerce-pdp-views-grid"><?php foreach (array_slice($gallery_ids, 0, 4) as $image_id) : ?><figure><?php echo wp_get_attachment_image((int) $image_id, 'large', false, ['loading' => 'lazy', 'decoding' => 'async']); ?></figure><?php endforeach; ?></div></div></section>
    <?php endif; ?>
    <section class="rwb-commerce-standard"><div class="rwb-shell"><p class="rwb-commerce-kicker">THE RUWAH STANDARD</p><h2>Exact pack details. Clear purchase decisions.</h2><div class="rwb-commerce-standard-grid"><article><b>FORMULA</b><p>Key ingredients shown from the RWB pack.</p></article><article><b>BENEFITS</b><p>Product-specific benefits, not generic claims.</p></article><article><b>PRICE & STOCK</b><p>Live WooCommerce price and availability.</p></article><article><b>REAL MEDIA</b><p>Four optimized RWB product shots per mapped product.</p></article></div></div></section>
    <?php if ($related) : ?>
        <section class="rwb-commerce-pair"><div class="rwb-shell"><div class="rwb-commerce-section-head"><p class="rwb-commerce-kicker">PAIR WITH</p><h2>Complete the ritual.</h2></div><div class="rwb-commerce-pair-grid"><?php foreach ($related as $rank => $candidate) : ?><article class="rwb-commerce-card"><?php Ruwah_Fresh_Commerce_Design::render_card($candidate, $rank); ?></article><?php endforeach; ?></div></div></section>
    <?php endif; ?>
</div>
<?php do_action('woocommerce_after_single_product'); ?>
