<?php
defined('ABSPATH') || exit;
global $product;
if (! $product || ! is_a($product, 'WC_Product')) return;
do_action('woocommerce_before_single_product');
if (post_password_required()) { echo get_the_password_form(); return; }

$info = Ruwah_Fresh_Commerce_Design::display_copy($product);
$gallery_ids = Ruwah_Fresh_Commerce_Design::gallery_ids($product);
$related = Ruwah_Fresh_Commerce_Design::related_products($product);
$reviews = (int) $product->get_review_count();
$description = trim((string) ($info['tagline'] ?? ''));
$sku = trim((string) $product->get_sku());

$usage = '';
foreach (['pa_how-to-use', 'how-to-use', 'how_to_use', 'pa_usage', 'usage', 'directions'] as $attribute) {
    $value = trim(wp_strip_all_tags((string) $product->get_attribute($attribute)));
    if ('' !== $value) { $usage = $value; break; }
}
if ('' === $usage) {
    foreach (['_how_to_use', 'how_to_use', '_usage', 'usage', '_directions', 'directions'] as $meta_key) {
        $value = trim(wp_strip_all_tags((string) get_post_meta($product->get_id(), $meta_key, true)));
        if ('' !== $value) { $usage = $value; break; }
    }
}
if ('' === $usage) $usage = 'Follow the directions printed on the product packaging.';

$verified_fields = [];
$field_sources = [
    'Complete ingredients / INCI' => ['pa_ingredients', 'ingredients', 'inci'],
    'Skin type' => ['pa_skin-type', 'skin-type', 'skin_type'],
    'Country of origin' => ['pa_country-of-origin', 'country-of-origin', 'country_of_origin'],
    'Manufacturer / importer' => ['pa_manufacturer', 'manufacturer', 'importer'],
    'Net quantity' => ['pa_size', 'size', 'pa_volume', 'volume'],
];
foreach ($field_sources as $label => $keys) {
    foreach ($keys as $key) {
        $value = trim(wp_strip_all_tags((string) $product->get_attribute($key)));
        if ('' === $value) $value = trim(wp_strip_all_tags((string) get_post_meta($product->get_id(), $key, true)));
        if ('' !== $value) { $verified_fields[$label] = $value; break; }
    }
}
if (! empty($info['size']) && empty($verified_fields['Net quantity'])) $verified_fields['Net quantity'] = trim((string) $info['size']);
$refund_url = home_url('/refund-policy/');
$contact_url = home_url('/contact/');
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('rwb-commerce-pdp rwb-dieux-pdp', $product); ?>>
    <section class="rwb-commerce-pdp-top rwb-dieux-pdp-top">
        <div class="rwb-commerce-pdp-gallery rwb-dieux-pdp-gallery"><?php do_action('woocommerce_before_single_product_summary'); ?></div>
        <div class="rwb-commerce-pdp-summary rwb-dieux-pdp-summary">
            <h1 class="product_title entry-title"><?php echo esc_html((string) ($info['name'] ?? $product->get_name())); ?></h1>
            <div class="rwb-dieux-pdp-rating"><?php if ($reviews > 0) : woocommerce_template_single_rating(); else : ?><span class="rwb-dieux-pdp-proof">RUWAH FORMULA</span><?php endif; ?></div>
            <?php if (! empty($info['benefits'])) : ?><p class="rwb-dieux-pdp-benefits"><?php echo esc_html(implode(', ', array_map('strtoupper', $info['benefits']))); ?></p><?php endif; ?>
            <?php if ('' !== $description) : ?><p class="rwb-dieux-pdp-description"><?php echo esc_html($description); ?></p><?php endif; ?>

            <div class="rwb-dieux-pdp-buy" data-live-price="<?php echo esc_attr(wp_strip_all_tags($product->get_price_html())); ?>">
                <?php woocommerce_template_single_add_to_cart(); ?>
                <span class="rwb-dieux-pdp-buy-price" aria-hidden="true"><?php echo wp_kses_post($product->get_price_html()); ?></span>
            </div>

            <div class="rwb-dieux-pdp-trust" role="note" aria-label="Delivery and payment information">
                <div class="rwb-dieux-pdp-trust-item"><strong>Cash on Delivery</strong><span>Available for current orders.</span></div>
                <div class="rwb-dieux-pdp-trust-item"><strong>Pakistan-wide delivery</strong><span>Charge and availability are confirmed at checkout.</span></div>
                <div class="rwb-dieux-pdp-trust-item"><strong>Order support</strong><span>Use your order number on the <a href="<?php echo esc_url($contact_url); ?>">Contact page</a>.</span></div>
                <div class="rwb-dieux-pdp-trust-item rwb-dieux-pdp-trust-item--muted"><strong>Online payment</strong><span>Coming soon.</span></div>
            </div>

            <div class="rwb-commerce-pdp-accordions rwb-dieux-pdp-accordions">
                <details><summary><span>DETAILS:</span><b aria-hidden="true">+</b></summary><div><ul><?php if (! empty($info['benefits'])) : foreach ($info['benefits'] as $benefit) : ?><li><?php echo esc_html((string) $benefit); ?></li><?php endforeach; endif; ?><?php if ('' !== $sku) : ?><li>SKU: <?php echo esc_html($sku); ?></li><?php endif; ?><?php foreach ($verified_fields as $label => $value) : ?><li><strong><?php echo esc_html($label); ?>:</strong> <?php echo esc_html($value); ?></li><?php endforeach; ?><li><?php echo esc_html($product->is_in_stock() ? 'In stock' : 'Out of stock'); ?></li></ul></div></details>
                <details><summary><span>HOW TO USE:</span><b aria-hidden="true">+</b></summary><div><p><?php echo esc_html($usage); ?></p></div></details>
                <details><summary><span>SAFETY &amp; CARE:</span><b aria-hidden="true">+</b></summary><div><p>For external cosmetic use only. Patch test before first use when appropriate for your skin. Avoid direct eye contact and stop use if persistent irritation occurs. Keep product packaging so you can follow the complete ingredient list, warnings, batch and expiry information printed by the manufacturer.</p><?php if (68 === (int) $product->get_id()) : ?><p>For sun-care performance, follow the amount, application and reapplication directions printed on the pack. This website does not add an SPF, broad-spectrum or test claim unless that information is verified from the product source.</p><?php endif; ?></div></details>
                <details><summary><span>DELIVERY &amp; RETURNS:</span><b aria-hidden="true">+</b></summary><div><p>Current checkout is Cash on Delivery. Delivery availability and charges are shown for the address entered at checkout. For damaged, incorrect, return or refund requests, review the <a href="<?php echo esc_url($refund_url); ?>">Refund Policy</a> and contact us with your order number.</p></div></details>
            </div>
        </div>
    </section>

    <?php if ($gallery_ids) : ?><section class="rwb-commerce-pdp-views"><div class="rwb-shell"><div class="rwb-commerce-section-head"><p class="rwb-commerce-kicker">THE PRODUCT, UP CLOSE</p><h2>See the product from every angle.</h2><p>Product photography for the exact item you are viewing.</p></div><div class="rwb-commerce-pdp-views-grid"><?php foreach (array_slice($gallery_ids, 0, 4) as $image_id) : ?><figure><?php echo wp_kses_post(wp_get_attachment_image((int) $image_id, 'large', false, ['loading' => 'lazy', 'decoding' => 'async'])); ?></figure><?php endforeach; ?></div></div></section><?php endif; ?>
    <section class="rwb-commerce-standard"><div class="rwb-shell"><p class="rwb-commerce-kicker">THE RUWAH STANDARD</p><h2>Clear product information. Easier purchase decisions.</h2><div class="rwb-commerce-standard-grid"><article><b>PRODUCT DETAILS</b><p>Key product and pack information shown clearly.</p></article><article><b>BENEFITS</b><p>Product-specific cosmetic benefits without guaranteed treatment claims.</p></article><article><b>PRICE &amp; STOCK</b><p>Current price and availability shown before purchase.</p></article><article><b>AUTHENTIC MEDIA</b><p>Product-specific photography to help you evaluate the item.</p></article></div></div></section>
    <?php if ($related) : ?><section class="rwb-commerce-pair"><div class="rwb-shell"><div class="rwb-commerce-section-head"><p class="rwb-commerce-kicker">PAIR WITH</p><h2>Complete the ritual.</h2></div><div class="rwb-commerce-pair-grid"><?php foreach ($related as $rank => $candidate) : ?><article class="rwb-commerce-card"><?php Ruwah_Fresh_Commerce_Design::render_card($candidate, $rank); ?></article><?php endforeach; ?></div></div></section><?php endif; ?>
</div>
<?php do_action('woocommerce_after_single_product'); ?>