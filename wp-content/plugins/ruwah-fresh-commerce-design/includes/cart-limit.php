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

/**
 * Keep checkout markup valid.
 * The reference checkout used to inject a second <form> for coupons inside
 * WooCommerce's main checkout <form>, which can prevent Place order from
 * submitting in browsers. Restore WooCommerce's native coupon placement.
 */
add_action('wp', static function (): void {
    if (! function_exists('is_checkout') || ! is_checkout()) return;
    if (function_exists('is_order_received_page') && is_order_received_page()) return;

    if (function_exists('rwb_reference_checkout_coupon')) {
        remove_action('woocommerce_checkout_order_review', 'rwb_reference_checkout_coupon', 15);
    }

    if (function_exists('wc_coupons_enabled') && wc_coupons_enabled()) {
        add_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
    }
}, 20);

/**
 * Checkout reliability: WooCommerce native validation remains authoritative.
 * Remove only Ruwah's extra strict validation errors so valid customer details
 * are not silently rejected by custom postcode/phone/address rules.
 */
add_action('woocommerce_after_checkout_validation', static function ($data, $errors): void {
    if (! $errors instanceof WP_Error) return;
    foreach ($errors->get_error_codes() as $code) {
        if (str_starts_with((string) $code, 'rwb_')) {
            $errors->remove($code);
        }
    }
}, 10050, 2);

/** Cash on Delivery is the only live method; keep it selected in the session. */
add_filter('woocommerce_available_payment_gateways', static function ($gateways) {
    if (isset($gateways['cod']) && function_exists('WC') && WC()->session) {
        WC()->session->set('chosen_payment_method', 'cod');
        return ['cod' => $gateways['cod']];
    }
    return $gateways;
}, 10050);

/** Keep COD visibly selected after WooCommerce AJAX checkout refreshes. */
add_action('wp_footer', static function (): void {
    if (! function_exists('is_checkout') || ! is_checkout()) return;
    if (function_exists('is_order_received_page') && is_order_received_page()) return;
    ?>
    <script id="rwb-cod-autoselect">
    (()=>{'use strict';
      const ensure=()=>{
        const radio=document.querySelector('input[name="payment_method"][value="cod"]');
        if(!radio)return;
        if(!radio.checked){
          radio.checked=true;
          radio.dispatchEvent(new Event('change',{bubbles:true}));
        }
      };
      if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',ensure,{once:true});else ensure();
      if(window.jQuery)jQuery(document.body).on('updated_checkout',ensure);
    })();
    </script>
    <?php
}, 99);

/** Theme-matched green confirmation mark inside the COD description box. */
add_action('wp_enqueue_scripts', static function (): void {
    if (! function_exists('is_checkout') || ! is_checkout()) return;
    if (function_exists('is_order_received_page') && is_order_received_page()) return;
    $css = 'body.rwb-reference-checkout-v1 #payment li.payment_method_cod:after{content:none!important;display:none!important}body.rwb-reference-checkout-v1 #payment li.payment_method_cod .payment_box{position:relative!important;padding-right:72px!important}body.rwb-reference-checkout-v1 #payment li.payment_method_cod .payment_box:after{content:"✓";position:absolute;right:22px;top:50%;transform:translateY(-50%);width:30px;height:30px;display:flex;align-items:center;justify-content:center;border-radius:50%;background:#18a957;color:#fff;font-family:Arial,sans-serif;font-size:18px;font-weight:900;line-height:1;box-shadow:0 5px 14px rgba(24,169,87,.20);pointer-events:none}';
    wp_add_inline_style('rwb-theme', $css);
}, 10020);

/**
 * Homepage-only visual override for Rice Repair Mask.
 * Use the product's first alternate gallery shot on the card without mutating
 * the product featured image or gallery data in WooCommerce.
 */
add_filter('woocommerce_product_get_image', static function ($image, $product, $size, $attr, $placeholder, $original_image) {
    if (! function_exists('is_front_page') || ! is_front_page()) return $image;
    if (! $product instanceof WC_Product || 58 !== (int) $product->get_id()) return $image;
    $gallery_ids = array_values(array_filter(array_map('intval', (array) $product->get_gallery_image_ids())));
    if (! $gallery_ids) return $image;
    $alternate = wp_get_attachment_image($gallery_ids[0], $size, false, is_array($attr) ? $attr : []);
    return $alternate ?: $image;
}, 10060, 6);