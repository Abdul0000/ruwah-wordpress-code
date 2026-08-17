<?php
defined('ABSPATH') || exit;

final class Ruwah_Reference_Cart_Drawer {
    private const VERSION = '1.0.0';
    private const NONCE_ACTION = 'rwb_reference_cart_drawer';

    public static function boot(): void {
        add_action('wp_enqueue_scripts', [self::class, 'assets'], 1000);
        add_action('wp_footer', [self::class, 'render'], 30);
        add_filter('woocommerce_add_to_cart_fragments', [self::class, 'fragments'], 50);
        add_action('wp_ajax_rwb_drawer_update', [self::class, 'ajax_update']);
        add_action('wp_ajax_nopriv_rwb_drawer_update', [self::class, 'ajax_update']);
    }

    public static function assets(): void {
        if (is_admin() || ! class_exists('WooCommerce')) return;
        $css_path = get_template_directory() . '/assets/reference-cart-drawer.css';
        if (is_readable($css_path)) {
            $css = file_get_contents($css_path);
            if (false !== $css && '' !== trim($css)) {
                wp_register_style('ruwah-reference-cart-drawer', false, [], self::VERSION);
                wp_enqueue_style('ruwah-reference-cart-drawer');
                wp_add_inline_style('ruwah-reference-cart-drawer', $css);
            }
        }
        wp_enqueue_script('wc-add-to-cart');
        wp_register_script('ruwah-reference-cart-drawer', false, [], self::VERSION, true);
        wp_enqueue_script('ruwah-reference-cart-drawer');
        wp_localize_script('ruwah-reference-cart-drawer', 'rwbCartDrawer', ['ajaxUrl' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce(self::NONCE_ACTION)]);
        $js_path = get_template_directory() . '/assets/reference-cart-drawer.js';
        if (is_readable($js_path)) {
            $js = file_get_contents($js_path);
            if (false !== $js && '' !== trim($js)) wp_add_inline_script('ruwah-reference-cart-drawer', $js, 'after');
        }
    }

    private static function cart(): ?WC_Cart {
        return function_exists('WC') && WC()->cart instanceof WC_Cart ? WC()->cart : null;
    }

    private static function item_count(): int {
        $cart = self::cart();
        return $cart ? (int) $cart->get_cart_contents_count() : 0;
    }

    private static function paired_product(): ?WC_Product {
        $cart = self::cart();
        $in_cart = [];
        if ($cart) foreach ($cart->get_cart() as $item) $in_cart[] = (int) ($item['product_id'] ?? 0);
        $candidates = function_exists('ruwah_products') ? ruwah_products(12, ['orderby' => 'menu_order', 'order' => 'ASC']) : [];
        foreach ($candidates as $candidate) {
            if (! $candidate instanceof WC_Product || in_array((int) $candidate->get_id(), $in_cart, true)) continue;
            if ($candidate->is_visible() && $candidate->is_type('simple') && $candidate->is_purchasable() && $candidate->is_in_stock()) return $candidate;
        }
        return null;
    }

    private static function money(float $amount): string {
        return wc_price($amount, ['decimals' => 0]);
    }

    private static function paired_tagline(WC_Product $product): string {
        if (class_exists('Ruwah_Fresh_Commerce_Design')) {
            $info = Ruwah_Fresh_Commerce_Design::product_info($product);
            if (! empty($info['tagline'])) return (string) $info['tagline'];
        }
        return wp_strip_all_tags($product->get_short_description());
    }

    public static function dynamic_html(): string {
        $cart = self::cart();
        ob_start();
        if (! $cart || $cart->is_empty()) {
            ?><div class="rwb-ref-cart-empty"><p>Your cart is empty.</p><a href="<?php echo esc_url(ruwah_shop_url()); ?>">Shop all</a></div><?php
            return (string) ob_get_clean();
        }
        foreach ($cart->get_cart() as $cart_key => $item) {
            $product = isset($item['data']) && $item['data'] instanceof WC_Product ? $item['data'] : null;
            if (! $product || ! $product->exists()) continue;
            $qty = max(1, (int) ($item['quantity'] ?? 1));
            $max_qty = (int) $product->get_max_purchase_quantity();
            if ($max_qty < 1) $max_qty = 99;
            ?>
            <article class="rwb-ref-cart-item">
                <a class="rwb-ref-cart-item-media" href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo wp_kses_post($product->get_image('woocommerce_thumbnail', ['loading' => 'lazy', 'decoding' => 'async'])); ?></a>
                <div class="rwb-ref-cart-item-copy"><h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a></h3><div class="rwb-ref-cart-item-price"><?php echo wp_kses_post(self::money((float) $product->get_price())); ?></div><div class="rwb-ref-cart-qty" aria-label="Quantity controls"><button type="button" data-rwb-qty data-key="<?php echo esc_attr($cart_key); ?>" data-delta="-1" aria-label="Decrease quantity">−</button><span data-rwb-qty-value data-key="<?php echo esc_attr($cart_key); ?>" data-qty="<?php echo esc_attr((string) $qty); ?>" data-max="<?php echo esc_attr((string) $max_qty); ?>"><?php echo esc_html((string) $qty); ?></span><button type="button" data-rwb-qty data-key="<?php echo esc_attr($cart_key); ?>" data-delta="1" aria-label="Increase quantity">+</button></div></div>
                <button class="rwb-ref-cart-remove" type="button" data-rwb-remove data-key="<?php echo esc_attr($cart_key); ?>">Remove</button>
            </article>
            <?php
        }
        $paired = self::paired_product();
        if ($paired) {
            $tagline = self::paired_tagline($paired);
            ?><section class="rwb-ref-cart-paired"><h3>Frequently Paired With:</h3><div class="rwb-ref-cart-paired-row"><a class="rwb-ref-cart-paired-media" href="<?php echo esc_url($paired->get_permalink()); ?>"><?php echo wp_kses_post($paired->get_image('woocommerce_thumbnail', ['loading' => 'lazy', 'decoding' => 'async'])); ?></a><div class="rwb-ref-cart-paired-copy"><a href="<?php echo esc_url($paired->get_permalink()); ?>"><?php echo esc_html($paired->get_name()); ?></a><?php if ($tagline) : ?><small><?php echo esc_html($tagline); ?></small><?php endif; ?></div><a rel="nofollow" class="rwb-ref-cart-paired-add add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr((string) $paired->get_id()); ?>" data-product_sku="<?php echo esc_attr($paired->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($paired->add_to_cart_url()); ?>">Add&nbsp;–&nbsp;<?php echo wp_kses_post(self::money((float) $paired->get_price())); ?></a></div></section><?php
        }
        ?>
        <section class="rwb-ref-cart-coupon"><label class="screen-reader-text" for="rwb-ref-cart-coupon-code">Discount code or gift card</label><input id="rwb-ref-cart-coupon-code" type="text" data-rwb-coupon-input placeholder="DISCOUNT CODE OR GIFT CARD" autocomplete="off"><button type="button" data-rwb-coupon-apply>Apply</button><div class="rwb-ref-cart-message" data-rwb-cart-message aria-live="polite"></div></section>
        <section class="rwb-ref-cart-summary"><div class="rwb-ref-cart-subtotal"><span>Subtotal</span><strong><?php echo wp_kses_post(self::money((float) $cart->get_subtotal())); ?></strong></div><p>Shipping and taxes calculated at checkout</p><a class="rwb-ref-cart-checkout" href="<?php echo esc_url(wc_get_checkout_url()); ?>">Checkout</a></section>
        <?php
        return (string) ob_get_clean();
    }

    public static function render(): void {
        if (is_admin() || ! class_exists('WooCommerce')) return;
        $count = self::item_count();
        ?><button class="rwb-ref-cart-overlay" type="button" data-rwb-cart-close hidden aria-label="Close cart"></button><aside class="rwb-ref-cart-drawer" data-rwb-cart-drawer hidden aria-hidden="true" aria-label="Cart"><header class="rwb-ref-cart-head"><button type="button" data-rwb-cart-close><span aria-hidden="true">×</span> Close</button><h2>Cart</h2><span class="rwb-ref-cart-count-label"><?php echo esc_html((string) $count); ?> <?php echo 1 === $count ? 'Item' : 'Items'; ?></span></header><div class="rwb-ref-cart-dynamic"><?php echo self::dynamic_html(); ?></div></aside><?php
    }

    public static function fragments(array $fragments): array {
        $count = self::item_count();
        $fragments['.rwb-ref-cart-dynamic'] = '<div class="rwb-ref-cart-dynamic">' . self::dynamic_html() . '</div>';
        $fragments['.rwb-ref-cart-count-label'] = '<span class="rwb-ref-cart-count-label">' . esc_html((string) $count) . ' ' . (1 === $count ? 'Item' : 'Items') . '</span>';
        return $fragments;
    }

    public static function ajax_update(): void {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');
        $cart = self::cart();
        if (! $cart) wp_send_json_error(['message' => 'Cart is unavailable.'], 400);
        $mode = isset($_POST['mode']) ? sanitize_key(wp_unslash($_POST['mode'])) : '';
        $message = '';
        if ('qty' === $mode) {
            $key = isset($_POST['key']) ? wc_clean(wp_unslash($_POST['key'])) : '';
            $qty = isset($_POST['qty']) ? max(0, absint($_POST['qty'])) : 0;
            $items = $cart->get_cart();
            if (! $key || ! isset($items[$key])) wp_send_json_error(['message' => 'Cart item not found.'], 404);
            $product = $items[$key]['data'] instanceof WC_Product ? $items[$key]['data'] : null;
            if ($product && $qty > 0) {
                $max = (int) $product->get_max_purchase_quantity();
                if ($max > 0) $qty = min($qty, $max);
            }
            $cart->set_quantity($key, $qty, true);
        } elseif ('remove' === $mode) {
            $key = isset($_POST['key']) ? wc_clean(wp_unslash($_POST['key'])) : '';
            if ($key) $cart->remove_cart_item($key);
        } elseif ('coupon' === $mode) {
            $code = isset($_POST['coupon']) ? wc_format_coupon_code(wp_unslash($_POST['coupon'])) : '';
            if (! $code) wp_send_json_error(['message' => 'Enter a discount code.'], 400);
            wc_clear_notices();
            if ($cart->has_discount($code)) $message = 'Discount code already applied.';
            elseif ($cart->apply_coupon($code)) $message = 'Discount code applied.';
            else $message = 'Discount code could not be applied.';
        } else wp_send_json_error(['message' => 'Invalid cart action.'], 400);
        $cart->calculate_totals();
        if (method_exists($cart, 'maybe_set_cart_cookies')) $cart->maybe_set_cart_cookies();
        wp_send_json_success(['html' => self::dynamic_html(), 'count' => self::item_count(), 'message' => $message]);
    }
}

Ruwah_Reference_Cart_Drawer::boot();
