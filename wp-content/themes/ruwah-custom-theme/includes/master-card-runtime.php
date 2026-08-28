<?php
defined('ABSPATH') || exit;

if (! function_exists('rwb_render_master_product_card')) {
    function rwb_render_master_product_card(WC_Product $product, int $rank = 0): void {
        if (! $product->is_visible()) return;
        $name = (string) $product->get_name();
        $regular = (float) $product->get_regular_price();
        $price = (float) $product->get_price();
        $saving = ($regular > $price && $price > 0) ? $regular - $price : 0;
        $reviews = (int) $product->get_review_count();
        $rating = (float) $product->get_average_rating();
        $can_cart = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
        $stock_text = $product->is_in_stock() ? 'In stock' : 'Out of stock';
        $image_url = wp_get_attachment_image_url((int) $product->get_image_id(), 'woocommerce_single') ?: '';
        $benefit = trim(wp_strip_all_tags((string) $product->get_short_description()));
        if ('' === $benefit) $benefit = wp_trim_words(wp_strip_all_tags((string) $product->get_description()), 24, '…');
        if (preg_match('/<li[^>]*>(.*?)<\/li>/is', (string) $product->get_description(), $match)) {
            $candidate = trim(wp_strip_all_tags((string) $match[1]));
            if ('' !== $candidate) $benefit = $candidate;
        }
        $size = '';
        foreach (['pa_size', 'size', 'pa_volume', 'volume'] as $attribute) {
            $value = trim(wp_strip_all_tags((string) $product->get_attribute($attribute)));
            if ('' !== $value) { $size = $value; break; }
        }
        ?>
        <article class="rhp-product-card">
            <a class="rhp-product-image" href="<?php echo esc_url($product->get_permalink()); ?>" aria-label="View <?php echo esc_attr($name); ?>">
                <?php echo wp_kses_post($product->get_image('woocommerce_single', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
                <?php if ($saving > 0) : ?><span class="rhp-product-badge">Save <?php echo wp_kses_post(wc_price($saving, ['decimals' => 0])); ?></span><?php elseif (0 === $rank) : ?><span class="rhp-product-badge">Popular pick</span><?php endif; ?>
            </a>
            <div class="rhp-product-copy">
                <div class="rhp-product-meta"><span><?php echo esc_html($stock_text); ?></span><?php if ($size) : ?><span><?php echo esc_html($size); ?></span><?php endif; ?></div>
                <h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($name); ?></a></h3>
                <?php if ($benefit) : ?><p><?php echo esc_html($benefit); ?></p><?php endif; ?>
                <?php if ($reviews > 0) : ?><div class="rhp-rating" aria-label="<?php echo esc_attr(number_format_i18n($rating, 1) . ' out of 5 from ' . $reviews . ' reviews'); ?>"><span aria-hidden="true">★ <?php echo esc_html(number_format_i18n($rating, 1)); ?></span><small><?php echo esc_html((string) $reviews); ?> reviews</small></div><?php endif; ?>
                <div class="rhp-price"><?php if ($saving > 0) : ?><del><?php echo wp_kses_post(wc_price($regular, ['decimals' => 0])); ?></del><?php endif; ?><strong><?php echo wp_kses_post(wc_price($price, ['decimals' => 0])); ?></strong><?php if ($saving > 0) : ?><small>You save <?php echo wp_kses_post(wc_price($saving, ['decimals' => 0])); ?></small><?php endif; ?></div>
                <div class="rhp-card-actions">
                    <?php if ($can_cart) : ?><a class="rhp-add add_to_cart_button ajax_add_to_cart" rel="nofollow" aria-label="Add <?php echo esc_attr($name); ?> to cart" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($product->add_to_cart_url()); ?>">Add to cart</a><?php else : ?><a class="rhp-add" href="<?php echo esc_url($product->get_permalink()); ?>">View product</a><?php endif; ?>
                    <button class="rhp-quick" type="button" data-quick-view data-qv-name="<?php echo esc_attr($name); ?>" data-qv-image="<?php echo esc_url($image_url); ?>" data-qv-copy="<?php echo esc_attr($benefit); ?>" data-qv-price="<?php echo esc_attr(wp_strip_all_tags(wc_price($price, ['decimals' => 0]))); ?>" data-qv-stock="<?php echo esc_attr($stock_text); ?>" data-qv-url="<?php echo esc_url($product->get_permalink()); ?>" data-qv-add="<?php echo esc_url($can_cart ? $product->add_to_cart_url() : $product->get_permalink()); ?>" data-qv-can-cart="<?php echo $can_cart ? '1' : '0'; ?>">Quick view</button>
                </div>
            </div>
        </article>
        <?php
    }
}

function rwb_master_card_runtime_surface(): bool {
    if (! class_exists('WooCommerce') || is_front_page()) return false;
    if (function_exists('is_shop') && is_shop()) return true;
    if (function_exists('is_product_taxonomy') && is_product_taxonomy()) return true;
    if (function_exists('is_product') && is_product()) return true;
    if (is_search()) {
        $post_type = get_query_var('post_type');
        return 'product' === $post_type || (is_array($post_type) && in_array('product', $post_type, true));
    }
    return false;
}

add_action('wp_enqueue_scripts', static function (): void {
    if (! rwb_master_card_runtime_surface()) return;
    wp_enqueue_style('rwb-master-card-safe', plugins_url('ruwah-fresh-commerce-design/assets/home-premium.css'), ['rwb-theme'], '20260828.5');
    wp_enqueue_script('wc-add-to-cart');
    wp_enqueue_script('rwb-master-card-safe', plugins_url('ruwah-fresh-commerce-design/assets/product-card.js'), [], '20260828.5', true);
    wp_script_add_data('rwb-master-card-safe', 'strategy', 'defer');
    wp_add_inline_style('rwb-master-card-safe', '.rhp-product-grid{display:grid;grid-template-columns:1fr;gap:14px}.rhp-loop-item{list-style:none!important}.rhp-loop-item>.rhp-product-card{height:100%}@media(min-width:600px){.rhp-product-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(min-width:1024px){.rhp-product-grid{grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}}');
}, 10005);

add_action('template_redirect', static function (): void {
    if (! function_exists('is_product') || ! is_product()) return;
    ob_start(static function (string $html): string {
        if (! function_exists('rwb_render_master_product_card') || ! class_exists('Ruwah_Fresh_Commerce_Design') || ! function_exists('wc_get_product')) return $html;
        $current = wc_get_product(get_queried_object_id());
        if (! $current instanceof WC_Product) return $html;
        $related = Ruwah_Fresh_Commerce_Design::related_products($current);
        if (! $related) return $html;
        ob_start();
        foreach ($related as $rank => $candidate) {
            if ($candidate instanceof WC_Product) rwb_render_master_product_card($candidate, (int) $rank);
        }
        $cards = (string) ob_get_clean();
        if ('' === $cards) return $html;
        $pattern = '~<div class="rwb-commerce-pair-grid">.*?</div>\s*</div>\s*</section>~s';
        $replacement = '<div class="rwb-commerce-pair-grid rhp-product-grid">' . $cards . '</div></div></section>';
        return preg_replace($pattern, $replacement, $html, 1) ?: $html;
    });
}, 5);

add_action('wp_footer', static function (): void {
    if (! rwb_master_card_runtime_surface()) return;
    ?>
    <dialog class="rhp-quick-view" data-quick-dialog aria-labelledby="rhp-qv-title-safe">
        <button class="rhp-qv-close" type="button" data-quick-close aria-label="Close quick view">×</button>
        <div class="rhp-qv-media"><img data-qv-image alt=""></div>
        <div class="rhp-qv-copy"><span data-qv-stock></span><h2 id="rhp-qv-title-safe" data-qv-name></h2><p data-qv-copy></p><strong data-qv-price></strong><div><a class="rhp-button rhp-button-dark" data-qv-add href="#">Add to cart</a><a class="rhp-text-link" data-qv-link href="#">View full product →</a></div></div>
    </dialog>
    <?php
}, 45);