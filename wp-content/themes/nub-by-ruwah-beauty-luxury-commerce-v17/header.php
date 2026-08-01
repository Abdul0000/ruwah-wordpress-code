<?php defined('ABSPATH') || exit; $progress = ruwa_shipping_progress(); ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="ruwa-skip" href="#main-content"><?php esc_html_e('Skip to content', 'nub-ruwah'); ?></a>
<div class="ruwa-announcement"><span><?php esc_html_e('Small-batch skincare for real rituals', 'nub-ruwah'); ?></span><i></i><span><?php esc_html_e('Free delivery over PKR 5,000', 'nub-ruwah'); ?></span></div>
<header class="ruwa-header" data-header>
  <div class="ruwa-shell ruwa-header-inner">
    <button class="ruwa-menu-button" type="button" aria-label="<?php esc_attr_e('Open menu', 'nub-ruwah'); ?>" aria-expanded="false" data-menu-button><span></span><span></span><span></span></button>
    <nav class="ruwa-nav" aria-label="<?php esc_attr_e('Primary navigation', 'nub-ruwah'); ?>" data-menu>
      <a href="<?php echo esc_url(ruwa_shop_url()); ?>"><?php esc_html_e('Shop', 'nub-ruwah'); ?></a>
      <a href="<?php echo esc_url(ruwa_page_url_any(['wholesale','bulk-gifting','bundles'])); ?>"><?php esc_html_e('Bulk & Gifting', 'nub-ruwah'); ?></a>
      <a href="<?php echo esc_url(ruwa_page_url_any(['story','about-us'])); ?>"><?php esc_html_e('About', 'nub-ruwah'); ?></a>
      <a href="<?php echo esc_url(ruwa_page_url_any(['contact','contact-us'])); ?>"><?php esc_html_e('Contact', 'nub-ruwah'); ?></a>
      <a href="<?php echo esc_url(ruwa_page_url_any(['faq','faqs'])); ?>"><?php esc_html_e('FAQ', 'nub-ruwah'); ?></a>
    </nav>
    <a class="ruwa-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php esc_attr_e('RUWA BEAUTY home', 'nub-ruwah'); ?>">
      <svg viewBox="0 0 40 40" aria-hidden="true"><path d="M20 4c7 8 11 14 11 21a11 11 0 1 1-22 0c0-7 4-13 11-21Z"/><path d="M13 27c5-2 10-6 14-12"/></svg><strong>RUWA BEAUTY</strong>
    </a>
    <div class="ruwa-tools">
      <button type="button" data-search-open aria-label="<?php esc_attr_e('Search', 'nub-ruwah'); ?>">⌕</button>
      <a href="<?php echo esc_url(ruwa_account_url()); ?>" aria-label="<?php esc_attr_e('Account', 'nub-ruwah'); ?>">◎</a>
      <a class="ruwa-wishlist-link" href="<?php echo esc_url(ruwa_page_url('wishlist')); ?>" aria-label="<?php esc_attr_e('Wishlist', 'nub-ruwah'); ?>">♡</a>
      <button class="ruwa-cart-link" type="button" data-cart-open aria-label="<?php esc_attr_e('Open cart', 'nub-ruwah'); ?>"><span><?php esc_html_e('Bag', 'nub-ruwah'); ?></span><b class="ruwa-cart-count"><?php echo esc_html((string) ruwa_cart_count()); ?></b></button>
    </div>
  </div>
</header>

<div class="ruwa-overlay" data-overlay hidden></div>
<section class="ruwa-search-overlay" data-search-dialog hidden aria-label="<?php esc_attr_e('Product search', 'nub-ruwah'); ?>">
  <div class="ruwa-search-card">
    <button type="button" class="ruwa-close" data-search-close aria-label="<?php esc_attr_e('Close search', 'nub-ruwah'); ?>">×</button>
    <span class="ruwa-eyebrow ruwa-eyebrow-light"><?php esc_html_e('FIND YOUR RITUAL', 'nub-ruwah'); ?></span>
    <h2><?php esc_html_e('What are you looking for?', 'nub-ruwah'); ?></h2>
    <form role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
      <label class="screen-reader-text" for="ruwa-product-search"><?php esc_html_e('Search products', 'nub-ruwah'); ?></label>
      <input id="ruwa-product-search" type="search" name="s" placeholder="<?php esc_attr_e('Search products, ingredients, rituals…', 'nub-ruwah'); ?>">
      <input type="hidden" name="post_type" value="product">
      <button class="ruwa-button ruwa-button-primary" type="submit"><?php esc_html_e('Search', 'nub-ruwah'); ?></button>
    </form>
  </div>
</section>

<aside class="ruwa-cart-drawer" data-cart-drawer aria-hidden="true">
  <header><h2><?php esc_html_e('Your Ritual Bag', 'nub-ruwah'); ?></h2><button type="button" class="ruwa-close" data-cart-close aria-label="<?php esc_attr_e('Close cart', 'nub-ruwah'); ?>">×</button></header>
  <div class="ruwa-cart-body widget_shopping_cart_content"><?php if (function_exists('woocommerce_mini_cart')) woocommerce_mini_cart(); ?></div>
  <footer>
    <p><?php echo $progress['remaining'] > 0 ? esc_html(sprintf(__('Add %s more for free shipping', 'nub-ruwah'), wp_strip_all_tags(wc_price($progress['remaining'])))) : esc_html__('You unlocked free shipping', 'nub-ruwah'); ?></p>
    <div class="ruwa-progress" aria-hidden="true"><i style="width:<?php echo esc_attr((string) $progress['percent']); ?>%"></i></div>
    <a class="ruwa-button ruwa-button-primary" href="<?php echo esc_url(ruwa_cart_url()); ?>"><?php esc_html_e('View bag & checkout', 'nub-ruwah'); ?></a>
  </footer>
</aside>
<main id="main-content" class="ruwa-main">