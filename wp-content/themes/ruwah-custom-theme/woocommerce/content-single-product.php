<?php
/**
 * Ruwah Beauty single-product layout.
 * Preserves native WooCommerce hooks and commerce behavior.
 */
defined('ABSPATH') || exit;

global $product;

do_action('woocommerce_before_single_product');

if (post_password_required()) {
    echo get_the_password_form();
    return;
}

$terms = wc_get_product_category_list($product->get_id(), ', ');
?>
<style id="ruwah-single-product-reference">
.single-product .rb-content{padding:42px 0 72px;background:#fff}
.single-product .rb-content>.rb-shell{max-width:1380px}
.single-product div.product{display:grid;grid-template-columns:minmax(0,1.08fr) minmax(420px,.92fr);gap:42px;align-items:start;padding:0!important}
.single-product div.product:after,.single-product div.product:before{display:none!important}
.single-product .rb-single-gallery{position:relative;min-width:0;width:100%}
.single-product .rb-single-gallery>.onsale{z-index:8;top:14px!important;left:14px!important;margin:0!important}
.single-product div.product .woocommerce-product-gallery{float:none!important;width:100%!important;max-width:100%!important;margin:0!important;display:grid;grid-template-columns:88px minmax(0,1fr);grid-template-rows:auto;gap:14px;position:sticky;top:190px;min-width:0}
.single-product .woocommerce-product-gallery>.woocommerce-product-gallery__wrapper{grid-column:2;grid-row:1;min-width:0;width:100%!important;border-radius:12px;background:#f5f4f3;overflow:hidden}
.single-product .woocommerce-product-gallery .flex-viewport{grid-column:2;grid-row:1;min-width:0;width:100%!important;border-radius:12px;background:#f5f4f3;overflow:hidden}
.single-product .woocommerce-product-gallery .flex-viewport .woocommerce-product-gallery__wrapper{width:100%!important;margin:0!important}
.single-product .woocommerce-product-gallery__wrapper{margin:0!important}
.single-product .woocommerce-product-gallery__image{display:flex!important;align-items:center;justify-content:center;width:100%!important;min-height:590px;background:#f5f4f3}
.single-product .woocommerce-product-gallery__image a{display:flex!important;align-items:center;justify-content:center;width:100%;height:100%}
.single-product .woocommerce-product-gallery__image img{display:block!important;width:100%!important;height:590px!important;max-width:100%!important;margin:0!important;padding:22px!important;object-fit:contain!important;object-position:center!important;border-radius:0!important;background:transparent!important}
.single-product .flex-control-thumbs{grid-column:1;grid-row:1;display:flex!important;flex-direction:column;gap:10px;margin:0!important;padding:0!important;list-style:none!important}
.single-product .flex-control-thumbs li{float:none!important;width:88px!important;margin:0!important}
.single-product .flex-control-thumbs img{display:block;width:88px!important;height:118px!important;margin:0!important;padding:5px;object-fit:contain;border:1px solid #e5e2df;border-radius:10px!important;background:#f6f5f4;opacity:1!important;cursor:pointer}
.single-product .flex-control-thumbs img.flex-active{border:2px solid #151515}
.single-product .woocommerce-product-gallery:not(.woocommerce-product-gallery--with-images){display:block}
.single-product .woocommerce-product-gallery__trigger{top:14px!important;right:14px!important;z-index:9}
.single-product div.product .summary{float:none!important;width:100%!important;margin:0!important;padding:2px 0 0}
.rb-product-category-kicker{display:block;margin:0 0 8px;color:#171717;font-size:14px;font-weight:800;line-height:1.2}
.rb-product-category-kicker a{color:inherit}
.single-product div.product .product_title{margin:0 0 14px!important;color:#111!important;font-family:Inter,Arial,sans-serif!important;font-size:clamp(36px,3.1vw,54px)!important;font-weight:800!important;letter-spacing:-.045em!important;line-height:.98!important}
.single-product .woocommerce-product-rating{display:flex;align-items:center;gap:10px;margin:0 0 20px!important}
.single-product .star-rating{width:6.2em!important;height:1.2em!important;color:#111!important;font-size:19px!important;letter-spacing:2px}
.single-product .woocommerce-review-link{color:#666;font-size:14px;text-decoration:none}
.single-product div.product p.price{display:flex;align-items:baseline;flex-wrap:wrap;gap:16px;margin:0 0 18px!important;color:#111!important;font-family:Inter,Arial,sans-serif!important;font-size:43px!important;font-weight:800!important;line-height:1!important}
.single-product div.product p.price del{order:2;color:#777!important;font-size:18px!important;font-weight:600!important;opacity:1!important}
.single-product div.product p.price ins{order:1;color:#111!important;text-decoration:none!important}
.single-product .woocommerce-product-details__short-description{max-width:620px;margin:0 0 22px;padding:0 0 22px;border-bottom:1px solid #ddd;color:#262626;font-size:16px;line-height:1.55}
.single-product .woocommerce-product-details__short-description p:last-child{margin-bottom:0}
.single-product form.cart{display:flex!important;align-items:stretch;gap:10px;margin:22px 0 18px!important;padding:0!important}
.single-product form.cart .quantity{flex:0 0 86px}
.single-product form.cart .quantity .qty{width:100%!important;height:50px!important;min-height:50px!important;border:1px solid #ccc!important;border-radius:8px!important;background:#fff!important;font-weight:700}
.single-product form.cart .single_add_to_cart_button{flex:1!important;min-height:50px!important;border-radius:7px!important;background:#090909!important;color:#fff!important;font-size:16px!important;font-weight:800!important;box-shadow:none!important}
.single-product form.cart .single_add_to_cart_button:hover{background:#5b1624!important;transform:none!important}
.single-product form.variations_form{display:block!important}
.single-product table.variations{margin:0 0 12px!important;border:0!important}
.single-product table.variations tr{display:grid;grid-template-columns:90px 1fr;align-items:center;margin-bottom:12px}
.single-product table.variations th,.single-product table.variations td{display:block!important;padding:0!important;border:0!important;text-align:left!important}
.single-product table.variations label{color:#111;font-size:18px;font-weight:800}
.single-product table.variations select{width:100%!important;min-height:46px!important;padding:0 14px!important;border:1px solid #ccc!important;border-radius:8px!important;background:#fff!important}
.single-product .single_variation_wrap .woocommerce-variation-price{margin:0 0 12px}
.single-product .reset_variations{display:inline-block;margin-top:6px;font-size:12px}
.rb-product-benefits{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin:18px 0 0;padding:18px 0;border-top:1px solid #ddd;border-bottom:1px solid #ddd}
.rb-product-benefits span{display:flex;align-items:center;gap:8px;color:#222;font-size:13px;font-weight:700}
.rb-product-benefits i{display:grid;flex:0 0 22px;width:22px;height:22px;place-items:center;border-radius:50%;background:#111;color:#fff;font-style:normal;font-size:12px}
.single-product .product_meta{margin-top:16px;color:#777;font-size:12px}
.single-product .product_meta>span{display:block;margin:3px 0}
.single-product .woocommerce-tabs,.single-product .related.products,.single-product .up-sells{grid-column:1/-1;width:100%;margin-top:54px!important}
.single-product .woocommerce-tabs{padding-top:36px;border-top:1px solid #e2dfdc}
.single-product .woocommerce-tabs ul.tabs{display:flex;gap:24px;margin:0 0 24px!important;padding:0!important;border:0!important}
.single-product .woocommerce-tabs ul.tabs:before,.single-product .woocommerce-tabs ul.tabs li:after,.single-product .woocommerce-tabs ul.tabs li:before{display:none!important}
.single-product .woocommerce-tabs ul.tabs li{margin:0!important;padding:0!important;border:0!important;background:transparent!important}
.single-product .woocommerce-tabs ul.tabs li a{padding:10px 0!important;color:#777!important;font-weight:800!important}
.single-product .woocommerce-tabs ul.tabs li.active a{color:#111!important;border-bottom:2px solid #111}
.single-product .woocommerce-tabs .panel{max-width:900px;color:#333}
.single-product .related.products>h2,.single-product .up-sells>h2{font-family:Inter,Arial,sans-serif!important;font-size:30px!important;font-weight:800!important;color:#111!important}
@media(max-width:1050px){.single-product div.product{grid-template-columns:1fr;gap:30px}.single-product div.product .woocommerce-product-gallery{position:relative;top:auto}.single-product .woocommerce-product-gallery__image,.single-product .woocommerce-product-gallery__image img{min-height:520px!important;height:520px!important}.single-product .woocommerce-tabs,.single-product .related.products,.single-product .up-sells{grid-column:1}}
@media(max-width:680px){.single-product .rb-content{padding:22px 0 54px}.single-product div.product .woocommerce-product-gallery{display:block}.single-product .woocommerce-product-gallery>.woocommerce-product-gallery__wrapper,.single-product .woocommerce-product-gallery .flex-viewport{width:100%!important;border-radius:10px}.single-product .woocommerce-product-gallery__image,.single-product .woocommerce-product-gallery__image img{min-height:390px!important;height:390px!important}.single-product .flex-control-thumbs{display:flex!important;flex-direction:row;gap:8px;margin-top:9px!important;overflow-x:auto}.single-product .flex-control-thumbs li{flex:0 0 70px;width:70px!important}.single-product .flex-control-thumbs img{width:70px!important;height:76px!important}.single-product div.product .product_title{font-size:36px!important}.single-product div.product p.price{font-size:32px!important}.single-product form.cart{flex-wrap:wrap}.single-product form.cart .quantity{flex:0 0 74px}.single-product form.cart .single_add_to_cart_button{flex:1}.rb-product-benefits{grid-template-columns:1fr;gap:9px}.single-product table.variations tr{grid-template-columns:1fr;gap:5px}}
</style>

<div id="product-<?php the_ID(); ?>" <?php wc_product_class('', $product); ?>>
    <div class="rb-single-gallery">
        <?php do_action('woocommerce_before_single_product_summary'); ?>
    </div>

    <div class="summary entry-summary">
        <?php if ($terms) : ?>
            <span class="rb-product-category-kicker"><?php echo wp_kses_post($terms); ?></span>
        <?php endif; ?>

        <?php do_action('woocommerce_single_product_summary'); ?>

        <div class="rb-product-benefits" aria-label="Shopping benefits">
            <span><i>✓</i><?php esc_html_e('Free shipping over PKR 5,000', 'ruwah'); ?></span>
            <span><i>✓</i><?php esc_html_e('Easy customer support', 'ruwah'); ?></span>
            <span><i>✓</i><?php esc_html_e('Secure payments', 'ruwah'); ?></span>
        </div>
    </div>

    <?php do_action('woocommerce_after_single_product_summary'); ?>
</div>

<?php do_action('woocommerce_after_single_product'); ?>