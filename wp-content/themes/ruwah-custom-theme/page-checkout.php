<?php
defined('ABSPATH') || exit;

if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received')) {
    get_header();
    while (have_posts()) :
        the_post();
        ?>
        <section class="rb-content"><div class="rb-shell"><article><?php the_content(); ?></article></div></section>
        <?php
    endwhile;
    get_footer();
    return;
}

get_header();
$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');
$account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/');
$privacy_url = get_privacy_policy_url();
$legal_pages = [
    'Refund policy' => 'refund-policy',
    'Shipping' => 'shipping',
    'Terms of service' => 'terms-of-service',
    'Contact' => 'contact',
];
?>
<section class="rwb-ref-checkout" aria-label="Secure checkout">
    <div class="rwb-ref-checkout-frame">
        <header class="rwb-ref-checkout-mast">
            <div class="rwb-ref-checkout-mast-left">
                <div class="rwb-ref-checkout-logo">
                    <?php if (has_custom_logo()) { the_custom_logo(); } else { ?><a href="<?php echo esc_url(home_url('/')); ?>">RUWAH</a><?php } ?>
                </div>
                <nav class="rwb-ref-checkout-steps" aria-label="Checkout progress">
                    <a href="<?php echo esc_url($cart_url); ?>">Cart</a><span aria-hidden="true">›</span>
                    <b>Information</b><span aria-hidden="true">›</span>
                    <em>Shipping</em><span aria-hidden="true">›</span>
                    <em>Payment</em>
                </nav>
            </div>
            <div aria-hidden="true"></div>
        </header>
    </div>

    <div class="rwb-ref-checkout-body">
        <?php while (have_posts()) : the_post(); ?>
            <article><?php the_content(); ?></article>
        <?php endwhile; ?>
    </div>

    <div class="rwb-ref-checkout-frame">
        <div class="rwb-ref-checkout-legal">
            <a class="rwb-ref-checkout-return" href="<?php echo esc_url($cart_url); ?>"><span aria-hidden="true">‹</span> Return to cart</a>
            <nav aria-label="Checkout policies">
                <?php foreach ($legal_pages as $label => $slug) : $page = get_page_by_path($slug); if ($page) : ?>
                    <a href="<?php echo esc_url(get_permalink($page)); ?>"><?php echo esc_html($label); ?></a>
                <?php endif; endforeach; ?>
                <?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>">Privacy policy</a><?php endif; ?>
                <a href="<?php echo esc_url($account_url); ?>">Sign in</a>
            </nav>
        </div>
    </div>
</section>
<?php get_footer();
