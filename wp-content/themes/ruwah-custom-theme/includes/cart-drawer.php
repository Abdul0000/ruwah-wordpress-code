<?php
defined('ABSPATH') || exit;

/**
 * Isolated Ruwah cart drawer. This file owns only the header side-cart UI and
 * current shopper cart-session mutations. It does not alter products/orders.
 */

function rwb_cart_drawer_ensure_cart(): bool {
    if (! function_exists('WC')) {
        return false;
    }
    if (! WC()->cart && function_exists('wc_load_cart')) {
        wc_load_cart();
    }
    return (bool) WC()->cart;
}

function rwb_cart_drawer_pair_product(): ?WC_Product {
    if (! rwb_cart_drawer_ensure_cart()) {
        return null;
    }

    $in_cart = [];
    foreach (WC()->cart->get_cart() as $item) {
        $in_cart[(int) ($item['product_id'] ?? 0)] = true;
        $in_cart[(int) ($item['variation_id'] ?? 0)] = true;
    }

    $candidates = function_exists('rwb_products') ? rwb_products(0) : [];
    if (! $candidates && function_exists('wc_get_products')) {
        $candidates = wc_get_products([
            'status' => 'publish',
            'limit' => 8,
            'orderby' => 'menu_order',
            'order' => 'ASC',
        ]);
    }

    foreach ($candidates as $candidate) {
        if (! $candidate instanceof WC_Product) {
            continue;
        }
        if (isset($in_cart[$candidate->get_id()])) {
            continue;
        }
        if (! $candidate->is_visible() || ! $candidate->is_purchasable() || ! $candidate->is_in_stock()) {
            continue;
        }
        return $candidate;
    }

    return null;
}

function rwb_cart_drawer_notice_text(): string {
    if (! function_exists('wc_get_notices')) {
        return '';
    }
    $notices = wc_get_notices();
    foreach (['error', 'success', 'notice'] as $type) {
        if (empty($notices[$type])) {
            continue;
        }
        $entry = reset($notices[$type]);
        $notice = is_array($entry) && isset($entry['notice']) ? $entry['notice'] : $entry;
        if (function_exists('wc_clear_notices')) {
            wc_clear_notices();
        }
        return trim(wp_strip_all_tags((string) $notice));
    }
    if (function_exists('wc_clear_notices')) {
        wc_clear_notices();
    }
    return '';
}

function rwb_render_cart_drawer_content(): void {
    $has_cart = rwb_cart_drawer_ensure_cart();
    $count = $has_cart ? (int) WC()->cart->get_cart_contents_count() : 0;
    $count_label = sprintf(_n('%s ITEM', '%s ITEMS', $count, 'ruwah'), number_format_i18n($count));
    $nonce = wp_create_nonce('rwb_cart_drawer');
    ?>
    <div class="rwb-cart-drawer-content" data-rwb-cart-content data-rwb-cart-nonce="<?php echo esc_attr($nonce); ?>">
        <header class="rwb-cart-drawer-head">
            <button class="rwb-cart-drawer-close" type="button" data-cart-close aria-label="Close cart"><span aria-hidden="true">×</span><b>CLOSE</b></button>
            <h2>CART</h2>
            <span class="rwb-cart-drawer-count"><?php echo esc_html($count_label); ?></span>
        </header>

        <?php if (! $has_cart || WC()->cart->is_empty()) : ?>
            <div class="rwb-cart-drawer-empty">
                <p>Your bag is empty.</p>
                <a href="<?php echo esc_url(function_exists('ruwah_shop_url') ? ruwah_shop_url() : home_url('/shop/')); ?>">SHOP ALL</a>
            </div>
        <?php else : ?>
            <section class="rwb-cart-drawer-items" aria-label="Cart items">
                <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                    $product = isset($cart_item['data']) && $cart_item['data'] instanceof WC_Product ? $cart_item['data'] : null;
                    if (! $product || ! $product->exists() || (int) ($cart_item['quantity'] ?? 0) < 1) {
                        continue;
                    }
                    $quantity = (int) $cart_item['quantity'];
                    $permalink = $product->is_visible() ? $product->get_permalink($cart_item) : '';
                    $unit_price = WC()->cart->get_product_price($product);
                    ?>
                    <article class="rwb-cart-drawer-item" data-rwb-cart-item="<?php echo esc_attr($cart_item_key); ?>">
                        <a class="rwb-cart-drawer-item-media" href="<?php echo esc_url($permalink ?: '#'); ?>" tabindex="<?php echo $permalink ? '0' : '-1'; ?>">
                            <?php echo wp_kses_post($product->get_image('woocommerce_thumbnail', ['loading' => 'lazy', 'decoding' => 'async'])); ?>
                        </a>
                        <div class="rwb-cart-drawer-item-copy">
                            <h3><?php if ($permalink) : ?><a href="<?php echo esc_url($permalink); ?>"><?php endif; ?><?php echo esc_html($product->get_name()); ?><?php if ($permalink) : ?></a><?php endif; ?></h3>
                            <div class="rwb-cart-drawer-item-price"><?php echo wp_kses_post($unit_price); ?></div>
                            <div class="rwb-cart-drawer-qty" aria-label="Quantity for <?php echo esc_attr($product->get_name()); ?>">
                                <button type="button" data-rwb-qty="minus" data-cart-key="<?php echo esc_attr($cart_item_key); ?>" data-quantity="<?php echo esc_attr((string) max(0, $quantity - 1)); ?>" aria-label="Decrease quantity">−</button>
                                <span aria-live="polite"><?php echo esc_html((string) $quantity); ?></span>
                                <button type="button" data-rwb-qty="plus" data-cart-key="<?php echo esc_attr($cart_item_key); ?>" data-quantity="<?php echo esc_attr((string) ($quantity + 1)); ?>" aria-label="Increase quantity">+</button>
                            </div>
                        </div>
                        <button class="rwb-cart-drawer-remove" type="button" data-rwb-remove data-cart-key="<?php echo esc_attr($cart_item_key); ?>">REMOVE</button>
                    </article>
                <?php endforeach; ?>
            </section>

            <?php $paired = rwb_cart_drawer_pair_product(); if ($paired) :
                $paired_info = function_exists('rwb_info') ? rwb_info($paired) : null;
                $paired_tagline = is_array($paired_info) ? trim((string) ($paired_info['tagline'] ?? '')) : '';
                if ('' === $paired_tagline) {
                    $paired_tagline = trim(wp_strip_all_tags($paired->get_short_description()));
                }
                $paired_tagline = strtoupper(wp_trim_words($paired_tagline, 6, ''));
                ?>
                <section class="rwb-cart-drawer-paired" aria-labelledby="rwb-cart-paired-title">
                    <h3 id="rwb-cart-paired-title">FREQUENTLY PAIRED WITH:</h3>
                    <div class="rwb-cart-drawer-paired-row">
                        <a class="rwb-cart-drawer-paired-media" href="<?php echo esc_url($paired->get_permalink()); ?>"><?php echo wp_kses_post($paired->get_image('woocommerce_thumbnail', ['loading' => 'lazy', 'decoding' => 'async'])); ?></a>
                        <div class="rwb-cart-drawer-paired-copy"><h4><a href="<?php echo esc_url($paired->get_permalink()); ?>"><?php echo esc_html($paired->get_name()); ?></a></h4><?php if ($paired_tagline) : ?><p><?php echo esc_html($paired_tagline); ?></p><?php endif; ?></div>
                        <a rel="nofollow" class="rwb-cart-drawer-paired-add add_to_cart_button ajax_add_to_cart" data-product_id="<?php echo esc_attr((string) $paired->get_id()); ?>" data-product_sku="<?php echo esc_attr($paired->get_sku()); ?>" data-quantity="1" href="<?php echo esc_url($paired->add_to_cart_url()); ?>"><span>Add</span><b>−</b><strong><?php echo wp_kses_post(wc_price((float) $paired->get_price())); ?></strong></a>
                    </div>
                </section>
            <?php endif; ?>

            <section class="rwb-cart-drawer-coupon">
                <form data-rwb-cart-coupon novalidate>
                    <label class="screen-reader-text" for="rwb-cart-coupon-code">Discount code or gift card</label>
                    <input id="rwb-cart-coupon-code" type="text" name="coupon_code" autocomplete="off" placeholder="DISCOUNT CODE OR GIFT CARD">
                    <button type="submit">Apply</button>
                </form>
                <p class="rwb-cart-drawer-message" data-rwb-cart-message aria-live="polite"></p>
            </section>

            <section class="rwb-cart-drawer-summary">
                <div class="rwb-cart-drawer-subtotal"><span>SUBTOTAL</span><strong><?php echo wp_kses_post(WC()->cart->get_cart_subtotal()); ?></strong></div>
                <p>SHIPPING AND TAXES CALCULATED AT CHECKOUT</p>
                <a class="rwb-cart-drawer-checkout" href="<?php echo esc_url(wc_get_checkout_url()); ?>">Checkout</a>
            </section>
        <?php endif; ?>
    </div>
    <?php
}

function rwb_cart_drawer_capture(): string {
    ob_start();
    rwb_render_cart_drawer_content();
    return (string) ob_get_clean();
}

add_filter('woocommerce_add_to_cart_fragments', function (array $fragments): array {
    $fragments['.rwb-cart-drawer-content'] = rwb_cart_drawer_capture();
    return $fragments;
}, 30);

function rwb_cart_drawer_ajax_response(string $message = '', bool $ok = true): void {
    $count = rwb_cart_drawer_ensure_cart() ? (int) WC()->cart->get_cart_contents_count() : 0;
    wp_send_json_success([
        'ok' => $ok,
        'html' => rwb_cart_drawer_capture(),
        'count' => $count,
        'message' => $message,
    ]);
}

function rwb_cart_drawer_ajax_boot(): bool {
    check_ajax_referer('rwb_cart_drawer', 'nonce');
    if (! rwb_cart_drawer_ensure_cart()) {
        wp_send_json_error(['message' => 'Cart is unavailable.'], 400);
    }
    return true;
}

function rwb_cart_drawer_update_qty(): void {
    rwb_cart_drawer_ajax_boot();
    $key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';
    $quantity = isset($_POST['quantity']) ? max(0, (int) wc_stock_amount(wp_unslash($_POST['quantity']))) : 0;
    $item = $key ? WC()->cart->get_cart_item($key) : null;
    if (! is_array($item)) {
        wp_send_json_error(['message' => 'Cart item was not found.'], 404);
    }
    $passed = apply_filters('woocommerce_update_cart_validation', true, $key, $item, $quantity);
    if (! $passed) {
        rwb_cart_drawer_ajax_response('That quantity is not available.', false);
    }
    WC()->cart->set_quantity($key, $quantity, true);
    WC()->cart->calculate_totals();
    rwb_cart_drawer_ajax_response();
}

function rwb_cart_drawer_remove_item(): void {
    rwb_cart_drawer_ajax_boot();
    $key = isset($_POST['cart_item_key']) ? sanitize_text_field(wp_unslash($_POST['cart_item_key'])) : '';
    if (! $key || ! WC()->cart->get_cart_item($key)) {
        wp_send_json_error(['message' => 'Cart item was not found.'], 404);
    }
    WC()->cart->remove_cart_item($key);
    WC()->cart->calculate_totals();
    rwb_cart_drawer_ajax_response();
}

function rwb_cart_drawer_apply_coupon(): void {
    rwb_cart_drawer_ajax_boot();
    $code = isset($_POST['coupon_code']) ? wc_format_coupon_code(wp_unslash($_POST['coupon_code'])) : '';
    if ('' === $code) {
        rwb_cart_drawer_ajax_response('Enter a discount code.', false);
    }
    if (function_exists('wc_clear_notices')) {
        wc_clear_notices();
    }
    $applied = WC()->cart->apply_coupon($code);
    WC()->cart->calculate_totals();
    $message = rwb_cart_drawer_notice_text();
    if ('' === $message) {
        $message = $applied ? 'Discount applied.' : 'Coupon could not be applied.';
    }
    rwb_cart_drawer_ajax_response($message, (bool) $applied);
}

foreach ([
    'rwb_cart_drawer_update_qty' => 'rwb_cart_drawer_update_qty',
    'rwb_cart_drawer_remove_item' => 'rwb_cart_drawer_remove_item',
    'rwb_cart_drawer_apply_coupon' => 'rwb_cart_drawer_apply_coupon',
] as $action => $callback) {
    add_action('wp_ajax_' . $action, $callback);
    add_action('wp_ajax_nopriv_' . $action, $callback);
}

add_action('wp_enqueue_scripts', function (): void {
    $css = <<<'CSS'
.rwb-shop-cart-drawer{width:760px!important;max-width:100%!important;height:100%!important;padding:0!important;overflow:auto!important;background:#f7f3e9!important;color:#111!important;font-family:var(--sans,Inter,Arial,sans-serif)}
.rwb-cart-drawer-content{min-height:100%;display:flex;flex-direction:column;background:#f7f3e9}.rwb-cart-drawer-head{position:sticky;z-index:3;top:0;min-height:86px;display:grid;grid-template-columns:1fr auto 1fr;align-items:center;padding:0 38px;border-bottom:1px solid rgba(17,17,17,.75);background:#f7f3e9}.rwb-cart-drawer-head h2{margin:0;font-family:var(--serif,'DM Serif Display',Georgia,serif);font-size:38px;font-weight:400;line-height:1;letter-spacing:-.02em}.rwb-cart-drawer-close{justify-self:start;display:inline-flex;align-items:center;gap:14px;padding:0;border:0;background:transparent;color:#111;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:17px;line-height:1;cursor:pointer}.rwb-cart-drawer-close span{font-family:Arial,sans-serif;font-size:31px;font-weight:200;line-height:.8}.rwb-cart-drawer-close b{font-weight:400}.rwb-cart-drawer-count{justify-self:end;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:17px;letter-spacing:.03em}.rwb-cart-drawer-items{border-bottom:1px solid rgba(17,17,17,.24)}.rwb-cart-drawer-item{min-height:270px;display:grid;grid-template-columns:210px minmax(0,1fr) auto;gap:30px;align-items:center;padding:22px 24px}.rwb-cart-drawer-item+.rwb-cart-drawer-item{border-top:1px solid rgba(17,17,17,.24)}.rwb-cart-drawer-item-media{width:210px;height:210px;display:grid;place-items:center;overflow:hidden;background:#e1e7e9}.rwb-cart-drawer-item-media img{width:100%!important;height:100%!important;padding:12%!important;object-fit:contain!important;background:transparent!important}.rwb-cart-drawer-item-copy{align-self:center}.rwb-cart-drawer-item-copy h3{margin:0;font-size:25px;font-weight:400;line-height:1.16;letter-spacing:-.02em}.rwb-cart-drawer-item-price{margin-top:12px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:16px}.rwb-cart-drawer-qty{display:grid;grid-template-columns:47px 61px 47px;width:max-content;margin-top:34px;border:1px solid #111}.rwb-cart-drawer-qty button,.rwb-cart-drawer-qty span{height:38px;display:grid;place-items:center;border:0;background:transparent;color:#111;font-size:19px;line-height:1}.rwb-cart-drawer-qty button{cursor:pointer}.rwb-cart-drawer-qty button+span,.rwb-cart-drawer-qty span+button{border-left:1px solid #111}.rwb-cart-drawer-remove{align-self:center;padding:0;border:0;border-bottom:1px solid #111;background:transparent;color:#111;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:13px;line-height:1.1;cursor:pointer}.rwb-cart-drawer-paired{padding:31px 24px 0;border-bottom:1px solid rgba(17,17,17,.8)}.rwb-cart-drawer-paired>h3{margin:0 0 28px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:16px;font-weight:400;letter-spacing:.03em}.rwb-cart-drawer-paired-row{display:grid;grid-template-columns:142px minmax(0,1fr) auto;gap:22px;align-items:center}.rwb-cart-drawer-paired-media{width:142px;height:124px;display:grid;place-items:center;overflow:hidden;background:#e1e7e9}.rwb-cart-drawer-paired-media img{width:100%!important;height:100%!important;padding:11%!important;object-fit:contain!important}.rwb-cart-drawer-paired-copy h4{margin:0;font-size:22px;font-weight:400;line-height:1.1}.rwb-cart-drawer-paired-copy p{margin:8px 0 0;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:13px;line-height:1.35;letter-spacing:.02em}.rwb-cart-drawer-paired-add{min-width:166px;height:44px;display:flex!important;align-items:center;justify-content:center;gap:9px;padding:0 16px!important;border:0!important;border-radius:0!important;background:#303030!important;color:#fff!important;font-family:ui-monospace,SFMono-Regular,Menlo,monospace!important;font-size:14px!important;line-height:1!important}.rwb-cart-drawer-paired-add b{font-weight:400}.rwb-cart-drawer-paired-add strong{font-weight:400}.rwb-cart-drawer-paired-add .woocommerce-Price-amount{white-space:nowrap}.rwb-cart-drawer-coupon{padding:22px 30px 20px;border-bottom:1px solid rgba(17,17,17,.8)}.rwb-cart-drawer-coupon form{display:grid;grid-template-columns:minmax(0,1fr) 118px;gap:15px}.rwb-cart-drawer-coupon input{width:100%;height:64px!important;padding:0 22px!important;border:1px solid #111!important;border-radius:0!important;background:#fff!important;color:#111!important;font-family:ui-monospace,SFMono-Regular,Menlo,monospace!important;font-size:16px!important;letter-spacing:.035em!important;outline:0}.rwb-cart-drawer-coupon input::placeholder{color:#777;opacity:1}.rwb-cart-drawer-coupon button{height:64px;border:0;background:#303030;color:#fff;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:15px;cursor:pointer}.rwb-cart-drawer-message{min-height:18px;margin:7px 0 0;color:#5e5b58;font-size:11px}.rwb-cart-drawer-summary{margin-top:auto;padding:26px 38px 38px}.rwb-cart-drawer-subtotal{display:flex;align-items:baseline;justify-content:space-between;gap:24px}.rwb-cart-drawer-subtotal>span,.rwb-cart-drawer-subtotal>strong{font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:24px;font-weight:400;line-height:1}.rwb-cart-drawer-summary>p{margin:18px 0 16px;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;letter-spacing:.02em}.rwb-cart-drawer-checkout{min-height:64px;display:flex;align-items:center;justify-content:center;width:100%;background:#303030;color:#fff!important;font-size:18px;font-weight:500}.rwb-cart-drawer-empty{margin:auto;padding:80px 30px;text-align:center}.rwb-cart-drawer-empty p{margin:0 0 26px;font-family:var(--serif,'DM Serif Display',Georgia,serif);font-size:36px}.rwb-cart-drawer-empty a{display:inline-flex;min-height:48px;align-items:center;padding:0 24px;background:#111;color:#fff;font-size:10px;font-weight:700;letter-spacing:.08em}
@media(max-width:680px){.rwb-cart-drawer-head{min-height:72px;padding:0 18px}.rwb-cart-drawer-head h2{font-size:30px}.rwb-cart-drawer-close,.rwb-cart-drawer-count{font-size:12px}.rwb-cart-drawer-close{gap:8px}.rwb-cart-drawer-close span{font-size:24px}.rwb-cart-drawer-item{min-height:0;grid-template-columns:118px minmax(0,1fr);gap:17px;padding:18px 16px}.rwb-cart-drawer-item-media{width:118px;height:142px}.rwb-cart-drawer-item-copy h3{font-size:18px}.rwb-cart-drawer-item-price{margin-top:7px;font-size:12px}.rwb-cart-drawer-qty{grid-template-columns:38px 46px 38px;margin-top:18px}.rwb-cart-drawer-qty button,.rwb-cart-drawer-qty span{height:34px;font-size:16px}.rwb-cart-drawer-remove{grid-column:2;justify-self:end;margin-top:-6px;font-size:11px}.rwb-cart-drawer-paired{padding:24px 16px 0}.rwb-cart-drawer-paired>h3{margin-bottom:20px;font-size:13px}.rwb-cart-drawer-paired-row{grid-template-columns:92px minmax(0,1fr);gap:14px}.rwb-cart-drawer-paired-media{width:92px;height:108px}.rwb-cart-drawer-paired-copy h4{font-size:17px}.rwb-cart-drawer-paired-copy p{font-size:10px}.rwb-cart-drawer-paired-add{grid-column:1/-1;width:100%;margin-bottom:18px}.rwb-cart-drawer-coupon{padding:18px 16px}.rwb-cart-drawer-coupon form{grid-template-columns:1fr 92px;gap:9px}.rwb-cart-drawer-coupon input,.rwb-cart-drawer-coupon button{height:54px!important}.rwb-cart-drawer-coupon input{padding:0 13px!important;font-size:12px!important}.rwb-cart-drawer-coupon button{font-size:12px}.rwb-cart-drawer-summary{padding:23px 18px 28px}.rwb-cart-drawer-subtotal>span,.rwb-cart-drawer-subtotal>strong{font-size:19px}.rwb-cart-drawer-summary>p{font-size:10px;line-height:1.35}.rwb-cart-drawer-checkout{min-height:58px;font-size:16px}}
CSS;
    wp_add_inline_style('rwb-theme', $css);

    $ajax_url = admin_url('admin-ajax.php');
    $js = <<<'JS'
(()=>{'use strict';const q=(s,c=document)=>c.querySelector(s),qa=(s,c=document)=>[...c.querySelectorAll(s)];const root=()=>q('[data-rwb-cart-content]');const nonce=()=>root()?.dataset.rwbCartNonce||'';const updateCount=n=>qa('.rwb-cart-count').forEach(el=>el.textContent=String(n));const replace=data=>{const current=root();if(current&&data?.html){const wrap=document.createElement('div');wrap.innerHTML=data.html.trim();const fresh=wrap.firstElementChild;if(fresh)current.replaceWith(fresh)}if(Number.isFinite(Number(data?.count)))updateCount(Number(data.count));const msg=q('[data-rwb-cart-message]');if(msg&&data?.message)msg.textContent=data.message||''};const post=async(action,payload={})=>{const body=new URLSearchParams({action,nonce:nonce(),...payload});const res=await fetch('RWB_AJAX_URL',{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'},body:body.toString()});const json=await res.json();if(!json?.success)throw new Error(json?.data?.message||'Unable to update cart.');replace(json.data||{});return json.data||{}};document.addEventListener('click',e=>{const close=e.target.closest('.rwb-cart-drawer-content [data-cart-close]');if(close){const layer=q('[data-cart]');if(layer){layer.classList.remove('is-open');setTimeout(()=>layer.hidden=true,220);document.body.classList.remove('rwb-lock')}return}const qty=e.target.closest('[data-rwb-qty]');if(qty){e.preventDefault();qty.disabled=true;post('rwb_cart_drawer_update_qty',{cart_item_key:qty.dataset.cartKey||'',quantity:qty.dataset.quantity||'0'}).catch(err=>{const msg=q('[data-rwb-cart-message]');if(msg)msg.textContent=err.message}).finally(()=>{qty.disabled=false});return}const remove=e.target.closest('[data-rwb-remove]');if(remove){e.preventDefault();remove.disabled=true;post('rwb_cart_drawer_remove_item',{cart_item_key:remove.dataset.cartKey||''}).catch(err=>{const msg=q('[data-rwb-cart-message]');if(msg)msg.textContent=err.message}).finally(()=>{remove.disabled=false})}},true);document.addEventListener('submit',e=>{const form=e.target.closest('[data-rwb-cart-coupon]');if(!form)return;e.preventDefault();const input=q('input[name="coupon_code"]',form);const button=q('button[type="submit"]',form);if(button)button.disabled=true;post('rwb_cart_drawer_apply_coupon',{coupon_code:input?.value||''}).catch(err=>{const msg=q('[data-rwb-cart-message]');if(msg)msg.textContent=err.message}).finally(()=>{if(button)button.disabled=false})});if(window.jQuery)window.jQuery(document.body).on('added_to_cart',()=>{const layer=q('[data-cart]');if(layer&&!layer.classList.contains('is-open')){layer.hidden=false;requestAnimationFrame(()=>layer.classList.add('is-open'));document.body.classList.add('rwb-lock')}})})();
JS;
    $js = str_replace('RWB_AJAX_URL', esc_js($ajax_url), $js);
    wp_add_inline_script('rwb-theme', $js, 'after');
}, 60);
