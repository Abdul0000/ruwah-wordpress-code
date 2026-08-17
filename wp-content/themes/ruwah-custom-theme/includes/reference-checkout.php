<?php
defined('ABSPATH') || exit;

/**
 * Reference-led checkout presentation and global cart drawer density polish.
 * WooCommerce remains the source of truth for cart, checkout, shipping,
 * coupons, payment gateways, validation and order creation.
 */

function rwb_reference_checkout_active(): bool {
    if (! function_exists('is_checkout') || ! is_checkout()) {
        return false;
    }
    if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received')) {
        return false;
    }
    return true;
}

add_filter('body_class', function (array $classes): array {
    if (rwb_reference_checkout_active()) {
        $classes[] = 'rwb-reference-checkout-v1';
    }
    return $classes;
});

add_filter('woocommerce_checkout_fields', function (array $fields): array {
    if (! rwb_reference_checkout_active()) {
        return $fields;
    }

    $billing_priorities = [
        'billing_email' => 10,
        'billing_country' => 20,
        'billing_first_name' => 30,
        'billing_last_name' => 40,
        'billing_company' => 45,
        'billing_address_1' => 50,
        'billing_address_2' => 60,
        'billing_city' => 70,
        'billing_postcode' => 80,
        'billing_state' => 90,
        'billing_phone' => 100,
    ];
    $shipping_priorities = [
        'shipping_country' => 20,
        'shipping_first_name' => 30,
        'shipping_last_name' => 40,
        'shipping_company' => 45,
        'shipping_address_1' => 50,
        'shipping_address_2' => 60,
        'shipping_city' => 70,
        'shipping_postcode' => 80,
        'shipping_state' => 90,
    ];

    foreach ($billing_priorities as $key => $priority) {
        if (isset($fields['billing'][$key])) {
            $fields['billing'][$key]['priority'] = $priority;
        }
    }
    foreach ($shipping_priorities as $key => $priority) {
        if (isset($fields['shipping'][$key])) {
            $fields['shipping'][$key]['priority'] = $priority;
        }
    }

    $placeholders = [
        'billing_email' => 'Email',
        'billing_first_name' => 'First name',
        'billing_last_name' => 'Last name',
        'billing_company' => 'Company (optional)',
        'billing_address_1' => 'Address',
        'billing_address_2' => 'Apartment, suite, etc. (optional)',
        'billing_city' => 'City',
        'billing_postcode' => 'Postal code',
        'billing_phone' => 'Phone',
        'shipping_first_name' => 'First name',
        'shipping_last_name' => 'Last name',
        'shipping_company' => 'Company (optional)',
        'shipping_address_1' => 'Address',
        'shipping_address_2' => 'Apartment, suite, etc. (optional)',
        'shipping_city' => 'City',
        'shipping_postcode' => 'Postal code',
    ];
    foreach ($placeholders as $key => $placeholder) {
        foreach (['billing', 'shipping'] as $section) {
            if (isset($fields[$section][$key])) {
                $fields[$section][$key]['placeholder'] = $placeholder;
            }
        }
    }

    foreach (['billing_city', 'shipping_city'] as $key) {
        $section = str_starts_with($key, 'billing_') ? 'billing' : 'shipping';
        if (isset($fields[$section][$key])) {
            $fields[$section][$key]['class'] = ['form-row-first'];
        }
    }
    foreach (['billing_postcode', 'shipping_postcode'] as $key) {
        $section = str_starts_with($key, 'billing_') ? 'billing' : 'shipping';
        if (isset($fields[$section][$key])) {
            $fields[$section][$key]['class'] = ['form-row-last'];
        }
    }

    return $fields;
}, 30);

add_action('wp', function (): void {
    if (! rwb_reference_checkout_active() || ! function_exists('wc_coupons_enabled') || ! wc_coupons_enabled()) {
        return;
    }
    remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
    add_action('woocommerce_checkout_order_review', 'rwb_reference_checkout_coupon', 15);
});

function rwb_reference_checkout_coupon(): void {
    if (! rwb_reference_checkout_active() || ! function_exists('wc_coupons_enabled') || ! wc_coupons_enabled()) {
        return;
    }
    ?>
    <form class="checkout_coupon woocommerce-form-coupon rwb-checkout-coupon" method="post">
        <p class="form-row form-row-first">
            <label class="screen-reader-text" for="rwb-checkout-coupon-code"><?php esc_html_e('Coupon code', 'woocommerce'); ?></label>
            <input type="text" name="coupon_code" class="input-text" placeholder="Discount code or gift card" id="rwb-checkout-coupon-code" value="">
        </p>
        <p class="form-row form-row-last">
            <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>"><?php esc_html_e('Apply', 'woocommerce'); ?></button>
        </p>
        <div class="clear"></div>
    </form>
    <?php
}

add_filter('woocommerce_cart_item_name', function (string $name, array $cart_item, string $cart_item_key): string {
    if (! rwb_reference_checkout_active()) {
        return $name;
    }
    $product = isset($cart_item['data']) && $cart_item['data'] instanceof WC_Product ? $cart_item['data'] : null;
    if (! $product) {
        return $name;
    }
    $image = $product->get_image('woocommerce_thumbnail', ['loading' => 'eager', 'decoding' => 'async']);
    return '<span class="rwb-checkout-product-thumb">' . wp_kses_post($image) . '</span><span class="rwb-checkout-product-name">' . $name . '</span>';
}, 20, 3);

add_filter('woocommerce_checkout_cart_item_quantity', function (string $html, array $cart_item, string $cart_item_key): string {
    if (! rwb_reference_checkout_active()) {
        return $html;
    }
    $quantity = max(1, (int) ($cart_item['quantity'] ?? 1));
    return '<strong class="rwb-checkout-product-quantity">' . esc_html((string) $quantity) . '</strong>';
}, 20, 3);

add_action('wp_enqueue_scripts', function (): void {
    $css = <<<'CSS'
/* Compact cart drawer: presentation only, no cart behavior changes. */
.rwb-shop-cart-drawer{width:620px!important}
.rwb-cart-drawer-head{min-height:72px;padding:0 26px}.rwb-cart-drawer-head h2{font-size:32px}.rwb-cart-drawer-close{gap:10px;font-size:13px}.rwb-cart-drawer-close span{font-size:26px}.rwb-cart-drawer-count{font-size:13px}
.rwb-cart-drawer-item{min-height:205px;grid-template-columns:154px minmax(0,1fr) auto;gap:22px;padding:18px 20px}.rwb-cart-drawer-item-media{width:154px;height:168px}.rwb-cart-drawer-item-copy h3{font-size:20px}.rwb-cart-drawer-item-price{margin-top:8px;font-size:13px}.rwb-cart-drawer-qty{grid-template-columns:40px 50px 40px;margin-top:22px}.rwb-cart-drawer-qty button,.rwb-cart-drawer-qty span{height:34px;font-size:16px}.rwb-cart-drawer-remove{font-size:11px}
.rwb-cart-drawer-paired{padding:24px 20px 0}.rwb-cart-drawer-paired>h3{margin-bottom:20px;font-size:13px}.rwb-cart-drawer-paired-row{grid-template-columns:110px minmax(0,1fr) auto;gap:16px}.rwb-cart-drawer-paired-media{width:110px;height:104px}.rwb-cart-drawer-paired-copy h4{font-size:18px}.rwb-cart-drawer-paired-copy p{margin-top:5px;font-size:10px}.rwb-cart-drawer-paired-add{min-width:134px;height:40px;padding:0 12px!important;font-size:12px!important}
.rwb-cart-drawer-coupon{padding:18px 22px 16px}.rwb-cart-drawer-coupon form{grid-template-columns:minmax(0,1fr) 94px;gap:10px}.rwb-cart-drawer-coupon input,.rwb-cart-drawer-coupon button{height:52px!important}.rwb-cart-drawer-coupon input{padding:0 15px!important;font-size:12px!important}.rwb-cart-drawer-coupon button{font-size:12px}.rwb-cart-drawer-summary{padding:20px 26px 28px}.rwb-cart-drawer-subtotal>span,.rwb-cart-drawer-subtotal>strong{font-size:19px}.rwb-cart-drawer-summary>p{margin:13px 0 14px;font-size:10px}.rwb-cart-drawer-checkout{min-height:54px;font-size:15px}
@media(max-width:680px){.rwb-shop-cart-drawer{width:100%!important}.rwb-cart-drawer-head{min-height:66px;padding:0 16px}.rwb-cart-drawer-item{grid-template-columns:104px minmax(0,1fr);gap:14px;padding:15px}.rwb-cart-drawer-item-media{width:104px;height:124px}.rwb-cart-drawer-item-copy h3{font-size:17px}.rwb-cart-drawer-paired{padding:20px 15px 0}.rwb-cart-drawer-coupon{padding:15px}.rwb-cart-drawer-summary{padding:20px 16px 24px}}
CSS;

    if (rwb_reference_checkout_active()) {
        $css .= <<<'CSS'
/* Dieux-reference checkout shell. Native WooCommerce mechanics remain intact. */
body.rwb-reference-checkout-v1{background:#fff;color:#111;font-family:Inter,Arial,sans-serif}
body.rwb-reference-checkout-v1>.rwb-announcement,body.rwb-reference-checkout-v1>.rwb-header,body.rwb-reference-checkout-v1>.rwb-mobile-menu,body.rwb-reference-checkout-v1>.rwb-layer{display:none!important}
.rwb-ref-checkout{min-height:100vh;background:linear-gradient(90deg,#fff 0,#fff 54%,#f7f7f7 54%,#f7f7f7 100%)}
.rwb-ref-checkout-frame{width:100%;max-width:1240px;margin:0 auto}.rwb-ref-checkout-mast{display:grid;grid-template-columns:54% 46%}.rwb-ref-checkout-mast-left{padding:38px 44px 16px}.rwb-ref-checkout-logo{display:flex;justify-content:center;min-height:70px;align-items:center}.rwb-ref-checkout-logo .custom-logo{width:auto;max-width:150px;max-height:72px;object-fit:contain}.rwb-ref-checkout-logo>a:not(.custom-logo-link){font-family:var(--serif,'DM Serif Display',Georgia,serif);font-size:34px}.rwb-ref-checkout-steps{display:flex;align-items:center;justify-content:center;gap:11px;margin-top:8px;color:#777;font-size:11px}.rwb-ref-checkout-steps a,.rwb-ref-checkout-steps b{color:#111;font-weight:500}.rwb-ref-checkout-steps span{color:#a0a0a0;font-size:16px}.rwb-ref-checkout-body{width:100%;max-width:1240px;margin:0 auto}.rwb-ref-checkout-body>article{margin:0!important}.rwb-ref-checkout-body .woocommerce{width:100%}.rwb-ref-checkout-body .woocommerce-notices-wrapper{max-width:54%;padding:0 44px}.rwb-ref-checkout-body .woocommerce-error,.rwb-ref-checkout-body .woocommerce-message,.rwb-ref-checkout-body .woocommerce-info{margin:0 0 18px!important;border:1px solid #e3b8b4!important;border-top:1px solid #e3b8b4!important;border-radius:0!important;background:#fff5f4!important;color:#8c332d!important;font-size:12px!important}.rwb-ref-checkout-body .woocommerce-info:before,.rwb-ref-checkout-body .woocommerce-error:before,.rwb-ref-checkout-body .woocommerce-message:before{display:none!important}
.rwb-ref-checkout-body form.checkout{display:grid!important;grid-template-columns:54% 46%;align-items:start;margin:0!important}.rwb-ref-checkout-body #customer_details{grid-column:1;width:auto!important;margin:0!important;padding:12px 44px 30px;border:0!important;background:transparent!important}.rwb-ref-checkout-body #customer_details:before{content:'CONTACT';display:block;margin:12px 0 11px;font-family:var(--serif,'DM Serif Display',Georgia,serif);font-size:20px;line-height:1}.rwb-ref-checkout-body #customer_details h3{margin:0!important;font-size:0!important}.rwb-ref-checkout-body #customer_details .col-1,.rwb-ref-checkout-body #customer_details .col-2{float:none!important;width:100%!important;margin:0!important}.rwb-ref-checkout-body .woocommerce-billing-fields__field-wrapper,.rwb-ref-checkout-body .woocommerce-shipping-fields__field-wrapper{display:grid;grid-template-columns:1fr 1fr;gap:9px}.rwb-ref-checkout-body .form-row{float:none!important;width:auto!important;margin:0!important;padding:0!important;position:relative}.rwb-ref-checkout-body #billing_email_field{grid-column:1/-1;margin-bottom:45px!important}.rwb-ref-checkout-body #billing_email_field:after{content:'SHIPPING ADDRESS';position:absolute;left:0;bottom:-34px;font-family:var(--serif,'DM Serif Display',Georgia,serif);font-size:20px;line-height:1;color:#111}.rwb-ref-checkout-body #billing_country_field,.rwb-ref-checkout-body #billing_company_field,.rwb-ref-checkout-body #billing_address_1_field,.rwb-ref-checkout-body #billing_address_2_field,.rwb-ref-checkout-body #billing_state_field,.rwb-ref-checkout-body #billing_phone_field,.rwb-ref-checkout-body #shipping_country_field,.rwb-ref-checkout-body #shipping_company_field,.rwb-ref-checkout-body #shipping_address_1_field,.rwb-ref-checkout-body #shipping_address_2_field,.rwb-ref-checkout-body #shipping_state_field{grid-column:1/-1}.rwb-ref-checkout-body .form-row label{position:absolute!important;width:1px!important;height:1px!important;padding:0!important;margin:-1px!important;overflow:hidden!important;clip:rect(0 0 0 0)!important;white-space:nowrap!important;border:0!important}.rwb-ref-checkout-body #billing_country_field label,.rwb-ref-checkout-body #shipping_country_field label,.rwb-ref-checkout-body #billing_state_field label,.rwb-ref-checkout-body #shipping_state_field label{position:absolute!important;z-index:4;left:11px;top:5px;width:auto!important;height:auto!important;margin:0!important;clip:auto!important;overflow:visible!important;color:#777;font-size:8px;font-weight:400;line-height:1;pointer-events:none}.rwb-ref-checkout-body input.input-text,.rwb-ref-checkout-body textarea,.rwb-ref-checkout-body select{width:100%!important;min-height:48px!important;margin:0!important;padding:0 11px!important;border:1px solid #d8d8d8!important;border-radius:0!important;background:#fff!important;color:#111!important;box-shadow:none!important;font-size:12px!important;line-height:1.2!important}.rwb-ref-checkout-body textarea{min-height:82px!important;padding-top:12px!important}.rwb-ref-checkout-body input::placeholder,.rwb-ref-checkout-body textarea::placeholder{color:#6c6c6c;opacity:1}.rwb-ref-checkout-body .select2-container{width:100%!important}.rwb-ref-checkout-body .select2-container .select2-selection--single{height:48px!important;border:1px solid #d8d8d8!important;border-radius:0!important;background:#fff!important}.rwb-ref-checkout-body .select2-container .select2-selection--single .select2-selection__rendered{height:48px!important;padding:15px 34px 0 11px!important;color:#111!important;font-size:12px!important;line-height:29px!important}.rwb-ref-checkout-body .select2-container .select2-selection--single .select2-selection__arrow{height:46px!important;right:5px!important}.rwb-ref-checkout-body .woocommerce-shipping-fields{margin-top:24px}.rwb-ref-checkout-body .woocommerce-shipping-fields>h3{font-family:var(--serif,'DM Serif Display',Georgia,serif)!important;font-size:17px!important}.rwb-ref-checkout-body .woocommerce-additional-fields{margin-top:22px}.rwb-ref-checkout-body .woocommerce-additional-fields h3{font-family:var(--serif,'DM Serif Display',Georgia,serif)!important;font-size:17px!important}.rwb-ref-checkout-body #order_comments_field{grid-column:1/-1}
.rwb-ref-checkout-body #order_review_heading{display:none!important}.rwb-ref-checkout-body #order_review{grid-column:2;grid-row:1;position:sticky;top:0;width:100%!important;min-height:100vh;margin:0!important;padding:34px 36px 50px;border:0!important;background:transparent!important}.rwb-ref-checkout-body #order_review table.shop_table{width:100%;margin:0 0 18px!important;border:0!important;border-collapse:collapse!important;background:transparent!important}.rwb-ref-checkout-body #order_review table.shop_table th,.rwb-ref-checkout-body #order_review table.shop_table td{padding:10px 0!important;border:0!important;border-top:1px solid #dedede!important;background:transparent!important;font-size:11px!important;vertical-align:middle}.rwb-ref-checkout-body #order_review table.shop_table thead{display:none}.rwb-ref-checkout-body #order_review table.shop_table tbody tr:first-child td{border-top:0!important}.rwb-ref-checkout-body #order_review td.product-name{position:relative;min-height:70px;display:flex;align-items:center;gap:13px;padding-right:14px!important}.rwb-checkout-product-thumb{position:relative;flex:0 0 64px;width:64px;height:64px;display:grid;place-items:center;border:1px solid #d3d3d3;background:#eef1f1}.rwb-checkout-product-thumb img{width:100%!important;height:100%!important;margin:0!important;padding:8px!important;object-fit:contain!important;background:transparent!important}.rwb-checkout-product-name{font-size:11px;line-height:1.3}.rwb-checkout-product-quantity{position:absolute;left:54px;top:5px;z-index:4;width:20px;height:20px;display:grid;place-items:center;border-radius:50%;background:#111;color:#fff;font-size:9px;font-weight:600;line-height:1}.rwb-ref-checkout-body #order_review td.product-total{text-align:right;white-space:nowrap}.rwb-ref-checkout-body #order_review tfoot th{font-weight:400;text-align:left}.rwb-ref-checkout-body #order_review tfoot td{text-align:right}.rwb-ref-checkout-body #order_review .order-total th,.rwb-ref-checkout-body #order_review .order-total td{padding-top:14px!important;font-size:17px!important;font-weight:600!important}.rwb-ref-checkout-body #order_review .woocommerce-Price-currencySymbol{font-size:.85em}.rwb-ref-checkout-body #order_review .shipping td,.rwb-ref-checkout-body #order_review .shipping th{font-size:11px!important}.rwb-ref-checkout-body #order_review .woocommerce-shipping-methods{margin:0!important;padding:0!important;list-style:none}.rwb-ref-checkout-body #order_review .woocommerce-shipping-methods li{margin:3px 0!important}
.rwb-checkout-coupon{display:grid!important;grid-template-columns:minmax(0,1fr) 82px;gap:8px;margin:0 0 17px!important;padding:0!important;border:0!important}.rwb-checkout-coupon .form-row{width:auto!important}.rwb-checkout-coupon input{height:48px!important}.rwb-checkout-coupon button{width:100%!important;height:48px!important;padding:0 12px!important;border:1px solid #dedede!important;border-radius:0!important;background:#efefef!important;color:#555!important;font-size:11px!important;font-weight:500!important}.rwb-checkout-coupon .clear{display:none}
.rwb-ref-checkout-body #payment{margin-top:18px!important;border:0!important;border-radius:0!important;background:transparent!important}.rwb-ref-checkout-body #payment ul.payment_methods{margin:0!important;padding:0!important;border-top:1px solid #dcdcdc!important;background:transparent!important}.rwb-ref-checkout-body #payment ul.payment_methods li{padding:12px 0!important;border-bottom:1px solid #dcdcdc!important;font-size:11px!important}.rwb-ref-checkout-body #payment div.payment_box{margin:9px 0 0!important;padding:12px!important;border-radius:0!important;background:#fff!important;font-size:10px!important}.rwb-ref-checkout-body #payment div.payment_box:before{display:none!important}.rwb-ref-checkout-body #payment .place-order{margin:0!important;padding:18px 0 0!important}.rwb-ref-checkout-body #payment .woocommerce-privacy-policy-text{font-size:9px;line-height:1.45;color:#666}.rwb-ref-checkout-body #place_order{width:100%!important;min-height:54px!important;margin-top:12px!important;border:1px solid #111!important;border-radius:0!important;background:#050505!important;color:#fff!important;font-size:12px!important;font-weight:600!important}.rwb-ref-checkout-body #place_order:hover{background:#222!important}.rwb-ref-checkout-body .woocommerce-terms-and-conditions-wrapper{font-size:9px!important}
.rwb-ref-checkout-return{display:inline-flex;align-items:center;gap:8px;margin:24px 0 0;color:#111;font-size:11px}.rwb-ref-checkout-return span{font-size:17px}.rwb-ref-checkout-legal{width:54%;padding:18px 44px 34px}.rwb-ref-checkout-legal nav{display:flex;flex-wrap:wrap;gap:8px 16px;padding-top:18px;border-top:1px solid #ddd}.rwb-ref-checkout-legal a{font-size:10px;text-decoration:underline;text-underline-offset:2px}
@media(max-width:900px){.rwb-ref-checkout{background:#fff}.rwb-ref-checkout-mast{grid-template-columns:1fr}.rwb-ref-checkout-mast-left{padding:24px 20px 12px}.rwb-ref-checkout-body form.checkout{grid-template-columns:1fr}.rwb-ref-checkout-body #customer_details{grid-column:1;padding:10px 20px 26px}.rwb-ref-checkout-body #order_review{grid-column:1;grid-row:auto;position:static;min-height:0;padding:26px 20px 34px;background:#f7f7f7}.rwb-ref-checkout-body .woocommerce-notices-wrapper{max-width:none;padding:0 20px}.rwb-ref-checkout-legal{width:100%;padding:0 20px 28px}.rwb-ref-checkout-steps{gap:7px;font-size:9px}.rwb-ref-checkout-steps span{font-size:13px}}
@media(max-width:560px){.rwb-ref-checkout-logo{min-height:58px}.rwb-ref-checkout-logo .custom-logo{max-width:122px;max-height:58px}.rwb-ref-checkout-body .woocommerce-billing-fields__field-wrapper,.rwb-ref-checkout-body .woocommerce-shipping-fields__field-wrapper{grid-template-columns:1fr}.rwb-ref-checkout-body #billing_first_name_field,.rwb-ref-checkout-body #billing_last_name_field,.rwb-ref-checkout-body #billing_city_field,.rwb-ref-checkout-body #billing_postcode_field,.rwb-ref-checkout-body #shipping_first_name_field,.rwb-ref-checkout-body #shipping_last_name_field,.rwb-ref-checkout-body #shipping_city_field,.rwb-ref-checkout-body #shipping_postcode_field{grid-column:1/-1}.rwb-ref-checkout-body #order_review{padding-left:15px;padding-right:15px}.rwb-checkout-product-thumb{flex-basis:58px;width:58px;height:58px}.rwb-checkout-product-quantity{left:49px}.rwb-ref-checkout-legal{padding-left:15px;padding-right:15px}}
CSS;
    }

    wp_add_inline_style('rwb-theme', $css);
}, 95);
