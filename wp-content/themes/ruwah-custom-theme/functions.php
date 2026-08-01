<?php
defined('ABSPATH') || exit;
define('RUWAH_THEME_VERSION','1.0.1');
add_action('after_setup_theme',function(){load_theme_textdomain('ruwah',get_template_directory().'/languages');add_theme_support('title-tag');add_theme_support('post-thumbnails');add_theme_support('custom-logo');add_theme_support('html5',['search-form','comment-form','comment-list','gallery','caption','style','script']);add_theme_support('woocommerce');add_theme_support('wc-product-gallery-zoom');add_theme_support('wc-product-gallery-lightbox');add_theme_support('wc-product-gallery-slider');register_nav_menus(['primary'=>__('Primary Menu','ruwah'),'footer'=>__('Footer Menu','ruwah')]);});
add_action('wp_enqueue_scripts',function(){wp_enqueue_style('ruwah-fonts','https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=Inter:wght@400;500;600;700;800&display=swap',[],null);wp_enqueue_style('ruwah-style',get_stylesheet_uri(),['ruwah-fonts'],RUWAH_THEME_VERSION);wp_enqueue_script('ruwah-theme',get_template_directory_uri().'/theme.js',[],RUWAH_THEME_VERSION,true);});
function ruwah_page_url($slug){$page=get_page_by_path(trim($slug,'/'));return $page?get_permalink($page):home_url('/'.trim($slug,'/').'/');}
function ruwah_shop_url(){return function_exists('wc_get_page_permalink')?wc_get_page_permalink('shop'):ruwah_page_url('shop');}
function ruwah_cart_url(){return function_exists('wc_get_cart_url')?wc_get_cart_url():ruwah_page_url('cart');}
function ruwah_account_url(){return function_exists('wc_get_page_permalink')?wc_get_page_permalink('myaccount'):ruwah_page_url('my-account');}
function ruwah_products($limit=8,$args=[]){if(!function_exists('wc_get_products'))return [];return wc_get_products(wp_parse_args($args,['status'=>'publish','limit'=>$limit,'orderby'=>'date','order'=>'DESC']));}
function ruwah_cart_count(){return function_exists('WC')&&WC()->cart?(int)WC()->cart->get_cart_contents_count():0;}
function ruwah_shipping_progress(){ $threshold=(float)apply_filters('ruwah_free_shipping_threshold',5000);$subtotal=function_exists('WC')&&WC()->cart?(float)WC()->cart->get_subtotal():0;$remaining=max(0,$threshold-$subtotal);$percent=$threshold>0?min(100,($subtotal/$threshold)*100):100;return compact('threshold','subtotal','remaining','percent');}
function ruwah_product_card($product,$badge=''){if(!$product||!is_a($product,'WC_Product'))return;$id=$product->get_id();echo '<article class="rb-product-card">';if($badge)echo '<span class="rb-badge">'.esc_html($badge).'</span>';echo '<a class="rb-product-media" href="'.esc_url($product->get_permalink()).'">'.wp_kses_post($product->get_image('woocommerce_thumbnail',['loading'=>'lazy'])).'</a><div class="rb-product-copy">';$cats=wc_get_product_category_list($id,', ');if($cats)echo '<small>'.wp_kses_post($cats).'</small>';echo '<h3><a href="'.esc_url($product->get_permalink()).'">'.esc_html($product->get_name()).'</a></h3>';if(wc_review_ratings_enabled())echo wp_kses_post(wc_get_rating_html((float)$product->get_average_rating(),(int)$product->get_review_count()));echo '<div class="rb-price">'.wp_kses_post($product->get_price_html()).'</div></div><div class="rb-product-actions"><a class="rb-text-link" href="'.esc_url($product->get_permalink()).'">'.esc_html__('View','ruwah').'</a>';if($product->is_purchasable()&&$product->is_in_stock())echo '<a rel="nofollow" data-product_id="'.esc_attr((string)$id).'" data-quantity="1" class="button add_to_cart_button ajax_add_to_cart" href="'.esc_url($product->add_to_cart_url()).'">'.esc_html__('Add','ruwah').'</a>';echo '</div></article>';}

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
    $script='document.addEventListener("DOMContentLoaded",function(){var map='.wp_json_encode($category_images).';document.querySelectorAll(".rb-category-card").forEach(function(card){var label=card.querySelector("b");var frame=card.querySelector("span");if(!label||!frame||!map[label.textContent.trim()])return;var img=document.createElement("img");img.src=map[label.textContent.trim()];img.alt=label.textContent.trim();img.loading="lazy";img.decoding="async";frame.replaceChildren(img);});});';
    wp_add_inline_script('ruwah-theme',$script,'after');
},20);
