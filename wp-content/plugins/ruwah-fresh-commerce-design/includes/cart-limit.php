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

/**
 * Homepage product source: the home template asks rwb_products() for five items,
 * then limits its essentials grid to four. Ensure that five-item query is made
 * only from the approved products that have the latest PNG image set.
 */
add_filter('woocommerce_product_object_query_args', static function (array $args): array {
    if (! function_exists('is_front_page') || ! is_front_page()) return $args;

    $limit = (int) ($args['limit'] ?? 0);
    $orderby = (string) ($args['orderby'] ?? '');
    $status = $args['status'] ?? '';
    if (5 !== $limit || 'menu_order' !== $orderby || 'publish' !== $status) return $args;

    $args['include'] = [54, 60, 62, 64, 68];
    $args['limit'] = 5;
    return $args;
}, 1000);

/**
 * Footer-only catalogue cleanup. The global commerce footer queries five
 * published products at wp_footer priority 5. Exclude the legacy Toner (56)
 * and Rice Repair Mask (58) only while footer callbacks are rendering, then
 * immediately remove the filter so shop/search/product queries stay unchanged.
 */
if (! function_exists('rwb_footer_product_query_cleanup')) {
    function rwb_footer_product_query_cleanup(array $args): array {
        $limit = (int) ($args['limit'] ?? 0);
        $orderby = (string) ($args['orderby'] ?? '');
        $status = $args['status'] ?? '';
        if (5 !== $limit || 'menu_order' !== $orderby || 'publish' !== $status) return $args;
        $exclude = array_map('intval', (array) ($args['exclude'] ?? []));
        $args['exclude'] = array_values(array_unique(array_merge($exclude, [56, 58])));
        return $args;
    }
}
add_action('wp_footer', static function (): void {
    add_filter('woocommerce_product_object_query_args', 'rwb_footer_product_query_cleanup', 2000);
}, 4);
add_action('wp_footer', static function (): void {
    remove_filter('woocommerce_product_object_query_args', 'rwb_footer_product_query_cleanup', 2000);
}, 7);

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
 * Customer-facing copy normalization only. This changes text and link targets,
 * never card/image/product data or structural classes/layout.
 */
add_action('template_redirect', static function (): void {
    $is_home = function_exists('is_front_page') && is_front_page();
    $is_checkout_page = function_exists('is_checkout') && is_checkout()
        && ! (function_exists('is_order_received_page') && is_order_received_page());
    if (! $is_home && ! $is_checkout_page) return;

    ob_start(static function (string $html) use ($is_home, $is_checkout_page): string {
        if ($is_home) {
            $copy = [
                'Cosmetic benefits are described in measured language; current price and stock come directly from WooCommerce.' => 'Clear product benefits, current pricing and availability, with Cash on Delivery for checkout orders.',
                'Four clear entry points into the current Ruwah range — without diagnostic or medical claims.' => 'Four simple ways to explore the current Ruwah range by routine goal.',
                'Current product data, price, stock and genuine review counts — no placeholder ratings.' => 'Explore four Ruwah essentials with current prices, availability and customer review counts.',
                'Existing products mapped to a simple routine position; product directions remain on the product page and packaging.' => 'A simple cleanse, treat, moisturize and protect sequence using products from the current Ruwah range.',
                'Its verified product copy highlights Vitamin C, Niacinamide and Hyaluronic Acid.' => 'Its product information highlights Vitamin C, Niacinamide and Hyaluronic Acid.',
                'Verifiable differences drawn from the current store — not manufacturing or clinical claims we cannot substantiate.' => 'Straightforward skincare information, clear shopping details and a focused routine-first collection.',
                'Live commerce details' => 'Clear shopping details',
                'Price, sale state and stock are pulled from WooCommerce at the moment you browse.' => 'Current pricing, sale savings and availability are shown while you shop.',
                'Existing product media is reused so shoppers can assess the actual item and packaging.' => 'Product imagery helps you see the item and packaging before ordering.',
                'Only reviews associated with verified WooCommerce owners are shown here.' => 'Reviews shown here come from customers linked to completed store purchases.',
            ];
            $html = str_replace(array_keys($copy), array_values($copy), $html);
        }

        if ($is_checkout_page) {
            $old_payment_note = '<div class="rwb-online-coming-soon" role="note"><strong>Online Payment</strong><span>Coming Soon</span><small>For now, orders are confirmed with Cash on Delivery.</small></div>';
            $new_payment_note = '<div class="rwb-online-coming-soon" role="note"><strong>Cash on Delivery</strong><span>Available</span><small>Pay with cash when your order is delivered.</small></div>';
            $html = str_replace($old_payment_note, $new_payment_note, $html);

            $html = preg_replace_callback(
                '~<nav aria-label="Checkout policies">(.*?)</nav>~s',
                static function (array $matches): string {
                    $inner = $matches[1];
                    $links = '';
                    if (false === strpos($inner, '/shipping-delivery/')) {
                        $links .= '<a href="' . esc_url(home_url('/shipping-delivery/')) . '">Shipping</a>';
                    }
                    if (false === strpos($inner, '/terms-conditions/')) {
                        $links .= '<a href="' . esc_url(home_url('/terms-conditions/')) . '">Terms of service</a>';
                    }
                    if ('' === $links) return $matches[0];
                    return '<nav aria-label="Checkout policies">' . $links . $inner . '</nav>';
                },
                $html,
                1
            ) ?: $html;
        }

        return $html;
    });
}, 2);