<?php
$ruwah_product_cleanup = __DIR__ . '/includes/product-62-image-cleanup.php';
if (is_readable($ruwah_product_cleanup)) {
    require_once $ruwah_product_cleanup;
    if (function_exists('ruwah_product_62_remove_baked_floor')) {
        ruwah_product_62_remove_baked_floor();
    }
}

/* One footer system sitewide: remove the plugin legacy footer before wp_footer runs. */
if (class_exists('Ruwah_Fresh_Commerce_Design')) {
    remove_action('wp_footer', [Ruwah_Fresh_Commerce_Design::class, 'reference_footer'], 5);
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
</main>

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
                    <input id="rwb-dieux-footer-email" type="email" name="email" autocomplete="email" placeholder="Email address">
                </div>
            </form>
            <?php if ($privacy_url) : ?>
                <small>Ruwah Notes signup is currently paused. See our <a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a>.</small>
            <?php endif; ?>
            <div class="rwb-dieux-footer-socials rwb-unified-socials" aria-label="Ruwah Beauty social channels">
                <a class="rwb-social-link" href="https://www.facebook.com/share/1BNAdjWpYW/" target="_blank" rel="noopener noreferrer" aria-label="Ruwah Beauty on Facebook" title="Facebook">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13.5 22v-8h2.7l.4-3h-3.1V9.1c0-.9.3-1.6 1.7-1.6h1.5V4.8c-.3 0-1.2-.1-2.3-.1-2.3 0-3.8 1.4-3.8 4v2.3H8v3h2.6v8z"/></svg>
                    <span class="screen-reader-text">Facebook</span>
                </a>
                <a class="rwb-social-link" href="https://www.instagram.com/rawah.beauty" target="_blank" rel="noopener noreferrer" aria-label="Ruwah Beauty on Instagram" title="Instagram">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm0 2.2A2.8 2.8 0 0 0 4.2 7v10A2.8 2.8 0 0 0 7 19.8h10a2.8 2.8 0 0 0 2.8-2.8V7A2.8 2.8 0 0 0 17 4.2H7zm5 3.3A4.5 4.5 0 1 1 7.5 12 4.5 4.5 0 0 1 12 7.5zm0 2.2A2.3 2.3 0 1 0 14.3 12 2.3 2.3 0 0 0 12 9.7zm4.9-3.4a1.1 1.1 0 1 1-1.1 1.1 1.1 1.1 0 0 1 1.1-1.1z"/></svg>
                    <span class="screen-reader-text">Instagram</span>
                </a>
                <a class="rwb-social-link" href="https://vt.tiktok.com/ZSX6WqwS2/" target="_blank" rel="noopener noreferrer" aria-label="Ruwah Beauty on TikTok" title="TikTok">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14.8 3c.3 1.8 1.4 3 3.2 3.3v2.6c-1.1 0-2.1-.3-3-.8v5.5a5.3 5.3 0 1 1-5.3-5.2c.3 0 .6 0 .9.1v2.7a2.6 2.6 0 1 0 1.8 2.5V3z"/></svg>
                    <span class="screen-reader-text">TikTok</span>
                </a>
            </div>
        </section>

        <section class="rwb-dieux-footer-col">
            <h2>Shop</h2>
            <?php foreach ($products as $product) : if (! $product instanceof WC_Product) continue; ?>
                <a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a>
            <?php endforeach; ?>
            <a href="<?php echo esc_url($shop_url); ?>">Shop All</a>
        </section>

        <section class="rwb-dieux-footer-col">
            <h2>Learn</h2>
            <a href="<?php echo esc_url(home_url('/#rwb-genesis')); ?>">Our Genesis</a>
            <a href="<?php echo esc_url(home_url('/#rwb-standard')); ?>">The Ruwah Standard</a>
            <a href="<?php echo esc_url(home_url('/#rituals')); ?>">Rituals</a>
            <a href="<?php echo esc_url($shop_url); ?>">Formula Guide</a>
        </section>

        <section class="rwb-dieux-footer-col">
            <h2>Contact</h2>
            <a href="mailto:rawahbeauty783@gmail.com" aria-label="Email Ruwah Beauty support at rawahbeauty783@gmail.com">rawahbeauty783@gmail.com</a>
            <a href="https://wa.me/923713923279" target="_blank" rel="noopener noreferrer" aria-label="Chat with Ruwah Beauty on WhatsApp">WhatsApp Support</a>
            <a href="<?php echo esc_url($contact_url); ?>">Contact Us</a>
            <?php if ($privacy_url) : ?><a href="<?php echo esc_url($privacy_url); ?>">Privacy Policy</a><?php endif; ?>
            <a href="<?php echo esc_url($account_url); ?>">My Account</a>
            <small>Support replies are typically sent within 2 business days.</small>
        </section>

        <section class="rwb-dieux-footer-promise">
            <h2>Our Promise</h2>
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
</footer>

<style id="rwb-unified-footer-runtime-styles">
.rwb-dieux-footer-promo{display:none!important}
.rwb-unified-socials{display:flex!important;gap:18px!important;margin-top:32px!important;align-items:center!important;flex-wrap:wrap!important}
.rwb-unified-socials .rwb-social-link{width:54px!important;height:54px!important;display:inline-flex!important;align-items:center!important;justify-content:center!important;border-radius:50%!important;background:#fff!important;color:#050505!important;text-decoration:none!important;line-height:0!important;transition:transform .18s ease,opacity .18s ease!important}
.rwb-unified-socials .rwb-social-link:hover,.rwb-unified-socials .rwb-social-link:focus-visible{transform:translateY(-2px)!important;opacity:.92!important}
.rwb-unified-socials .rwb-social-link svg{width:26px!important;height:26px!important;display:block!important;fill:currentColor!important}
.rwb-dieux-footer-form button{display:none!important}
.rwb-dieux-footer-bottom{display:flex!important;align-items:flex-end!important;justify-content:space-between!important;gap:24px!important;flex-wrap:wrap!important}
.rwb-dieux-footer-meta{flex:1 1 680px!important;width:auto!important;max-width:none!important;min-width:0!important}
.rwb-dieux-footer-legal{flex:0 1 auto!important;margin-left:auto!important}
@media(max-width:899px){.rwb-dieux-footer-bottom{align-items:flex-start!important}.rwb-dieux-footer-meta{flex-basis:100%!important}.rwb-dieux-footer-legal{margin-left:0!important}}
@media(max-width:560px){.rwb-unified-socials .rwb-social-link{width:48px!important;height:48px!important}.rwb-unified-socials .rwb-social-link svg{width:23px!important;height:23px!important}}
.rwb-contact-dock{position:fixed;right:22px;bottom:max(22px,env(safe-area-inset-bottom));z-index:125;display:flex;flex-direction:column;align-items:flex-end;font-family:Inter,Arial,sans-serif}
.rwb-contact-dock-item{position:relative;width:56px;height:56px;display:grid;place-items:center;border-radius:50%;box-shadow:0 7px 24px rgba(0,0,0,.18);transition:transform .18s ease,box-shadow .18s ease;text-decoration:none!important}
.rwb-contact-dock-item:hover,.rwb-contact-dock-item:focus-visible{transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,0,0,.24)}
.rwb-contact-dock-item--whatsapp{background:#25D366;color:#fff!important}
.rwb-contact-dock-item svg{width:28px;height:28px;display:block}
.rwb-contact-dock-label{position:absolute;right:66px;top:50%;min-width:max-content;padding:7px 10px;border-radius:4px;background:#111;color:#fff;font-size:11px;font-weight:600;line-height:1;opacity:0;pointer-events:none;transform:translate(7px,-50%);transition:.18s ease}
.rwb-contact-dock-item:hover .rwb-contact-dock-label,.rwb-contact-dock-item:focus-visible .rwb-contact-dock-label{opacity:1;transform:translate(0,-50%)}
body.rwb-lock .rwb-contact-dock{opacity:0;pointer-events:none}
@media(max-width:820px){.rwb-contact-dock{right:14px;bottom:max(14px,env(safe-area-inset-bottom))}.rwb-contact-dock-item{width:50px;height:50px}.rwb-contact-dock-item svg{width:25px;height:25px}.rwb-contact-dock-label{display:none}}
</style>

<nav id="rwb-contact-dock" class="rwb-contact-dock" aria-label="Contact Ruwah Beauty">
    <a class="rwb-contact-dock-item rwb-contact-dock-item--whatsapp" href="https://wa.me/923713923279" target="_blank" rel="noopener noreferrer" aria-label="Chat with Ruwah Beauty on WhatsApp at +92 371 3923279" title="WhatsApp: +92 371 3923279">
        <span class="rwb-contact-dock-label">WhatsApp</span>
        <svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.93 7.93 0 0 0 3.79.965h.004c4.366 0 7.926-3.558 7.93-7.93a7.9 7.9 0 0 0-2.327-5.607m-5.607 12.2a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.25a6.56 6.56 0 0 1-1.007-3.505c0-3.64 2.963-6.601 6.591-6.601a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.003 3.64-2.963 6.605-6.592 6.608m3.615-4.943c-.197-.099-1.17-.578-1.352-.643-.182-.065-.315-.099-.445.099-.133.197-.513.643-.629.775-.116.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.984-.59-.525-.985-1.174-1.101-1.372-.116-.197-.013-.304.086-.402.089-.088.197-.232.296-.348.099-.116.132-.197.197-.33.066-.132.033-.247-.016-.346-.05-.099-.445-1.074-.61-1.47-.16-.389-.323-.336-.445-.342l-.378-.007a.72.72 0 0 0-.527.247c-.182.197-.692.676-.692 1.65s.708 1.916.807 2.049c.099.132 1.394 2.128 3.377 2.984.471.203.839.324 1.125.414.472.15.902.129 1.242.078.379-.057 1.17-.478 1.335-.94.164-.462.164-.858.115-.94-.049-.083-.182-.132-.38-.231"/></svg>
    </a>
</nav>

<?php wp_footer(); ?>
</body></html>
