<?php
defined('ABSPATH') || exit;

if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received')) {
    get_header();
    while (have_posts()) :
        the_post();
        ?>
        <section class="rb-content"><div class="rb-shell"><article><?php the_content(); ?></article></div></section>
        <?php
    endwhile;
    get_footer();
    return;
}

/* Late checkout-only premium skin. It intentionally does not alter WooCommerce mechanics. */
add_action('wp_enqueue_scripts', static function (): void {
    $css = <<<'CSS'
/* Ruwah Beauty — premium checkout refinement v2 */
body.rwb-reference-checkout-v1{background:#f7f3e9!important;color:#111!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout{background:linear-gradient(90deg,#f7f3e9 0%,#f7f3e9 55%,#f1edf3 55%,#f1edf3 100%)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-frame,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body{max-width:1360px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-mast{grid-template-columns:55% 45%!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-mast-left{padding:30px 52px 18px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-logo{min-height:76px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-logo .custom-logo{max-width:158px!important;max-height:76px!important;filter:drop-shadow(0 5px 18px rgba(90,63,116,.13))}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-steps{gap:12px!important;margin-top:5px!important;color:#847c86!important;font-size:11px!important;letter-spacing:.01em}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-steps a,body.rwb-reference-checkout-v1 .rwb-ref-checkout-steps b{color:#171417!important;font-weight:600!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-steps em{color:#876cad!important;font-style:normal!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-steps span{color:#b9acbd!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body form.checkout{grid-template-columns:55% 45%!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #customer_details{padding:18px 52px 44px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #customer_details:before,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #billing_email_field:after{font-family:var(--serif,'DM Serif Display',Georgia,serif)!important;color:#181418!important;font-weight:400!important;letter-spacing:.01em}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #customer_details:before{margin:14px 0 13px!important;font-size:22px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #billing_email_field{margin-bottom:51px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #billing_email_field:after{bottom:-38px!important;font-size:22px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-billing-fields__field-wrapper,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-shipping-fields__field-wrapper{gap:11px 12px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body input.input-text,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body textarea,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body select{min-height:54px!important;padding:0 14px!important;border:1px solid #d8cedb!important;border-radius:9px!important;background:#fffdf9!important;color:#171417!important;font-size:12px!important;transition:border-color .18s ease,box-shadow .18s ease,background .18s ease}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body textarea{min-height:100px!important;padding-top:14px!important;line-height:1.5!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body input.input-text:focus,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body textarea:focus,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body select:focus{outline:0!important;border-color:#876cad!important;background:#fff!important;box-shadow:0 0 0 3px rgba(135,108,173,.12)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body input::placeholder,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body textarea::placeholder{color:#837b82!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #billing_country_field label,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #shipping_country_field label,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #billing_state_field label,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #shipping_state_field label{left:14px!important;top:7px!important;color:#817881!important;font-size:8px!important;letter-spacing:.02em}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .select2-container .select2-selection--single{height:54px!important;border:1px solid #d8cedb!important;border-radius:9px!important;background:#fffdf9!important;transition:border-color .18s ease,box-shadow .18s ease}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .select2-container--open .select2-selection--single{border-color:#876cad!important;box-shadow:0 0 0 3px rgba(135,108,173,.12)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .select2-container .select2-selection--single .select2-selection__rendered{height:54px!important;padding:17px 38px 0 14px!important;color:#171417!important;font-size:12px!important;line-height:31px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .select2-container .select2-selection--single .select2-selection__arrow{height:52px!important;right:9px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-shipping-fields,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-additional-fields{margin-top:26px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-shipping-fields>h3,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-additional-fields h3{font-family:var(--serif,'DM Serif Display',Georgia,serif)!important;color:#181418!important;font-size:19px!important;font-weight:400!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-invalid input.input-text{border-color:#a63f54!important;box-shadow:0 0 0 3px rgba(166,63,84,.08)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-validated input.input-text{border-color:#6e8a7b!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review{top:18px!important;width:calc(100% - 56px)!important;min-height:0!important;margin:18px 28px 42px!important;padding:28px 30px 34px!important;border:1px solid #ddd3df!important;border-radius:18px!important;background:rgba(255,253,249,.96)!important;box-shadow:0 18px 44px rgba(55,37,67,.08)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review table.shop_table{margin-bottom:20px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review table.shop_table th,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review table.shop_table td{padding:13px 0!important;border-top-color:#e3dce5!important;font-size:11px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review td.product-name{gap:15px!important;min-height:76px!important}
body.rwb-reference-checkout-v1 .rwb-checkout-product-thumb{flex-basis:68px!important;width:68px!important;height:68px!important;border:1px solid #d3cbd5!important;border-radius:10px!important;background:#DFE5E6!important;overflow:hidden!important}
body.rwb-reference-checkout-v1 .rwb-checkout-product-thumb img{padding:9px!important}
body.rwb-reference-checkout-v1 .rwb-checkout-product-name{color:#252025!important;font-size:12px!important;font-weight:500!important}
body.rwb-reference-checkout-v1 .rwb-checkout-product-quantity{left:57px!important;top:3px!important;width:21px!important;height:21px!important;background:#876cad!important;color:#fff!important;box-shadow:0 2px 7px rgba(68,45,80,.18)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review .order-total th,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review .order-total td{padding-top:17px!important;color:#171417!important;font-family:var(--serif,'DM Serif Display',Georgia,serif)!important;font-size:19px!important;font-weight:400!important}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon{grid-template-columns:minmax(0,1fr) 92px!important;gap:9px!important;margin:4px 0 20px!important}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon input{height:52px!important;min-height:52px!important;border-radius:9px!important;background:#fff!important}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon button{height:52px!important;border:1px solid #876cad!important;border-radius:9px!important;background:#876cad!important;color:#fff!important;font-weight:700!important;transition:background .18s ease,transform .18s ease,box-shadow .18s ease}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon button:hover{background:#705591!important;color:#fff!important;transform:translateY(-1px);box-shadow:0 8px 18px rgba(92,67,119,.16)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment{margin-top:20px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment ul.payment_methods{border-top:1px solid #e0d7e2!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment ul.payment_methods li{padding:14px 0!important;border-bottom:1px solid #e0d7e2!important;color:#282328!important;font-size:11px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment input[type=radio],body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment input[type=checkbox]{accent-color:#876cad}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment div.payment_box{margin:10px 0 2px!important;padding:14px 15px!important;border:1px solid #e3dce5!important;border-radius:10px!important;background:#fffdf9!important;color:#625b62!important;font-size:10px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment .place-order{padding-top:20px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment .woocommerce-privacy-policy-text{color:#706870!important;font-size:9px!important;line-height:1.55!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment .woocommerce-privacy-policy-text a{color:#705591!important;text-decoration:underline;text-underline-offset:2px}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #place_order{min-height:56px!important;margin-top:14px!important;border:1px solid #876cad!important;border-radius:9px!important;background:#876cad!important;color:#fff!important;font-size:12px!important;font-weight:700!important;letter-spacing:.01em!important;box-shadow:0 12px 28px rgba(79,55,99,.16)!important;transition:background .18s ease,transform .18s ease,box-shadow .18s ease}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #place_order:hover{background:#705591!important;color:#fff!important;transform:translateY(-1px);box-shadow:0 16px 32px rgba(79,55,99,.22)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-notices-wrapper{max-width:55%!important;padding:0 52px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-error,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-message,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-info{border-radius:9px!important;box-shadow:0 7px 20px rgba(55,37,67,.05)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-legal{width:55%!important;padding:18px 52px 38px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-return{color:#5f4a70!important;font-weight:600!important;transition:color .18s ease}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-return:hover{color:#876cad!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-legal nav{gap:9px 18px!important;border-top-color:#ddd3df!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-legal a{color:#413941!important;font-size:10px!important;text-decoration-color:#a99daf!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-legal a:hover{color:#876cad!important;text-decoration-color:#876cad!important}
@media(max-width:900px){body.rwb-reference-checkout-v1 .rwb-ref-checkout{background:#f7f3e9!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-mast{grid-template-columns:1fr!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-mast-left{padding:24px 22px 14px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-body form.checkout{grid-template-columns:1fr!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #customer_details{padding:12px 22px 28px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review{grid-column:1!important;grid-row:auto!important;position:static!important;width:calc(100% - 44px)!important;margin:18px 22px 34px!important;padding:24px 24px 30px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-notices-wrapper{max-width:none!important;padding:0 22px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-legal{width:100%!important;padding:0 22px 30px!important}}
@media(max-width:560px){body.rwb-reference-checkout-v1 .rwb-ref-checkout-logo{min-height:62px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-logo .custom-logo{max-width:130px!important;max-height:62px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-steps{gap:7px!important;font-size:9px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #customer_details{padding-left:15px!important;padding-right:15px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review{width:calc(100% - 30px)!important;margin-left:15px!important;margin-right:15px!important;padding:20px 16px 26px!important;border-radius:13px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-notices-wrapper{padding-left:15px!important;padding-right:15px!important}body.rwb-reference-checkout-v1 .rwb-ref-checkout-legal{padding-left:15px!important;padding-right:15px!important}body.rwb-reference-checkout-v1 .rwb-checkout-coupon{grid-template-columns:1fr 78px!important}body.rwb-reference-checkout-v1 .rwb-checkout-product-thumb{flex-basis:60px!important;width:60px!important;height:60px!important}body.rwb-reference-checkout-v1 .rwb-checkout-product-quantity{left:50px!important}}

/* Ruwah Beauty — checkout field geometry v3: uniform full-width square controls. */
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-billing-fields__field-wrapper,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-shipping-fields__field-wrapper{grid-template-columns:minmax(0,1fr)!important;gap:12px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-billing-fields__field-wrapper>.form-row,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-shipping-fields__field-wrapper>.form-row{grid-column:1/-1!important;width:100%!important;max-width:none!important;box-sizing:border-box!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .form-row-first,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .form-row-last{float:none!important;width:100%!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body input.input-text,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body textarea,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body select{width:100%!important;min-height:56px!important;box-sizing:border-box!important;border:1px solid #cfc5d2!important;border-radius:0!important;background:#fffdfa!important;box-shadow:none!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body textarea{min-height:108px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body input.input-text:focus,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body textarea:focus,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body select:focus{border-color:#876cad!important;box-shadow:0 0 0 1px rgba(135,108,173,.18),0 7px 20px rgba(59,42,73,.05)!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .select2-container{width:100%!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .select2-container .select2-selection--single{height:56px!important;border:1px solid #cfc5d2!important;border-radius:0!important;background:#fffdfa!important;box-shadow:none!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .select2-container .select2-selection--single .select2-selection__rendered{height:56px!important;line-height:33px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .select2-container .select2-selection--single .select2-selection__arrow{height:54px!important}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon{grid-template-columns:1fr!important;row-gap:12px!important;column-gap:0!important}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon .form-row-first{width:100%!important}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon .form-row-last{width:auto!important;margin-top:0!important;justify-self:start!important}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon input,body.rwb-reference-checkout-v1 .rwb-checkout-coupon button{height:56px!important;min-height:56px!important;border-radius:0!important}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon input{width:100%!important;border-color:#cfc5d2!important;background:#fffdfa!important}
body.rwb-reference-checkout-v1 .rwb-checkout-coupon button{width:auto!important;min-width:110px!important;padding:0 22px!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #payment div.payment_box{min-height:56px!important;display:flex!important;align-items:center!important;border-color:#d8cedb!important;border-radius:0!important;background:#fffdfa!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #place_order{border-radius:0!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review{border-radius:0!important;box-shadow:0 18px 46px rgba(55,37,67,.07)!important}
body.rwb-reference-checkout-v1 .rwb-checkout-product-thumb{border-radius:0!important}
body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-error,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-message,body.rwb-reference-checkout-v1 .rwb-ref-checkout-body .woocommerce-info{border-radius:0!important}
@media(max-width:560px){body.rwb-reference-checkout-v1 .rwb-ref-checkout-body #order_review{border-radius:0!important}}
CSS;
    wp_add_inline_style('rwb-theme', $css);
}, 9999);

get_header();
$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');
$account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/');
$privacy_url = get_privacy_policy_url();
$legal_pages = [
    'Refund policy' => 'refund-policy',
    'Shipping' => 'shipping',
    'Terms of service' => 'terms-of-service',
    'Contact' => 'contact',
];
?>
<section class="rwb-ref-checkout" aria-label="Secure checkout">
    <div class="rwb-ref-checkout-frame">
        <header class="rwb-ref-checkout-mast">
            <div class="rwb-ref-checkout-mast-left">
                <div class="rwb-ref-checkout-logo">
                    <?php if (has_custom_logo()) { the_custom_logo(); } else { ?><a href="<?php echo esc_url(home_url('/')); ?>">RUWAH</a><?php } ?>
                </div>
                <nav class="rwb-ref-checkout-steps" aria-label="Checkout progress">
                    <a href="<?php echo esc_url($cart_url); ?>">Cart</a><span aria-hidden="true">›</span>
                    <b>Information</b><span aria-hidden="true">›</span>
                    <em>Shipping</em><span aria-hidden="true">›</span>
                    <em>Payment</em>
                </nav>
            </div>
            <div aria-hidden="true"></div>
        </header>
    </div>

    <div class="rwb-ref-checkout-body">
        <?php while (have_posts()) : the_post(); ?>
            <article><?php the_content(); ?></article>
        <?php endwhile; ?>
    </div>

    <div class="rwb-ref-checkout-frame">
        <div class="rwb-ref-checkout-legal">
            <a class="rwb-ref-checkout-return" href="<?php echo esc_url($cart_url); ?>"><span aria-hidden="true">‹</span> Return to cart</a>
            <nav aria-label="Checkout policies">
                <?php foreach ($legal_pages as $label => $slug) : $page = get_page_by_path($slug); if ($page) : ?>
                    <a href="<?php echo esc_url(get_permalink($page)); ?>"><?php echo esc_html($label); ?></a>
                <?php endif; endforeach; ?>
                <?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>">Privacy policy</a><?php endif; ?>
                <a href="<?php echo esc_url($account_url); ?>">Sign in</a>
            </nav>
        </div>
    </div>
</section>
<?php get_footer();