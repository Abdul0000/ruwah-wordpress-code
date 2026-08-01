<?php
defined('ABSPATH') || exit;
get_header();
$products = ruwa_products(12);
$rituals = [
    ['morning', '01', 'DAILY GLOW RITUAL', 'Wake up your glow.', 'Fresh hydration, brightening support and daily protection for skin that feels ready for the day.', 0],
    ['night', '02', 'RESET & RECHARGE RITUAL', 'Reset while you rest.', 'Comforting textures and barrier-first care designed to replenish skin overnight.', 1],
    ['weekly', '03', 'CLEAR & BALANCE RITUAL', 'Make space for balance.', 'A focused reset for smoother texture, clearer-looking pores and renewed softness.', 2],
];
?>
<section class="ruwa-hero ruwa-editorial-hero" data-ritual-switcher data-active-ritual="morning">
  <div class="ruwa-grain" aria-hidden="true"></div>
  <div class="ruwa-editorial-guides" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="ruwa-editorial-stage">
    <div class="ruwa-editorial-meta ruwa-editorial-meta-left" aria-live="polite">
      <?php foreach ($rituals as $index => $ritual) : ?>
        <span class="ruwa-ritual-panel<?php echo $index === 0 ? ' is-active' : ''; ?>" data-ritual-panel="<?php echo esc_attr($ritual[0]); ?>">PRODUCT NO.<?php echo esc_html($ritual[1]); ?></span>
      <?php endforeach; ?>
    </div>
    <div class="ruwa-editorial-meta ruwa-editorial-meta-right" aria-live="polite">
      <?php foreach ($rituals as $index => $ritual) : ?>
        <span class="ruwa-ritual-panel<?php echo $index === 0 ? ' is-active' : ''; ?>" data-ritual-panel="<?php echo esc_attr($ritual[0]); ?>"><?php echo esc_html($ritual[2]); ?></span>
      <?php endforeach; ?>
    </div>

    <button class="ruwa-editorial-arrow ruwa-editorial-prev" type="button" data-ritual-prev aria-label="<?php esc_attr_e('Previous ritual', 'nub-ruwah'); ?>">←</button>

    <div class="ruwa-editorial-center">
      <div class="ruwa-editorial-disc" aria-hidden="true"></div>
      <div class="ruwa-editorial-orbit orbit-one" aria-hidden="true"></div>
      <div class="ruwa-editorial-orbit orbit-two" aria-hidden="true"></div>
      <?php foreach ($rituals as $index => $ritual) : $product = $products[$ritual[5]] ?? null; ?>
        <article class="ruwa-hero-product<?php echo $index === 0 ? ' is-active' : ''; ?>" data-ritual-image="<?php echo esc_attr($ritual[0]); ?>">
          <a href="<?php echo esc_url($product ? $product->get_permalink() : ruwa_shop_url()); ?>" aria-label="<?php echo esc_attr($product ? sprintf(__('View %s', 'nub-ruwah'), $product->get_name()) : __('Explore Ruwa Beauty products', 'nub-ruwah')); ?>">
            <?php echo $product ? wp_kses_post($product->get_image('woocommerce_single', ['loading' => $index === 0 ? 'eager' : 'lazy'])) : '<div class="ruwa-placeholder-bottle">RUWA</div>'; ?>
          </a>
        </article>
      <?php endforeach; ?>
      <div class="ruwa-editorial-copy">
        <?php foreach ($rituals as $index => $ritual) : ?>
          <article class="ruwa-ritual-panel<?php echo $index === 0 ? ' is-active' : ''; ?>" data-ritual-panel="<?php echo esc_attr($ritual[0]); ?>">
            <span class="ruwa-eyebrow"><?php echo esc_html($ritual[2]); ?></span>
            <h1><?php echo esc_html($ritual[3]); ?></h1>
            <p><?php echo esc_html($ritual[4]); ?></p>
          </article>
        <?php endforeach; ?>
      </div>
      <a class="ruwa-editorial-cta" href="<?php echo esc_url(ruwa_shop_url()); ?>"><?php esc_html_e('Shop now', 'nub-ruwah'); ?></a>
    </div>

    <button class="ruwa-editorial-arrow ruwa-editorial-next" type="button" data-ritual-next aria-label="<?php esc_attr_e('Next ritual', 'nub-ruwah'); ?>">→</button>

    <div class="ruwa-ritual-tabs ruwa-editorial-tabs" role="tablist" aria-label="<?php esc_attr_e('Choose a ritual', 'nub-ruwah'); ?>">
      <?php foreach ($rituals as $index => $ritual) : ?>
        <button type="button" role="tab" aria-selected="<?php echo $index === 0 ? 'true' : 'false'; ?>" data-ritual-tab="<?php echo esc_attr($ritual[0]); ?>"><small><?php echo esc_html($ritual[1]); ?></small><?php echo esc_html(ucfirst($ritual[0])); ?></button>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="ruwa-story-strip"><div class="ruwa-shell ruwa-reveal"><span class="ruwa-eyebrow ruwa-eyebrow-light">OUR PHILOSOPHY</span><blockquote>Small-batch. Real ingredients. <em>Rituals made to last.</em></blockquote><p>We create uncomplicated skincare that turns everyday care into something tactile, expressive and easy to return to.</p></div></section>

<section class="ritual-claims-stack" id="ritual-claims" aria-label="<?php esc_attr_e('Our formulation standards', 'nub-ruwah'); ?>">
  <article class="claim-card" data-index="0"><span class="claim-icon" aria-hidden="true">◇</span><h3><?php esc_html_e('No Sulfates', 'nub-ruwah'); ?></h3><p><?php esc_html_e('Gentle cleansing without harsh stripping agents.', 'nub-ruwah'); ?></p></article>
  <article class="claim-card" data-index="1"><span class="claim-icon" aria-hidden="true">✦</span><h3><?php esc_html_e('No Parabens', 'nub-ruwah'); ?></h3><p><?php esc_html_e('Preserved naturally, without synthetic preservatives.', 'nub-ruwah'); ?></p></article>
  <article class="claim-card" data-index="2"><span class="claim-icon" aria-hidden="true">♡</span><h3><?php esc_html_e('Cruelty-Free', 'nub-ruwah'); ?></h3><p><?php esc_html_e('Never tested on animals, ever.', 'nub-ruwah'); ?></p></article>
  <article class="claim-card" data-index="3"><span class="claim-icon" aria-hidden="true">◎</span><h3><?php esc_html_e('Dermatologist Tested', 'nub-ruwah'); ?></h3><p><?php esc_html_e('Verified safe and gentle for everyday use.', 'nub-ruwah'); ?></p></article>
</section>

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