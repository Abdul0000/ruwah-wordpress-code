<?php
defined('ABSPATH') || exit;
global $product;
if (! $product || ! is_a($product, 'WC_Product') || ! $product->is_visible()) return;
$rank = function_exists('wc_get_loop_prop') ? (int) wc_get_loop_prop('loop', 0) : 0;
$copy = Ruwah_Fresh_Commerce_Design::display_copy($product);
ob_start();
Ruwah_Fresh_Commerce_Design::render_card($product, $rank);
$card_html = (string) ob_get_clean();
$action_label = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()
    ? sprintf('Add %s to cart', (string) ($copy['name'] ?? $product->get_name()))
    : sprintf('View %s', (string) ($copy['name'] ?? $product->get_name()));
$card_html = preg_replace('/(<a\b[^>]*class="[^"]*rwb-commerce-add[^"]*")/i', '$1 aria-label="' . esc_attr($action_label) . '"', $card_html, 1) ?: $card_html;
?>
<li <?php wc_product_class('rwb-commerce-card', $product); ?>><?php echo wp_kses_post($card_html); ?></li>
