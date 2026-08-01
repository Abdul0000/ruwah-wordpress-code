<?php
/**
 * NUB by Ruwah Beauty child-theme functions.
 *
 * @package Nub_Ruwah
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register child-theme support.
 */
function ruwah_theme_setup() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
}
add_action( 'after_setup_theme', 'ruwah_theme_setup', 20 );

/**
 * Enqueue child-theme assets after Astra.
 */
function ruwah_enqueue_assets() {
	$theme = wp_get_theme();
	wp_enqueue_style(
		'ruwah-luxury-child',
		get_stylesheet_uri(),
		array(),
		$theme->get( 'Version' )
	);

	$fixes_path = get_stylesheet_directory() . '/assets/css/storefront-fixes.css';
	if ( file_exists( $fixes_path ) ) {
		wp_enqueue_style(
			'ruwah-storefront-fixes',
			get_stylesheet_directory_uri() . '/assets/css/storefront-fixes.css',
			array( 'ruwah-luxury-child' ),
			(string) filemtime( $fixes_path )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'ruwah_enqueue_assets', 30 );

/**
 * Publish the storefront after the approved production launch.
 * The updates are idempotent and become no-ops after the options are corrected.
 */
function ruwah_publish_storefront() {
	if ( 'yes' === get_option( 'woocommerce_coming_soon', 'no' ) ) {
		update_option( 'woocommerce_coming_soon', 'no' );
	}

	if ( 'yes' === get_option( 'woocommerce_store_pages_only', 'no' ) ) {
		update_option( 'woocommerce_store_pages_only', 'no' );
	}
}
add_action( 'init', 'ruwah_publish_storefront', 1 );

/**
 * Return the WooCommerce shop URL with a safe fallback.
 *
 * @return string
 */
function ruwah_shop_url() {
	if ( function_exists( 'wc_get_page_id' ) ) {
		$shop_id = wc_get_page_id( 'shop' );
		if ( $shop_id > 0 ) {
			return get_permalink( $shop_id );
		}
	}

	return home_url( '/shop/' );
}

/**
 * Display the slim announcement bar above the parent-theme header.
 */
function ruwah_announcement_bar() {
	if ( is_admin() ) {
		return;
	}
	?>
	<div class="ruwah-announcement" role="region" aria-label="Store announcement">
		<div class="ruwah-announcement__inner">
			<p><?php echo esc_html__( 'Complimentary delivery above PKR 5,000', 'nub-ruwah' ); ?></p>
			<div class="ruwah-announcement__links">
				<a href="<?php echo esc_url( home_url( '/track-order/' ) ); ?>"><?php echo esc_html__( 'Track order', 'nub-ruwah' ); ?></a>
				<a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>"><?php echo esc_html__( 'Customer care', 'nub-ruwah' ); ?></a>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_body_open', 'ruwah_announcement_bar', 5 );

/**
 * Use a focused front-page document title.
 *
 * @param array<string,string> $parts Document title parts.
 * @return array<string,string>
 */
function ruwah_front_page_title( $parts ) {
	if ( is_front_page() ) {
		$parts['title'] = __( 'Premium Skincare in Pakistan', 'nub-ruwah' );
		$parts['site']  = __( 'NUB by Ruwah Beauty', 'nub-ruwah' );
		unset( $parts['tagline'] );
	}

	return $parts;
}
add_filter( 'document_title_parts', 'ruwah_front_page_title' );

/**
 * Replace inherited starter-menu labels without mutating the menu database.
 *
 * @param array<int,WP_Post> $items Menu items.
 * @param stdClass           $args  Menu arguments.
 * @return array<int,WP_Post>
 */
function ruwah_primary_menu_items( $items, $args ) {
	if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
		return $items;
	}

	$map = array(
		'Home'     => array( __( 'Home', 'nub-ruwah' ), home_url( '/' ) ),
		'Services' => array( __( 'Shop', 'nub-ruwah' ), ruwah_shop_url() ),
		'About'    => array( __( 'New Arrivals', 'nub-ruwah' ), home_url( '/new-arrivals/' ) ),
		'Reviews'  => array( __( 'Best Sellers', 'nub-ruwah' ), home_url( '/best-sellers/' ) ),
		'Why Us'   => array( __( 'Rituals', 'nub-ruwah' ), home_url( '/#ritual' ) ),
		'Contact'  => array( __( 'Our Story', 'nub-ruwah' ), home_url( '/about-us/' ) ),
	);

	foreach ( $items as $item ) {
		$title = wp_strip_all_tags( $item->title );
		if ( isset( $map[ $title ] ) ) {
			$item->title = $map[ $title ][0];
			$item->url   = $map[ $title ][1];
		}
	}

	return $items;
}
add_filter( 'wp_nav_menu_objects', 'ruwah_primary_menu_items', 20, 2 );

/**
 * Replace Astra's unassigned mobile fallback with a concise storefront menu.
 *
 * @param string $menu Existing fallback markup.
 * @param array  $args Page-menu arguments.
 * @return string
 */
function ruwah_mobile_fallback_menu( $menu, $args ) {
	$links = array(
		__( 'Home', 'nub-ruwah' )         => home_url( '/' ),
		__( 'Shop', 'nub-ruwah' )         => ruwah_shop_url(),
		__( 'New Arrivals', 'nub-ruwah' ) => home_url( '/new-arrivals/' ),
		__( 'Best Sellers', 'nub-ruwah' ) => home_url( '/best-sellers/' ),
		__( 'Rituals', 'nub-ruwah' )      => home_url( '/#ritual' ),
		__( 'Our Story', 'nub-ruwah' )    => home_url( '/about-us/' ),
		__( 'My Account', 'nub-ruwah' )   => home_url( '/my-account/' ),
	);

	$output = '<ul class="main-header-menu ast-nav-menu ast-flex submenu-with-border astra-menu-animation-fade stack-on-mobile">';
	foreach ( $links as $label => $url ) {
		$output .= sprintf(
			'<li class="menu-item"><a class="menu-link" href="%1$s">%2$s</a></li>',
			esc_url( $url ),
			esc_html( $label )
		);
	}
	$output .= '</ul>';

	return $output;
}
add_filter( 'wp_page_menu', 'ruwah_mobile_fallback_menu', 20, 2 );

/**
 * Output lightweight front-page metadata and schema when no SEO plugin is active.
 */
function ruwah_front_page_meta() {
	if ( ! is_front_page() ) {
		return;
	}

	$description = __( 'Discover NUB by Ruwah Beauty: premium skincare in Pakistan for hydration, radiance, barrier support and thoughtful everyday rituals.', 'nub-ruwah' );
	$canonical   = home_url( '/' );
	$logo_id     = get_theme_mod( 'custom_logo' );
	$logo_url    = $logo_id ? wp_get_attachment_image_url( $logo_id, 'full' ) : '';
	$schema      = array(
		'@context' => 'https://schema.org',
		'@graph'   => array(
			array(
				'@type' => 'Organization',
				'@id'   => trailingslashit( $canonical ) . '#organization',
				'name'  => 'NUB by Ruwah Beauty',
				'url'   => $canonical,
				'logo'  => $logo_url,
			),
			array(
				'@type'           => 'WebSite',
				'@id'             => trailingslashit( $canonical ) . '#website',
				'url'             => $canonical,
				'name'            => 'NUB by Ruwah Beauty',
				'description'     => $description,
				'potentialAction' => array(
					'@type'       => 'SearchAction',
					'target'      => home_url( '/?s={search_term_string}&post_type=product' ),
					'query-input' => 'required name=search_term_string',
				),
			),
		),
	);
	?>
	<meta name="description" content="<?php echo esc_attr( $description ); ?>">
	<link rel="canonical" href="<?php echo esc_url( $canonical ); ?>">
	<meta property="og:type" content="website">
	<meta property="og:title" content="<?php echo esc_attr__( 'Premium Skincare in Pakistan | NUB by Ruwah Beauty', 'nub-ruwah' ); ?>">
	<meta property="og:description" content="<?php echo esc_attr( $description ); ?>">
	<meta property="og:url" content="<?php echo esc_url( $canonical ); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr__( 'NUB by Ruwah Beauty', 'nub-ruwah' ); ?>">
	<meta name="twitter:card" content="summary_large_image">
	<script type="application/ld+json"><?php echo wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ); ?></script>
	<?php
}
add_action( 'wp_head', 'ruwah_front_page_meta', 5 );

/**
 * Get a curated set of published products for the homepage.
 * Featured products are preferred, then the newest products fill the grid.
 *
 * @param int $limit Number of products.
 * @return array<int,WC_Product>
 */
function ruwah_get_home_products( $limit = 4 ) {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return array();
	}

	$limit    = max( 1, absint( $limit ) );
	$products = array();
	$used_ids = array();

	if ( function_exists( 'wc_get_featured_product_ids' ) ) {
		$featured_ids = array_values( array_filter( array_map( 'absint', wc_get_featured_product_ids() ) ) );
		if ( $featured_ids ) {
			$featured = wc_get_products(
				array(
					'status'  => 'publish',
					'limit'   => $limit,
					'include' => array_slice( $featured_ids, 0, $limit ),
					'orderby' => 'include',
				)
			);

			foreach ( $featured as $product ) {
				if ( $product instanceof WC_Product ) {
					$products[] = $product;
					$used_ids[] = $product->get_id();
				}
			}
		}
	}

	$remaining = $limit - count( $products );
	if ( $remaining > 0 ) {
		$latest = wc_get_products(
			array(
				'status'  => 'publish',
				'limit'   => $remaining,
				'exclude' => $used_ids,
				'orderby' => 'date',
				'order'   => 'DESC',
			)
		);

		foreach ( $latest as $product ) {
			if ( $product instanceof WC_Product ) {
				$products[] = $product;
			}
		}
	}

	return array_slice( $products, 0, $limit );
}

/**
 * Render one accessible homepage product card.
 *
 * @param WC_Product $product Product object.
 */
function ruwah_render_product_card( $product ) {
	if ( ! $product instanceof WC_Product ) {
		return;
	}

	$product_id  = $product->get_id();
	$product_url = get_permalink( $product_id );
	?>
	<article class="ruwah-product-card">
		<a class="ruwah-product-card__media" href="<?php echo esc_url( $product_url ); ?>" aria-label="<?php echo esc_attr( $product->get_name() ); ?>">
			<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'loading' => 'lazy', 'decoding' => 'async' ) ) ); ?>
			<span class="ruwah-product-card__tag"><?php echo esc_html( $product->is_featured() ? __( 'Featured', 'nub-ruwah' ) : __( 'New ritual', 'nub-ruwah' ) ); ?></span>
		</a>
		<div class="ruwah-product-card__body">
			<h3 class="ruwah-product-card__title"><a href="<?php echo esc_url( $product_url ); ?>"><?php echo esc_html( $product->get_name() ); ?></a></h3>
			<div class="ruwah-product-card__price"><?php echo wp_kses_post( $product->get_price_html() ); ?></div>
			<a
				class="ruwah-product-card__button add_to_cart_button ajax_add_to_cart"
				href="<?php echo esc_url( $product->add_to_cart_url() ); ?>"
				data-quantity="1"
				data-product_id="<?php echo esc_attr( $product_id ); ?>"
				data-product_sku="<?php echo esc_attr( $product->get_sku() ); ?>"
				aria-label="<?php echo esc_attr( $product->add_to_cart_description() ); ?>"
				rel="nofollow"
			><?php echo esc_html( $product->add_to_cart_text() ); ?></a>
		</div>
	</article>
	<?php
}
