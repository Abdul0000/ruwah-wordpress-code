<?php
defined('ABSPATH') || exit;
global $product;
if (! $product || ! is_a($product, 'WC_Product') || ! $product->is_visible()) return;
$rank = function_exists('wc_get_loop_prop') ? (int) wc_get_loop_prop('loop', 0) : 0;
?>
<li <?php wc_product_class('rwb-commerce-card', $product); ?>><?php Ruwah_Fresh_Commerce_Design::render_card($product, $rank); ?></li>
