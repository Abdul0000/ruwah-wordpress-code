<?php
defined( 'ABSPATH' ) || exit;
get_header();

$shop      = ruwah_shop_url();
$rituals   = ruwah_page_url( 'build-your-ritual' );
$story     = ruwah_page_url( 'our-story' );
$account   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : ruwah_page_url( 'my-account' );
$cart      = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : ruwah_page_url( 'cart' );
$products  = ruwah_products( 10 );
$hero      = $products[0] ?? null;
$hero_two  = $products[1] ?? null;
$hero_three= $products[2] ?? null;
$cart_count= function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
?>
<div class="rr-page">
    <div class="rr-announcement"><div class="rr-shell"><span>Free delivery on qualifying orders</span><span>Two samples with every order</span><span>Thoughtful formulas for real skin</span><span>Easy returns</span></div></div>
    <header class="rr-header">
        <div class="rr-shell rr-header-row">
            <button class="rr-menu" type="button" aria-expanded="false" aria-label="Open menu"><i></i><i></i><i></i></button>
            <a class="rr-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><strong>RUWAH</strong><small>BEAUTY</small></a>
            <nav class="rr-nav" aria-label="Primary navigation">
                <a href="<?php echo esc_url( $shop ); ?>">Shop</a>
                <a href="#concerns">Skin Concerns</a>
                <a href="<?php echo esc_url( $rituals ); ?>">Rituals</a>
                <a href="#ingredients">Ingredients</a>
                <a href="<?php echo esc_url( $story ); ?>">Our Story</a>
            </nav>
            <div class="rr-tools">
                <a href="<?php echo esc_url( home_url( '/?s=&post_type=product' ) ); ?>" aria-label="Search">⌕</a>
                <a href="<?php echo esc_url( $account ); ?>" aria-label="Account">♙</a>
                <a href="<?php echo esc_url( $cart ); ?>" aria-label="Cart">♧<b><?php echo esc_html( (string) $cart_count ); ?></b></a>
            </div>
        </div>
    </header>

    <main>
        <section class="rr-hero">
            <div class="rr-shell rr-hero-grid">
                <div class="rr-hero-copy">
                    <p class="rr-kicker">Simple steps. Powerful results.</p>
                    <h1>Build your<br>skincare ritual</h1>
                    <p>A thoughtful routine built around your real skin, your pace, and the products already available in the Ruwah collection.</p>
                    <div class="rr-actions"><a class="rr-button" href="<?php echo esc_url( $rituals ); ?>">Find your ritual</a><a class="rr-button rr-button-light" href="<?php echo esc_url( $shop ); ?>">Shop all products</a></div>
                    <div class="rr-hero-points"><span>Thoughtful ingredients</span><span>Clean & effective</span><span>Cruelty-conscious</span></div>
                </div>
                <div class="rr-hero-stage">
                    <div class="rr-blob"></div>
                    <div class="rr-products-stage">
                        <?php foreach ( array_filter( array( $hero, $hero_two, $hero_three ) ) as $product ) : ?>
                            <a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo wp_kses_post( $product->get_image( 'woocommerce_single', array( 'alt' => $product->get_name() ) ) ); ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </section>

        <section class="rr-tabs-wrap">
            <div class="rr-shell rr-tabs" role="tablist" aria-label="Ritual filters">
                <button class="is-active" type="button" data-ritual-tab="all">All rituals</button>
                <button type="button" data-ritual-tab="morning">Morning</button>
                <button type="button" data-ritual-tab="night">Night</button>
                <button type="button" data-ritual-tab="weekly">Weekly</button>
            </div>
        </section>

        <section class="rr-section rr-rituals">
            <div class="rr-shell">
                <div class="rr-section-head"><div><p class="rr-kicker">Made for real routines</p><h2>Choose your ritual</h2></div><a href="<?php echo esc_url( $rituals ); ?>">View all rituals →</a></div>
                <div class="rr-ritual-grid">
                    <article class="rr-ritual-card" data-ritual-card="morning"><div><span>04 minutes</span><h3>The Daily Glow Ritual</h3><p>Cleanse, treat, moisturise and protect for a calm, balanced start.</p><ul><li>Cleanse</li><li>Treat</li><li>Moisturise</li><li>Protect</li></ul><a class="rr-small-button" href="<?php echo esc_url( $rituals ); ?>">View ritual</a></div><div class="rr-ritual-image"><?php if ( $hero ) echo wp_kses_post( $hero->get_image( 'woocommerce_single' ) ); ?></div></article>
                    <article class="rr-ritual-card" data-ritual-card="night"><div><span>06 minutes</span><h3>Reset & Recharge</h3><p>A nourishing evening routine that supports comfort and hydration.</p><ul><li>Cleanse</li><li>Restore</li><li>Treat</li><li>Moisturise</li></ul><a class="rr-small-button" href="<?php echo esc_url( $rituals ); ?>">View ritual</a></div><div class="rr-ritual-image"><?php if ( $hero_two ) echo wp_kses_post( $hero_two->get_image( 'woocommerce_single' ) ); ?></div></article>
                    <article class="rr-ritual-card" data-ritual-card="weekly"><div><span>08 minutes</span><h3>Clear & Balance</h3><p>A focused weekly ritual to refresh, rebalance and simplify your routine.</p><ul><li>Cleanse</li><li>Exfoliate</li><li>Treat</li><li>Moisturise</li></ul><a class="rr-small-button" href="<?php echo esc_url( $rituals ); ?>">View ritual</a></div><div class="rr-ritual-image"><?php if ( $hero_three ) echo wp_kses_post( $hero_three->get_image( 'woocommerce_single' ) ); ?></div></article>
                </div>
            </div>
        </section>

        <section id="concerns" class="rr-section rr-concerns">
            <div class="rr-shell"><div class="rr-section-head"><div><p class="rr-kicker">Start with what your skin needs</p><h2>Shop by concern</h2></div><a href="<?php echo esc_url( $shop ); ?>">Shop all →</a></div>
                <div class="rr-concern-grid">
                    <?php
                    $concerns = array(
                        array( 'Hydration', 'Support a softer, more comfortable skin barrier.', 'blue' ),
                        array( 'Uneven Tone', 'Reveal a brighter and more even-looking finish.', 'cream' ),
                        array( 'Dullness', 'Bring radiance back to tired-looking skin.', 'peach' ),
                        array( 'Sensitive Skin', 'Soothe, simplify and strengthen your routine.', 'sage' ),
                        array( 'Breakouts', 'Clarify without overwhelming the skin.', 'olive' ),
                        array( 'Fine Lines', 'Support smoothness and lasting hydration.', 'lilac' ),
                    );
                    foreach ( $concerns as $concern ) : ?>
                        <a class="rr-concern rr-<?php echo esc_attr( $concern[2] ); ?>" href="<?php echo esc_url( add_query_arg( 's', $concern[0], $shop ) ); ?>"><i></i><h3><?php echo esc_html( $concern[0] ); ?></h3><p><?php echo esc_html( $concern[1] ); ?></p><span>→</span></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="rr-section rr-products">
            <div class="rr-shell"><div class="rr-section-head"><div><p class="rr-kicker">Current collection</p><h2>Your glow lineup</h2></div><a href="<?php echo esc_url( $shop ); ?>">Shop all products →</a></div>
                <div class="rr-product-grid"><?php foreach ( array_slice( $products, 0, 8 ) as $product ) { ruwah_product_card( $product ); } ?></div>
            </div>
        </section>

        <section id="ingredients" class="rr-values">
            <div class="rr-shell rr-values-grid"><div><b>01</b><h3>Thoughtful ingredients</h3><p>Every formula should have a reason to be in your ritual.</p></div><div><b>02</b><h3>Clean & effective</h3><p>Simple, focused products designed for daily consistency.</p></div><div><b>03</b><h3>Cruelty-conscious</h3><p>Care that reflects both skin and values.</p></div><div><b>04</b><h3>Made for real life</h3><p>Routines that fit your morning, evening and budget.</p></div></div>
        </section>

        <section class="rr-newsletter"><div class="rr-shell rr-newsletter-grid"><div><p class="rr-kicker">Inside the Ruwah ritual</p><h2>Join the glow list</h2><p>New launches, skincare education and routine inspiration.</p></div><form method="post" action="#"><label class="screen-reader-text" for="rr-email">Email address</label><input id="rr-email" type="email" name="email" placeholder="Enter your email" required><button type="submit">Join now</button></form></div></section>
    </main>

    <footer class="rr-footer"><div class="rr-shell rr-footer-grid"><div><a class="rr-logo rr-logo-footer" href="<?php echo esc_url( home_url( '/' ) ); ?>"><strong>RUWAH</strong><small>BEAUTY</small></a><p>Skincare made for the skin you live in.</p></div><div><h3>Shop</h3><a href="<?php echo esc_url( $shop ); ?>">All products</a><a href="#concerns">Skin concerns</a><a href="<?php echo esc_url( $rituals ); ?>">Rituals</a></div><div><h3>About</h3><a href="<?php echo esc_url( $story ); ?>">Our story</a><a href="<?php echo esc_url( ruwah_page_url( 'faq' ) ); ?>">FAQ</a><a href="<?php echo esc_url( ruwah_page_url( 'contact' ) ); ?>">Contact</a></div><div><h3>Account</h3><a href="<?php echo esc_url( $account ); ?>">My account</a><a href="<?php echo esc_url( $cart ); ?>">Cart</a><a href="<?php echo esc_url( ruwah_page_url( 'checkout' ) ); ?>">Checkout</a></div></div><div class="rr-shell rr-legal"><span>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Ruwah Beauty</span><span>Privacy · Terms · Accessibility</span></div></footer>
</div>
<?php get_footer(); ?>
