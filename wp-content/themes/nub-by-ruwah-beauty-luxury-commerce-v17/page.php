<?php
defined('ABSPATH') || exit;
get_header();
while (have_posts()) : the_post();
$slug = get_post_field('post_name', get_the_ID());
$content = trim((string) get_the_content());
$is_story = in_array($slug, ['story','about-us'], true);
$is_contact = in_array($slug, ['contact','contact-us'], true);
$is_faq = in_array($slug, ['faq','faqs'], true);
$is_wholesale = in_array($slug, ['wholesale','bulk-gifting'], true);
$is_legal = in_array($slug, ['privacy-policy','terms-conditions','terms-and-conditions','shipping-delivery','shipping-policy','returns-refunds','return-policy','refund-policy','cancellation-policy','claims-policy','quality-safety','quality-testing'], true);
?>
<section class="ruwa-page-hero<?php echo $is_legal ? ' ruwa-page-hero-quiet' : ''; ?>"><div class="ruwa-grain"></div><div class="ruwa-shell"><span class="ruwa-eyebrow">RUWA BEAUTY</span><h1><?php the_title(); ?></h1><?php if ($is_contact) : ?><p><?php esc_html_e('Questions about products, orders or gifting? Start here.', 'nub-ruwah'); ?></p><?php endif; ?></div></section>

<?php if ($is_story) : $products = ruwa_products(2); ?>
<section class="ruwa-page-section"><div class="ruwa-shell">
  <article class="ruwa-story-layout">
    <div class="ruwa-story-intro ruwa-reveal"><span class="ruwa-eyebrow ruwa-eyebrow-light">OUR STORY</span><h2>Care made slower, clearer and more intentional.</h2><p>RUWA BEAUTY is built around uncomplicated rituals, purposeful ingredients and textures that make daily care feel worth returning to.</p></div>
    <div class="ruwa-story-row ruwa-reveal"><div class="ruwa-story-image"><?php echo !empty($products[0]) ? wp_kses_post($products[0]->get_image('woocommerce_single')) : '<span>01</span>'; ?></div><div><small>01</small><h3>Begin with the ritual.</h3><p>Each product is designed to have a clear place in a routine, so the collection feels easy to understand and combine.</p></div></div>
    <div class="ruwa-story-row reverse ruwa-reveal"><div class="ruwa-story-image"><?php echo !empty($products[1]) ? wp_kses_post($products[1]->get_image('woocommerce_single')) : '<span>02</span>'; ?></div><div><small>02</small><h3>Formulate with care.</h3><p>Small-batch thinking keeps attention on freshness, texture, stability and the way a product feels in real use.</p></div></div>
    <?php if ($content !== '') : ?><div class="ruwa-content-card"><?php the_content(); ?></div><?php endif; ?>
    <div class="ruwa-founder-quote"><span>“</span><p>Skincare should feel thoughtful without feeling complicated.</p><small>RUWA BEAUTY</small></div>
    <a class="ruwa-button ruwa-button-primary" href="<?php echo esc_url(ruwa_shop_url()); ?>"><?php esc_html_e('Shop the rituals', 'nub-ruwah'); ?></a>
  </article>
</div></section>

<?php elseif ($is_contact) : ?>
<section class="ruwa-page-section"><div class="ruwa-shell ruwa-contact-layout"><div class="ruwa-content-card"><?php ruwa_contact_form('contact'); ?></div><aside class="ruwa-contact-cards"><a href="mailto:support@ruwahbeauty.com"><i>✉</i><small><?php esc_html_e('EMAIL US', 'nub-ruwah'); ?></small><strong>support@ruwahbeauty.com</strong></a><a href="<?php echo esc_url(ruwa_page_url_any(['faq','faqs'])); ?>"><i>?</i><small><?php esc_html_e('QUICK ANSWERS', 'nub-ruwah'); ?></small><strong><?php esc_html_e('Read the FAQ', 'nub-ruwah'); ?></strong></a></aside></div></section>

<?php elseif ($is_wholesale) : ?>
<section class="ruwa-page-section"><div class="ruwa-shell"><div class="ruwa-path-grid"><article><span>01</span><h2><?php esc_html_e('Curated gifting', 'nub-ruwah'); ?></h2><p><?php esc_html_e('Explore ritual sets for thoughtful personal or team gifting.', 'nub-ruwah'); ?></p><a class="ruwa-button ruwa-button-secondary" href="<?php echo esc_url(ruwa_page_url('bundles')); ?>"><?php esc_html_e('Explore gift sets', 'nub-ruwah'); ?></a></article><article><span>02</span><h2><?php esc_html_e('Bulk & wholesale', 'nub-ruwah'); ?></h2><p><?php esc_html_e('Tell us what you need and the team will respond with current availability and terms.', 'nub-ruwah'); ?></p></article></div><div class="ruwa-content-card ruwa-wholesale-form"><?php ruwa_contact_form('wholesale'); ?></div></div></section>

<?php elseif ($is_faq) : ?>
<section class="ruwa-page-section"><div class="ruwa-shell"><div class="ruwa-faq-layout"><nav class="ruwa-faq-categories"><a href="#shipping-0">Shipping</a><a href="#ingredients-2">Ingredients</a><a href="#storage-4">Storage</a><a href="#returns-5">Returns</a></nav><div class="ruwa-accordion">
<?php
$faqs = [
    ['shipping','Where does RUWA BEAUTY ship?','Shipping coverage and available methods are shown at checkout. Contact support when your destination is not available.'],
    ['shipping','How can I track an order?','Use the Track Order page or the tracking details included in your order update.'],
    ['ingredients','Where can I find full ingredients?','Open the product page and review the Ingredients section for the product-specific list.'],
    ['ingredients','How do I choose an actives intensity?','Use the product description and any intensity guidance shown on the product page. Contact support when you are unsure.'],
    ['storage','How should products be stored?','Follow the storage directions printed on the product packaging and keep products away from conditions the label advises against.'],
    ['returns','Where can I read the return policy?','The current Returns & Refunds policy is available from Customer Care in the footer.'],
];
foreach ($faqs as $index => $faq) : ?>
<section id="<?php echo esc_attr($faq[0] . '-' . $index); ?>"><button type="button" aria-expanded="false"><span><?php echo esc_html($faq[1]); ?></span><i>⌄</i></button><div hidden><p><?php echo esc_html($faq[2]); ?></p></div></section>
<?php endforeach; ?>
<?php if ($content !== '') : ?><div class="ruwa-content-card"><?php the_content(); ?></div><?php endif; ?>
</div></div><div class="ruwa-closing-cta"><h2><?php esc_html_e('Still have questions?', 'nub-ruwah'); ?></h2><a class="ruwa-button ruwa-button-primary" href="<?php echo esc_url(ruwa_page_url_any(['contact','contact-us'])); ?>"><?php esc_html_e('Contact us', 'nub-ruwah'); ?></a></div></div></section>

<?php else : ?>
<section class="ruwa-page-section"><div class="ruwa-shell"><article class="ruwa-content-card<?php echo $is_legal ? ' ruwa-legal' : ''; ?>"><?php the_content(); ?></article></div></section>
<?php endif; ?>

<?php endwhile; get_footer(); ?>