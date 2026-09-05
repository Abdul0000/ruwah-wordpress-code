<?php
defined('ABSPATH') || exit;

/* One approved logo source everywhere, including WordPress/browser site icon. */
add_filter('pre_option_site_icon', static fn() => 262, 20000);
add_filter('site_icon_url', static function (string $url): string {
    return $url ? add_query_arg('rwb-icon', '20260828-2', $url) : $url;
}, 20000);

require_once __DIR__ . '/checkout-quickview-fix.php';
require_once __DIR__ . '/quick-view-functional.php';
require_once __DIR__ . '/home-nav-links-fix.php';
require_once __DIR__ . '/seo-indexability.php';

if (! function_exists('rwb_render_master_product_card')) {
    function rwb_render_master_product_card(WC_Product $product, int $rank = 0): void {
        if (is_front_page() && 56 === (int) $product->get_id() && function_exists('wc_get_product')) {
            $replacement = wc_get_product(64);
            if ($replacement instanceof WC_Product && $replacement->is_visible()) $product = $replacement;
        }
        if (! $product->is_visible()) return;
        $name = (string) $product->get_name();
        $regular = (float) $product->get_regular_price();
        $price = (float) $product->get_price();
        $saving = ($regular > $price && $price > 0) ? $regular - $price : 0;
        $reviews = (int) $product->get_review_count();
        $rating = (float) $product->get_average_rating();
        $can_cart = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
        $stock_text = $product->is_in_stock() ? 'In stock' : 'Out of stock';
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
                <?php if ($saving > 0) : ?><span class="rhp-product-badge rhp-product-badge--offer">OFFER · <?php echo wp_kses_post(wc_price($saving, ['decimals' => 0])); ?> OFF</span><?php elseif (0 === $rank) : ?><span class="rhp-product-badge">Popular pick</span><?php endif; ?>
            </a>
            <div class="rhp-product-copy">
                <div class="rhp-product-meta"><span><?php echo esc_html($stock_text); ?></span><?php if ($size) : ?><span><?php echo esc_html($size); ?></span><?php endif; ?></div>
                <h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($name); ?></a></h3>
                <?php if ($benefit) : ?><p><?php echo esc_html($benefit); ?></p><?php endif; ?>
                <?php if ($reviews > 0) : ?><div class="rhp-rating" aria-label="<?php echo esc_attr(number_format_i18n($rating, 1) . ' out of 5 from ' . $reviews . ' reviews'); ?>"><span aria-hidden="true">★ <?php echo esc_html(number_format_i18n($rating, 1)); ?></span><small><?php echo esc_html((string) $reviews); ?> reviews</small></div><?php endif; ?>
                <div class="rhp-price"><?php if ($saving > 0) : ?><del><?php echo wp_kses_post(wc_price($regular, ['decimals' => 0])); ?></del><?php endif; ?><strong><?php echo wp_kses_post(wc_price($price, ['decimals' => 0])); ?></strong><?php if ($saving > 0) : ?><small>You save <?php echo wp_kses_post(wc_price($saving, ['decimals' => 0])); ?></small><?php endif; ?></div>
                <div class="rhp-card-actions">
                    <?php if ($can_cart) : ?><a class="rhp-add add_to_cart_button ajax_add_to_cart" rel="nofollow" aria-label="Add <?php echo esc_attr($name); ?> to cart" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>" data-product_sku="<?php echo esc_attr($product->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($product->add_to_cart_url()); ?>">Add to cart</a><?php else : ?><a class="rhp-add" href="<?php echo esc_url($product->get_permalink()); ?>">View product</a><?php endif; ?>
                    <button class="rhp-quick" type="button" data-quick-view data-qv-product-id="<?php echo esc_attr((string) $product->get_id()); ?>">Quick view</button>
                </div>
            </div>
        </article>
        <?php
    }
}

/* Premium, theme-matched card badges for the actual master-card renderer. */
add_action('wp_enqueue_scripts', static function (): void {
    if (! is_front_page() && ! rwb_master_card_runtime_surface()) return;
    $css = '.rhp-product-badge{position:absolute!important;left:14px!important;top:14px!important;z-index:4!important;display:inline-flex!important;align-items:center!important;gap:8px!important;min-height:34px!important;padding:0 13px!important;border:1px solid rgba(91,72,116,.20)!important;border-radius:999px!important;background:rgba(251,250,246,.92)!important;color:#211d24!important;box-shadow:0 8px 24px rgba(29,22,34,.10)!important;backdrop-filter:blur(10px)!important;-webkit-backdrop-filter:blur(10px)!important;font-size:9px!important;font-weight:700!important;line-height:1!important;letter-spacing:.12em!important;text-transform:uppercase!important}.rhp-product-badge--offer:before{content:"";width:7px;height:7px;flex:0 0 7px;border-radius:50%;background:#876cad;box-shadow:0 0 0 3px rgba(135,108,173,.12)}@media(max-width:620px){.rhp-product-badge{left:10px!important;top:10px!important;min-height:30px!important;padding:0 11px!important;gap:7px!important;font-size:8px!important}.rhp-product-badge--offer:before{width:6px;height:6px;flex-basis:6px}}';
    wp_add_inline_style('rwb-theme', $css);
    if (wp_style_is('rwb-master-card-safe', 'enqueued')) wp_add_inline_style('rwb-master-card-safe', $css);
}, 10100);

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

/* Shop-only catalogue: remove duplicate/non-core range from the main listing. */
add_filter('woocommerce_product_object_query_args', static function (array $args): array {
    if (! function_exists('is_shop') || ! is_shop()) return $args;
    $exclude = array_map('intval', (array) ($args['exclude'] ?? []));
    $args['exclude'] = array_values(array_unique(array_merge($exclude, [56, 58, 66])));
    return $args;
}, 50);

/* Homepage already renders the exact Shop/footer markup through home-footer-dedup.php. */
add_action('wp_enqueue_scripts', static function (): void {
    if (! is_front_page()) return;
    wp_add_inline_style('rwb-theme', 'body.rwb-home-premium .rwb-dieux-footer{display:block!important}');
}, 10050);

/* Compact only the approved card text area; image ratio/height remains unchanged. */
add_action('wp_enqueue_scripts', static function (): void {
    if (! is_front_page() && ! rwb_master_card_runtime_surface()) return;
    $compact = '.rhp-product-copy{padding:10px 14px 12px!important}.rhp-product-copy h3{margin-top:6px!important}.rhp-product-copy>p{min-height:32px!important;margin-top:6px!important;line-height:1.4!important}.rhp-rating{margin-top:6px!important}.rhp-price{margin-top:8px!important;gap:5px!important}.rhp-price small{line-height:1.2!important}.rhp-card-actions{padding-top:10px!important;gap:7px!important}.rhp-add,.rhp-quick{min-height:42px!important}';
    if (is_front_page()) {
        wp_add_inline_style('rwb-theme', $compact);
    } else {
        wp_add_inline_style('rwb-master-card-safe', $compact);
    }
}, 10060);

/* Match the homepage-approved logo crop and proportions across non-home headers. */
add_action('wp_enqueue_scripts', static function (): void {
    if (is_front_page()) return;
    wp_add_inline_style('rwb-theme', '.rwb-brand{display:grid!important;place-items:center!important;overflow:hidden!important;height:58px!important}.rwb-brand .custom-logo-link{display:block!important;height:58px!important;overflow:hidden!important}.rwb-brand .custom-logo{width:auto!important;max-width:145px!important;height:82px!important;max-height:none!important;object-fit:contain!important;object-position:top center!important;transform:translateY(-1px)!important}@media(max-width:782px){.rwb-brand,.rwb-brand .custom-logo-link{height:52px!important}.rwb-brand .custom-logo{max-width:128px!important;height:72px!important}}');
}, 10070);

add_action('wp_enqueue_scripts', static function (): void {
    if (! rwb_master_card_runtime_surface()) return;
    wp_enqueue_style('rwb-master-card-safe', plugins_url('ruwah-fresh-commerce-design/assets/home-premium.css'), ['rwb-theme'], '20260828.9');
    wp_enqueue_script('wc-add-to-cart');
    wp_enqueue_script('rwb-master-card-safe', plugins_url('ruwah-fresh-commerce-design/assets/product-card.js'), [], '20260828.9', true);
    wp_script_add_data('rwb-master-card-safe', 'strategy', 'defer');
    wp_add_inline_style('rwb-master-card-safe', 'body{--rhp-ink:#171419;--rhp-muted:#625d65;--rhp-cream:#f7f3e9;--rhp-paper:#fbfaf6;--rhp-lilac:#876cad;--rhp-lilac-dark:#665082;--rhp-blue:#dce9e9;--rhp-line:rgba(23,20,25,.16);--rhp-radius:2px}.rhp-product-grid{display:grid;grid-template-columns:1fr;gap:14px}.rhp-loop-item{list-style:none!important}.rhp-loop-item>.rhp-product-card{height:100%}.rhp-shop-feature-banner{position:relative;min-height:520px;display:flex;align-items:flex-end;overflow:hidden;border:1px solid var(--rhp-line);background:#dfe8e9;color:#fff}.rhp-shop-feature-banner>a:first-child{position:absolute;inset:0}.rhp-shop-feature-banner img{width:100%!important;height:100%!important;object-fit:cover!important;margin:0!important}.rhp-shop-feature-banner:after{content:"";position:absolute;inset:0;background:linear-gradient(180deg,rgba(17,14,18,.04) 35%,rgba(17,14,18,.76) 100%);pointer-events:none}.rhp-shop-feature-banner__copy{position:relative;z-index:2;padding:24px}.rhp-shop-feature-banner__copy small{display:block;margin-bottom:8px;font-size:9px;letter-spacing:.08em;text-transform:uppercase}.rhp-shop-feature-banner__copy h2{margin:0 0 14px!important;color:#fff!important;font-family:var(--serif,Georgia,serif)!important;font-size:36px!important;font-weight:400!important;line-height:1!important}.rhp-shop-feature-banner__copy a{display:inline-flex;min-height:44px;align-items:center;padding:0 16px;border:1px solid rgba(255,255,255,.8);color:#fff!important;font-size:10px;font-weight:700;letter-spacing:.05em;text-decoration:none;text-transform:uppercase}@media(min-width:600px){.rhp-product-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}@media(min-width:1024px){.rhp-product-grid{grid-template-columns:repeat(4,minmax(0,1fr));gap:12px}.rhp-shop-feature-banner{min-height:100%}}');
}, 10005);

/* Restore the left Shop feature banner using an existing product image only. */
add_action('template_redirect', static function (): void {
    if (! function_exists('is_shop') || ! is_shop() || ! function_exists('wc_get_product')) return;
    ob_start(static function (string $html): string {
        if (false !== strpos($html, 'rhp-shop-feature-banner')) return $html;
        $featured = wc_get_product(54);
        if (! $featured instanceof WC_Product || ! $featured->is_visible()) return $html;
        $image = $featured->get_image('woocommerce_single', ['loading' => 'eager', 'decoding' => 'async']);
        $banner = '<article class="rhp-shop-feature-banner"><a href="' . esc_url($featured->get_permalink()) . '" aria-label="View ' . esc_attr($featured->get_name()) . '">' . wp_kses_post($image) . '</a><div class="rhp-shop-feature-banner__copy"><small>Featured</small><h2>' . esc_html($featured->get_name()) . '</h2><a href="' . esc_url($featured->get_permalink()) . '">Shop product</a></div></article>';
        return preg_replace('/(<div class="rhp-product-grid">)/', '$1' . $banner, $html, 1) ?: $html;
    });
}, 0);

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