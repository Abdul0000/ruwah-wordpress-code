<?php
defined('ABSPATH') || exit;
get_header();
$products = ruwa_products(12);
$rituals = [
    ['morning', 'n01', 'DAILY GLOW RITUAL', 'Wake up your glow.', 'Fresh hydration, brightening support and daily protection for skin that feels ready for the day.', 0],
    ['night', 'n02', 'RESET & RECHARGE RITUAL', 'Reset while you rest.', 'Comforting textures and barrier-first care designed to replenish skin overnight.', 1],
    ['weekly', 'n03', 'CLEAR & BALANCE RITUAL', 'Make space for balance.', 'A focused reset for smoother texture, clearer-looking pores and renewed softness.', 2],
];
?>
<section class="ruwa-hero" data-ritual-switcher>
  <div class="ruwa-grain"></div>
  <div class="ruwa-shell ruwa-hero-grid">
    <div class="ruwa-hero-copy ruwa-reveal">
      <span class="ruwa-eyebrow">SMALL-BATCH SKINCARE</span>
      <div class="ruwa-ritual-tabs" role="tablist" aria-label="<?php esc_attr_e('Choose a ritual', 'nub-ruwah'); ?>">
        <?php foreach ($rituals as $index => $ritual) : ?>
          <button type="button" role="tab" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" data-ritual-tab="<?php echo esc_attr($ritual[0]); ?>"><small><?php echo esc_html($ritual[1]); ?></small><?php echo esc_html(ucfirst($ritual[0])); ?></button>
        <?php endforeach; ?>
      </div>
      <?php foreach ($rituals as $index => $ritual) : ?>
        <article class="ruwa-ritual-panel<?php echo $index === 0 ? ' is-active' : ''; ?>" data-ritual-panel="<?php echo esc_attr($ritual[0]); ?>">
          <span><?php echo esc_html($ritual[2]); ?></span><h1><?php echo esc_html($ritual[3]); ?></h1><p><?php echo esc_html($ritual[4]); ?></p>
          <a class="ruwa-button ruwa-button-primary" href="<?php echo esc_url(ruwa_shop_url()); ?>"><?php esc_html_e('Shop now', 'nub-ruwah'); ?></a>
        </article>
      <?php endforeach; ?>
      <div class="ruwa-switch-controls"><button type="button" data-ritual-prev aria-label="<?php esc_attr_e('Previous ritual', 'nub-ruwah'); ?>">←</button><button type="button" data-ritual-next aria-label="<?php esc_attr_e('Next ritual', 'nub-ruwah'); ?>">→</button></div>
    </div>
    <div class="ruwa-hero-visual ruwa-reveal" data-ritual-visual>
      <div class="ruwa-glow"></div><div class="ruwa-pedestal"></div>
      <svg class="ruwa-botanical botanical-one" viewBox="0 0 120 90" aria-hidden="true"><path d="M12 75C44 52 51 20 103 12M38 58C27 43 24 31 28 18M63 39C78 35 91 38 104 48"/></svg>
      <svg class="ruwa-botanical botanical-two" viewBox="0 0 100 100" aria-hidden="true"><circle cx="50" cy="50" r="36"/><path d="M50 14v72M14 50h72M25 25l50 50M75 25L25 75"/></svg>
      <?php foreach ($rituals as $index => $ritual) : $product = $products[$ritual[5]] ?? null; ?>
        <div class="ruwa-hero-product<?php echo $index === 0 ? ' is-active' : ''; ?>" data-ritual-image="<?php echo esc_attr($ritual[0]); ?>">
          <?php echo $product ? wp_kses_post($product->get_image('woocommerce_single', ['loading' => $index === 0 ? 'eager' : 'lazy'])) : '<div class="ruwa-placeholder-bottle">RUWA</div>'; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="ruwa-story-strip"><div class="ruwa-shell ruwa-reveal"><span class="ruwa-eyebrow ruwa-eyebrow-light">OUR PHILOSOPHY</span><blockquote>Small-batch. Real ingredients. <em>Rituals made to last.</em></blockquote><p>We create uncomplicated skincare that turns everyday care into something tactile, expressive and easy to return to.</p></div></section>

<section class="ruwa-claims"><div class="ruwa-shell ruwa-claims-grid">
<?php
$claims = [
    ['◇','No Sulfates','Gentle cleansing without the harsh finish.'],
    ['✦','No Parabens','Thoughtful formulas, nothing unnecessary.'],
    ['♡','Cruelty-Free','Beauty made with care and respect.'],
    ['◎','Dermatologist Tested','Designed with skin comfort in mind.'],
];
foreach ($claims as $claim) : ?>
<article><i><?php echo esc_html($claim[0]); ?></i><strong><?php echo esc_html($claim[1]); ?></strong><span><?php echo esc_html($claim[2]); ?></span></article>
<?php endforeach; ?>
</div></section>

<section class="ruwa-section ruwa-ritual-shop"><div class="ruwa-shell"><header class="ruwa-section-heading ruwa-reveal"><span class="ruwa-eyebrow ruwa-eyebrow-light">CHOOSE YOUR RITUAL</span><h2>Three ways to meet your skin.</h2><p>Start with the feeling you want, then build the routine around it.</p></header><div class="ruwa-product-grid"><?php $badges = ['Daily glow','Night reset','Weekly balance']; foreach (array_slice($products, 0, 3) as $i => $product) ruwa_product_card($product, $badges[$i] ?? ''); ?></div></div></section>

<section class="ruwa-section ruwa-why"><div class="ruwa-shell ruwa-split"><div class="ruwa-photo-card ruwa-reveal"><div class="ruwa-photo-art"><?php if (!empty($products[0])) echo wp_kses_post($products[0]->get_image('woocommerce_single')); else echo '<span>RUWA</span>'; ?><i></i><b></b></div></div><div class="ruwa-why-copy ruwa-reveal"><span class="ruwa-eyebrow ruwa-eyebrow-light">WHY RUWA BEAUTY</span><h2>Crafted with intention, not complication.</h2><ol><li><b>01</b><div><h3>Small batches</h3><p>Made with close attention to texture, stability and freshness.</p></div></li><li><b>02</b><div><h3>Real ingredients</h3><p>Purposeful actives and comforting support ingredients in balanced formulas.</p></div></li><li><b>03</b><div><h3>Founder-led care</h3><p>A focused collection built around clear roles and everyday consistency.</p></div></li></ol><a class="ruwa-text-link" href="<?php echo esc_url(ruwa_page_url_any(['story','about-us'])); ?>"><?php esc_html_e('Read our story', 'nub-ruwah'); ?></a></div></div></section>

<section class="ruwa-bundle-band" data-bundle-switcher><div class="ruwa-shell ruwa-bundle-grid"><div><span class="ruwa-eyebrow">RITUAL SETS</span><h2>More ritual. Better value.</h2><p>Choose a focused three-piece daily set or explore the complete collection.</p><div class="ruwa-bundle-options"><button class="is-active" type="button" data-bundle="3">3-piece set</button><button type="button" data-bundle="6">6-piece set</button></div><a class="ruwa-button ruwa-button-secondary" href="<?php echo esc_url(ruwa_page_url('bundles')); ?>"><?php esc_html_e('Explore sets', 'nub-ruwah'); ?></a></div><div class="ruwa-bundle-stack" data-bundle-stack><?php foreach (array_slice($products, 0, 6) as $index => $product) echo '<a data-bundle-item="' . esc_attr((string) ($index + 1)) . '" href="' . esc_url($product->get_permalink()) . '">' . wp_kses_post($product->get_image('woocommerce_thumbnail', ['loading'=>'lazy'])) . '</a>'; ?></div></div></section>

<?php $reviews = ruwa_product_reviews(8); ?>
<section class="ruwa-reviews"><div class="ruwa-shell"><header class="ruwa-section-heading"><span class="ruwa-eyebrow ruwa-eyebrow-dark">REAL ROUTINES, REAL SKIN</span><h2>What customers are sharing.</h2></header>
<?php if ($reviews) : ?><div class="ruwa-review-track"><?php foreach ($reviews as $review) ruwa_render_review($review); ?></div>
<?php else : ?><div class="ruwa-review-empty"><span>“</span><h3><?php esc_html_e('Be the first to share your ritual experience.', 'nub-ruwah'); ?></h3><a class="ruwa-button ruwa-button-secondary" href="<?php echo esc_url(ruwa_shop_url()); ?>"><?php esc_html_e('Explore rituals', 'nub-ruwah'); ?></a></div><?php endif; ?>
</div></section>

<section class="ruwa-newsletter"><div class="ruwa-shell ruwa-newsletter-card"><div><span class="ruwa-eyebrow ruwa-eyebrow-dark">JOIN THE RITUAL CLUB</span><h2>Skincare notes worth opening.</h2><p>New rituals, restocks and practical skin notes—shared thoughtfully.</p></div><div class="ruwa-newsletter-action"><a class="ruwa-button ruwa-button-primary" href="<?php echo esc_url(ruwa_page_url_any(['contact','contact-us'])); ?>"><?php esc_html_e('Join the conversation', 'nub-ruwah'); ?></a><small><?php esc_html_e('Newsletter integration can be connected to your preferred provider.', 'nub-ruwah'); ?></small></div></div></section>
<?php get_footer(); ?>