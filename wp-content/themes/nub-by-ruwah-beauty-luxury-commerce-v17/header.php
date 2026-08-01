<?php defined('ABSPATH') || exit; ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="ruwa-skip" href="#main-content">Skip to content</a>
<div class="ruwa-announcement"><span>Free delivery over PKR 5,000</span><i></i><span>Small-batch skincare for real rituals</span></div>
<header class="ruwa-header" data-header>
  <div class="ruwa-shell ruwa-header-inner">
    <button class="ruwa-menu-button" type="button" aria-label="Open menu" aria-expanded="false" data-menu-button><span></span><span></span><span></span></button>
    <nav class="ruwa-nav" aria-label="Primary navigation" data-menu>
      <a href="<?php echo esc_url(ruwa_shop_url()); ?>">Shop</a>
      <a href="<?php echo esc_url(ruwa_page_url('story')); ?>">Our Story</a>
      <a href="<?php echo esc_url(ruwa_page_url('faq')); ?>">FAQ</a>
    </nav>
    <a class="ruwa-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="RUWA BEAUTY home"><svg viewBox="0 0 32 32" aria-hidden="true"><path d="M16 3c5 6 8 10 8 15a8 8 0 1 1-16 0c0-5 3-9 8-15Z"/><path d="M11 20c3-1 6-3 10-7"/></svg><strong>RUWA BEAUTY</strong></a>
    <div class="ruwa-tools">
      <a href="<?php echo esc_url(home_url('/?s=&post_type=product')); ?>" aria-label="Search"><span>⌕</span></a>
      <a href="<?php echo esc_url(function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : ruwa_page_url('my-account')); ?>" aria-label="Account"><span>◎</span></a>
      <a href="<?php echo esc_url(ruwa_page_url('wishlist')); ?>" aria-label="Wishlist"><span>♡</span></a>
      <a class="ruwa-cart-link" href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : ruwa_page_url('cart')); ?>" aria-label="Cart"><span>Bag</span><b><?php echo esc_html((string) ruwa_cart_count()); ?></b></a>
    </div>
  </div>
</header>
<main id="main-content" class="ruwa-main">