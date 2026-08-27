<?php
defined('ABSPATH') || exit;

/*
 * The active homepage is supplied by the commerce plugin, which also attaches
 * a legacy global footer through wp_footer(). Suppress only that legacy footer
 * on the homepage and render one corrected, launch-ready footer below.
 */
add_action('wp', static function (): void {
    if (! is_front_page() || ! class_exists('Ruwah_Fresh_Commerce_Design')) {
        return;
    }
    remove_action('wp_footer', [Ruwah_Fresh_Commerce_Design::class, 'reference_footer'], 5);
}, 1000);

/*
 * Normalize final homepage markup server-side so browsers, screen readers and
 * crawlers all receive the same launch-ready copy. This is intentionally
 * bounded to the front page and only replaces exact known commerce fragments.
 */
add_action('template_redirect', static function (): void {
    if (! is_front_page()) {
        return;
    }

    ob_start(static function (string $html): string {
        $html = str_replace(
            'PAKISTAN-WIDE DELIVERY · CASH ON DELIVERY · ONLINE PAYMENT COMING SOON',
            'PAKISTAN-WIDE DELIVERY · CASH ON DELIVERY',
            $html
        );

        /*
         * Product cards already expose the sale/current price immediately above
         * the CTA and the CTA itself has an accessible action label. Replace the
         * duplicate CTA price with a decorative plus so assistive output does not
         * announce the same current price twice.
         */
        $html = preg_replace_callback(
            '/(<a\b[^>]*class="[^"]*rwb-commerce-add[^"]*"[^>]*>\s*<span>[^<]+<\/span>)\s*<span>.*?<\/span>(\s*<\/a>)/is',
            static function (array $matches): string {
                return $matches[1] . '<span aria-hidden="true">+</span>' . $matches[2];
            },
            $html
        ) ?: $html;

        return $html;
    });
}, 0);

add_action('wp_footer', static function (): void {
    if (! is_front_page()) {
        return;
    }

    $products = function_exists('rwb_products') ? array_slice((array) rwb_products(), 0, 5) : [];
    $shop_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('shop') : home_url('/shop/');
    $account_url = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/');
    $privacy_url = get_privacy_policy_url();
    $contact_url = function_exists('ruwah_page_url') ? ruwah_page_url('contact') : home_url('/contact/');
    $refund_url = function_exists('ruwah_page_url') ? ruwah_page_url('refund-policy') : home_url('/refund-policy/');
    $terms_url = '';
    if (function_exists('wc_get_page_id')) {
        $terms_id = (int) wc_get_page_id('terms');
        if ($terms_id > 0) {
            $terms_url = (string) get_permalink($terms_id);
        }
    }
    ?>
    <footer class="rwb-dieux-footer" id="rwb-reference-footer">
        <div class="rwb-dieux-footer-main">
            <section class="rwb-dieux-footer-signup" aria-labelledby="rwb-footer-signup-title">
                <h2 id="rwb-footer-signup-title">Join Ruwah Notes</h2>
                <p>Skincare guidance, product updates and occasional offers. Unsubscribe any time.</p>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="rwb_newsletter">
                    <?php wp_nonce_field('rwb_newsletter', 'rwb_nonce'); ?>
                    <label class="screen-reader-text" for="rwb-dieux-footer-email">Email address</label>
                    <div class="rwb-dieux-footer-form">
                        <input id="rwb-dieux-footer-email" type="email" name="email" required autocomplete="email" placeholder="Email address">
                        <button type="submit">Subscribe</button>
                    </div>
                </form>
                <?php if ($privacy_url) : ?><small>By subscribing, you agree to receive Ruwah Notes. See our <a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a>.</small><?php endif; ?>
                <div class="rwb-dieux-footer-socials" aria-label="Ruwah Beauty social channels">
                    <a href="https://www.facebook.com/share/1BNAdjWpYW/" target="_blank" rel="noopener noreferrer" aria-label="Ruwah Beauty on Facebook">Facebook</a>
                    <a href="https://www.instagram.com/rawah.beauty" target="_blank" rel="noopener noreferrer" aria-label="Ruwah Beauty on Instagram">Instagram</a>
                    <a href="https://vt.tiktok.com/ZSX6WqwS2/" target="_blank" rel="noopener noreferrer" aria-label="Ruwah Beauty on TikTok">TikTok</a>
                </div>
            </section>

            <section class="rwb-dieux-footer-col"><h2>Shop</h2>
                <?php foreach ($products as $product) : if (! $product instanceof WC_Product) continue; $copy = Ruwah_Fresh_Commerce_Design::display_copy($product); ?>
                    <a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($copy['name']); ?></a>
                <?php endforeach; ?>
                <a href="<?php echo esc_url($shop_url); ?>">Shop All</a>
            </section>

            <section class="rwb-dieux-footer-col"><h2>Learn</h2>
                <a href="<?php echo esc_url(home_url('/#rwb-genesis')); ?>">Our Genesis</a>
                <a href="<?php echo esc_url(home_url('/#rwb-standard')); ?>">The Ruwah Standard</a>
                <a href="<?php echo esc_url(home_url('/#rituals')); ?>">Rituals</a>
                <a href="<?php echo esc_url($shop_url); ?>">Formula Guide</a>
            </section>

            <section class="rwb-dieux-footer-col"><h2>Contact</h2>
                <a href="mailto:rawahbeauty783@gmail.com" aria-label="Email Ruwah Beauty support at rawahbeauty783@gmail.com">rawahbeauty783@gmail.com</a>
                <a href="https://wa.me/923713923279" target="_blank" rel="noopener noreferrer" aria-label="Chat with Ruwah Beauty on WhatsApp">WhatsApp Support</a>
                <a href="<?php echo esc_url($contact_url); ?>">Contact Us</a>
                <?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a><?php endif; ?>
                <a href="<?php echo esc_url($account_url); ?>">My Account</a>
                <small>Support replies are typically sent within 2 business days.</small>
            </section>

            <section class="rwb-dieux-footer-promise"><h2>Our Promise</h2>
                <div class="rwb-dieux-promise-mark" aria-hidden="true"><span>◉</span><b>RUWAH<br>PROMISE</b></div>
                <p>Clear product details.<br>Current price &amp; availability.<br>Measured skincare claims.</p>
            </section>
        </div>

        <div class="rwb-dieux-footer-bottom">
            <div class="rwb-dieux-footer-meta">
                <b>© <?php echo esc_html(wp_date('Y')); ?> Ruwah Beauty</b>
                <span>Pakistan · Online skincare</span>
                <div class="rwb-dieux-payments"><span>COD ONLY</span></div>
            </div>
            <nav class="rwb-dieux-footer-legal" aria-label="Footer legal links">
                <?php if ($terms_url) : ?><a href="<?php echo esc_url($terms_url); ?>">Terms of Service</a><?php endif; ?>
                <?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a><?php endif; ?>
                <a href="<?php echo esc_url($refund_url); ?>">Refund Policy</a>
                <a href="<?php echo esc_url($contact_url); ?>">Contact</a>
            </nav>
        </div>
        <a class="rwb-dieux-footer-promo" href="#rwb-dieux-footer-email">Join Ruwah Notes <span aria-hidden="true">×</span></a>
    </footer>
    <?php
}, 6);

/* Advertise the canonical WordPress core sitemap to compliant crawlers. */
add_filter('robots_txt', static function (string $output, bool $public): string {
    if (! $public) {
        return $output;
    }
    $sitemap = home_url('/sitemap_index.xml');
    if (false === stripos($output, 'Sitemap:')) {
        $output = rtrim($output) . "\nSitemap: " . esc_url_raw($sitemap) . "\n";
    }
    return $output;
}, 20, 2);

/*
 * SEO/audit compatibility endpoint. Serve a real 200 sitemap-index document
 * and point it at WordPress core's canonical sitemap implementation.
 */
add_action('template_redirect', static function (): void {
    $path = isset($_SERVER['REQUEST_URI']) ? (string) wp_parse_url(wp_unslash($_SERVER['REQUEST_URI']), PHP_URL_PATH) : '';
    if ('/sitemap_index.xml' !== rtrim($path, '/') && '/sitemap_index.xml' !== $path) {
        return;
    }

    status_header(200);
    nocache_headers();
    header('Content-Type: application/xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    echo '  <sitemap><loc>' . esc_url(home_url('/wp-sitemap.xml')) . '</loc></sitemap>' . "\n";
    echo '</sitemapindex>';
    exit;
}, 1);
