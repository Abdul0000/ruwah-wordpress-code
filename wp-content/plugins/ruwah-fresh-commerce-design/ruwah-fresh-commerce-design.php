<?php
/**
 * Plugin Name: Ruwah Fresh Commerce Design
 * Description: Reference-led editorial commerce experience for Ruwah Beauty using live WooCommerce products, pricing, stock, media and reviews.
 * Version: 6.4.4
 * Author: Ruwah Beauty
 * Requires PHP: 8.1
 */

defined('ABSPATH') || exit;

final class Ruwah_Fresh_Commerce_Design {
    private const VERSION = '6.4.4';

    public static function boot(): void {
        add_filter('template_include', [self::class, 'front_page_template'], 99);
        add_filter('template_include', [self::class, 'shop_template'], 100);
        add_filter('woocommerce_locate_template', [self::class, 'woocommerce_template'], 999, 3);
        add_filter('wc_get_template_part', [self::class, 'woocommerce_template_part'], 999, 3);
        add_action('wp_enqueue_scripts', [self::class, 'assets'], 999);
        add_filter('body_class', [self::class, 'body_class']);
        add_filter('woocommerce_currency', [self::class, 'currency_code'], 99);
        add_filter('woocommerce_currency_symbol', [self::class, 'currency_symbol'], 99, 2);
        add_filter('woocommerce_price_format', [self::class, 'price_format'], 99, 2);
        add_filter('wc_price_args', [self::class, 'price_args'], 20);
        add_action('wp_footer', [self::class, 'reference_footer'], 5);
    }

    public static function front_page_template(string $template): string {
        if (! is_front_page() || ! class_exists('WooCommerce')) return $template;
        $custom = __DIR__ . '/templates/home.php';
        return is_readable($custom) ? $custom : $template;
    }

    public static function shop_template(string $template): string {
        if (! class_exists('WooCommerce') || ! function_exists('is_shop') || ! is_shop()) return $template;
        $custom = __DIR__ . '/templates/shop-all.php';
        return is_readable($custom) ? $custom : $template;
    }

    public static function woocommerce_template(string $template, string $template_name, string $template_path): string {
        if (is_admin() && ! wp_doing_ajax()) return $template;
        $map = [
            'content-product.php' => __DIR__ . '/templates/woocommerce/content-product.php',
            'content-single-product.php' => __DIR__ . '/templates/woocommerce/content-single-product.php',
            'loop/loop-start.php' => __DIR__ . '/templates/woocommerce/loop/loop-start.php',
        ];
        return isset($map[$template_name]) && is_readable($map[$template_name]) ? $map[$template_name] : $template;
    }

    public static function woocommerce_template_part(string $template, string $slug, string $name): string {
        if (is_admin() && ! wp_doing_ajax()) return $template;
        if ('content' !== $slug) return $template;
        $map = [
            'product' => __DIR__ . '/templates/woocommerce/content-product.php',
            'single-product' => __DIR__ . '/templates/woocommerce/content-single-product.php',
        ];
        return isset($map[$name]) && is_readable($map[$name]) ? $map[$name] : $template;
    }

    private static function is_commerce_surface(): bool {
        if (! class_exists('WooCommerce')) return false;
        return (function_exists('is_shop') && is_shop())
            || (function_exists('is_product_taxonomy') && is_product_taxonomy())
            || (function_exists('is_product') && is_product())
            || (function_exists('is_cart') && is_cart());
    }

    public static function assets(): void {
        self::inline_global_style('ruwah-reference-footer', __DIR__ . '/assets/footer-dieux.css');
        if (is_front_page()) {
            self::inline_style('ruwah-reference-home', __DIR__ . '/assets/home.css');
            self::inline_style('ruwah-reference-commerce-home', __DIR__ . '/assets/commerce.css');
            self::inline_style('ruwah-reference-card-parity-home', __DIR__ . '/assets/card-parity.css');
            self::inline_style('ruwah-reference-home-commerce-adapter', __DIR__ . '/assets/home-commerce.css');
            wp_enqueue_script('wc-add-to-cart');
            $js_path = __DIR__ . '/assets/home.js';
            if (is_readable($js_path)) {
                $js = file_get_contents($js_path);
                if (false !== $js && '' !== trim($js)) wp_add_inline_script('wc-add-to-cart', $js, 'after');
            }
            return;
        }
        if (function_exists('is_shop') && is_shop()) {
            self::inline_style('ruwah-reference-commerce', __DIR__ . '/assets/commerce.css');
            self::inline_style('ruwah-dieux-shop-all', __DIR__ . '/assets/shop-dieux.css');
            wp_enqueue_script('wc-add-to-cart');
            return;
        }
        if (self::is_commerce_surface()) {
            self::inline_style('ruwah-reference-commerce', __DIR__ . '/assets/commerce.css');
            self::inline_style('ruwah-reference-card-parity', __DIR__ . '/assets/card-parity.css');
            if (function_exists('is_product') && is_product()) {
                self::inline_style('ruwah-dieux-pdp', __DIR__ . '/assets/pdp-dieux.css');
                wp_enqueue_script('ruwah-dieux-pdp', plugins_url('assets/pdp-dieux.js', __FILE__), ['jquery'], self::VERSION, true);
                wp_script_add_data('ruwah-dieux-pdp', 'strategy', 'defer');
            }
            if (function_exists('is_cart') && is_cart()) self::inline_style('ruwah-reference-cart', __DIR__ . '/assets/cart.css');
            wp_enqueue_script('wc-add-to-cart');
        }
    }

    private static function inline_style(string $handle, string $path): void {
        if (! is_readable($path)) return;
        $css = file_get_contents($path);
        if (false === $css || '' === trim($css)) return;
        wp_register_style($handle, false, ['rwb-theme'], self::VERSION);
        wp_enqueue_style($handle);
        wp_add_inline_style($handle, $css);
    }

    private static function inline_global_style(string $handle, string $path): void {
        if (! is_readable($path)) return;
        $css = file_get_contents($path);
        if (false === $css || '' === trim($css)) return;
        wp_register_style($handle, false, [], self::VERSION);
        wp_enqueue_style($handle);
        wp_add_inline_style($handle, $css);
    }

    public static function body_class(array $classes): array {
        if (is_front_page()) $classes[] = 'rwb-reference-home-v5';
        elseif (self::is_commerce_surface()) {
            $classes[] = 'rwb-reference-commerce-v6';
            if (function_exists('is_shop') && is_shop()) $classes[] = 'rwb-dieux-shop-v1';
        }
        return $classes;
    }

    public static function currency_code(string $currency): string { return 'PKR'; }
    public static function currency_symbol(string $symbol, string $currency = ''): string { return 'PKR' === $currency ? 'PKR' : $symbol; }
    public static function price_format(string $format, string $currency_pos = ''): string { return '%1$s&nbsp;%2$s'; }
    public static function price_args(array $args): array {
        if (is_front_page() || self::is_commerce_surface()) $args['decimals'] = 0;
        return $args;
    }

    public static function product_info(WC_Product $product): array {
        if (function_exists('rwb_info')) {
            $info = rwb_info($product);
            if (is_array($info)) return $info;
        }
        return ['tagline' => wp_strip_all_tags($product->get_short_description()), 'benefits' => [], 'size' => '', 'facts' => []];
    }

    public static function display_copy(WC_Product $product): array {
        $info = self::product_info($product);
        return [
            'name' => (string) $product->get_name(),
            'tagline' => (string) ($info['tagline'] ?? ''),
            'benefits' => (array) ($info['benefits'] ?? []),
            'size' => (string) ($info['size'] ?? ''),
            'facts' => (array) ($info['facts'] ?? []),
        ];
    }

    public static function product_badge(WC_Product $product, int $rank = 0): string {
        if (! $product->is_in_stock()) return 'OUT OF STOCK';
        if ($product->is_on_sale()) return 'OFFER';
        return 0 === $rank ? 'BESTSELLER' : '';
    }

    public static function render_card(WC_Product $product, int $rank = 0): void {
        $info = self::display_copy($product); $reviews = (int) $product->get_review_count(); $rating = (float) $product->get_average_rating();
        $badge = self::product_badge($product, $rank); $regular = (float) $product->get_regular_price(); $current = (float) $product->get_price(); ?>
        <a class="rwb-commerce-card-media" href="<?php echo esc_url($product->get_permalink()); ?>" aria-label="<?php echo esc_attr($info['name']); ?>">
            <?php if ($badge) : ?><span class="rwb-commerce-badge"><?php echo esc_html($badge); ?></span><?php endif; ?>
            <?php echo wp_kses_post($product->get_image('woocommerce_single', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
        </a>
        <div class="rwb-commerce-card-copy">
            <h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($info['name']); ?></a></h3>
            <?php if (! empty($info['tagline'])) : ?><p class="rwb-commerce-card-tagline"><?php echo esc_html($info['tagline']); ?></p><?php endif; ?>
            <?php if ($reviews > 0) : ?><div class="rwb-commerce-card-rating" aria-label="<?php echo esc_attr(number_format_i18n($rating, 1) . ' out of 5'); ?>"><span><?php echo esc_html(str_repeat('★', max(1, min(5, (int) round($rating))))); ?></span><small><?php echo esc_html((string) $reviews); ?></small></div><?php else : ?><div class="rwb-commerce-card-proof">RUWAH FORMULA</div><?php endif; ?>
            <?php if ($product->is_on_sale() && $regular > 0 && $current < $regular) : ?><div class="rwb-commerce-card-sale"><del><?php echo wp_kses_post(wc_price($regular, ['decimals' => 0])); ?></del><ins><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></ins></div><?php endif; ?>
            <?php if (! empty($info['size'])) : ?><label class="rwb-commerce-size"><span class="screen-reader-text">Pack size</span><select aria-label="Pack size for <?php echo esc_attr($info['name']); ?>"><option><?php echo esc_html(strtoupper($info['size'])); ?></option></select></label><?php endif; ?>
            <div class="rwb-commerce-card-action">
                <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?><a rel="nofollow" class="rwb-commerce-add add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($product->add_to_cart_url()); ?>"><span>Add to Cart</span><span><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></span></a><?php else : ?><a class="rwb-commerce-add" href="<?php echo esc_url($product->get_permalink()); ?>"><span>View Product</span><span><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></span></a><?php endif; ?>
            </div>
        </div><?php
    }

    public static function gallery_ids(WC_Product $product): array { return array_values(array_unique(array_filter(array_merge([(int) $product->get_image_id()], array_map('intval', $product->get_gallery_image_ids()))))); }

    public static function related_products(WC_Product $product): array {
        if (! function_exists('wc_get_related_products') || ! function_exists('wc_get_product')) return [];
        $ids = wc_get_related_products($product->get_id(), 4, []);
        $items = [];
        foreach ($ids as $id) {
            $candidate = wc_get_product((int) $id);
            if ($candidate instanceof WC_Product && $candidate->is_visible()) $items[] = $candidate;
        }
        return $items;
    }

    private static function footer_products(): array {
        if (! function_exists('wc_get_products')) return [];
        $products = wc_get_products(['status' => 'publish', 'limit' => 5, 'orderby' => 'menu_order', 'order' => 'ASC']);
        return array_values(array_filter($products, static fn($item) => $item instanceof WC_Product && $item->is_visible()));
    }

    public static function reference_footer(): void {
        if (is_admin()) return;
        $products = self::footer_products(); $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
        $account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/'); $privacy_url = get_privacy_policy_url();
        $contact_url = function_exists('ruwah_page_url') ? ruwah_page_url('contact') : home_url('/contact/'); $refund_url = function_exists('ruwah_page_url') ? ruwah_page_url('refund-policy') : home_url('/refund-policy/');
        $terms_url = ''; if (function_exists('wc_get_page_id')) { $terms_id = (int) wc_get_page_id('terms'); if ($terms_id > 0) $terms_url = get_permalink($terms_id); } ?>
        <footer class="rwb-dieux-footer" id="rwb-reference-footer">
            <div class="rwb-dieux-footer-main">
                <section class="rwb-dieux-footer-signup" aria-labelledby="rwb-footer-signup-title"><h2 id="rwb-footer-signup-title">Join Ruwah Notes</h2><p>Skincare guidance, product updates and occasional offers. Unsubscribe any time.</p><form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>"><input type="hidden" name="action" value="rwb_newsletter"><?php wp_nonce_field('rwb_newsletter', 'rwb_nonce'); ?><label class="screen-reader-text" for="rwb-dieux-footer-email">Email address</label><div class="rwb-dieux-footer-form"><input id="rwb-dieux-footer-email" type="email" name="email" required autocomplete="email" placeholder="Email address"><button type="submit">Subscribe</button></div><?php if ($privacy_url) : ?><small>By subscribing, you agree to receive Ruwah Notes by email. See our <a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a>.</small><?php endif; ?><div class="rwb-dieux-footer-socials" aria-label="Ruwah Beauty social channels"><a href="https://www.facebook.com/share/1BNAdjWpYW/" target="_blank" rel="noopener noreferrer" aria-label="Ruwah Beauty on Facebook"><span aria-hidden="true">f</span></a><a href="https://www.instagram.com/rawah.beauty" target="_blank" rel="noopener noreferrer" aria-label="Ruwah Beauty on Instagram"><span aria-hidden="true">◎</span></a><a href="https://vt.tiktok.com/ZSX6WqwS2/" target="_blank" rel="noopener noreferrer" aria-label="Ruwah Beauty on TikTok"><span aria-hidden="true">♪</span></a></div></section>
                <section class="rwb-dieux-footer-col"><h2>Shop</h2><?php foreach ($products as $product) : $copy = self::display_copy($product); ?><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($copy['name']); ?></a><?php endforeach; ?><a href="<?php echo esc_url($shop_url); ?>">Shop All</a></section>
                <section class="rwb-dieux-footer-col"><h2>Learn</h2><a href="<?php echo esc_url(home_url('/#rwb-genesis')); ?>">Our Genesis</a><a href="<?php echo esc_url(home_url('/#rwb-standard')); ?>">The Ruwah Standard</a><a href="<?php echo esc_url(home_url('/#rituals')); ?>">Rituals</a><a href="<?php echo esc_url($shop_url); ?>">Formula Guide</a></section>
                <section class="rwb-dieux-footer-col"><h2>Contact</h2><a href="mailto:rawahbeauty783@gmail.com" aria-label="Email Ruwah Beauty support at rawahbeauty783@gmail.com">rawahbeauty783@gmail.com</a><a href="https://wa.me/923713923279" target="_blank" rel="noopener noreferrer" aria-label="Chat with Ruwah Beauty on WhatsApp">WhatsApp Support</a><a href="<?php echo esc_url($contact_url); ?>">Contact Us</a><?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a><?php endif; ?><a href="<?php echo esc_url($account_url); ?>">My Account</a><small>Support replies are typically sent within 2 business days.</small></section>
                <section class="rwb-dieux-footer-promise"><h2>Our Promise</h2><div class="rwb-dieux-promise-mark" aria-hidden="true"><span>◉</span><b>RUWAH<br>PROMISE</b></div><p>Clear product details.<br>Current price &amp; availability.<br>Measured skincare claims.</p></section>
            </div>
            <div class="rwb-dieux-footer-bottom"><div class="rwb-dieux-footer-meta"><b>© <?php echo esc_html(wp_date('Y')); ?> Ruwah Beauty</b><span>Pakistan · Online skincare</span><div class="rwb-dieux-payments"><span>COD ONLY</span></div></div><nav class="rwb-dieux-footer-legal" aria-label="Footer legal links"><?php if ($terms_url) : ?><a href="<?php echo esc_url($terms_url); ?>">Terms of Service</a><?php endif; ?><?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a><?php endif; ?><a href="<?php echo esc_url($refund_url); ?>">Refund Policy</a><a href="<?php echo esc_url($contact_url); ?>">Contact</a></nav></div>
            <a class="rwb-dieux-footer-promo" href="#rwb-dieux-footer-email">Join Ruwah Notes <span aria-hidden="true">×</span></a>
        </footer><?php
    }
}

Ruwah_Fresh_Commerce_Design::boot();
require_once __DIR__ . '/includes/cart-limit.php';

if (! function_exists('rwb_checkout_value')) {
    function rwb_checkout_value(array $data, string $key): string { return isset($data[$key]) ? trim(wp_strip_all_tags((string) $data[$key])) : ''; }
}
if (! function_exists('rwb_checkout_length')) {
    function rwb_checkout_length(string $value): int { return function_exists('mb_strlen') ? (int) mb_strlen($value) : strlen($value); }
}
if (! function_exists('rwb_checkout_validate_name')) {
    function rwb_checkout_validate_name(array $data, WP_Error $errors, string $key, string $label): void {
        $value = rwb_checkout_value($data, $key); if ('' === $value) return; $length = rwb_checkout_length($value);
        if ($length < 2 || $length > 60 || ! preg_match("/^[\\p{L}\\p{M}][\\p{L}\\p{M}\\s.'-]*$/u", $value)) $errors->add('rwb_' . $key . '_invalid', sprintf('Please enter a valid %s using letters only.', $label));
    }
}
if (! function_exists('rwb_checkout_validate_address')) {
    function rwb_checkout_validate_address(array $data, WP_Error $errors, string $key, string $label): void {
        $value = rwb_checkout_value($data, $key); if ('' === $value) return; $length = rwb_checkout_length($value); $tokens = preg_split('/[\\s,\\/-]+/u', $value, -1, PREG_SPLIT_NO_EMPTY);
        if ($length < 8 || $length > 160 || ! preg_match('/\\p{L}/u', $value) || count((array) $tokens) < 2) $errors->add('rwb_' . $key . '_invalid', sprintf('Please enter a complete %s with street/area details.', $label));
    }
}
if (! function_exists('rwb_checkout_validate_city')) {
    function rwb_checkout_validate_city(array $data, WP_Error $errors, string $key, string $label): void {
        $value = rwb_checkout_value($data, $key); if ('' === $value) return; $length = rwb_checkout_length($value);
        if ($length < 2 || $length > 80 || ! preg_match("/^[\\p{L}\\p{M}][\\p{L}\\p{M}\\s.'-]*$/u", $value)) $errors->add('rwb_' . $key . '_invalid', sprintf('Please enter a valid %s.', $label));
    }
}
if (! function_exists('rwb_checkout_validate_postcode')) {
    function rwb_checkout_validate_postcode(array $data, WP_Error $errors, string $key, string $country_key, string $label): void {
        $value = rwb_checkout_value($data, $key); if ('' === $value) return; $country = strtoupper(rwb_checkout_value($data, $country_key));
        if ('PK' === $country) { if (! preg_match('/^\\d{5}$/', $value)) $errors->add('rwb_' . $key . '_invalid', sprintf('%s must be a 5-digit Pakistan postal code.', $label)); return; }
        if (rwb_checkout_length($value) > 12 || ! preg_match('/^[A-Za-z0-9 -]+$/', $value)) $errors->add('rwb_' . $key . '_invalid', sprintf('Please enter a valid %s.', $label));
    }
}
if (! function_exists('rwb_checkout_validate_phone')) {
    function rwb_checkout_validate_phone(array $data, WP_Error $errors): void {
        $phone = rwb_checkout_value($data, 'billing_phone'); if ('' === $phone) return; $country = strtoupper(rwb_checkout_value($data, 'billing_country')) ?: 'PK'; $digits = preg_replace('/\\D+/', '', $phone);
        if ('PK' === $country) { $valid = (bool) preg_match('/^(?:03\\d{9}|923\\d{9}|3\\d{9})$/', (string) $digits); if (! $valid) $errors->add('rwb_billing_phone_invalid', 'Please enter a valid Pakistani mobile number, e.g. 03001234567 or +923001234567.'); return; }
        $length = strlen((string) $digits); if ($length < 7 || $length > 15) $errors->add('rwb_billing_phone_invalid', 'Please enter a valid phone number including country code when needed.');
    }
}
if (! function_exists('rwb_validate_checkout_contact_fields')) {
    function rwb_validate_checkout_contact_fields(array $data, WP_Error $errors): void {
        rwb_checkout_validate_name($data, $errors, 'billing_first_name', 'first name'); rwb_checkout_validate_name($data, $errors, 'billing_last_name', 'last name'); rwb_checkout_validate_address($data, $errors, 'billing_address_1', 'billing address'); rwb_checkout_validate_city($data, $errors, 'billing_city', 'billing city'); rwb_checkout_validate_postcode($data, $errors, 'billing_postcode', 'billing_country', 'Billing postcode'); rwb_checkout_validate_phone($data, $errors);
        if (! empty($data['ship_to_different_address'])) { rwb_checkout_validate_name($data, $errors, 'shipping_first_name', 'shipping first name'); rwb_checkout_validate_name($data, $errors, 'shipping_last_name', 'shipping last name'); rwb_checkout_validate_address($data, $errors, 'shipping_address_1', 'shipping address'); rwb_checkout_validate_city($data, $errors, 'shipping_city', 'shipping city'); rwb_checkout_validate_postcode($data, $errors, 'shipping_postcode', 'shipping_country', 'Shipping postcode'); }
    }
}
add_action('woocommerce_after_checkout_validation', 'rwb_validate_checkout_contact_fields', 20, 2);

if (! function_exists('rwb_checkout_field_hints')) {
    function rwb_checkout_field_hints(array $fields): array {
        if (isset($fields['billing']['billing_phone'])) { $fields['billing']['billing_phone']['type'] = 'tel'; $fields['billing']['billing_phone']['placeholder'] = '03001234567'; $fields['billing']['billing_phone']['autocomplete'] = 'tel'; $fields['billing']['billing_phone']['custom_attributes']['inputmode'] = 'tel'; $fields['billing']['billing_phone']['custom_attributes']['maxlength'] = '18'; }
        foreach (['billing_first_name','billing_last_name'] as $key) if (isset($fields['billing'][$key])) $fields['billing'][$key]['custom_attributes']['maxlength'] = '60';
        if (isset($fields['billing']['billing_address_1'])) $fields['billing']['billing_address_1']['custom_attributes']['maxlength'] = '160'; if (isset($fields['billing']['billing_city'])) $fields['billing']['billing_city']['custom_attributes']['maxlength'] = '80';
        foreach (['shipping_first_name','shipping_last_name'] as $key) if (isset($fields['shipping'][$key])) $fields['shipping'][$key]['custom_attributes']['maxlength'] = '60';
        if (isset($fields['shipping']['shipping_address_1'])) $fields['shipping']['shipping_address_1']['custom_attributes']['maxlength'] = '160'; if (isset($fields['shipping']['shipping_city'])) $fields['shipping']['shipping_city']['custom_attributes']['maxlength'] = '80'; return $fields;
    }
}
add_filter('woocommerce_checkout_fields', 'rwb_checkout_field_hints', 50);

if (! function_exists('rwb_checkout_phone_country_rules')) {
    function rwb_checkout_phone_country_rules(): array {
        return [
            'PK' => ['dial' => '92', 'example' => '+92 300 1234567'], 'AE' => ['dial' => '971', 'example' => '+971 50 123 4567'], 'SA' => ['dial' => '966', 'example' => '+966 50 123 4567'],
            'QA' => ['dial' => '974', 'example' => '+974 3312 3456'], 'KW' => ['dial' => '965', 'example' => '+965 5000 1234'], 'BH' => ['dial' => '973', 'example' => '+973 3600 1234'], 'OM' => ['dial' => '968', 'example' => '+968 9212 3456'],
            'US' => ['dial' => '1', 'example' => '+1 202 555 0123'], 'CA' => ['dial' => '1', 'example' => '+1 416 555 0123'], 'GB' => ['dial' => '44', 'example' => '+44 7700 900123'], 'IE' => ['dial' => '353', 'example' => '+353 85 123 4567'],
            'AU' => ['dial' => '61', 'example' => '+61 412 345 678'], 'NZ' => ['dial' => '64', 'example' => '+64 21 123 4567'], 'IN' => ['dial' => '91', 'example' => '+91 98765 43210'], 'BD' => ['dial' => '880', 'example' => '+880 1712 345678'],
            'LK' => ['dial' => '94', 'example' => '+94 77 123 4567'], 'SG' => ['dial' => '65', 'example' => '+65 8123 4567'], 'MY' => ['dial' => '60', 'example' => '+60 12 345 6789'], 'ID' => ['dial' => '62', 'example' => '+62 812 3456 7890'],
            'TH' => ['dial' => '66', 'example' => '+66 81 234 5678'], 'PH' => ['dial' => '63', 'example' => '+63 917 123 4567'], 'CN' => ['dial' => '86', 'example' => '+86 138 0013 8000'], 'JP' => ['dial' => '81', 'example' => '+81 90 1234 5678'],
            'KR' => ['dial' => '82', 'example' => '+82 10 1234 5678'], 'HK' => ['dial' => '852', 'example' => '+852 9123 4567'], 'DE' => ['dial' => '49', 'example' => '+49 1512 3456789'], 'FR' => ['dial' => '33', 'example' => '+33 6 12 34 56 78'],
            'IT' => ['dial' => '39', 'example' => '+39 312 345 6789'], 'ES' => ['dial' => '34', 'example' => '+34 612 345 678'], 'NL' => ['dial' => '31', 'example' => '+31 6 12345678'], 'BE' => ['dial' => '32', 'example' => '+32 470 12 34 56'],
            'CH' => ['dial' => '41', 'example' => '+41 79 123 45 67'], 'AT' => ['dial' => '43', 'example' => '+43 664 1234567'], 'SE' => ['dial' => '46', 'example' => '+46 70 123 45 67'], 'NO' => ['dial' => '47', 'example' => '+47 412 34 567'],
            'DK' => ['dial' => '45', 'example' => '+45 20 12 34 56'], 'FI' => ['dial' => '358', 'example' => '+358 40 123 4567'], 'PL' => ['dial' => '48', 'example' => '+48 512 345 678'], 'PT' => ['dial' => '351', 'example' => '+351 912 345 678'],
            'GR' => ['dial' => '30', 'example' => '+30 691 234 5678'], 'TR' => ['dial' => '90', 'example' => '+90 532 123 4567'], 'ZA' => ['dial' => '27', 'example' => '+27 82 123 4567'], 'NG' => ['dial' => '234', 'example' => '+234 803 123 4567'],
            'KE' => ['dial' => '254', 'example' => '+254 712 345678'], 'EG' => ['dial' => '20', 'example' => '+20 100 123 4567'], 'BR' => ['dial' => '55', 'example' => '+55 11 91234 5678'], 'MX' => ['dial' => '52', 'example' => '+52 55 1234 5678'],
        ];
    }
}

if (! function_exists('rwb_checkout_validate_phone_international')) {
    function rwb_checkout_validate_phone_international(array $data, WP_Error $errors): void {
        $phone = rwb_checkout_value($data, 'billing_phone'); if ('' === $phone) return;
        $country = strtoupper(rwb_checkout_value($data, 'billing_country')) ?: 'PK'; if ('PK' === $country) return;
        if (! preg_match('/^\\+[0-9() .-]+$/', $phone)) {
            $example = rwb_checkout_phone_country_rules()[$country]['example'] ?? '+44 7700 900123';
            $errors->add('rwb_billing_phone_international_format', sprintf('For international orders, enter the phone with + country code, e.g. %s.', $example)); return;
        }
        $digits = (string) preg_replace('/\\D+/', '', $phone); $length = strlen($digits);
        if ($length < 8 || $length > 15 || preg_match('/^(\\d)\\1{7,}$/', $digits)) {
            $errors->add('rwb_billing_phone_international_invalid', 'Please enter a valid international phone number with 8 to 15 digits.'); return;
        }
        $rules = rwb_checkout_phone_country_rules();
        if (isset($rules[$country]) && ! str_starts_with($digits, $rules[$country]['dial'])) {
            $errors->add('rwb_billing_phone_country_mismatch', sprintf('Phone number does not match the selected country. Use a number like %s.', $rules[$country]['example']));
        }
    }
}
add_action('woocommerce_after_checkout_validation', 'rwb_checkout_validate_phone_international', 30, 2);

if (! function_exists('rwb_checkout_international_phone_hints')) {
    function rwb_checkout_international_phone_hints(array $fields): array {
        if (! isset($fields['billing']['billing_phone'])) return $fields;
        $country = 'PK'; if (function_exists('WC') && WC()->customer) $country = strtoupper((string) WC()->customer->get_billing_country()) ?: 'PK';
        $rules = rwb_checkout_phone_country_rules(); $example = $rules[$country]['example'] ?? '+Country code Phone number';
        $fields['billing']['billing_phone']['type'] = 'tel'; $fields['billing']['billing_phone']['placeholder'] = $example; $fields['billing']['billing_phone']['autocomplete'] = 'tel';
        $fields['billing']['billing_phone']['custom_attributes']['inputmode'] = 'tel'; $fields['billing']['billing_phone']['custom_attributes']['maxlength'] = '24';
        $fields['billing']['billing_phone']['custom_attributes']['pattern'] = '\\+?[0-9() .-]{7,24}'; $fields['billing']['billing_phone']['custom_attributes']['title'] = 'Use + country code for international numbers. Example: ' . $example;
        return $fields;
    }
}
add_filter('woocommerce_checkout_fields', 'rwb_checkout_international_phone_hints', 80);

if (! function_exists('rwb_checkout_phone_placeholder_script')) {
    function rwb_checkout_phone_placeholder_script(): void {
        if (! function_exists('is_checkout') || ! is_checkout() || (function_exists('is_order_received_page') && is_order_received_page())) return;
        $examples = []; foreach (rwb_checkout_phone_country_rules() as $code => $rule) $examples[$code] = $rule['example']; ?>
        <script id="rwb-checkout-phone-hints">
        (function(){
            const examples=<?php echo wp_json_encode($examples); ?>;
            function updatePhoneHint(){const c=document.getElementById('billing_country');const p=document.getElementById('billing_phone');if(!p)return;const code=c&&c.value?String(c.value).toUpperCase():'PK';const ex=examples[code]||'+Country code Phone number';p.placeholder=ex;p.title='Use + country code for international numbers. Example: '+ex;p.maxLength=24;p.setAttribute('inputmode','tel');}
            document.addEventListener('change',function(e){if(e.target&&e.target.id==='billing_country')updatePhoneHint();});
            if(window.jQuery){jQuery(document.body).on('updated_checkout country_to_state_changed',updatePhoneHint);} document.addEventListener('DOMContentLoaded',updatePhoneHint); updatePhoneHint();
        })();
        </script><?php
    }
}
add_action('wp_footer', 'rwb_checkout_phone_placeholder_script', 40);