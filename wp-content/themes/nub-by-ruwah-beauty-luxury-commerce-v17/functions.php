<?php
/** NUB by Ruwah Beauty child theme functions. */
defined( 'ABSPATH' ) || exit;
add_action( 'wp_enqueue_scripts', 'nub19_enqueue_assets', 20 );
function nub19_enqueue_assets() {
    $theme = wp_get_theme();
    wp_enqueue_style( 'astra-parent', get_template_directory_uri() . '/style.css', array(), wp_get_theme( 'astra' )->get( 'Version' ) );
    wp_enqueue_style( 'nub19-home', get_stylesheet_uri(), array( 'astra-parent' ), $theme->get( 'Version' ) );
    wp_enqueue_script( 'nub19-home', get_stylesheet_directory_uri() . '/home.js', array(), $theme->get( 'Version' ), true );
}
add_action( 'after_setup_theme', 'nub19_theme_setup' );
function nub19_theme_setup() { add_theme_support( 'woocommerce' ); add_theme_support( 'wc-product-gallery-zoom' ); add_theme_support( 'wc-product-gallery-lightbox' ); add_theme_support( 'wc-product-gallery-slider' ); }
add_filter( 'body_class', 'nub19_body_class' );
function nub19_body_class( $classes ) { $classes[] = 'nub19-theme'; return $classes; }
add_action( 'wp_head', 'nub19_home_schema', 5 );
function nub19_home_schema() { if ( ! is_front_page() ) return; $schema = array( '@context' => 'https://schema.org', '@type' => 'WebSite', 'name' => 'NUB by Ruwah Beauty', 'url' => home_url( '/' ), 'potentialAction' => array( '@type' => 'SearchAction', 'target' => home_url( '/?s={search_term_string}&post_type=product' ), 'query-input' => 'required name=search_term_string' ) ); echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>'; }
