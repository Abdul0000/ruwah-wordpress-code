<?php
defined('ABSPATH') || exit;
add_action('after_setup_theme',function(){add_theme_support('woocommerce');add_theme_support('wc-product-gallery-zoom');add_theme_support('wc-product-gallery-lightbox');add_theme_support('wc-product-gallery-slider');add_theme_support('title-tag');add_theme_support('post-thumbnails');register_nav_menus(['primary'=>'Primary Menu']);});
add_action('wp_enqueue_scripts',function(){wp_enqueue_style('astra-parent',get_template_directory_uri().'/style.css',[],wp_get_theme('astra')->get('Version'));$p=get_stylesheet_directory().'/style.css';wp_enqueue_style('ruwah-ritual',get_stylesheet_directory_uri().'/style.css',['astra-parent'],file_exists($p)?filemtime($p):'23.0.0');wp_enqueue_script('ruwah-ritual',get_stylesheet_directory_uri().'/theme.js',[],file_exists(get_stylesheet_directory().'/theme.js')?filemtime(get_stylesheet_directory().'/theme.js'):'23.0.0',true);},30);
add_filter('body_class',function($c){$c[]='ruwah-ritual-theme';return $c;});
function ruwah_url($slug){$p=get_page_by_path($slug);return $p?get_permalink($p):home_url('/'.trim($slug,'/').'/');}
function ruwah_shop(){return function_exists('wc_get_page_permalink')?wc_get_page_permalink('shop'):home_url('/shop/');}
function ruwah_cart_count(){return function_exists('WC')&&WC()->cart?WC()->cart->get_cart_contents_count():0;}
function ruwah_products($limit=8){return function_exists('wc_get_products')?wc_get_products(['status'=>'publish','limit'=>$limit,'orderby'=>'date','order'=>'DESC']):[];}
add_filter('woocommerce_enqueue_styles',function($s){return $s;});
add_filter('woocommerce_output_related_products_args',function($a){$a['posts_per_page']=4;$a['columns']=4;return $a;});
