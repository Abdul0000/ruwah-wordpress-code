<?php
/**
 * Ruwah cart quantity guard.
 * Maximum five total product units across the entire WooCommerce cart.
 */
defined('ABSPATH') || exit;

if (! function_exists('rwb_cart_unit_limit')) {
    function rwb_cart_unit_limit(): int {
        return 5;
    }
}

if (! function_exists('rwb_cart_total_units')) {
    function rwb_cart_total_units(): int {
        if (! function_exists('WC') || ! WC()->cart) return 0;
        return (int) WC()->cart->get_cart_contents_count();
    }
}

if (! function_exists('rwb_cart_limit_message')) {
    function rwb_cart_limit_message(): string {
        return sprintf('You can purchase a maximum of %d items per order.', rwb_cart_unit_limit());
    }
}

add_filter('woocommerce_add_to_cart_validation', function ($passed, $product_id, $quantity, $variation_id = 0, $variations = []) {
    if (! $passed || ! function_exists('WC') || ! WC()->cart) return $passed;
    $requested = max(0, (int) $quantity);
    if (rwb_cart_total_units() + $requested > rwb_cart_unit_limit()) {
        wc_add_notice(rwb_cart_limit_message(), 'error');
        return false;
    }
    return true;
}, 999, 5);

add_filter('woocommerce_update_cart_validation', function ($passed, $cart_item_key, $values, $quantity) {
    if (! $passed || ! function_exists('WC') || ! WC()->cart) return $passed;
    $current_line_quantity = isset($values['quantity']) ? (int) $values['quantity'] : 0;
    $other_units = max(0, rwb_cart_total_units() - $current_line_quantity);
    $requested = max(0, (int) $quantity);
    if ($other_units + $requested > rwb_cart_unit_limit()) {
        wc_add_notice(rwb_cart_limit_message(), 'error');
        return false;
    }
    return true;
}, 999, 4);

add_filter('woocommerce_quantity_input_args', function ($args, $product) {
    $limit = rwb_cart_unit_limit();
    $remaining = $limit;

    if (function_exists('WC') && WC()->cart) {
        $remaining = max(0, $limit - rwb_cart_total_units());
        if (function_exists('is_cart') && is_cart() && $product instanceof WC_Product) {
            foreach (WC()->cart->get_cart() as $cart_item) {
                if ((int) ($cart_item['product_id'] ?? 0) === (int) $product->get_id()) {
                    $remaining += (int) ($cart_item['quantity'] ?? 0);
                    break;
                }
            }
        }
    }

    $product_max = isset($args['max_value']) ? (int) $args['max_value'] : -1;
    $allowed_max = max(1, min($limit, $remaining > 0 ? $remaining : 1));
    if ($product_max > 0) $allowed_max = min($allowed_max, $product_max);
    $args['max_value'] = $allowed_max;
    return $args;
}, 999, 2);

if (! function_exists('rwb_validate_cart_unit_limit')) {
    function rwb_validate_cart_unit_limit(): void {
        if (! function_exists('WC') || ! WC()->cart) return;
        if (rwb_cart_total_units() > rwb_cart_unit_limit()) {
            wc_add_notice(rwb_cart_limit_message(), 'error');
        }
    }
}
add_action('woocommerce_check_cart_items', 'rwb_validate_cart_unit_limit', 1);
