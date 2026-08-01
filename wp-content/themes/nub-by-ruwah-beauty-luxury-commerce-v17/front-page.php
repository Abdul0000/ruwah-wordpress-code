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
<style id="rb-stage-home-css">
body.home{background:#0b0906}
body.home .ruwa-announcement{display:none!important}
body.home .ruwa-header{position:relative!important;top:auto!important;background:#0b0906!important;border-bottom:1px solid rgba(248,231,199,.2)!important;box-shadow:none!important}
body.home .ruwa-header-inner{min-height:112px!important}
body.home .ruwa-header a,body.home .ruwa-header button,body.home .ruwa-brand,body.home .ruwa-brand strong{color:#f8e7c7!important}
body.home .ruwa-brand svg{fill:none!important;stroke:#c99943!important}
body.home .ruwa-tools>*,body.home .ruwa-menu-button{border-color:#f8e7c7!important;background:#0b0906!important}
body.home .ruwa-cart-count{background:#f8e7c7!important;color:#0b0906!important}
.rb-stage-hero{--rb-ink:#0b0906;--rb-cream:#f8e7c7;--rb-gold:#c9973e;position:relative;isolation:isolate;height:calc(100svh - 112px);min-height:610px;max-height:760px;overflow:hidden;background:var(--rb-ink);color:var(--rb-cream);border-bottom:1px solid rgba(248,231,199,.22)}
.rb-stage-guides{position:absolute;inset:0;z-index:0;pointer-events:none}
.rb-stage-guides i{position:absolute;left:14px;right:14px;height:1px;background:repeating-linear-gradient(90deg,rgba(248,231,199,.22) 0 4px,transparent 4px 8px)}
.rb-stage-guides i:nth-child(1){top:17%}.rb-stage-guides i:nth-child(2){top:53%}.rb-stage-guides i:nth-child(3){bottom:8%}
.rb-stage-shell{position:relative;z-index:1;display:grid;grid-template-columns:minmax(150px,1fr) minmax(500px,660px) minmax(150px,1fr);grid-template-rows:84px 1fr 92px;height:100%;padding:0 clamp(18px,2.8vw,54px);align-items:center}
.rb-stage-meta{position:relative;z-index:8;align-self:end;padding-bottom:12px;font-family:Inter,Arial,sans-serif;font-size:clamp(12px,1vw,17px);font-weight:800;letter-spacing:.02em;text-transform:uppercase;color:var(--rb-cream)}
.rb-stage-meta-left{grid-column:1;grid-row:1;text-align:left}.rb-stage-meta-right{grid-column:3;grid-row:1;text-align:right;white-space:nowrap}
.rb-stage-center{position:relative;grid-column:2;grid-row:1/4;align-self:stretch;display:grid;place-items:center;min-height:0}
.rb-stage-disc{position:absolute;z-index:1;width:min(43vw,520px);aspect-ratio:1;border-radius:50%;overflow:hidden;background:var(--rb-gold);box-shadow:none}
.rb-stage-disc:before,.rb-stage-disc:after,.rb-stage-ring,.rb-stage-tabs{display:none!important}
.rb-stage-slide{position:absolute;z-index:3;width:min(43vw,520px);aspect-ratio:1;height:auto;display:grid;place-items:center;overflow:hidden;border-radius:50%;background:var(--rb-gold);opacity:0;visibility:hidden;transform:translateY(10px) scale(.97);transition:opacity .28s ease,transform .42s cubic-bezier(.2,.8,.2,1),visibility .28s ease}
.rb-stage-slide.is-active{opacity:1;visibility:visible;transform:none}
.rb-stage-slide a{display:block;width:100%;height:100%;overflow:hidden;border-radius:50%;background:var(--rb-gold)}
.rb-stage-slide img{display:block!important;width:100%!important;height:100%!important;max-width:none!important;max-height:none!important;object-fit:cover!important;object-position:center!important;border-radius:50%!important;background:var(--rb-gold)!important;mix-blend-mode:multiply;filter:contrast(1.14) saturate(.94) brightness(.96);transform:scale(1.03)!important}
.rb-stage-arrow{position:relative;z-index:8;grid-row:2;justify-self:center;width:clamp(86px,7vw,118px);aspect-ratio:1;padding:0!important;border:0!important;border-radius:50%!important;background:var(--rb-cream)!important;color:var(--rb-ink)!important;font-size:clamp(32px,2.8vw,46px)!important;line-height:1!important;box-shadow:none!important;cursor:pointer;transition:transform .2s ease,background .2s ease}
.rb-stage-arrow:hover{transform:scale(1.05);background:var(--rb-gold)!important}.rb-stage-prev{grid-column:1}.rb-stage-next{grid-column:3}
.rb-stage-cta{position:absolute;z-index:9;left:50%;bottom:16px;display:grid;place-items:center;width:min(370px,70vw);min-height:92px;padding:14px 30px;border:0;border-radius:14px;background:var(--rb-cream);color:var(--rb-ink)!important;font-family:Impact,'Arial Narrow',Inter,sans-serif;font-size:clamp(26px,2.25vw,36px);font-weight:900;line-height:1;text-decoration:none!important;text-transform:uppercase;transform:translateX(-50%);box-shadow:none;transition:transform .2s ease,background .2s ease}
.rb-stage-cta:hover{background:var(--rb-gold);transform:translateX(-50%) translateY(-3px)}
@media(max-width:980px){body.home .ruwa-header-inner{min-height:92px!important}.rb-stage-hero{height:650px;min-height:650px}.rb-stage-shell{grid-template-columns:82px minmax(0,1fr) 82px;grid-template-rows:76px 1fr 84px;padding-inline:14px}.rb-stage-disc,.rb-stage-slide{width:min(60vw,470px)}.rb-stage-meta{font-size:10px}.rb-stage-meta-right{max-width:145px;white-space:normal}.rb-stage-arrow{width:70px}.rb-stage-cta{min-height:76px;width:min(330px,68vw)}}
@media(max-width:640px){body.home .ruwa-header-inner{min-height:76px!important}.rb-stage-hero{height:610px;min-height:610px}.rb-stage-shell{grid-template-columns:50px minmax(0,1fr) 50px;grid-template-rows:72px 1fr 76px;padding:0 8px}.rb-stage-meta{align-self:center;padding:0;font-size:8px}.rb-stage-meta-right{max-width:80px}.rb-stage-disc,.rb-stage-slide{width:min(78vw,360px)}.rb-stage-arrow{width:50px;font-size:24px!important}.rb-stage-prev{justify-self:start}.rb-stage-next{justify-self:end}.rb-stage-cta{bottom:12px;width:min(260px,68vw);min-height:62px;font-size:24px}}
@media(prefers-reduced-motion:reduce){.rb-stage-slide,.rb-stage-arrow,.rb-stage-cta{transition:none!important}}
</style>
<section class="rb-stage-hero" data-rb-stage aria-label="Featured skincare rituals">
  <div class="rb-stage-guides" aria-hidden="true"><i></i><i></i><i></i></div>
  <div class="rb-stage-shell">
    <div class="rb-stage-meta rb-stage-meta-left" data-rb-number>PRODUCT NO.01</div>
    <div class="rb-stage-meta rb-stage-meta-right" data-rb-title><?php echo esc_html(!empty($products[0]) ? $products[0]->get_name() : 'DAILY GLOW RITUAL'); ?></div>
    <button class="rb-stage-arrow rb-stage-prev" type="button" data-rb-prev aria-label="Previous product">←</button>
    <div class="rb-stage-center">
      <div class="rb-stage-disc" aria-hidden="true"></div>
      <?php foreach ($rituals as $index => $ritual) : $product = $products[$ritual[5]] ?? null; ?>
        <article class="rb-stage-slide<?php echo $index === 0 ? ' is-active' : ''; ?>" data-rb-slide data-number="<?php echo esc_attr($ritual[1]); ?>" data-title="<?php echo esc_attr($product ? $product->get_name() : $ritual[2]); ?>">
          <a href="<?php echo esc_url($product ? $product->get_permalink() : ruwa_shop_url()); ?>" aria-label="<?php echo esc_attr($product ? sprintf('View %s', $product->get_name()) : 'Explore skincare'); ?>">
            <?php echo $product ? wp_kses_post($product->get_image('woocommerce_single', ['loading' => $index === 0 ? 'eager' : 'lazy'])) : '<span>RUWA</span>'; ?>
          </a>
        </article>
      <?php endforeach; ?>
      <a class="rb-stage-cta" href="<?php echo esc_url(ruwa_shop_url()); ?>">Shop now</a>
    </div>
    <button class="rb-stage-arrow rb-stage-next" type="button" data-rb-next aria-label="Next product">→</button>
  </div>
</section>
<script id="rb-stage-controller">
(function(){var root=document.querySelector('[data-rb-stage]');if(!root){return;}var slides=Array.prototype.slice.call(root.querySelectorAll('[data-rb-slide]'));var number=root.querySelector('[data-rb-number]');var title=root.querySelector('[data-rb-title]');var index=0;function show(next){index=(next+slides.length)%slides.length;slides.forEach(function(slide,i){slide.classList.toggle('is-active',i===index);});var active=slides[index];if(number){number.textContent='PRODUCT NO.'+(active.getAttribute('data-number')||String(index+1).padStart(2,'0'));}if(title){title.textContent=active.getAttribute('data-title')||'';}}var prev=root.querySelector('[data-rb-prev]');var next=root.querySelector('[data-rb-next]');if(prev){prev.addEventListener('click',function(){show(index-1);});}if(next){next.addEventListener('click',function(){show(index+1);});}})();
</script>

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
<section class="ruwa-reviews"><div class="ruwa-shell"><header class="ruwa-section-heading"><span class="ruwa-eyebrow ruwa-eyebrow-dark">REAL ROUTINES, REAL SKIN</span><h2>What customers are sharing.</h2></header><?php if ($reviews) : ?><div class="ruwa-review-track"><?php foreach ($reviews as $review) ruwa_render_review($review); ?></div><?php else : ?><div class="ruwa-review-empty"><span>“</span><h3><?php esc_html_e('Be the first to share your ritual experience.', 'nub-ruwah'); ?></h3><a class="ruwa-button ruwa-button-secondary" href="<?php echo esc_url(ruwa_shop_url()); ?>"><?php esc_html_e('Explore rituals', 'nub-ruwah'); ?></a></div><?php endif; ?></div></section>
<section class="ruwa-newsletter"><div class="ruwa-shell ruwa-newsletter-card"><div><span class="ruwa-eyebrow ruwa-eyebrow-dark">JOIN THE RITUAL CLUB</span><h2>Skincare notes worth opening.</h2><p>New rituals, restocks and practical skin notes—shared thoughtfully.</p></div><div class="ruwa-newsletter-action"><a class="ruwa-button ruwa-button-primary" href="<?php echo esc_url(ruwa_page_url_any(['contact','contact-us'])); ?>"><?php esc_html_e('Join the conversation', 'nub-ruwah'); ?></a><small><?php esc_html_e('Newsletter integration can be connected to your preferred provider.', 'nub-ruwah'); ?></small></div></div></section>
<?php get_footer(); ?>