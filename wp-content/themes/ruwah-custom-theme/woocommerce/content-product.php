<?php
defined('ABSPATH') || exit;

global $product;

if (!$product || !is_a($product, 'WC_Product') || !$product->is_visible()) {
    return;
}

$info     = function_exists('rwb_info') ? rwb_info($product) : null;
$rank     = function_exists('wc_get_loop_prop') ? (int) wc_get_loop_prop('loop', 0) : 0;
$reviews  = (int) $product->get_review_count();
$rating   = (float) $product->get_average_rating();
$is_sale  = $product->is_on_sale();
$badge    = !$product->is_in_stock() ? 'Out of stock' : ($is_sale ? 'Sale' : ($rank === 0 ? 'Bestseller' : ''));
$regular  = (float) $product->get_regular_price();
$current  = (float) $product->get_price();
?>
<li <?php wc_product_class('rwb-card rwb-shop-card', $product); ?>>
    <a class="rwb-card-media" href="<?php echo esc_url($product->get_permalink()); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>">
        <?php if ($badge) : ?>
            <span class="rwb-badge"><?php echo esc_html($badge); ?></span>
        <?php endif; ?>
        <span class="rwb-cloud c1" aria-hidden="true"></span>
        <span class="rwb-cloud c2" aria-hidden="true"></span>
        <?php echo wp_kses_post($product->get_image('full', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
    </a>

    <div class="rwb-card-copy">
        <div class="rwb-card-head">
            <h2 class="rwb-shop-title"><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a></h2>
            <div class="rwb-price rwb-shop-price" aria-label="Price">
                <?php if ($is_sale && $regular > 0 && $current < $regular) : ?>
                    <del><?php echo wp_kses_post(wc_price($regular, ['decimals' => 0])); ?></del>
                    <ins><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></ins>
                <?php else : ?>
                    <?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?>
                <?php endif; ?>
            </div>
        </div>

        <?php if ($info) : ?>
            <p class="rwb-shop-tagline"><?php echo esc_html($info['tagline']); ?></p>
            <div class="rwb-benefits"><?php echo esc_html(implode(' • ', $info['benefits'])); ?></div>
        <?php endif; ?>

        <?php if ($reviews) : ?>
            <div class="rwb-rating" aria-label="<?php echo esc_attr(number_format_i18n($rating, 1) . ' out of 5'); ?>">
                <span><?php echo esc_html(str_repeat('★', max(1, min(5, (int) round($rating))))); ?></span>
                <small><?php echo esc_html(number_format_i18n($rating, 1) . ' · ' . $reviews); ?></small>
            </div>
        <?php endif; ?>

        <div class="rwb-card-action">
            <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                <a rel="nofollow" class="rwb-add add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1" aria-label="<?php echo esc_attr(sprintf('Add %s to bag', $product->get_name())); ?>" href="<?php echo esc_url($product->add_to_cart_url()); ?>"><span>Add to bag</span><b aria-hidden="true">+</b></a>
            <?php else : ?>
                <a class="rwb-add" href="<?php echo esc_url($product->get_permalink()); ?>"><span>View product</span><b aria-hidden="true">↗</b></a>
            <?php endif; ?>
        </div>
    </div>
</li>