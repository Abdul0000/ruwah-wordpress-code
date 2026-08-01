<?php
defined('ABSPATH') || exit;

define('RUWA_THEME_VERSION', '31.0.0');

add_action('after_setup_theme', function () {
    load_theme_textdomain('nub-ruwah', get_stylesheet_directory() . '/languages');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    register_nav_menus([
        'primary' => __('Primary Menu', 'nub-ruwah'),
        'footer'  => __('Footer Menu', 'nub-ruwah'),
    ]);
});

add_action('wp_enqueue_scripts', function () {
    $parent = wp_get_theme('astra');
    if ($parent->exists()) {
        wp_enqueue_style('astra-parent', get_template_directory_uri() . '/style.css', [], $parent->get('Version'));
    }
    $style_path = get_stylesheet_directory() . '/style.css';
    $script_path = get_stylesheet_directory() . '/theme.js';
    wp_enqueue_style(
        'ruwa-rituals',
        get_stylesheet_directory_uri() . '/style.css',
        $parent->exists() ? ['astra-parent'] : [],
        is_readable($style_path) ? (string) filemtime($style_path) : RUWA_THEME_VERSION
    );
    wp_enqueue_script(
        'ruwa-rituals',
        get_stylesheet_directory_uri() . '/theme.js',
        [],
        is_readable($script_path) ? (string) filemtime($script_path) : RUWA_THEME_VERSION,
        true
    );
}, 40);

add_filter('body_class', function ($classes) {
    $classes[] = 'ruwa-ritual-theme';
    return $classes;
});

function ruwa_page_url(string $slug): string {
    $page = get_page_by_path(trim($slug, '/'));
    return $page ? get_permalink($page) : home_url('/' . trim($slug, '/') . '/');
}

function ruwa_page_url_any(array $slugs): string {
    foreach ($slugs as $slug) {
        $page = get_page_by_path(trim((string) $slug, '/'));
        if ($page) {
            return get_permalink($page);
        }
    }
    return home_url('/' . trim((string) ($slugs[0] ?? ''), '/') . '/');
}

function ruwa_shop_url(): string {
    return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : ruwa_page_url('shop');
}

function ruwa_account_url(): string {
    return function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : ruwa_page_url('my-account');
}

function ruwa_cart_url(): string {
    return function_exists('wc_get_cart_url') ? wc_get_cart_url() : ruwa_page_url('cart');
}

function ruwa_cart_count(): int {
    return function_exists('WC') && WC()->cart ? (int) WC()->cart->get_cart_contents_count() : 0;
}

function ruwa_cart_subtotal_number(): float {
    if (!function_exists('WC') || !WC()->cart) {
        return 0.0;
    }
    return (float) WC()->cart->get_subtotal();
}

function ruwa_shipping_progress(): array {
    $threshold = (float) apply_filters('ruwa_free_shipping_threshold', 5000);
    $subtotal = ruwa_cart_subtotal_number();
    $remaining = max(0, $threshold - $subtotal);
    $percent = $threshold > 0 ? min(100, ($subtotal / $threshold) * 100) : 100;
    return compact('threshold', 'subtotal', 'remaining', 'percent');
}

function ruwa_products(int $limit = 12, array $extra = []): array {
    if (!function_exists('wc_get_products')) {
        return [];
    }
    $args = wp_parse_args($extra, [
        'status'  => 'publish',
        'limit'   => $limit,
        'orderby' => 'date',
        'order'   => 'DESC',
    ]);
    return wc_get_products($args);
}

function ruwa_product_card($product, string $badge = ''): void {
    if (!$product || !is_a($product, 'WC_Product')) {
        return;
    }
    $id = $product->get_id();
    $classes = implode(' ', array_map('sanitize_html_class', wc_get_product_class('ruwa-product-card', $product)));
    echo '<article class="' . esc_attr($classes) . '">';
    if ($badge !== '') {
        echo '<span class="ruwa-badge">' . esc_html($badge) . '</span>';
    }
    echo '<a class="ruwa-product-media" href="' . esc_url($product->get_permalink()) . '">';
    echo wp_kses_post($product->get_image('woocommerce_thumbnail', ['loading' => 'lazy']));
    echo '</a>';
    echo '<div class="ruwa-product-copy">';
    $categories = wc_get_product_category_list($id, ', ');
    if ($categories) {
        echo '<small>' . wp_kses_post($categories) . '</small>';
    }
    echo '<h3><a href="' . esc_url($product->get_permalink()) . '">' . esc_html($product->get_name()) . '</a></h3>';
    if (wc_review_ratings_enabled()) {
        echo wp_kses_post(wc_get_rating_html((float) $product->get_average_rating(), (int) $product->get_review_count()));
    }
    echo '<div class="ruwa-price">' . wp_kses_post($product->get_price_html()) . '</div>';
    echo '</div><div class="ruwa-product-actions">';
    echo '<a class="ruwa-text-link" href="' . esc_url($product->get_permalink()) . '">' . esc_html__('View ritual', 'nub-ruwah') . '</a>';
    if ($product->is_purchasable() && $product->is_in_stock()) {
        echo '<a rel="nofollow" data-product_id="' . esc_attr((string) $id) . '" data-quantity="1" class="button add_to_cart_button ajax_add_to_cart ruwa-mini-add" href="' . esc_url($product->add_to_cart_url()) . '">' . esc_html__('Add', 'nub-ruwah') . '</a>';
    }
    echo '</div></article>';
}

function ruwa_product_reviews(int $limit = 8): array {
    if (!function_exists('wc_get_product')) {
        return [];
    }
    return get_comments([
        'status'  => 'approve',
        'type'    => 'review',
        'number'  => $limit,
        'orderby' => 'comment_date_gmt',
        'order'   => 'DESC',
        'post_type' => 'product',
    ]);
}

function ruwa_render_review($review): void {
    $rating = (int) get_comment_meta($review->comment_ID, 'rating', true);
    $rating = max(0, min(5, $rating));
    echo '<blockquote class="ruwa-review-card">';
    echo '<div class="ruwa-stars" aria-label="' . esc_attr(sprintf(__('%d out of 5 stars', 'nub-ruwah'), $rating)) . '">' . esc_html(str_repeat('★', $rating) . str_repeat('☆', 5 - $rating)) . '</div>';
    echo '<p>' . esc_html(wp_trim_words(wp_strip_all_tags($review->comment_content), 28)) . '</p>';
    echo '<cite>' . esc_html($review->comment_author) . '</cite>';
    echo '</blockquote>';
}

function ruwa_contact_form(string $type = 'contact'): void {
    $is_wholesale = $type === 'wholesale';
    $status = isset($_GET['ruwa_sent']) ? sanitize_key(wp_unslash($_GET['ruwa_sent'])) : '';
    if ($status === '1') {
        echo '<div class="ruwa-notice success" role="status">' . esc_html__('Thank you. Your message has been sent.', 'nub-ruwah') . '</div>';
    } elseif ($status === '0') {
        echo '<div class="ruwa-notice error" role="alert">' . esc_html__('Your message could not be sent. Please email us directly.', 'nub-ruwah') . '</div>';
    }
    ?>
    <form class="ruwa-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
        <input type="hidden" name="action" value="ruwa_inquiry">
        <input type="hidden" name="inquiry_type" value="<?php echo esc_attr($type); ?>">
        <?php wp_nonce_field('ruwa_inquiry', 'ruwa_inquiry_nonce'); ?>
        <div class="ruwa-honeypot" aria-hidden="true"><label>Website<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
        <?php if ($is_wholesale) : ?>
            <label><?php esc_html_e('Business name', 'nub-ruwah'); ?><input type="text" name="business_name" required></label>
        <?php endif; ?>
        <div class="ruwa-form-grid">
            <label><?php esc_html_e('Name', 'nub-ruwah'); ?><input type="text" name="name" autocomplete="name" required></label>
            <label><?php esc_html_e('Email', 'nub-ruwah'); ?><input type="email" name="email" autocomplete="email" required></label>
        </div>
        <?php if ($is_wholesale) : ?>
            <label><?php esc_html_e('Estimated order volume', 'nub-ruwah'); ?><input type="text" name="volume" placeholder="<?php esc_attr_e('Tell us what you have in mind', 'nub-ruwah'); ?>"></label>
        <?php else : ?>
            <label><?php esc_html_e('Subject', 'nub-ruwah'); ?><select name="subject"><option><?php esc_html_e('Product question', 'nub-ruwah'); ?></option><option><?php esc_html_e('Order support', 'nub-ruwah'); ?></option><option><?php esc_html_e('Wholesale or gifting', 'nub-ruwah'); ?></option><option><?php esc_html_e('Other', 'nub-ruwah'); ?></option></select></label>
        <?php endif; ?>
        <label><?php esc_html_e('Message', 'nub-ruwah'); ?><textarea name="message" rows="6" required></textarea></label>
        <button class="ruwa-button ruwa-button-primary" type="submit"><?php echo esc_html($is_wholesale ? __('Send wholesale inquiry', 'nub-ruwah') : __('Send message', 'nub-ruwah')); ?></button>
    </form>
    <?php
}

function ruwa_handle_inquiry(): void {
    $redirect = wp_get_referer() ?: home_url('/');
    if (
        !isset($_POST['ruwa_inquiry_nonce']) ||
        !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['ruwa_inquiry_nonce'])), 'ruwa_inquiry') ||
        !empty($_POST['website'])
    ) {
        wp_safe_redirect(add_query_arg('ruwa_sent', '0', $redirect));
        exit;
    }
    $type = isset($_POST['inquiry_type']) ? sanitize_key(wp_unslash($_POST['inquiry_type'])) : 'contact';
    $name = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $email = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $subject = isset($_POST['subject']) ? sanitize_text_field(wp_unslash($_POST['subject'])) : '';
    $message = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';
    $business = isset($_POST['business_name']) ? sanitize_text_field(wp_unslash($_POST['business_name'])) : '';
    $volume = isset($_POST['volume']) ? sanitize_text_field(wp_unslash($_POST['volume'])) : '';
    if ($name === '' || !is_email($email) || $message === '') {
        wp_safe_redirect(add_query_arg('ruwa_sent', '0', $redirect));
        exit;
    }
    $mail_subject = $type === 'wholesale' ? 'RUWA wholesale inquiry' : 'RUWA contact inquiry';
    $lines = ["Name: {$name}", "Email: {$email}"];
    if ($business !== '') $lines[] = "Business: {$business}";
    if ($volume !== '') $lines[] = "Volume: {$volume}";
    if ($subject !== '') $lines[] = "Subject: {$subject}";
    $lines[] = '';
    $lines[] = $message;
    $sent = wp_mail(get_option('admin_email'), $mail_subject, implode("\n", $lines), ['Reply-To: ' . $name . ' <' . $email . '>']);
    wp_safe_redirect(add_query_arg('ruwa_sent', $sent ? '1' : '0', $redirect));
    exit;
}
add_action('admin_post_nopriv_ruwa_inquiry', 'ruwa_handle_inquiry');
add_action('admin_post_ruwa_inquiry', 'ruwa_handle_inquiry');

add_filter('woocommerce_output_related_products_args', function ($args) {
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;
    return $args;
});
add_filter('loop_shop_columns', fn() => 4);

add_filter('woocommerce_add_to_cart_fragments', function ($fragments) {
    $fragments['.ruwa-cart-count'] = '<b class="ruwa-cart-count">' . esc_html((string) ruwa_cart_count()) . '</b>';
    ob_start();
    ?>
    <div class="widget_shopping_cart_content">
        <?php woocommerce_mini_cart(); ?>
    </div>
    <?php
    $fragments['.ruwa-cart-drawer .widget_shopping_cart_content'] = ob_get_clean();
    return $fragments;
});

add_action('woocommerce_single_product_summary', function () {
    global $product;
    if (!$product) return;
    $id = $product->get_id();
    $intensity = function_exists('get_field') ? get_field('actives_intensity', $id) : get_post_meta($id, 'actives_intensity', true);
    $feel = function_exists('get_field') ? get_field('skin_feel', $id) : get_post_meta($id, 'skin_feel', true);
    if (!$intensity && !$feel) return;
    echo '<div class="ruwa-product-meta">';
    if ($intensity) {
        $map = ['gentle' => 33, 'balanced' => 66, 'intensive' => 100];
        $key = sanitize_key(is_array($intensity) ? reset($intensity) : (string) $intensity);
        $width = $map[$key] ?? 50;
        echo '<div class="ruwa-intensity"><span>' . esc_html__('Actives intensity', 'nub-ruwah') . '</span><div><i style="width:' . esc_attr((string) $width) . '%"></i></div><small>' . esc_html(ucfirst($key)) . '</small></div>';
    }
    if ($feel) {
        $items = is_array($feel) ? $feel : array_filter(array_map('trim', explode(',', (string) $feel)));
        echo '<div class="ruwa-feel-pills">';
        foreach ($items as $item) echo '<span>' . esc_html((string) $item) . '</span>';
        echo '</div>';
    }
    echo '</div>';
}, 24);
