<?php
/** Ruwah Beauty Bold Ritual child theme functions. */
defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'rw_theme_setup' );
function rw_theme_setup() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
}

add_action( 'wp_enqueue_scripts', 'rw_enqueue_assets', 20 );
function rw_enqueue_assets() {
    $theme = wp_get_theme();
    wp_enqueue_style( 'astra-parent', get_template_directory_uri() . '/style.css', array(), wp_get_theme( 'astra' )->get( 'Version' ) );
    wp_enqueue_style( 'rw-bold-ritual', get_stylesheet_uri(), array( 'astra-parent' ), $theme->get( 'Version' ) );
    wp_register_script( 'rw-bold-ritual', '', array(), $theme->get( 'Version' ), true );
    wp_enqueue_script( 'rw-bold-ritual' );
    wp_add_inline_script( 'rw-bold-ritual', "document.addEventListener('DOMContentLoaded',function(){var b=document.querySelector('.rw-menu'),n=document.querySelector('.rw-links');if(b&&n){b.addEventListener('click',function(){var o=b.getAttribute('aria-expanded')==='true';b.setAttribute('aria-expanded',String(!o));n.classList.toggle('is-open',!o);});}});" );
}

add_filter( 'body_class', 'rw_body_class' );
function rw_body_class( $classes ) { $classes[] = 'rw-theme'; return $classes; }

add_filter( 'woocommerce_add_to_cart_fragments', 'rw_cart_fragment' );
function rw_cart_fragment( $fragments ) {
    ob_start(); ?>
    <span class="rw-cart-count"><?php echo esc_html( WC()->cart ? WC()->cart->get_cart_contents_count() : 0 ); ?></span>
    <?php $fragments['.rw-cart-count'] = ob_get_clean(); return $fragments;
}

add_action( 'wp_head', 'rw_home_schema', 5 );
function rw_home_schema() {
    if ( ! is_front_page() ) return;
    $schema = array('@context'=>'https://schema.org','@type'=>'WebSite','name'=>'Ruwah Beauty','url'=>home_url('/'),'potentialAction'=>array('@type'=>'SearchAction','target'=>home_url('/?s={search_term_string}&post_type=product'),'query-input'=>'required name=search_term_string'));
    echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
}
