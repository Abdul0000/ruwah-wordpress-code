<?php
defined('ABSPATH') || exit;

/**
 * Checkout phone + reusable card Quick View reliability patch.
 * Keeps WooCommerce as the source of truth and changes no product data.
 */

function rwb_remove_closure_at_priority(string $hook, int $priority, string $source_file): void {
    global $wp_filter;
    if (empty($wp_filter[$hook]) || empty($wp_filter[$hook]->callbacks[$priority])) {
        return;
    }
    foreach ($wp_filter[$hook]->callbacks[$priority] as $entry) {
        $callback = $entry['function'] ?? null;
        if (! $callback instanceof Closure) {
            continue;
        }
        try {
            $reflection = new ReflectionFunction($callback);
        } catch (ReflectionException $e) {
            continue;
        }
        if (wp_normalize_path((string) $reflection->getFileName()) === wp_normalize_path($source_file)) {
            remove_filter($hook, $callback, $priority);
        }
    }
}

/* Checkout masthead must use the same approved homepage logo attachment. */
add_filter('get_custom_logo', static function (string $html): string {
    if (! function_exists('is_checkout') || ! is_checkout() || (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received'))) {
        return $html;
    }
    $image = wp_get_attachment_image(262, 'full', false, [
        'class' => 'custom-logo',
        'alt' => 'Ruwah Beauty',
        'loading' => 'eager',
        'decoding' => 'async',
    ]);
    if (! $image) {
        return $html;
    }
    return '<a href="' . esc_url(home_url('/')) . '" class="custom-logo-link" rel="home" aria-label="Ruwah Beauty home">' . wp_kses_post($image) . '</a>';
}, 30000);

/* Remove only the older phone closures from home-footer-dedup.php so one implementation owns the field. */
add_action('wp_loaded', static function (): void {
    $legacy = __DIR__ . '/home-footer-dedup.php';
    rwb_remove_closure_at_priority('woocommerce_checkout_fields', 20000, $legacy);
    rwb_remove_closure_at_priority('woocommerce_checkout_get_value', 20000, $legacy);
    rwb_remove_closure_at_priority('woocommerce_after_checkout_validation', 20000, $legacy);
    rwb_remove_closure_at_priority('wp_footer', 20000, $legacy);
}, 99999);

function rwb_local_phone_checkout_active(): bool {
    if (! function_exists('is_checkout') || ! is_checkout()) {
        return false;
    }
    return ! (function_exists('is_wc_endpoint_url') && is_wc_endpoint_url('order-received'));
}

/* Customer sees fixed +92 and enters exactly 10 local mobile digits after it. */
add_filter('woocommerce_checkout_fields', static function (array $fields): array {
    if (! rwb_local_phone_checkout_active() || ! isset($fields['billing']['billing_phone'])) {
        return $fields;
    }
    $phone =& $fields['billing']['billing_phone'];
    $phone['type'] = 'tel';
    $phone['placeholder'] = '+923XXXXXXXXX';
    $phone['default'] = '+92';
    $phone['autocomplete'] = 'tel';
    $phone['custom_attributes']['inputmode'] = 'numeric';
    $phone['custom_attributes']['maxlength'] = '13';
    $phone['custom_attributes']['minlength'] = '13';
    $phone['custom_attributes']['pattern'] = '\\+923[0-9]{9}';
    $phone['custom_attributes']['title'] = 'Enter exactly 10 Pakistan mobile digits after the fixed +92 prefix.';
    $phone['custom_attributes']['data-rwb-fixed-prefix'] = '+92';
    return $fields;
}, 30000);

add_filter('woocommerce_checkout_get_value', static function ($value, string $input) {
    if ('billing_phone' !== $input || ! rwb_local_phone_checkout_active()) {
        return $value;
    }
    $digits = preg_replace('/\D+/', '', (string) $value);
    if (str_starts_with((string) $digits, '92')) {
        $digits = substr((string) $digits, 2);
    }
    if (str_starts_with((string) $digits, '0')) {
        $digits = substr((string) $digits, 1);
    }
    return '+92' . substr((string) $digits, 0, 10);
}, 30000, 2);

/* Server gate: invalid number blocks Place Order. */
add_action('woocommerce_after_checkout_validation', static function (array $data, WP_Error $errors): void {
    $country = strtoupper(trim((string) ($data['billing_country'] ?? 'PK')));
    if ('PK' !== $country) {
        return;
    }
    $phone = preg_replace('/\s+/', '', trim((string) ($data['billing_phone'] ?? '')));
    if (! preg_match('/^\+923\d{9}$/', (string) $phone)) {
        $errors->add('rwb_pk_phone_visible_prefix', 'Enter exactly 10 Pakistan mobile digits after +92.');
        return;
    }
    $errors->remove('rwb_billing_phone_invalid');
    $errors->remove('rwb_pk_phone_fixed_prefix');
    $errors->remove('rwb_pk_phone_local_10');
}, 30000, 2);

/* Keep +92 fixed/visible and allow only 10 local digits after it. */
add_action('wp_footer', static function (): void {
    if (! rwb_local_phone_checkout_active()) {
        return;
    }
    ?>
    <script id="rwb-pk-visible-prefix-phone">
    (()=>{'use strict';
      const PREFIX='+92',MAX_LOCAL=10;
      const getPhone=()=>document.getElementById('billing_phone');
      const getCountry=()=>document.getElementById('billing_country');
      const isPK=()=>{const c=getCountry();return !c||String(c.value||'PK').toUpperCase()==='PK';};
      const localDigits=value=>{
        let d=String(value||'').replace(/\D/g,'');
        if(d.startsWith('92'))d=d.slice(2);
        if(d.startsWith('0'))d=d.slice(1);
        return d.slice(0,MAX_LOCAL);
      };
      const normalize=()=>{
        const p=getPhone();if(!p||!isPK())return;
        p.value=PREFIX+localDigits(p.value);
        p.maxLength=13;p.minLength=13;
        p.placeholder='+923XXXXXXXXX';
        p.autocomplete='tel';
        p.setAttribute('inputmode','numeric');
        p.setAttribute('pattern','\\+923[0-9]{9}');
        p.setAttribute('title','Enter exactly 10 Pakistan mobile digits after the fixed +92 prefix.');
        p.setAttribute('data-rwb-fixed-prefix',PREFIX);
        try{if(p.selectionStart!==null&&p.selectionStart<PREFIX.length)p.setSelectionRange(PREFIX.length,PREFIX.length);}catch(e){}
      };
      const protect=e=>{
        const p=getPhone();if(!p||e.target!==p||!isPK())return;
        const start=p.selectionStart==null?PREFIX.length:p.selectionStart;
        const end=p.selectionEnd==null?start:p.selectionEnd;
        if((e.key==='Backspace'&&start<=PREFIX.length&&end<=PREFIX.length)||(e.key==='Delete'&&start<PREFIX.length)){e.preventDefault();return;}
        if(e.key==='Home'){e.preventDefault();try{p.setSelectionRange(PREFIX.length,PREFIX.length);}catch(err){}}
      };
      document.addEventListener('keydown',protect,true);
      document.addEventListener('input',e=>{if(e.target&&e.target.id==='billing_phone')normalize();},true);
      document.addEventListener('paste',e=>{if(!e.target||e.target.id!=='billing_phone'||!isPK())return;setTimeout(normalize,0);},true);
      document.addEventListener('focusin',e=>{if(e.target&&e.target.id==='billing_phone')normalize();},true);
      document.addEventListener('change',e=>{if(e.target&&e.target.id==='billing_country')setTimeout(normalize,0);},true);
      if(window.jQuery){
        window.jQuery(document.body).on('updated_checkout country_to_state_changed checkout_error',()=>setTimeout(normalize,0));
      }
      if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',normalize,{once:true});else normalize();
    })();
    </script>
    <?php
}, 30000);

/* Late Quick View handler: dialog is resolved at click time, after footer markup exists. */
add_action('wp_footer', static function (): void {
    if (! function_exists('rwb_master_card_runtime_surface') || (! is_front_page() && ! rwb_master_card_runtime_surface())) {
        return;
    }
    ?>
    <script id="rwb-quick-view-runtime-fix">
    (()=>{'use strict';
      if(window.__rwbQuickViewRuntimeFix)return;window.__rwbQuickViewRuntimeFix=true;
      let returnTo=null;
      const dialog=()=>document.querySelector('[data-quick-dialog]');
      const fill=(dlg,trigger)=>{
        const set=(sel,val)=>{const el=dlg.querySelector(sel);if(el)el.textContent=val||'';};
        set('[data-qv-name]',trigger.dataset.qvName);
        set('[data-qv-copy]',trigger.dataset.qvCopy);
        set('[data-qv-price]',trigger.dataset.qvPrice);
        set('[data-qv-stock]',trigger.dataset.qvStock);
        const img=dlg.querySelector('[data-qv-image]');if(img){img.src=trigger.dataset.qvImage||'';img.alt=trigger.dataset.qvName||'';}
        const link=dlg.querySelector('[data-qv-link]');if(link)link.href=trigger.dataset.qvUrl||'#';
        const add=dlg.querySelector('[data-qv-add]');if(add){add.href=trigger.dataset.qvAdd||trigger.dataset.qvUrl||'#';add.textContent=trigger.dataset.qvCanCart==='1'?'Add to cart':'View product';}
      };
      document.addEventListener('click',e=>{
        const trigger=e.target.closest?.('[data-quick-view]');
        if(trigger){
          const dlg=dialog();if(!dlg)return;
          e.preventDefault();returnTo=trigger;fill(dlg,trigger);
          if(typeof dlg.showModal==='function'){if(!dlg.open)dlg.showModal();}
          else{dlg.setAttribute('open','');dlg.classList.add('is-open');}
          setTimeout(()=>dlg.querySelector('[data-quick-close]')?.focus(),0);return;
        }
        const close=e.target.closest?.('[data-quick-close]');
        if(close){const dlg=dialog();if(!dlg)return;e.preventDefault();if(typeof dlg.close==='function'&&dlg.open)dlg.close();else{dlg.removeAttribute('open');dlg.classList.remove('is-open');returnTo?.focus?.();}}
      });
      const dlg=dialog();if(dlg){dlg.addEventListener('click',e=>{if(e.target===dlg&&dlg.open)dlg.close();});dlg.addEventListener('close',()=>returnTo?.focus?.());}
    })();
    </script>
    <?php
}, 50000);