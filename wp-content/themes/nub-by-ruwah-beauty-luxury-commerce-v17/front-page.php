<?php
defined( 'ABSPATH' ) || exit;
get_header();
$shop = ruwah_shop_url();
$rituals = ruwah_page_url( 'build-your-ritual' );
$story = ruwah_page_url( 'our-story' );
$account = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/my-account/' );
$cart = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' );
$products = ruwah_products( 12 );
$cart_count = function_exists( 'WC' ) && WC()->cart ? WC()->cart->get_cart_contents_count() : 0;
function rb_product_image( $products, $index, $class = '' ) {
    if ( empty( $products[ $index ] ) || ! $products[ $index ] instanceof WC_Product ) return;
    $p = $products[ $index ];
    echo '<a class="' . esc_attr( $class ) . '" href="' . esc_url( $p->get_permalink() ) . '">' . wp_kses_post( $p->get_image( 'woocommerce_single', array( 'alt' => $p->get_name() ) ) ) . '</a>';
}
?>
<div class="rb-page">
  <div class="rb-top">COMPLIMENTARY DELIVERY OVER PKR 5,000</div>
  <header class="rb-header">
    <div class="rb-header-inner">
      <button class="rb-menu rr-menu" aria-label="Open menu" aria-expanded="false"><span></span><span></span><span></span></button>
      <a class="rb-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><strong>RUWAH</strong><small>BEAUTY</small></a>
      <nav class="rb-nav rr-nav"><a href="<?php echo esc_url( $shop ); ?>">Shop</a><a href="<?php echo esc_url( $rituals ); ?>">Rituals</a><a href="#morning">Morning</a><a href="#night">Night</a><a href="<?php echo esc_url( $story ); ?>">Our Story</a></nav>
      <div class="rb-tools"><a href="<?php echo esc_url( home_url( '/?s=&post_type=product' ) ); ?>" aria-label="Search">⌕</a><a href="<?php echo esc_url( $account ); ?>" aria-label="Account">♙</a><a href="<?php echo esc_url( $cart ); ?>" aria-label="Cart">♡<b><?php echo esc_html( (string) $cart_count ); ?></b></a></div>
    </div>
  </header>
  <main>
    <section class="rb-hero">
      <div class="rb-curve"></div>
      <div class="rb-wrap rb-hero-grid">
        <div class="rb-hero-copy"><p class="rb-eyebrow">Simple steps. Powerful results.</p><h1>Build your<br>skincare ritual</h1><p>Thoughtful routines made for real skin, real schedules and everyday care.</p><div class="rb-actions"><a class="rb-btn rb-btn-dark" href="#rituals">Explore rituals</a><a class="rb-btn rb-btn-outline" href="<?php echo esc_url( $shop ); ?>">Shop products</a></div></div>
        <div class="rb-hero-art"><div class="rb-stone"></div><div class="rb-hero-products"><?php rb_product_image( $products, 0, 'rb-hp rb-hp-1' ); rb_product_image( $products, 1, 'rb-hp rb-hp-2' ); rb_product_image( $products, 2, 'rb-hp rb-hp-3' ); rb_product_image( $products, 3, 'rb-hp rb-hp-4' ); ?></div></div>
      </div>
    </section>
    <section class="rb-tabs"><div class="rb-wrap"><button class="is-active" data-ritual-tab="all">All rituals</button><button data-ritual-tab="morning">Morning</button><button data-ritual-tab="night">Night</button><button data-ritual-tab="weekly">Weekly</button></div></section>
    <section class="rb-rituals" id="rituals"><div class="rb-wrap">
      <article class="rb-card" id="morning" data-ritual-card="morning"><div class="rb-card-copy"><span>4–5 minutes</span><h2>The Daily Glow Ritual</h2><p>A simple daily routine to cleanse, hydrate and protect for healthy-looking skin.</p><ul><li>Cleanse</li><li>Treat</li><li>Moisturise</li><li>Protect</li></ul><a class="rb-btn rb-btn-green" href="<?php echo esc_url( $rituals ); ?>">View ritual</a></div><div class="rb-card-art rb-pink"><div class="rb-mini-stone"></div><?php rb_product_image( $products, 4, 'rb-cp rb-cp-a' ); rb_product_image( $products, 5, 'rb-cp rb-cp-b' ); ?></div></article>
      <article class="rb-card" id="night" data-ritual-card="night"><div class="rb-card-copy"><span>6–8 minutes</span><h2>Reset &amp; Recharge Ritual</h2><p>A deeply nourishing routine to restore hydration and strengthen the skin barrier.</p><ul><li>Cleanse</li><li>Restore</li><li>Treat</li><li>Moisturise</li></ul><a class="rb-btn rb-btn-green" href="<?php echo esc_url( $rituals ); ?>">View ritual</a></div><div class="rb-card-art rb-sand"><div class="rb-mini-stone"></div><?php rb_product_image( $products, 6, 'rb-cp rb-cp-a' ); rb_product_image( $products, 7, 'rb-cp rb-cp-b' ); rb_product_image( $products, 8, 'rb-cp rb-cp-c' ); ?></div></article>
      <article class="rb-card" data-ritual-card="weekly"><div class="rb-card-copy"><span>6–8 minutes</span><h2>Clear &amp; Balance Ritual</h2><p>A refining routine to support clearer, balanced-looking skin without compromising comfort.</p><ul><li>Cleanse</li><li>Exfoliate</li><li>Treat</li><li>Moisturise</li></ul><a class="rb-btn rb-btn-green" href="<?php echo esc_url( $rituals ); ?>">View ritual</a></div><div class="rb-card-art rb-sage"><div class="rb-mini-stone"></div><?php rb_product_image( $products, 9, 'rb-cp rb-cp-a' ); rb_product_image( $products, 10, 'rb-cp rb-cp-b' ); rb_product_image( $products, 11, 'rb-cp rb-cp-c' ); ?></div></article>
    </div></section>
    <section class="rb-quiz"><div class="rb-wrap rb-quiz-grid"><div><p class="rb-eyebrow">Not sure where to start?</p><h2>Find your perfect ritual.</h2><p>Answer a few simple questions and discover a routine matched to your skin goals.</p><a class="rb-btn rb-btn-outline" href="<?php echo esc_url( $rituals ); ?>">Take the quiz</a></div><div class="rb-drops"><i></i><i></i><i></i></div></div></section>
  </main>
  <footer class="rb-footer"><a class="rb-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><strong>RUWAH</strong><small>BEAUTY</small></a><p>Skincare rituals made for real life.</p><small>© <?php echo esc_html( gmdate( 'Y' ) ); ?> Ruwah Beauty</small></footer>
</div>
<?php get_footer(); ?>
