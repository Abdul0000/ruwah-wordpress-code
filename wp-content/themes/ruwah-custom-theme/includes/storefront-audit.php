<?php
defined('ABSPATH') || exit;

/**
 * Storefront audit hardening + the approved homepage product-card renderer.
 * Product names, images, prices, savings, stock and URLs come from WooCommerce.
 */

function rwb_audit_card_surface(): bool {
    if (! function_exists('WC')) return false;
    if (is_front_page()) return true;
    if (function_exists('is_shop') && is_shop()) return true;
    if (function_exists('is_product_taxonomy') && is_product_taxonomy()) return true;
    if (function_exists('is_product') && is_product()) return true;
    if (function_exists('is_cart') && is_cart()) return true;
    if (is_search()) {
        $post_type = get_query_var('post_type');
        return 'product' === $post_type || (is_array($post_type) && in_array('product', $post_type, true));
    }
    return false;
}

function rwb_master_card_info(WC_Product $product): array {
    $tagline = trim(wp_strip_all_tags((string) $product->get_short_description()));
    if ('' === $tagline) $tagline = wp_trim_words(wp_strip_all_tags((string) $product->get_description()), 24, '…');
    $benefits = [];
    $description = (string) $product->get_description();
    if ($description && preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $description, $matches)) {
        foreach ($matches[1] as $item) {
            $item = trim(wp_strip_all_tags((string) $item));
            if ('' !== $item && ! in_array($item, $benefits, true)) $benefits[] = $item;
        }
    }
    $size = '';
    foreach (['pa_size', 'size', 'pa_volume', 'volume'] as $attribute) {
        $value = trim(wp_strip_all_tags((string) $product->get_attribute($attribute)));
        if ('' !== $value) { $size = $value; break; }
    }
    return [
        'name' => (string) $product->get_name(),
        'tagline' => $tagline,
        'benefits' => array_slice($benefits, 0, 4),
        'size' => $size,
    ];
}

function rwb_render_master_product_card(WC_Product $product, int $rank = 0): void {
    if (! $product->is_visible()) return;
    $info = rwb_master_card_info($product);
    $regular = (float) $product->get_regular_price();
    $price = (float) $product->get_price();
    $saving = ($regular > $price && $price > 0) ? $regular - $price : 0;
    $reviews = (int) $product->get_review_count();
    $rating = (float) $product->get_average_rating();
    $can_cart = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
    $image_url = wp_get_attachment_image_url((int) $product->get_image_id(), 'woocommerce_single') ?: '';
    $benefit = ! empty($info['benefits'][0]) ? (string) $info['benefits'][0] : (string) ($info['tagline'] ?? '');
    $stock_text = $product->is_in_stock() ? 'In stock' : 'Out of stock';
    $name = (string) $product->get_name();
    ?>
    <article class="rhp-product-card">
        <a class="rhp-product-image" href="<?php echo esc_url($product->get_permalink()); ?>" aria-label="View <?php echo esc_attr($name); ?>">
            <?php echo wp_kses_post($product->get_image('woocommerce_single', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
            <?php if ($saving > 0) : ?><span class="rhp-product-badge">Save <?php echo wp_kses_post(wc_price($saving, ['decimals' => 0])); ?></span><?php elseif (0 === $rank) : ?><span class="rhp-product-badge">Popular pick</span><?php endif; ?>
        </a>
        <div class="rhp-product-copy">
            <div class="rhp-product-meta"><span><?php echo esc_html($stock_text); ?></span><?php if (! empty($info['size'])) : ?><span><?php echo esc_html((string) $info['size']); ?></span><?php endif; ?></div>
            <h3><a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($name); ?></a></h3>
            <p><?php echo esc_html($benefit); ?></p>
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

function rwb_audit_all_visible_products(): array {
    if (! function_exists('wc_get_products')) return [];
    $items = wc_get_products(['status' => 'publish', 'limit' => -1, 'orderby' => 'menu_order', 'order' => 'ASC']);
    return array_values(array_filter($items, static fn($item) => $item instanceof WC_Product && $item->is_visible()));
}

function rwb_audit_home_bestsellers(): array {
    $items = rwb_audit_all_visible_products();
    usort($items, static fn(WC_Product $a, WC_Product $b) => (int) $b->get_total_sales() <=> (int) $a->get_total_sales());
    return array_slice($items, 0, 4);
}

add_action('wp_enqueue_scripts', static function (): void {
    if (! rwb_audit_card_surface()) return;
    if (! is_front_page()) {
        wp_enqueue_style('rwb-master-product-card', plugins_url('ruwah-fresh-commerce-design/assets/home-premium.css'), ['rwb-theme'], '20260828.3');
    }
    wp_enqueue_script('rwb-master-product-card', plugins_url('ruwah-fresh-commerce-design/assets/product-card.js'), [], '20260828.3', true);
    wp_script_add_data('rwb-master-product-card', 'strategy', 'defer');
    $css = <<<'CSS'
.rhp-product-card,.rhp-quick-view{--rhp-ink:#171419;--rhp-muted:#625d65;--rhp-cream:#f7f3e9;--rhp-paper:#fbfaf6;--rhp-lilac:#876cad;--rhp-lilac-dark:#665082;--rhp-blue:#dce9e9;--rhp-line:rgba(23,20,25,.16);--rhp-radius:2px}
ul.products li.product.rhp-loop-item{float:none!important;width:auto!important;margin:0!important;padding:0!important;list-style:none!important;background:transparent!important}
ul.products li.product.rhp-loop-item>.rhp-product-card{height:100%}
.rwb-reference-commerce-v6 ul.products{display:grid!important;grid-template-columns:1fr!important;gap:14px!important}
.rwb-commerce-pair-grid.rhp-product-grid{display:grid!important}
@media(min-width:600px){.rwb-reference-commerce-v6 ul.products{grid-template-columns:repeat(2,minmax(0,1fr))!important}}
@media(min-width:1024px){.rwb-reference-commerce-v6 ul.products{grid-template-columns:repeat(4,minmax(0,1fr))!important;gap:12px!important}}
CSS;
    if (! is_front_page()) wp_add_inline_style('rwb-master-product-card', $css);
}, 10010);

add_action('wp_footer', static function (): void {
    if (! rwb_audit_card_surface() || is_front_page()) return;
    ?>
    <dialog class="rhp-quick-view" data-quick-dialog aria-labelledby="rhp-qv-title-global">
        <button class="rhp-qv-close" type="button" data-quick-close aria-label="Close quick view">×</button>
        <div class="rhp-qv-media"><img data-qv-image alt=""></div>
        <div class="rhp-qv-copy"><span data-qv-stock></span><h2 id="rhp-qv-title-global" data-qv-name></h2><p data-qv-copy></p><strong data-qv-price></strong><div><a class="rhp-button rhp-button-dark" data-qv-add href="#">Add to cart</a><a class="rhp-text-link" data-qv-link href="#">View full product →</a></div></div>
    </dialog>
    <?php
}, 45);

add_action('template_redirect', static function (): void {
    if (! is_front_page()) return;
    ob_start(static function (string $html): string {
        $canonical_contact = home_url('/contact/');
        $canonical_refund = home_url('/refund-policy/');
        $html = str_replace([home_url('/contact-us/'), home_url('/returns-refunds/')], [$canonical_contact, $canonical_refund], $html);
        $html = str_replace(
            [
                'Cosmetic benefits are described in measured language; current price and stock come directly from WooCommerce.',
                'Current product data, price, stock and genuine review counts — no placeholder ratings.',
                'Verifiable differences drawn from the current store — not manufacturing or clinical claims we cannot substantiate.',
                'Price, sale state and stock are pulled from WooCommerce at the moment you browse.',
            ],
            [
                'Clear cosmetic benefit language, current availability and no invented treatment promises.',
                'See current price, availability and what each product adds to a routine. Ratings appear only when genuine reviews exist.',
                'Practical reasons to shop the range, built around clear product information and a focused routine.',
                'See current price, sale savings and availability before you add a product to your bag.',
            ],
            $html
        );
        if (false !== strpos($html, 'id="bestsellers"')) {
            ob_start();
            foreach (rwb_audit_home_bestsellers() as $rank => $product) rwb_render_master_product_card($product, (int) $rank);
            $cards = (string) ob_get_clean();
            $pattern = '~(<section class="rhp-section rhp-bestsellers"[^>]*>.*?<div class="rhp-product-grid">).*?(</div>\s*<div class="rhp-centered">)~s';
            $html = preg_replace($pattern, '$1' . $cards . '$2', $html, 1) ?: $html;
        }
        $html = preg_replace('~<footer class="rwb-dieux-footer"[^>]*>.*?</footer>~s', '', $html) ?: $html;
        return $html;
    });
}, 2);

add_action('template_redirect', static function (): void {
    if (! function_exists('is_product') || ! is_product()) return;
    ob_start(static function (string $html): string {
        $product = function_exists('wc_get_product') ? wc_get_product(get_queried_object_id()) : null;
        if (! $product instanceof WC_Product || ! class_exists('Ruwah_Fresh_Commerce_Design')) return $html;
        $related = Ruwah_Fresh_Commerce_Design::related_products($product);
        if ($related) {
            ob_start();
            foreach ($related as $rank => $candidate) rwb_render_master_product_card($candidate, (int) $rank);
            $cards = (string) ob_get_clean();
            $pattern = '~(<div class="rwb-commerce-pair-grid(?: rhp-product-grid)?">).*?(</div>\s*</div>\s*</section>)~s';
            $html = preg_replace($pattern, '<div class="rwb-commerce-pair-grid rhp-product-grid">' . $cards . '$2', $html, 1) ?: $html;
        }
        if (64 === (int) $product->get_id() && 'NUB-EYE-01' === $product->get_sku()) $html = str_replace('<li>SKU: NUB-EYE-01</li>', '', $html);
        if (68 === (int) $product->get_id()) {
            $html = str_replace(
                'A lightweight daily sun lotion designed to protect while keeping skin comfortable and radiant-looking.',
                'A lightweight daily sun-care lotion for comfortable everyday routines and a radiant-looking finish. Check the packaging for verified protection claims and directions.',
                $html
            );
        }
        return $html;
    });
}, 3);

add_action('template_redirect', static function (): void {
    if (is_admin()) return;
    $path = isset($_SERVER['REQUEST_URI']) ? (string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';
    $path = '/' . trim($path, '/');
    $redirects = [
        '/contact-us' => home_url('/contact/'),
        '/returns-refunds' => home_url('/refund-policy/'),
        '/skin-care' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/'),
        '/bundles' => function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/'),
        '/quality-testing' => home_url('/quality-safety/'),
    ];
    if (isset($redirects[$path])) { wp_safe_redirect($redirects[$path], 301, 'Ruwah Storefront'); exit; }
    if (function_exists('is_product_category') && is_product_category('eye-care')) {
        $serums = get_term_by('slug', 'serums', 'product_cat');
        $target = $serums instanceof WP_Term ? get_term_link($serums) : false;
        wp_safe_redirect(! is_wp_error($target) && $target ? $target : home_url('/shop/'), 301, 'Ruwah Storefront');
        exit;
    }
    if (is_author() || is_category('uncategorized')) { wp_safe_redirect(home_url('/'), 301, 'Ruwah Storefront'); exit; }
}, 0);

add_filter('the_content', static function (string $content): string {
    if (is_admin() || ! is_main_query() || ! in_the_loop() || '' !== trim(wp_strip_all_tags($content))) return $content;
    $id = get_the_ID();
    $pages = [
        82 => '<h2>Pakistan-wide delivery</h2><p>Ruwah Beauty currently accepts delivery orders across Pakistan through checkout.</p><h2>Cash on delivery</h2><p>Cash on Delivery is the current customer-facing payment method. Enter a complete delivery address and a reachable Pakistani mobile number at checkout.</p><h2>Delivery availability and charges</h2><p>Availability and any delivery charge are confirmed for the address entered during checkout. A fixed nationwide delivery fee is not currently published.</p><h2>Delivery time</h2><p>A fixed delivery-time range is not currently published. For an order-specific estimate, contact support with your order number and delivery city.</p><h2>Tracking and order status</h2><p>Keep your order number after checkout. Signed-in customers can review account order history, and support can use the order number to help with dispatch or delivery-status questions.</p><h2>Need help?</h2><p>Use the <a href="' . esc_url(home_url('/contact/')) . '">Contact page</a> or WhatsApp and include your order number.</p>',
        84 => '<h2>What we publish clearly</h2><p>Ruwah product pages show current price, availability, product-specific photography and measured cosmetic benefit language.</p><h2>Packaging remains important</h2><p>When a complete ingredient list, warning, batch detail, expiry detail or application direction is not verified online, the product packaging remains the source to follow.</p><h2>Sun-care claims</h2><p>Ruwah Beauty does not add an SPF value, UVA rating, broad-spectrum statement, water-resistance claim or test standard unless that information is verified from the product source. Check the product packaging and supporting manufacturer information before relying on a protection claim.</p><h2>Questions before ordering</h2><p>If you need a product detail that is not published online, please <a href="' . esc_url(home_url('/contact/')) . '">contact Ruwah</a> before ordering.</p>',
        25 => '<h2>Formula guide</h2><p>This guide explains the cosmetic role of ingredients already named in current Ruwah product information. It does not replace a complete INCI list on the product packaging.</p><h2>Vitamin C</h2><p>Commonly used in cosmetic skincare to support a brighter-looking, more radiant appearance.</p><h2>Niacinamide</h2><p>Used in skincare to support an even-looking tone and a comfortable skin-barrier appearance.</p><h2>Hyaluronic Acid</h2><p>A humectant used to help skin feel hydrated and look more comfortably plumped.</p><h2>Rice Extract</h2><p>Used in Ruwah rice-focused products as part of their conditioning and radiance-focused positioning.</p><h2>Alpha Arbutin</h2><p>Named in the current rice cream information for even-looking tone and radiance-focused cosmetic care.</p><p>For the complete ingredient list of a specific product, check its product page and packaging.</p>',
        85 => '<h2>Store terms</h2><p>These terms apply to orders placed through Ruwah Beauty in Pakistan.</p><h2>Products, prices and availability</h2><p>Product prices are shown in PKR. Availability can change, and an order is subject to the product remaining available when it is processed.</p><h2>Orders and payment</h2><p>Cash on Delivery is the current customer-facing checkout method. Customers are responsible for providing accurate contact and delivery information.</p><h2>Delivery</h2><p>Delivery availability and any charge are confirmed for the checkout address. Ruwah does not publish an unverified fixed delivery-time promise.</p><h2>Returns and refunds</h2><p>Return, damaged-item and refund eligibility is governed by the published <a href="' . esc_url(home_url('/refund-policy/')) . '">Refund Policy</a>.</p><h2>Product information</h2><p>Website product information is intended to support purchase decisions. Packaging directions, warnings and complete ingredient information should be followed where the website does not publish a verified equivalent.</p><h2>Contact</h2><p>Questions about an order or these terms can be sent through the <a href="' . esc_url(home_url('/contact/')) . '">Contact page</a>.</p>',
    ];
    return $pages[$id] ?? $content;
}, 50);

add_filter('wp_sitemaps_add_provider', static function ($provider, string $name) { return 'users' === $name ? false : $provider; }, 10, 2);
add_filter('wp_sitemaps_posts_query_args', static function (array $args, string $post_type): array {
    if ('page' === $post_type) $args['post__not_in'] = array_values(array_unique(array_merge((array) ($args['post__not_in'] ?? []), [81, 83, 79, 80, 26])));
    return $args;
}, 10, 2);
add_filter('wp_sitemaps_taxonomies_query_args', static function (array $args, string $taxonomy): array {
    if ('category' === $taxonomy) {
        $term = get_term_by('slug', 'uncategorized', 'category');
        if ($term instanceof WP_Term) $args['exclude'] = array_values(array_unique(array_merge((array) ($args['exclude'] ?? []), [(int) $term->term_id])));
    }
    if ('product_cat' === $taxonomy) {
        $term = get_term_by('slug', 'eye-care', 'product_cat');
        if ($term instanceof WP_Term && 0 === (int) $term->count) $args['exclude'] = array_values(array_unique(array_merge((array) ($args['exclude'] ?? []), [(int) $term->term_id])));
    }
    return $args;
}, 10, 2);

function rwb_audit_meta_description(): string {
    if (is_front_page()) return '';
    if (function_exists('is_product') && is_product() && function_exists('wc_get_product')) {
        $product = wc_get_product(get_queried_object_id());
        if ($product instanceof WC_Product) {
            if (68 === (int) $product->get_id()) return 'Shop Rice Glow Sun Lotion from Ruwah Beauty Pakistan for an everyday sun-care routine. Check the packaging for verified protection claims and directions. Cash on delivery.';
            $info = rwb_master_card_info($product);
            $copy = trim((string) ($info['tagline'] ?? ''));
            if ('' === $copy) $copy = trim(wp_strip_all_tags((string) $product->get_short_description()));
            return wp_trim_words($copy . ' Shop Ruwah Beauty in Pakistan with cash on delivery.', 28, '');
        }
    }
    if (function_exists('is_shop') && is_shop()) return 'Shop Ruwah Beauty skincare in Pakistan. Browse brightening, hydrating, cleansing, body-care and daily sun-care products with cash on delivery.';
    if (function_exists('is_product_taxonomy') && is_product_taxonomy()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) return 'Shop ' . $term->name . ' from Ruwah Beauty in Pakistan. View current prices, availability and cash-on-delivery shopping options.';
    }
    if (is_page()) {
        $map = [
            'shipping-delivery' => 'Ruwah Beauty Pakistan shipping and delivery information, including cash on delivery, checkout charges, address guidance and order-status support.',
            'quality-safety' => 'How Ruwah Beauty presents product details, packaging guidance and measured cosmetic claims, including responsible handling of unverified sun-care claims.',
            'beauty-guide' => 'Ruwah Beauty formula guide to Vitamin C, Niacinamide, Hyaluronic Acid, Rice Extract and Alpha Arbutin in simple cosmetic skincare language.',
            'terms-conditions' => 'Ruwah Beauty Pakistan store terms covering product availability, PKR pricing, cash on delivery, shipping, returns and customer responsibilities.',
            'privacy-policy' => 'Read how Ruwah Beauty handles website, account and customer information when you browse or place an order in Pakistan.',
            'contact' => 'Contact Ruwah Beauty in Pakistan for product questions, order support and delivery-status help.',
            'refund-policy' => 'Read Ruwah Beauty refund and returns guidance for damaged, incorrect or eligible orders in Pakistan.',
        ];
        $post = get_queried_object();
        if ($post instanceof WP_Post && isset($map[$post->post_name])) return $map[$post->post_name];
        if ($post instanceof WP_Post) {
            $excerpt = trim(wp_strip_all_tags((string) $post->post_excerpt));
            if ('' === $excerpt) $excerpt = trim(wp_strip_all_tags((string) $post->post_content));
            if ('' !== $excerpt) return wp_trim_words($excerpt, 28, '');
        }
    }
    return '';
}
add_action('wp_head', static function (): void {
    $description = rwb_audit_meta_description();
    if ('' !== $description) echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    if (function_exists('is_shop') && is_shop()) echo '<link rel="canonical" href="' . esc_url(wc_get_page_permalink('shop')) . '">' . "\n";
    elseif (function_exists('is_product_taxonomy') && is_product_taxonomy()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) { $url = get_term_link($term); if (! is_wp_error($url)) echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n"; }
    }
}, 2);

add_filter('woocommerce_show_page_title', static function (bool $show): bool {
    return (function_exists('is_product_taxonomy') && is_product_taxonomy()) ? false : $show;
}, 100);

add_filter('term_description', static function (string $description, int $term_id = 0, string $taxonomy = ''): string {
    if ('' !== trim(wp_strip_all_tags($description))) return $description;
    $term = $term_id > 0 ? get_term($term_id, $taxonomy ?: 'product_cat') : get_queried_object();
    if (! $term instanceof WP_Term || 'product_cat' !== $term->taxonomy) return $description;
    return '<p>Browse Ruwah Beauty ' . esc_html($term->name) . ' products with current price, availability and product-specific cosmetic information. Choose by your routine needs and review the product page before ordering.</p>';
}, 20, 3);

add_action('wp', static function (): void {
    if (! function_exists('is_product') || ! is_product() || ! function_exists('WC') || ! WC()->structured_data || ! function_exists('wc_get_product')) return;
    $product = wc_get_product(get_queried_object_id());
    if ($product instanceof WC_Product) WC()->structured_data->generate_product_data($product);
}, 40);
add_filter('woocommerce_structured_data_product', static function (array $markup, WC_Product $product): array {
    if (64 === (int) $product->get_id() && 'NUB-EYE-01' === $product->get_sku()) unset($markup['sku']);
    if (68 === (int) $product->get_id() && isset($markup['description'])) $markup['description'] = 'A lightweight daily sun-care lotion for comfortable everyday routines. Check the packaging for verified protection claims and directions.';
    return $markup;
}, 50, 2);

add_filter('woocommerce_get_stock_html', static function (string $html, WC_Product $product): string {
    return $product->is_in_stock() ? '<p class="stock in-stock">In stock</p>' : '<p class="stock out-of-stock">Out of stock</p>';
}, 100, 2);

add_action('init', static function (): void {
    if ('20260828-v1' === get_option('rwb_rice_glow_category_fix')) return;
    if (! taxonomy_exists('product_cat') || 'product' !== get_post_type(64)) return;
    $eye = get_term_by('slug', 'eye-care', 'product_cat');
    $serums = get_term_by('slug', 'serums', 'product_cat');
    if ($eye instanceof WP_Term) wp_remove_object_terms(64, [(int) $eye->term_id], 'product_cat');
    if ($serums instanceof WP_Term) wp_set_object_terms(64, [(int) $serums->term_id], 'product_cat', true);
    update_option('rwb_rice_glow_category_fix', '20260828-v1', false);
}, 80);

add_filter('template_include', static function (string $template): string {
    if (! is_search()) return $template;
    $post_type = get_query_var('post_type');
    $is_product_search = 'product' === $post_type || (is_array($post_type) && in_array('product', $post_type, true));
    if (! $is_product_search) return $template;
    $custom = WP_PLUGIN_DIR . '/ruwah-fresh-commerce-design/templates/product-search.php';
    return is_readable($custom) ? $custom : $template;
}, 1000);
