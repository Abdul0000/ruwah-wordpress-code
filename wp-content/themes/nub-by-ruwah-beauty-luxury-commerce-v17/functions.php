<?php
/**
 * Ruwah Beauty ritual-led Astra child theme.
 */
defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'ruwah_ritual_setup' );
function ruwah_ritual_setup() {
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
}

add_action( 'wp_enqueue_scripts', 'ruwah_ritual_assets', 30 );
function ruwah_ritual_assets() {
    $theme       = wp_get_theme();
    $style_path  = get_stylesheet_directory() . '/style.css';
    $style_url   = get_stylesheet_directory_uri() . '/style.css';
    $style_ver   = file_exists( $style_path ) ? (string) filemtime( $style_path ) : $theme->get( 'Version' );
    $astra_theme = wp_get_theme( 'astra' );

    wp_enqueue_style(
        'astra-parent',
        get_template_directory_uri() . '/style.css',
        array(),
        $astra_theme->exists() ? $astra_theme->get( 'Version' ) : null
    );

    wp_enqueue_style(
        'ruwah-ritual',
        $style_url,
        array( 'astra-parent' ),
        $style_ver
    );

    if ( file_exists( $style_path ) && is_readable( $style_path ) ) {
        $inline_css = file_get_contents( $style_path );
        if ( is_string( $inline_css ) && '' !== trim( $inline_css ) ) {
            wp_add_inline_style( 'ruwah-ritual', $inline_css );
        }
    }

    wp_register_script( 'ruwah-ritual', '', array(), $theme->get( 'Version' ), true );
    wp_enqueue_script( 'ruwah-ritual' );
    wp_add_inline_script( 'ruwah-ritual', "document.addEventListener('DOMContentLoaded',function(){const menu=document.querySelector('.rr-menu');const nav=document.querySelector('.rr-nav');if(menu&&nav){menu.addEventListener('click',function(){const open=menu.getAttribute('aria-expanded')==='true';menu.setAttribute('aria-expanded',String(!open));nav.classList.toggle('is-open',!open);});}document.querySelectorAll('[data-ritual-tab]').forEach(function(tab){tab.addEventListener('click',function(){document.querySelectorAll('[data-ritual-tab]').forEach(function(x){x.classList.remove('is-active');});tab.classList.add('is-active');const value=tab.getAttribute('data-ritual-tab');document.querySelectorAll('[data-ritual-card]').forEach(function(card){card.hidden=value!=='all'&&card.getAttribute('data-ritual-card')!==value;});});});});" );
}

add_filter( 'body_class', 'ruwah_ritual_body_class' );
function ruwah_ritual_body_class( $classes ) {
    $classes[] = 'ruwah-ritual-theme';
    return $classes;
}

function ruwah_shop_url() {
    return function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
}

function ruwah_page_url( $slug ) {
    $page = get_page_by_path( $slug );
    return $page instanceof WP_Post ? get_permalink( $page ) : home_url( '/' . trim( $slug, '/' ) . '/' );
}

function ruwah_products( $limit = 8, $offset = 0 ) {
    if ( ! function_exists( 'wc_get_products' ) ) {
        return array();
    }
    return wc_get_products( array(
        'status'  => 'publish',
        'limit'   => absint( $limit ),
        'offset'  => absint( $offset ),
        'orderby' => 'date',
        'order'   => 'DESC',
        'return'  => 'objects',
    ) );
}

function ruwah_product_card( $product ) {
    if ( ! $product instanceof WC_Product ) {
        return;
    }
    $simple = $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock();
    $url = $simple ? $product->add_to_cart_url() : $product->get_permalink();
    $classes = $simple ? 'rr-add add_to_cart_button ajax_add_to_cart product_type_simple' : 'rr-add';
    echo '<article class="rr-product-card">';
    echo '<a class="rr-product-media" href="' . esc_url( $product->get_permalink() ) . '">';
    if ( $product->is_on_sale() ) {
        echo '<span class="rr-badge">Sale</span>';
    }
    echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'alt' => $product->get_name() ) ) );
    echo '</a><div class="rr-product-copy">';
    echo '<h3><a href="' . esc_url( $product->get_permalink() ) . '">' . esc_html( $product->get_name() ) . '</a></h3>';
    echo '<div class="rr-product-meta"><strong>' . wp_kses_post( $product->get_price_html() ) . '</strong><span>★ ' . esc_html( $product->get_average_rating() ?: 'New' ) . '</span></div>';
    echo '<a class="' . esc_attr( $classes ) . '" href="' . esc_url( $url ) . '" data-product_id="' . esc_attr( (string) $product->get_id() ) . '" data-quantity="1" rel="nofollow">' . esc_html( $simple ? 'Quick Add' : 'View Product' ) . '</a>';
    echo '</div></article>';
}

add_action( 'wp_head', 'ruwah_ritual_schema', 5 );
function ruwah_ritual_schema() {
    if ( ! is_front_page() ) {
        return;
    }
    $schema = array(
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => 'Ruwah Beauty',
        'url'      => home_url( '/' ),
        'potentialAction' => array(
            '@type'       => 'SearchAction',
            'target'      => home_url( '/?s={search_term_string}&post_type=product' ),
            'query-input' => 'required name=search_term_string',
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>';
}
