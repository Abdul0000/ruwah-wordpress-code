<?php
defined('ABSPATH') || exit;
global $product;
if (! $product || ! is_a($product, 'WC_Product') || ! $product->is_visible()) return;
$rank = function_exists('wc_get_loop_prop') ? (int) wc_get_loop_prop('loop', 0) : 0;
?>
<li <?php wc_product_class('rhp-loop-item', $product); ?>>
    <?php if (function_exists('rwb_render_master_product_card')) : ?>
        <?php rwb_render_master_product_card($product, $rank); ?>
    <?php elseif (class_exists('Ruwah_Fresh_Commerce_Design')) : ?>
        <?php Ruwah_Fresh_Commerce_Design::render_card($product, $rank); ?>
    <?php endif; ?>
</li>
