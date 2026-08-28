<?php
defined('ABSPATH') || exit;

$products = function_exists('rwb_audit_all_visible_products')
    ? rwb_audit_all_visible_products()
    : (function_exists('wc_get_products') ? wc_get_products(['status' => 'publish', 'limit' => -1, 'orderby' => 'menu_order', 'order' => 'ASC']) : []);
$products = array_values(array_filter((array) $products, static fn($item) => $item instanceof WC_Product && $item->is_visible()));

get_header();
?>
<main id="main-content" class="rwb-dieux-shop rhp-shop" aria-labelledby="rwb-shop-all-title">
    <section class="rhp-section">
        <header class="rhp-section-head">
            <div><p class="rhp-eyebrow">Shop Ruwah</p><h1 id="rwb-shop-all-title">Skincare for everyday routines.</h1></div>
            <p>Browse the full published Ruwah range with current price, availability, savings and product-specific information.</p>
        </header>
        <?php woocommerce_output_all_notices(); ?>
        <?php if ($products) : ?>
            <div class="rhp-product-grid">
                <?php foreach ($products as $rank => $product) : ?>
                    <?php if (function_exists('rwb_render_master_product_card')) rwb_render_master_product_card($product, (int) $rank); ?>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <p>No products are currently available.</p>
        <?php endif; ?>
    </section>
</main>
<?php get_footer();
