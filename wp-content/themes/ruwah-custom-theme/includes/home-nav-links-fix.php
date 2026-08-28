<?php
defined('ABSPATH') || exit;

/**
 * Correct only the standalone homepage navigation targets supplied by the
 * commerce plugin. Reuses existing content; creates no pages or forms.
 */
add_action('template_redirect', static function (): void {
    if (! is_front_page()) {
        return;
    }

    ob_start(static function (string $html): string {
        $learn_old   = esc_url(home_url('/beauty-guide/'));
        $learn_new   = esc_url(home_url('/#ingredient-guide'));
        $contact_old = esc_url(home_url('/contact-us/'));
        $contact_new = esc_url(home_url('/contact/'));

        return str_replace(
            [
                'href="' . $learn_old . '"',
                'href="' . $contact_old . '"',
            ],
            [
                'href="' . $learn_new . '"',
                'href="' . $contact_new . '"',
            ],
            $html
        );
    });
}, 2);

/* Checkout masthead: use the same visible crop as the approved homepage logo. */
add_action('wp_enqueue_scripts', static function (): void {
    if (! function_exists('is_checkout') || ! is_checkout()) {
        return;
    }
    if (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received')) {
        return;
    }

    wp_add_inline_style('rwb-theme', '
        body.rwb-reference-checkout-v1 .rwb-ref-checkout-logo{
            width:128px!important;
            height:52px!important;
            min-height:52px!important;
            margin:0 auto!important;
            display:grid!important;
            place-items:start center!important;
            overflow:hidden!important;
        }
        body.rwb-reference-checkout-v1 .rwb-ref-checkout-logo .custom-logo-link{
            display:block!important;
            width:128px!important;
            height:52px!important;
            overflow:hidden!important;
        }
        body.rwb-reference-checkout-v1 .rwb-ref-checkout-logo .custom-logo{
            display:block!important;
            width:auto!important;
            max-width:128px!important;
            height:72px!important;
            max-height:none!important;
            margin:0 auto!important;
            object-fit:contain!important;
            object-position:top center!important;
            transform:translateY(-1px)!important;
        }
    ');
}, 40000);

/* Product search: reuse the existing master-card search template. */
add_filter('template_include', static function (string $template): string {
    if (! is_search()) return $template;
    $post_type = get_query_var('post_type');
    $is_product_search = 'product' === $post_type || (is_array($post_type) && in_array('product', $post_type, true));
    if (! $is_product_search) return $template;

    $custom = WP_PLUGIN_DIR . '/ruwah-fresh-commerce-design/templates/product-search.php';
    return is_readable($custom) ? $custom : $template;
}, 500);