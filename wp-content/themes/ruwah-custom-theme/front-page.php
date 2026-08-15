<?php
defined('ABSPATH')||exit;
get_header();
$products=ruwah_products(12);
$hero=$products[0]??null;
$categories=function_exists('get_terms')?get_terms(['taxonomy'=>'product_cat','hide_empty'=>true,'number'=>8,'exclude'=>[get_option('default_product_cat')]]):[];
?>
<section class="rb-hero">
  <div class="rb-hero-media"><?php echo $hero?wp_kses_post($hero->get_image('full',['loading'=>'eager'])):''; ?></div>
  <div class="rb-shell rb-hero-copy">
    <span class="rb-kicker">RUWAH BEAUTY · SKINCARE RITUALS</span>
    <h1>Thoughtful skincare for naturally confident skin.</h1>
    <p>Simple, purposeful routines designed to cleanse, treat, hydrate and protect—without making skincare feel complicated.</p>
    <div class="rb-actions">
      <a class="rb-button" href="<?php echo esc_url(add_query_arg('orderby','popularity',ruwah_shop_url())); ?>">Shop bestsellers</a>
      <a class="rb-button rb-button--light" href="<?php echo esc_url(ruwah_page_url('routine-builder')); ?>">Find my routine</a>
    </div>
    <div class="rb-proof"><span>Pakistan-wide delivery</span><span>Secure checkout</span><span>Customer support</span></div>
  </div>
</section>
<section class="rb-section rb-category-slider-section">
  <div class="rb-shell"><div class="rb-category-slider" data-category-slider>
    <button class="rb-category-arrow rb-category-arrow--prev" type="button" data-category-prev aria-label="Previous categories">‹</button>
    <div class="rb-category-viewport"><div class="rb-category-row" data-category-track>
      <?php if(!is_wp_error($categories))foreach($categories as $cat){$thumb=get_term_meta($cat->term_id,'thumbnail_id',true);echo '<a class="rb-category-card" href="'.esc_url(get_term_link($cat)).'"><span>'.($thumb?wp_get_attachment_image($thumb,'woocommerce_thumbnail'):'◇').'</span><b>'.esc_html($cat->name).'</b></a>';} ?>
    </div></div>
    <button class="rb-category-arrow rb-category-arrow--next" type="button" data-category-next aria-label="Next categories">›</button>
  </div></div>
</section>
<script id="ruwah-category-loop-script">document.addEventListener('DOMContentLoaded',function(){document.querySelectorAll('[data-category-slider]').forEach(function(s){var t=s.querySelector('[data-category-track]'),p=s.querySelector('[data-category-prev]'),n=s.querySelector('[data-category-next]');if(!t)return;var a=Array.from(t.children),i=0,b=false;function v(){return innerWidth<=560?2:(innerWidth<=980?3:5)}function g(){return parseFloat(getComputedStyle(t).gap)||0}function w(){var c=t.querySelector('.rb-category-card');return c?c.getBoundingClientRect().width+g():0}function paint(x){t.style.transition=x?'transform .5s ease':'none';t.style.transform='translate3d('+(-i*w())+'px,0,0)'}function build(){i=0;t.innerHTML='';a.forEach(function(c){t.appendChild(c)});a.slice(0,v()).forEach(function(c){t.appendChild(c.cloneNode(true))});paint(false)}function go(d){if(b||a.length<=v())return;b=true;if(d>0){i++;paint(true);setTimeout(function(){if(i===a.length){i=0;paint(false)}b=false},520)}else{if(i===0){i=a.length;paint(false)}requestAnimationFrame(function(){i--;paint(true);setTimeout(function(){b=false},520)})}}build();n&&n.addEventListener('click',function(){go(1)});p&&p.addEventListener('click',function(){go(-1)});var r;addEventListener('resize',function(){clearTimeout(r);r=setTimeout(build,180)});});});</script>
<section class="rb-section rb-section--blush"><div class="rb-shell">
  <header class="rb-section-head rb-section-head--split"><div><span class="rb-kicker">FRESHLY ADDED</span><h2>New arrivals</h2></div><a class="rb-text-link" href="<?php echo esc_url(add_query_arg('orderby','date',ruwah_shop_url())); ?>">View all products</a></header>
  <div class="rb-product-grid"><?php foreach(array_slice($products,0,8) as $p)ruwah_product_card($p,'New'); ?></div>
</div></section>
<section class="rb-section"><div class="rb-shell rb-promo-grid">
  <article class="rb-promo"><span class="rb-kicker" style="color:#fff">PERSONALISED CARE</span><h3>Build your glow routine.</h3><p>Choose a simple routine around your skin type and concern.</p><a class="rb-button rb-button--light" href="<?php echo esc_url(ruwah_page_url('routine-builder')); ?>">Start routine finder</a></article>
  <article class="rb-promo"><span class="rb-kicker">BETTER TOGETHER</span><h3>Save more with bundles.</h3><p>Complete routines with products designed to work together.</p><a class="rb-button" href="<?php echo esc_url(ruwah_page_url('bundles')); ?>">Shop bundles</a></article>
</div></section>
<section class="rb-section rb-section--blush"><div class="rb-shell">
  <header class="rb-section-head"><span class="rb-kicker">SHOP BY CONCERN</span><h2>Care focused on what matters to you.</h2></header>
  <div class="rb-concern-grid"><?php foreach(['Acne & Breakouts','Pigmentation','Dryness','Dullness','Fine Lines','Sensitive Skin'] as $concern)echo '<a class="rb-concern-card" href="'.esc_url(add_query_arg('s',$concern,ruwah_shop_url())).'"><span class="rb-kicker">EXPLORE CARE</span><strong>'.esc_html($concern).'</strong></a>'; ?></div>
</div></section>
<?php if($hero): $rating=(float)$hero->get_average_rating();$count=(int)$hero->get_review_count(); ?>
<section class="rb-section"><div class="rb-shell rb-feature">
  <div class="rb-feature-media"><?php echo wp_kses_post($hero->get_image('full')); ?></div>
  <div class="rb-feature-copy"><span class="rb-kicker">FEATURED BESTSELLER</span><h2><?php echo esc_html($hero->get_name()); ?></h2>
    <?php if($count>0): ?><div class="rb-card-rating"><span aria-hidden="true"><?php echo esc_html(str_repeat('★',(int)round($rating)).str_repeat('☆',5-(int)round($rating))); ?></span><span class="rb-rating-count"><?php echo esc_html(number_format_i18n($rating,1).' · '.$count.' reviews'); ?></span></div><?php endif; ?>
    <p><?php echo wp_kses_post(wp_trim_words($hero->get_short_description()?:$hero->get_description(),28)); ?></p>
    <div class="rb-price"><?php echo wp_kses_post($hero->get_price_html()); ?></div>
    <ul><li>Lightweight everyday protection</li><li>Niacinamide and hyaluronic acid</li><li>Easy to layer into your routine</li></ul>
    <div class="rb-actions">
      <?php if($hero->is_purchasable()&&$hero->is_in_stock()): ?><a rel="nofollow" data-product_id="<?php echo esc_attr((string)$hero->get_id()); ?>" data-quantity="1" class="rb-button add_to_cart_button ajax_add_to_cart" href="<?php echo esc_url($hero->add_to_cart_url()); ?>">Add to Cart</a><?php endif; ?>
      <a class="rb-button rb-button--light" href="<?php echo esc_url($hero->get_permalink()); ?>">View details</a>
    </div>
  </div>
</div></section>
<?php endif; ?>
<section class="rb-section rb-section--blush"><div class="rb-shell rb-trust">
  <div><b>Authentic products</b><span>Original Ruwah Beauty formulas</span></div><div><b>Quality tested</b><span>Clear product information</span></div><div><b>Secure checkout</b><span>Protected payment experience</span></div><div><b>Pakistan-wide delivery</b><span>Shipping across Pakistan</span></div>
</div></section>
<section class="rb-section"><div class="rb-shell">
  <header class="rb-section-head rb-section-head--split"><div><span class="rb-kicker">SHOP BY INGREDIENT</span><h2>Discover formulas by key ingredient.</h2></div><a class="rb-text-link" href="<?php echo esc_url(ruwah_shop_url()); ?>">View all products</a></header>
  <div class="rb-ingredient-grid"><?php foreach(['Niacinamide','Hyaluronic Acid','Vitamin C','Retinol'] as $ingredient)echo '<a class="rb-ingredient-card" href="'.esc_url(add_query_arg('s',$ingredient,ruwah_shop_url())).'"><span class="rb-kicker">INGREDIENT GUIDE</span><strong>'.esc_html($ingredient).'</strong></a>'; ?></div>
</div></section>
<section class="rb-section rb-section--blush"><div class="rb-shell rb-newsletter">
  <div><span class="rb-kicker" style="color:#F1E8F8">JOIN THE RUWAH COMMUNITY</span><h2>Your best skin starts in your inbox.</h2><p>Product updates, routines and skincare education.</p></div>
  <form action="<?php echo esc_url(ruwah_page_url('contact')); ?>" method="get"><input type="email" name="email" placeholder="Your email address" required><button class="rb-button rb-button--light">Join the community</button></form>
</div></section>
<?php get_footer(); ?>