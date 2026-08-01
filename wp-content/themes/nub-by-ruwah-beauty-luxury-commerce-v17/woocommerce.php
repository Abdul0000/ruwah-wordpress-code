<?php
defined('ABSPATH') || exit;
get_header();
$is_product_view = is_product();
$terms = (!$is_product_view && taxonomy_exists('product_cat')) ? get_terms(['taxonomy'=>'product_cat','hide_empty'=>true,'number'=>8]) : [];
?>
<section class="ruwa-shop-hero"><div class="ruwa-shell">
  <nav class="ruwa-breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'nub-ruwah'); ?>"><a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'nub-ruwah'); ?></a><span>/</span><span><?php echo esc_html($is_product_view ? get_the_title() : __('Shop', 'nub-ruwah')); ?></span></nav>
  <span class="ruwa-eyebrow ruwa-eyebrow-light"><?php echo esc_html($is_product_view ? __('THE RITUAL, UP CLOSE', 'nub-ruwah') : __('SHOP RUWA BEAUTY', 'nub-ruwah')); ?></span>
  <h1><?php echo esc_html($is_product_view ? get_the_title() : __('Shop All Rituals', 'nub-ruwah')); ?></h1>
  <?php if (!$is_product_view) : ?><p><?php esc_html_e('Browse by ritual, concern or the feeling you want your routine to create.', 'nub-ruwah'); ?></p>
  <div class="ruwa-filter-pills"><a class="is-active" href="<?php echo esc_url(ruwa_shop_url()); ?>"><?php esc_html_e('All', 'nub-ruwah'); ?></a><?php if (!is_wp_error($terms)) foreach ($terms as $term) echo '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>'; ?></div><?php endif; ?>
</div></section>
<section class="ruwa-woo-section"><div class="ruwa-shell"><?php woocommerce_content(); ?></div></section>
<?php get_footer(); ?>