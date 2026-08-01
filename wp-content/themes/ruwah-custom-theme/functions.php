<?php
defined('ABSPATH') || exit;
define('RUWAH_THEME_VERSION','1.0.2');

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

    $compact_cards_css='\n    .rb-product-grid{align-items:stretch}\n    .rb-product-card{display:flex!important;flex-direction:column;min-height:0!important;padding:10px 10px 12px!important;border-radius:16px!important}\n    .rb-product-media{aspect-ratio:auto!important;height:265px!important;border-radius:12px!important}\n    .rb-product-media img{width:100%!important;height:100%!important;object-fit:cover!important}\n    .rb-product-copy{display:flex;flex:1;flex-direction:column;padding:12px 4px 4px!important}\n    .rb-product-copy small{min-height:17px;font-size:10px!important;line-height:1.25;letter-spacing:.02em}\n    .rb-product-copy h3{display:-webkit-box;min-height:48px;margin:7px 0 5px!important;overflow:hidden;-webkit-box-orient:vertical;-webkit-line-clamp:2;font-size:21px!important;line-height:1.14!important}\n    .rb-card-rating{display:flex;align-items:center;gap:7px;min-height:22px;margin:0 0 6px;color:#d9a529;font-size:15px;letter-spacing:.08em;line-height:1}\n    .rb-card-rating .rb-rating-count{color:#8b7779;font-size:11px;letter-spacing:0}\n    .rb-price{display:flex;align-items:center;flex-wrap:wrap;gap:4px;min-height:30px;margin-top:auto;font-size:16px!important;line-height:1.25}\n    .rb-price del,.rb-price ins{white-space:nowrap}\n    .rb-product-actions{min-height:38px;margin-top:7px;padding:0 2px;align-items:center!important}\n    .rb-product-actions .rb-text-link,.rb-product-actions .button{font-size:14px!important;line-height:1.1}\n    .rb-product-actions .button{min-height:auto!important;padding:7px 10px!important;background:transparent!important;color:var(--rb-burgundy)!important}\n    .rb-badge{top:18px!important;left:18px!important;padding:7px 10px!important}\n    @media(max-width:1180px){.rb-product-media{height:235px!important}.rb-product-copy h3{font-size:19px!important}}\n    @media(max-width:760px){.rb-product-card{padding:8px 8px 10px!important}.rb-product-media{height:190px!important}.rb-product-copy h3{min-height:42px;font-size:17px!important}.rb-price{font-size:14px!important}.rb-card-rating{font-size:13px}.rb-product-actions .rb-text-link,.rb-product-actions .button{font-size:13px!important}}';
    wp_add_inline_style('ruwah-style',$compact_cards_css);
});

function ruwah_page_url($slug){$page=get_page_by_path(trim($slug,'/'));return $page?get_permalink($page):home_url('/'.trim($slug,'/').'/');}
function ruwah_shop_url(){return function_exists('wc_get_page_permalink')?wc_get_page_permalink('shop'):ruwah_page_url('shop');}
function ruwah_cart_url(){return function_exists('wc_get_cart_url')?wc_get_cart_url():ruwah_page_url('cart');}
function ruwah_account_url(){return function_exists('wc_get_page_permalink')?wc_get_page_permalink('myaccount'):ruwah_page_url('my-account');}
function ruwah_products($limit=8,$args=[]){if(!function_exists('wc_get_products'))return [];return wc_get_products(wp_parse_args($args,['status'=>'publish','limit'=>$limit,'orderby'=>'date','order'=>'DESC']));}
function ruwah_cart_count(){return function_exists('WC')&&WC()->cart?(int)WC()->cart->get_cart_contents_count():0;}
function ruwah_shipping_progress(){ $threshold=(float)apply_filters('ruwah_free_shipping_threshold',5000);$subtotal=function_exists('WC')&&WC()->cart?(float)WC()->cart->get_subtotal():0;$remaining=max(0,$threshold-$subtotal);$percent=$threshold>0?min(100,($subtotal/$threshold)*100):100;return compact('threshold','subtotal','remaining','percent');}

function ruwah_product_card($product,$badge=''){
    if(!$product||!is_a($product,'WC_Product'))return;
    $id=$product->get_id();
    $rating=(float)$product->get_average_rating();
    $review_count=(int)$product->get_review_count();
    $rounded=max(0,min(5,(int)round($rating)));
    $stars=str_repeat('★',$rounded).str_repeat('☆',5-$rounded);
    $rating_label=$review_count?sprintf(_n('%1$s out of 5 from %2$s review','%1$s out of 5 from %2$s reviews',$review_count,'ruwah'),number_format_i18n($rating,1),number_format_i18n($review_count)):__('No reviews yet','ruwah');

    echo '<article class="rb-product-card">';
    if($badge)echo '<span class="rb-badge">'.esc_html($badge).'</span>';
    echo '<a class="rb-product-media" href="'.esc_url($product->get_permalink()).'">'.wp_kses_post($product->get_image('woocommerce_thumbnail',['loading'=>'lazy'])).'</a>';
    echo '<div class="rb-product-copy">';
    $cats=wc_get_product_category_list($id,', ');
    if($cats)echo '<small>'.wp_kses_post($cats).'</small>';
    echo '<h3><a href="'.esc_url($product->get_permalink()).'">'.esc_html($product->get_name()).'</a></h3>';
    echo '<div class="rb-card-rating" role="img" aria-label="'.esc_attr($rating_label).'"><span aria-hidden="true">'.esc_html($stars).'</span><span class="rb-rating-count">'.($review_count?'('.esc_html(number_format_i18n($review_count)).')':esc_html__('No reviews','ruwah')).'</span></div>';
    echo '<div class="rb-price">'.wp_kses_post($product->get_price_html()).'</div></div>';
    echo '<div class="rb-product-actions"><a class="rb-text-link" href="'.esc_url($product->get_permalink()).'">'.esc_html__('View','ruwah').'</a>';
    if($product->is_purchasable()&&$product->is_in_stock())echo '<a rel="nofollow" data-product_id="'.esc_attr((string)$id).'" data-quantity="1" class="button add_to_cart_button ajax_add_to_cart" href="'.esc_url($product->add_to_cart_url()).'">'.esc_html__('Add','ruwah').'</a>';
    echo '</div></article>';
}

add_action('wp_enqueue_scripts',function(){
    if(!is_front_page())return;
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
    $css='.rb-category-card span{position:relative;background:#f6e8e8!important}.rb-category-card span img{width:100%!important;height:100%!important;object-fit:cover!important;object-position:center!important;display:block;transform:scale(1.01);transition:transform .35s ease}.rb-category-card:hover span img{transform:scale(1.08)}';
    wp_add_inline_style('ruwah-style',$css);
    $js='document.addEventListener("DOMContentLoaded",function(){var map='.wp_json_encode($category_images).';document.querySelectorAll(".rb-category-card").forEach(function(card){var label=card.querySelector("b");var frame=card.querySelector("span");if(!label||!frame||!map[label.textContent.trim()])return;var img=document.createElement("img");img.src=map[label.textContent.trim()];img.alt=label.textContent.trim();img.loading="lazy";img.decoding="async";frame.replaceChildren(img);});});';
    wp_add_inline_script('ruwah-theme',$js,'after');
},20);
