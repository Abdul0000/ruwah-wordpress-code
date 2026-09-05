<?php
/** Ruwah Beauty single-product layout. Preserves native WooCommerce hooks. */
defined('ABSPATH') || exit;

global $product;

/* Clear legacy gallery IDs before WooCommerce renders the product gallery. */
$ruwah_product_cleanup = get_template_directory() . '/includes/product-62-image-cleanup.php';
if (is_readable($ruwah_product_cleanup)) {
    require_once $ruwah_product_cleanup;
    if (function_exists('ruwah_enforce_new_only_product_galleries')) {
        ruwah_enforce_new_only_product_galleries();
    }
}

do_action('woocommerce_before_single_product');
if (post_password_required()) {
    echo get_the_password_form();
    return;
}
$terms = wc_get_product_category_list($product->get_id(), ', ');
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class('rb-single-product', $product); ?>><div class="rb-single-gallery"><?php do_action('woocommerce_before_single_product_summary'); ?></div><div class="summary entry-summary rb-single-summary"><?php if($terms): ?><span class="rb-product-category-kicker"><?php echo wp_kses_post($terms); ?></span><?php endif; ?><?php do_action('woocommerce_single_product_summary'); ?><div class="rb-product-benefits" aria-label="Shopping benefits"><span><i>✓</i>Free delivery over PKR 5,000</span><span><i>◇</i>Easy customer support</span><span><i>⌁</i>Secure payments</span></div></div><?php do_action('woocommerce_after_single_product_summary'); ?></div><?php do_action('woocommerce_after_single_product'); ?>