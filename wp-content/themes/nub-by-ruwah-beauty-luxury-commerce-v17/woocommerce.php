<?php defined('ABSPATH') || exit; get_header(); ?>
<section class="ruwa-shop-hero"><div class="ruwa-shell"><span class="ruwa-eyebrow ruwa-eyebrow-light"><?php echo is_product()?'THE RITUAL, UP CLOSE':'SHOP RUWA BEAUTY'; ?></span><h1><?php echo esc_html(is_product() ? get_the_title() : 'Skincare with a clear purpose.'); ?></h1><?php if(!is_product()){ ?><p>Browse by ritual, concern or the feeling you want your routine to create.</p><?php } ?></div></section>
<section class="ruwa-woo-section"><div class="ruwa-shell"><?php woocommerce_content(); ?></div></section>
<?php get_footer(); ?>