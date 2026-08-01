<?php
defined( 'ABSPATH' ) || exit;
get_header();

$shop       = ruwah_shop_url();
$rituals    = ruwah_page_url( 'build-your-ritual' );
$story      = ruwah_page_url( 'our-story' );
$account    = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : ruwah_page_url( 'my-account' );
$cart       = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : ruwah_page_url( 'cart' );
$products   = ruwah_products( 12 );
$cart_count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
$hero_set   = array_slice( $products, 0, 4 );
?>
<div class="ritual-page">
  <div class="ritual-topbar">Complimentary delivery over PKR 5,000</div>
  <header class="ritual-header">
    <div class="ritual-shell ritual-header-inner">
      <button class="ritual-menu rr-menu" type="button" aria-expanded="false" aria-label="Open menu"><i></i><i></i><i></i></button>
      <a class="ritual-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>"><strong>RUWAH</strong><span>BEAUTY</span></a>
      <nav class="ritual-nav rr-nav" aria-label="Primary navigation">
        <a href="<?php echo esc_url( $shop ); ?>">Shop</a>
        <a href="<?php echo esc_url( $rituals ); ?>">Rituals</a>
        <a href="#morning">Morning</a>
        <a href="#night">Night</a>
        <a href="<?php echo esc_url( $story ); ?>">Our Story</a>
      </nav>
      <div class="ritual-tools"><a href="<?php echo esc_url( home_url( '/?s=&post_type=product' ) ); ?>">⌕</a><a href="<?php echo esc_url( $account ); ?>">♡</a><a href="<?php echo esc_url( $cart ); ?>">Bag <b><?php echo esc_html( (string) $cart_count ); ?></b></a></div>
    </div>
  </header>

  <main>
    <section class="ritual-hero">
      <div class="ritual-wave"></div>
      <div class="ritual-shell ritual-hero-grid">
        <div class="ritual-hero-copy">
          <p class="ritual-eyebrow">Simple steps. Powerful results.</p>
          <h1>Build your<br>skincare ritual</h1>
          <p>Thoughtful routines for real skin. Choose a rhythm that fits your morning, evening and everyday life.</p>
          <div class="ritual-actions"><a class="ritual-btn ritual-btn-dark" href="#ritual-list">Explore rituals</a><a class="ritual-btn ritual-btn-light" href="<?php echo esc_url( $shop ); ?>">Shop products</a></div>
        </div>
        <div class="ritual-stage" aria-label="Ruwah skincare collection">
          <div class="ritual-pedestal"></div>
          <div class="ritual-products">
            <?php foreach ( $hero_set as $index => $product ) : ?>
              <a class="ritual-product ritual-product-<?php echo esc_attr( (string) ( $index + 1 ) ); ?>" href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo wp_kses_post( $product->get_image( 'woocommerce_single', array( 'alt' => $product->get_name() ) ) ); ?></a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </section>

    <section class="ritual-filter" aria-label="Ritual categories">
      <div class="ritual-shell ritual-tabs"><button class="is-active" data-ritual-tab="all">All rituals</button><button data-ritual-tab="morning">Morning</button><button data-ritual-tab="night">Night</button><button data-ritual-tab="weekly">Weekly</button></div>
    </section>

    <section class="ritual-list" id="ritual-list">
      <div class="ritual-shell">
        <article class="ritual-card" id="morning" data-ritual-card="morning">
          <div class="ritual-card-copy"><span>4–5 minutes</span><h2>The Daily Glow Ritual</h2><p>A simple daily routine to cleanse, hydrate and protect for healthy-looking skin.</p><ul><li>Cleanse</li><li>Treat</li><li>Moisturise</li><li>Protect</li></ul><a class="ritual-btn ritual-btn-green" href="<?php echo esc_url( $rituals ); ?>">View ritual</a></div>
          <div class="ritual-card-visual ritual-pink"><?php foreach ( array_slice( $products, 0, 2 ) as $product ) echo '<a href="' . esc_url( $product->get_permalink() ) . '">' . wp_kses_post( $product->get_image( 'woocommerce_single' ) ) . '</a>'; ?></div>
        </article>

        <article class="ritual-card" id="night" data-ritual-card="night">
          <div class="ritual-card-copy"><span>6–8 minutes</span><h2>Reset & Recharge Ritual</h2><p>A deeply nourishing routine to restore hydration and strengthen your skin barrier.</p><ul><li>Cleanse</li><li>Restore</li><li>Treat</li><li>Moisturise</li></ul><a class="ritual-btn ritual-btn-green" href="<?php echo esc_url( $rituals ); ?>">View ritual</a></div>
          <div class="ritual-card-visual ritual-sand"><?php foreach ( array_slice( $products, 2, 3 ) as $product ) echo '<a href="' . esc_url( $product->get_permalink() ) . '">' . wp_kses_post( $product->get_image( 'woocommerce_single' ) ) . '</a>'; ?></div>
        </article>

        <article class="ritual-card" data-ritual-card="weekly">
          <div class="ritual-card-copy"><span>6–8 minutes</span><h2>Clear & Balance Ritual</h2><p>A refining routine to support clearer, balanced-looking skin without compromising comfort.</p><ul><li>Cleanse</li><li>Exfoliate</li><li>Treat</li><li>Moisturise</li></ul><a class="ritual-btn ritual-btn-green" href="<?php echo esc_url( $rituals ); ?>">View ritual</a></div>
          <div class="ritual-card-visual ritual-sage"><?php foreach ( array_slice( $products, 5, 3 ) as $product ) echo '<a href="' . esc_url( $product->get_permalink() ) . '">' . wp_kses_post( $product->get_image( 'woocommerce_single' ) ) . '</a>'; ?></div>
        </article>
      </div>
    </section>

    <section class="ritual-quiz">
      <div class="ritual-shell ritual-quiz-grid"><div><p class="ritual-eyebrow">Not sure where to start?</p><h2>Find your perfect ritual.</h2><p>Answer a few simple questions and discover a routine matched to your skin goals.</p><a class="ritual-btn ritual-btn-light" href="<?php echo esc_url( $rituals ); ?>">Take the quiz</a></div><div class="ritual-texture"><span></span><span></span><span></span></div></div>
    </section>
  </main>

  <footer class="ritual-footer"><div class="ritual-shell"><a class="ritual-logo ritual-logo-footer" href="<?php echo esc_url( home_url( '/' ) ); ?>"><strong>RUWAH</strong><span>BEAUTY</span></a><p>Skincare rituals made for real life.</p><small>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Ruwah Beauty</small></div></footer>
</div>
<?php get_footer(); ?>