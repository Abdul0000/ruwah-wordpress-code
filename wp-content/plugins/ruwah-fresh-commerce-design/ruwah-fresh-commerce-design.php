<?php
/**
 * Plugin Name: Ruwah Fresh Commerce Design
 * Description: Reference-led editorial commerce experience for Ruwah Beauty using live WooCommerce products, pricing, stock, media and reviews.
 * Version: 6.2.0
 * Author: Ruwah Beauty
 * Requires PHP: 8.1
 */

defined('ABSPATH') || exit;

final class Ruwah_Fresh_Commerce_Design {
    private const VERSION = '6.2.0';

    public static function boot(): void {
        add_filter('template_include', [self::class, 'front_page_template'], 99);
        add_filter('woocommerce_locate_template', [self::class, 'woocommerce_template'], 999, 3);
        add_filter('wc_get_template_part', [self::class, 'woocommerce_template_part'], 999, 3);
        add_action('wp_enqueue_scripts', [self::class, 'assets'], 999);
        add_filter('body_class', [self::class, 'body_class']);
        add_filter('wc_price_args', [self::class, 'price_args'], 20);
        add_action('get_footer', [self::class, 'commerce_newsletter'], 5);
    }

    public static function front_page_template(string $template): string {
        if (! is_front_page() || ! class_exists('WooCommerce')) {
            return $template;
        }
        $custom = __DIR__ . '/templates/home.php';
        return is_readable($custom) ? $custom : $template;
    }

    public static function woocommerce_template(string $template, string $template_name, string $template_path): string {
        if (is_admin() && ! wp_doing_ajax()) {
            return $template;
        }
        $map = [
            'content-product.php' => __DIR__ . '/templates/woocommerce/content-product.php',
            'content-single-product.php' => __DIR__ . '/templates/woocommerce/content-single-product.php',
            'loop/loop-start.php' => __DIR__ . '/templates/woocommerce/loop/loop-start.php',
        ];
        if (isset($map[$template_name]) && is_readable($map[$template_name])) {
            return $map[$template_name];
        }
        return $template;
    }

    public static function woocommerce_template_part(string $template, string $slug, string $name): string {
        if (is_admin() && ! wp_doing_ajax()) {
            return $template;
        }
        if ('content' !== $slug) {
            return $template;
        }
        $map = [
            'product' => __DIR__ . '/templates/woocommerce/content-product.php',
            'single-product' => __DIR__ . '/templates/woocommerce/content-single-product.php',
        ];
        if (isset($map[$name]) && is_readable($map[$name])) {
            return $map[$name];
        }
        return $template;
    }

    private static function is_commerce_surface(): bool {
        if (! class_exists('WooCommerce')) {
            return false;
        }
        return (function_exists('is_shop') && is_shop())
            || (function_exists('is_product_taxonomy') && is_product_taxonomy())
            || (function_exists('is_product') && is_product())
            || (function_exists('is_cart') && is_cart());
    }

    public static function assets(): void {
        if (is_front_page()) {
            self::inline_style('ruwah-reference-home', __DIR__ . '/assets/home.css');
            self::inline_style('ruwah-reference-commerce-home', __DIR__ . '/assets/commerce.css');
            self::inline_style('ruwah-reference-card-parity-home', __DIR__ . '/assets/card-parity.css');
            self::inline_style('ruwah-reference-home-commerce-adapter', __DIR__ . '/assets/home-commerce.css');
            wp_enqueue_script('wc-add-to-cart');
            $js_path = __DIR__ . '/assets/home.js';
            if (is_readable($js_path)) {
                $js = file_get_contents($js_path);
                if (false !== $js && '' !== trim($js)) {
                    wp_add_inline_script('wc-add-to-cart', $js, 'after');
                }
            }
            return;
        }
        if (self::is_commerce_surface()) {
            self::inline_style('ruwah-reference-commerce', __DIR__ . '/assets/commerce.css');
            self::inline_style('ruwah-reference-card-parity', __DIR__ . '/assets/card-parity.css');
            if (function_exists('is_cart') && is_cart()) {
                self::inline_style('ruwah-reference-cart', __DIR__ . '/assets/cart.css');
            }
            wp_enqueue_script('wc-add-to-cart');
        }
    }

    private static function inline_style(string $handle, string $path): void {
        if (! is_readable($path)) {
            return;
        }
        $css = file_get_contents($path);
        if (false === $css || '' === trim($css)) {
            return;
        }
        wp_register_style($handle, false, ['rwb-theme'], self::VERSION);
        wp_enqueue_style($handle);
        wp_add_inline_style($handle, $css);
    }

    public static function body_class(array $classes): array {
        if (is_front_page()) {
            $classes[] = 'rwb-reference-home-v5';
        } elseif (self::is_commerce_surface()) {
            $classes[] = 'rwb-reference-commerce-v6';
        }
        return $classes;
    }

    public static function price_args(array $args): array {
        if (is_front_page() || self::is_commerce_surface()) {
            $args['decimals'] = 0;
        }
        return $args;
    }

    public static function product_info(WC_Product $product): array {
        if (function_exists('rwb_info')) {
            $info = rwb_info($product);
            if (is_array($info)) {
                return $info;
            }
        }
        return [
            'tagline' => wp_strip_all_tags($product->get_short_description()),
            'benefits' => [],
            'size' => '',
            'facts' => [],
        ];
    }

    public static function product_badge(WC_Product $product, int $rank = 0): string {
        if (! $product->is_in_stock()) {
            return 'OUT OF STOCK';
        }
        if ($product->is_on_sale()) {
            return 'OFFER';
        }
        if (0 === $rank) {
            return 'BESTSELLER';
        }
        return '';
    }

    public static function render_card(WC_Product $product, int $rank = 0): void {
        $info = self::product_info($product);
        $reviews = (int) $product->get_review_count();
        $rating = (float) $product->get_average_rating();
        $badge = self::product_badge($product, $rank);
        $regular = (float) $product->get_regular_price();
        $current = (float) $product->get_price();
        ?>
        <a class="rwb-commerce-card-media" href="<?php echo esc_url($product->get_permalink()); ?>" aria-label="<?php echo esc_attr($product->get_name()); ?>">
            <?php if ($badge) : ?><span class="rwb-commerce-badge"><?php echo esc_html($badge); ?></span><?php endif; ?>
            <?php echo wp_kses_post($product->get_image('woocommerce_single', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
        </a>
        <div class="rwb-commerce-card-copy">
            <h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a></h3>
            <?php if (! empty($info['tagline'])) : ?><p class="rwb-commerce-card-tagline"><?php echo esc_html($info['tagline']); ?></p><?php endif; ?>
            <?php if ($reviews > 0) : ?>
                <div class="rwb-commerce-card-rating" aria-label="<?php echo esc_attr(number_format_i18n($rating, 1) . ' out of 5'); ?>"><span><?php echo esc_html(str_repeat('★', max(1, min(5, (int) round($rating))))); ?></span><small><?php echo esc_html((string) $reviews); ?></small></div>
            <?php else : ?>
                <div class="rwb-commerce-card-proof">RUWAH FORMULA</div>
            <?php endif; ?>
            <?php if ($product->is_on_sale() && $regular > 0 && $current < $regular) : ?>
                <div class="rwb-commerce-card-sale"><del><?php echo wp_kses_post(wc_price($regular, ['decimals' => 0])); ?></del><ins><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></ins></div>
            <?php endif; ?>
            <?php if (! empty($info['size'])) : ?>
                <label class="rwb-commerce-size"><span class="screen-reader-text">Pack size</span><select aria-label="Pack size for <?php echo esc_attr($product->get_name()); ?>"><option><?php echo esc_html(strtoupper($info['size'])); ?></option></select></label>
            <?php endif; ?>
            <div class="rwb-commerce-card-action">
                <?php if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) : ?>
                    <a rel="nofollow" class="rwb-commerce-add add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($product->add_to_cart_url()); ?>"><span>Add to Cart</span><span><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></span></a>
                <?php else : ?>
                    <a class="rwb-commerce-add" href="<?php echo esc_url($product->get_permalink()); ?>"><span>View Product</span><span><?php echo wp_kses_post(wc_price($current, ['decimals' => 0])); ?></span></a>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    public static function gallery_ids(WC_Product $product): array {
        return array_values(array_unique(array_filter(array_merge([(int) $product->get_image_id()], array_map('intval', $product->get_gallery_image_ids())))));
    }

    public static function related_products(WC_Product $product): array {
        $items = [];
        if (function_exists('rwb_products')) {
            foreach (rwb_products() as $candidate) {
                if ($candidate instanceof WC_Product && $candidate->get_id() !== $product->get_id()) {
                    $items[] = $candidate;
                }
            }
        }
        return array_slice($items, 0, 4);
    }

    public static function commerce_newsletter(): void {
        if (is_front_page() || ! self::is_commerce_surface()) {
            return;
        }
        ?>
        <section class="rwb-commerce-newsletter">
            <div class="rwb-shell">
                <div><p class="rwb-eyebrow">GET RUWAH-Y</p><h2>New drops, ritual edits and useful skin notes.</h2></div>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="rwb_newsletter">
                    <?php wp_nonce_field('rwb_newsletter', 'rwb_nonce'); ?>
                    <input type="email" name="email" required placeholder="My email address is">
                    <button type="submit">Initiate me</button>
                </form>
            </div>
        </section>
        <?php
    }
}

Ruwah_Fresh_Commerce_Design::boot();