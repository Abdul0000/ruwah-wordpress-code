<?php
defined('ABSPATH') || exit;

if (function_exists('is_shop') && (is_shop() || is_product_taxonomy())) {
    wp_enqueue_style(
        'rwb-shop',
        get_template_directory_uri() . '/shop.css',
        ['rwb-theme'],
        '4.1.2'
    );
    wp_print_styles('rwb-shop');
}
?>
<ul class="products columns-<?php echo esc_attr(wc_get_loop_prop('columns')); ?>">