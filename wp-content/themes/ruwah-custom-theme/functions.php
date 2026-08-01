<?php
defined('ABSPATH') || exit;
define('RUWAH_THEME_VERSION','1.0.5');

add_action('after_setup_theme',function(){
    load_theme_textdomain('ruwah',get_template_directory().'/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5',['search-form','comment-form','comment-list','gallery','caption','style','script']);
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    register_nav_menus(['primary'=>__('Primary Menu','ruwah'),'footer'=>__('Footer Menu','ruwah')]);
});

add_action('wp_enqueue_scripts',function(){
    wp_enqueue_style('ruwah-fonts','https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700;800&display=swap',[],null);
    wp_enqueue_style('ruwah-style',get_stylesheet_uri(),['ruwah-fonts'],RUWAH_THEME_VERSION);
    wp_enqueue_script('ruwah-theme',get_template_directory_uri().'/theme.js',[],RUWAH_THEME_VERSION,true);

    $card_css=<<<CSS
.rb-product-grid{display:grid!important;grid-template-columns:repeat(5,minmax(0,1fr))!important;gap:16px!important;align-items:stretch!important}
.rb-product-card{display:flex!important;flex-direction:column!important;min-width:0!important;min-height:0!important;padding:8px!important;border:1px solid #eadfdf!important;border-radius:18px!important;background:#fff!important;box-shadow:none!important;overflow:hidden!important}
.rb-product-media{display:flex!important;align-items:center!important;justify-content:center!important;height:205px!important;padding:14px!important;border-radius:13px!important;overflow:hidden!important;background:#f8f6f6!important}
.rb-product-media img{display:block!important;width:100%!important;height:100%!important;max-width:100%!important;object-fit:contain!important;object-position:center!important}
.rb-product-copy{display:flex!important;flex:1!important;flex-direction:column!important;padding:10px 10px 4px!important}
.rb-product-copy small{display:none!important}
.rb-product-copy h3{display:-webkit-box!important;min-height:39px!important;margin:0 0 7px!important;overflow:hidden!important;-webkit-box-orient:vertical!important;-webkit-line-clamp:2!important;font-family:Inter,sans-serif!important;font-size:16px!important;font-weight:800!important;line-height:1.2!important;letter-spacing:-.015em!important}
.rb-product-copy h3 a{color:#171313!important;text-decoration:none!important}
.rb-card-rating{display:flex!important;align-items:center!important;gap:5px!important;min-height:18px!important;margin:0 0 7px!important;color:#111!important;font-size:13px!important;line-height:1!important;white-space:nowrap!important}
.rb-card-rating>span:first-child{font-size:15px!important;letter-spacing:-2px!important}
.rb-card-rating .rb-rating-count{overflow:hidden!important;color:#111!important;font-size:10px!important;text-overflow:ellipsis!important;text-decoration:underline!important;text-underline-offset:2px!important}
.rb-price{display:flex!important;align-items:baseline!important;flex-wrap:nowrap!important;gap:6px!important;min-height:22px!important;margin-top:auto!important;color:#171313!important;font-size:16px!important;font-weight:800!important;line-height:1.1!important;white-space:nowrap!important}
.rb-price del{color:#887d7d!important;font-size:10px!important;font-weight:500!important;opacity:1!important}
.rb-price ins{color:#171313!important;font-size:16px!important;font-weight:800!important;text-decoration:none!important}
.rb-product-actions{display:block!important;margin-top:9px!important;padding:0 10px 7px!important}
.rb-product-actions .rb-text-link{display:none!important}
.rb-product-actions .button{display:flex!important;align-items:center!important;justify-content:center!important;width:100%!important;min-height:39px!important;margin:0!important;padding:9px 10px!important;border:0!important;border-radius:7px!important;background:#111!important;color:#fff!important;font-family:Inter,sans-serif!important;font-size:12px!important;font-weight:800!important;line-height:1!important;text-decoration:none!important;box-shadow:none!important}
.rb-product-actions .button:hover{background:var(--rb-burgundy,#681426)!important;color:#fff!important}
.rb-badge{top:16px!important;left:16px!important;padding:6px 9px!important;border-radius:999px!important;font-size:10px!important;line-height:1!important}
@media(max-width:1500px){.rb-product-grid{grid-template-columns:repeat(4,minmax(0,1fr))!important}.rb-product-media{height:215px!important}}
@media(max-width:980px){.rb-product-grid{grid-template-columns:repeat(2,minmax(0,1fr))!important}.rb-product-media{height:210px!important}}
@media(max-width:560px){.rb-product-grid{gap:10px!important}.rb-product-card{padding:6px!important;border-radius:14px!important}.rb-product-media{height:145px!important;padding:8px!important;border-radius:10px!important}.rb-product-copy{padding:8px 6px 3px!important}.rb-product-copy h3{min-height:34px!important;margin-bottom:5px!important;font-size:13px!important}.rb-card-rating{gap:3px!important;margin-bottom:5px!important;font-size:10px!important}.rb-card-rating>span:first-child{font-size:12px!important}.rb-card-rating .rb-rating-count{font-size:8px!important}.rb-price{gap:3px!important;font-size:12px!important}.rb-price ins{font-size:12px!important}.rb-price del{font-size:8px!important}.rb-product-actions{margin-top:7px!important;padding:0 6px 5px!important}.rb-product-actions .button{min-height:34px!important;padding:8px 6px!important;font-size:10px!important}.rb-badge{top:11px!important;left:11px!important;padding:5px 7px!important;font-size:8px!important}}
.rb-category-card span{position:relative;background:#f6e8e8!important}.rb-category-card span img{display:block;width:100%!important;height:100%!important;object-fit:cover!important;object-position:center!important;transform:scale(1.01);transition:transform .35s ease}.rb-category-card:hover span img{transform:scale(1.08)}
/* Keep the existing hero design, but prevent text/image overlap. */
.home .rb-hero{min-height:620px!important;grid-template-columns:minmax(0,54%) minmax(0,46%)!important;background:linear-gradient(90deg,#f6e8e8 0 56%,#ead4d4 56%)!important}
.home .rb-hero-copy{width:calc(100% - 40px)!important;max-width:1240px!important;padding:58px 0!important}
.home .rb-hero-copy>*{max-width:570px!important}
.home .rb-hero h1{max-width:570px!important;margin:0 0 20px!important;font-size:clamp(52px,5.35vw,76px)!important;line-height:.98!important;overflow-wrap:normal!important;word-break:normal!important}
.home .rb-hero p{max-width:520px!important;font-size:17px!important}
.home .rb-hero-media{inset:0 0 0 56%!important;padding:42px 4vw 34px 2vw!important}
.home .rb-hero-media:before{width:min(38vw,500px)!important;height:min(38vw,500px)!important}
.home .rb-hero-media img{width:88%!important;height:88%!important;max-width:650px!important;object-fit:contain!important}
@media(max-width:1180px){.home .rb-hero h1{max-width:510px!important;font-size:clamp(48px,5.5vw,66px)!important}.home .rb-hero-copy>*{max-width:510px!important}.home .rb-hero-media{inset:0 0 0 57%!important;padding-left:1vw!important}}
@media(max-width:900px){.home .rb-hero{min-height:700px!important}.home .rb-hero h1{max-width:440px!important;font-size:54px!important}.home .rb-hero-copy>*{max-width:440px!important}.home .rb-hero-media{inset:0 0 0 55%!important}.home .rb-hero-media img{width:94%!important;height:84%!important}}
@media(max-width:760px){.home .rb-hero{min-height:780px!important;display:block!important;background:linear-gradient(#f6e8e8 0 59%,#ead4d4 59%)!important}.home .rb-hero-copy{padding:42px 0 34px!important}.home .rb-hero-copy>*{max-width:100%!important}.home .rb-hero h1{max-width:100%!important;font-size:clamp(43px,13vw,56px)!important;line-height:1!important}.home .rb-hero p{max-width:100%!important;font-size:16px!important}.home .rb-hero-media{inset:auto 0 0!important;height:41%!important;padding:10px 24px 20px!important}.home .rb-hero-media:before{width:300px!important;height:300px!important}.home .rb-hero-media img{width:92%!important;height:92%!important}}
CSS;
    wp_add_inline_style('ruwah-style',$card_css);

    if(is_front_page()){
        $category_images=[
            'Body Care'=>home_url('/wp-content/uploads/2026/07/white-pearl-body-lotion-1.jpg'),
            'Cleansers'=>home_url('/wp-content/uploads/2026/07/rice-cleansing-cream-1.jpg'),
            'Eye Care'=>home_url('/wp-content/uploads/2026/07/radiance-eye-serum-1.jpg'),
            'Masks'=>home_url('/wp-content/uploads/2026/07/rice-repair-mask-1.jpg'),
            'Moisturizers'=>home_url('/wp-content/uploads/2026/07/hydrating-moisturizer-1.jpg'),
            'Serums'=>home_url('/wp-content/uploads/2026/07/triple-action-serum-1.jpg'),
            'Sun Protection'=>home_url('/wp-content/uploads/2026/07/mineral-shield-sunscreen-spf50-1.jpg'),
            'Toners'=>home_url('/wp-content/uploads/2026/07/rice-renewing-glowing-toner-1.jpg'),
        ];
        $category_js='document.addEventListener("DOMContentLoaded",function(){var map='.wp_json_encode($category_images).';document.querySelectorAll(".rb-category-card").forEach(function(card){var label=card.querySelector("b"),frame=card.querySelector("span");if(!label||!frame||!map[label.textContent.trim()])return;var img=document.createElement("img");img.src=map[label.textContent.trim()];img.alt=label.textContent.trim();img.loading="lazy";img.decoding="async";frame.replaceChildren(img);});});';
        wp_add_inline_script('ruwah-theme',$category_js,'after');
    }
});

function ruwah_page_url($slug){
    $page=get_page_by_path(trim($slug,'/'));
    return $page?get_permalink($page):home_url('/'.trim($slug,'/').'/');
}
function ruwah_shop_url(){return function_exists('wc_get_page_permalink')?wc_get_page_permalink('shop'):ruwah_page_url('shop');}
function ruwah_cart_url(){return function_exists('wc_get_cart_url')?wc_get_cart_url():ruwah_page_url('cart');}
function ruwah_account_url(){return function_exists('wc_get_page_permalink')?wc_get_page_permalink('myaccount'):ruwah_page_url('my-account');}
function ruwah_products($limit=8,$args=[]){
    if(!function_exists('wc_get_products'))return [];
    return wc_get_products(wp_parse_args($args,['status'=>'publish','limit'=>$limit,'orderby'=>'date','order'=>'DESC']));
}
function ruwah_cart_count(){return function_exists('WC')&&WC()->cart?(int)WC()->cart->get_cart_contents_count():0;}
function ruwah_shipping_progress(){
    $threshold=(float)apply_filters('ruwah_free_shipping_threshold',5000);
    $subtotal=function_exists('WC')&&WC()->cart?(float)WC()->cart->get_subtotal():0;
    $remaining=max(0,$threshold-$subtotal);
    $percent=$threshold>0?min(100,($subtotal/$threshold)*100):100;
    return compact('threshold','subtotal','remaining','percent');
}
function ruwah_product_card($product,$badge=''){
    if(!$product||!is_a($product,'WC_Product'))return;
    $id=$product->get_id();
    $rating=(float)$product->get_average_rating();
    $count=(int)$product->get_review_count();
    echo '<article class="rb-product-card">';
    if($badge)echo '<span class="rb-badge">'.esc_html($badge).'</span>';
    echo '<a class="rb-product-media" href="'.esc_url($product->get_permalink()).'">'.wp_kses_post($product->get_image('woocommerce_thumbnail',['loading'=>'lazy'])).'</a>';
    echo '<div class="rb-product-copy">';
    echo '<h3><a href="'.esc_url($product->get_permalink()).'">'.esc_html($product->get_name()).'</a></h3>';
    if($count>0){
        $filled=max(0,min(5,(int)round($rating)));
        $stars=str_repeat('★',$filled).str_repeat('☆',5-$filled);
        echo '<div class="rb-card-rating" role="img" aria-label="'.esc_attr(sprintf(__('%1$s out of 5 based on %2$d reviews','ruwah'),number_format_i18n($rating,1),$count)).'"><span aria-hidden="true">'.esc_html($stars).'</span><span class="rb-rating-count">'.esc_html(number_format_i18n($rating,1).' · '.$count.' '.($count===1?__('Review','ruwah'):__('Reviews','ruwah'))).'</span></div>';
    }else{
        echo '<div class="rb-card-rating" role="img" aria-label="'.esc_attr__('No reviews yet','ruwah').'"><span aria-hidden="true">☆☆☆☆☆</span><span class="rb-rating-count">'.esc_html__('No reviews','ruwah').'</span></div>';
    }
    echo '<div class="rb-price">'.wp_kses_post($product->get_price_html()).'</div></div>';
    echo '<div class="rb-product-actions"><a class="rb-text-link" href="'.esc_url($product->get_permalink()).'">'.esc_html__('View','ruwah').'</a>';
    if($product->is_purchasable()&&$product->is_in_stock()){
        echo '<a rel="nofollow" data-product_id="'.esc_attr((string)$id).'" data-quantity="1" class="button add_to_cart_button ajax_add_to_cart" href="'.esc_url($product->add_to_cart_url()).'">'.esc_html__('Add to Cart','ruwah').'</a>';
    }
    echo '</div></article>';
}