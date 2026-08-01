<?php
/**
 * Premium front page for NUB by Ruwah Beauty.
 *
 * @package Nub_Ruwah
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$shop_url      = function_exists( 'ruwah_shop_url' ) ? ruwah_shop_url() : home_url( '/shop/' );
$products      = function_exists( 'ruwah_get_home_products' ) ? ruwah_get_home_products( 4 ) : array();
$hero_product  = ! empty( $products ) ? $products[0] : null;
$product_count = wp_count_posts( 'product' );
$published     = isset( $product_count->publish ) ? absint( $product_count->publish ) : 0;
$display_count = str_pad( (string) max( 1, $published ), 2, '0', STR_PAD_LEFT );
$concerns      = array(
	array(
		'title' => __( 'Deep hydration', 'nub-ruwah' ),
		'copy'  => __( 'Replenish moisture and support a soft, comfortable complexion.', 'nub-ruwah' ),
		'query' => 'hydration',
	),
	array(
		'title' => __( 'Visible radiance', 'nub-ruwah' ),
		'copy'  => __( 'Bring clarity and luminosity back to tired-looking skin.', 'nub-ruwah' ),
		'query' => 'brightening',
	),
	array(
		'title' => __( 'Balanced skin', 'nub-ruwah' ),
		'copy'  => __( 'Refine the look of congestion without compromising comfort.', 'nub-ruwah' ),
		'query' => 'acne',
	),
	array(
		'title' => __( 'Barrier support', 'nub-ruwah' ),
		'copy'  => __( 'Strengthen the everyday ritual for resilient, calmer-looking skin.', 'nub-ruwah' ),
		'query' => 'barrier',
	),
);
$steps = array(
	array(
		'title' => __( 'Cleanse', 'nub-ruwah' ),
		'copy'  => __( 'Begin with a gentle reset that removes the day while respecting your skin.', 'nub-ruwah' ),
	),
	array(
		'title' => __( 'Treat', 'nub-ruwah' ),
		'copy'  => __( 'Apply a focused serum to address the concern that matters most to you.', 'nub-ruwah' ),
	),
	array(
		'title' => __( 'Hydrate', 'nub-ruwah' ),
		'copy'  => __( 'Seal in comfort with balanced moisture and barrier-supporting care.', 'nub-ruwah' ),
	),
	array(
		'title' => __( 'Protect', 'nub-ruwah' ),
		'copy'  => __( 'Complete the morning ritual with daily sun protection.', 'nub-ruwah' ),
	),
);
?>

<main id="primary" class="site-main ruwah-home">
	<section class="ruwah-hero" aria-labelledby="ruwah-hero-title">
		<div class="ruwah-shell">
			<div class="ruwah-hero__grid">
				<div class="ruwah-hero__content">
					<p class="ruwah-kicker"><?php echo esc_html__( 'NUB by Ruwah Beauty', 'nub-ruwah' ); ?></p>
					<h1 id="ruwah-hero-title" class="ruwah-title"><?php echo wp_kses_post( __( 'Skincare,<br><em>elevated.</em>', 'nub-ruwah' ) ); ?></h1>
					<p class="ruwah-copy"><?php echo esc_html__( 'Thoughtful formulas for hydration, radiance and barrier support—designed to make effective skincare feel beautifully uncomplicated.', 'nub-ruwah' ); ?></p>
					<div class="ruwah-actions">
						<a class="ruwah-button" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html__( 'Shop the collection', 'nub-ruwah' ); ?></a>
						<a class="ruwah-button ruwah-button--ghost" href="#ritual"><?php echo esc_html__( 'Build your ritual', 'nub-ruwah' ); ?></a>
					</div>
				</div>

				<div class="ruwah-hero__visual" aria-label="Featured skincare product">
					<div class="ruwah-hero__frame">
						<?php if ( $hero_product instanceof WC_Product && $hero_product->get_image_id() ) : ?>
							<a href="<?php echo esc_url( get_permalink( $hero_product->get_id() ) ); ?>">
								<?php
								echo wp_get_attachment_image(
									$hero_product->get_image_id(),
									'large',
									false,
									array(
										'class'         => 'ruwah-hero__image',
										'loading'       => 'eager',
										'fetchpriority' => 'high',
										'decoding'      => 'async',
										'alt'           => $hero_product->get_name(),
									)
								);
								?>
							</a>
						<?php else : ?>
							<div class="ruwah-hero__fallback"><?php echo esc_html__( 'NUB', 'nub-ruwah' ); ?></div>
						<?php endif; ?>
					</div>
					<div class="ruwah-hero__badge"><?php echo esc_html__( 'Made for an intentional daily ritual', 'nub-ruwah' ); ?></div>
				</div>
			</div>

			<div class="ruwah-metrics" aria-label="Collection highlights">
				<div class="ruwah-metric"><strong><?php echo esc_html( $display_count ); ?></strong><span><?php echo esc_html__( 'Focused products', 'nub-ruwah' ); ?></span></div>
				<div class="ruwah-metric"><strong>04</strong><span><?php echo esc_html__( 'Essential steps', 'nub-ruwah' ); ?></span></div>
				<div class="ruwah-metric"><strong>03</strong><span><?php echo esc_html__( 'Quality priorities', 'nub-ruwah' ); ?></span></div>
			</div>
		</div>
	</section>

	<section class="ruwah-section" aria-labelledby="ruwah-products-title">
		<div class="ruwah-shell">
			<div class="ruwah-section__head">
				<div>
					<p class="ruwah-kicker"><?php echo esc_html__( 'Selected essentials', 'nub-ruwah' ); ?></p>
					<h2 id="ruwah-products-title" class="ruwah-title"><?php echo wp_kses_post( __( 'A focused edit for<br><em>everyday skin.</em>', 'nub-ruwah' ) ); ?></h2>
				</div>
				<a class="ruwah-text-link" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html__( 'View all products', 'nub-ruwah' ); ?></a>
			</div>

			<div class="ruwah-product-grid">
				<?php if ( $products ) : ?>
					<?php foreach ( $products as $product ) : ?>
						<?php ruwah_render_product_card( $product ); ?>
					<?php endforeach; ?>
				<?php else : ?>
					<div class="ruwah-empty">
						<p><?php echo esc_html__( 'The collection is being prepared. Explore the shop for available skincare.', 'nub-ruwah' ); ?></p>
						<a class="ruwah-button" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html__( 'Visit shop', 'nub-ruwah' ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="ruwah-section ruwah-section--dark" aria-labelledby="ruwah-concerns-title">
		<div class="ruwah-shell">
			<div class="ruwah-section__head">
				<div>
					<p class="ruwah-kicker" style="color:#d6b38f"><?php echo esc_html__( 'Shop by need', 'nub-ruwah' ); ?></p>
					<h2 id="ruwah-concerns-title" class="ruwah-title" style="color:#fff"><?php echo wp_kses_post( __( 'Start with what your<br><em style="color:#d6b38f">skin is asking for.</em>', 'nub-ruwah' ) ); ?></h2>
				</div>
			</div>

			<div class="ruwah-concern-grid">
				<?php foreach ( $concerns as $index => $concern ) : ?>
					<a class="ruwah-concern" href="<?php echo esc_url( add_query_arg( array( 's' => $concern['query'], 'post_type' => 'product' ), home_url( '/' ) ) ); ?>">
						<span class="ruwah-concern__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></span>
						<h3><?php echo esc_html( $concern['title'] ); ?></h3>
						<p><?php echo esc_html( $concern['copy'] ); ?></p>
					</a>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section id="ritual" class="ruwah-section ruwah-section--cream" aria-labelledby="ruwah-ritual-title">
		<div class="ruwah-shell ruwah-ritual">
			<div class="ruwah-ritual__intro">
				<p class="ruwah-kicker"><?php echo esc_html__( 'The daily ritual', 'nub-ruwah' ); ?></p>
				<h2 id="ruwah-ritual-title" class="ruwah-title"><?php echo wp_kses_post( __( 'Four steps.<br><em>One clear rhythm.</em>', 'nub-ruwah' ) ); ?></h2>
				<p class="ruwah-copy"><?php echo esc_html__( 'A consistent routine does not need to be complicated. Begin with the essentials, then adapt them to your skin.', 'nub-ruwah' ); ?></p>
				<div class="ruwah-actions"><a class="ruwah-button" href="<?php echo esc_url( $shop_url ); ?>"><?php echo esc_html__( 'Explore the ritual', 'nub-ruwah' ); ?></a></div>
			</div>

			<div class="ruwah-ritual__steps">
				<?php foreach ( $steps as $index => $step ) : ?>
					<article class="ruwah-step">
						<div class="ruwah-step__number"><?php echo esc_html( str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT ) ); ?></div>
						<div>
							<h3><?php echo esc_html( $step['title'] ); ?></h3>
							<p><?php echo esc_html( $step['copy'] ); ?></p>
						</div>
					</article>
				<?php endforeach; ?>
			</div>
		</div>
	</section>

	<section class="ruwah-manifesto" aria-labelledby="ruwah-manifesto-title">
		<div class="ruwah-shell">
			<p class="ruwah-kicker"><?php echo esc_html__( 'Skincare with intention', 'nub-ruwah' ); ?></p>
			<h2 id="ruwah-manifesto-title" class="ruwah-title"><?php echo wp_kses_post( __( 'Less noise.<br><em>More considered care.</em>', 'nub-ruwah' ) ); ?></h2>
			<p class="ruwah-copy"><?php echo esc_html__( 'NUB by Ruwah Beauty brings together purposeful formulas, clear routines and an elevated everyday experience.', 'nub-ruwah' ); ?></p>
			<div class="ruwah-actions"><a class="ruwah-button" href="<?php echo esc_url( home_url( '/our-story/' ) ); ?>"><?php echo esc_html__( 'Discover our story', 'nub-ruwah' ); ?></a></div>
		</div>
	</section>
</main>

<?php
get_footer();
