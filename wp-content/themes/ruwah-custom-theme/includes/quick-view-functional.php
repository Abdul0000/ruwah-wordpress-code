<?php
defined('ABSPATH') || exit;

/**
 * Functional Quick View controller.
 * Reads live WC_Product state only; never mutates product/catalog data.
 */

add_action('wp_ajax_rwb_quick_view_product', 'rwb_quick_view_product_payload');
add_action('wp_ajax_nopriv_rwb_quick_view_product', 'rwb_quick_view_product_payload');

if (! function_exists('rwb_quick_view_price_text')) {
    function rwb_quick_view_price_text(float $amount): string {
        $html = function_exists('wc_price') ? wc_price($amount, ['decimals' => 0]) : (string) $amount;
        $text = html_entity_decode(wp_strip_all_tags((string) $html), ENT_QUOTES | ENT_HTML5, get_bloginfo('charset') ?: 'UTF-8');
        $text = str_replace("\xC2\xA0", ' ', $text);
        return trim((string) preg_replace('/\s+/u', ' ', $text));
    }
}

function rwb_quick_view_product_payload(): void {
    $product_id = isset($_GET['product_id']) ? absint(wp_unslash($_GET['product_id'])) : 0;
    $product = $product_id && function_exists('wc_get_product') ? wc_get_product($product_id) : false;

    if (! $product instanceof WC_Product || 'publish' !== get_post_status($product_id) || ! $product->is_visible()) {
        wp_send_json_error(['message' => 'This product is not available.'], 404);
    }

    $image_url = wp_get_attachment_image_url((int) $product->get_image_id(), 'woocommerce_single') ?: wc_placeholder_img_src('woocommerce_single');
    $stock_text = $product->is_in_stock() ? 'In stock' : 'Out of stock';
    $can_cart = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock();
    $max_qty = $can_cart ? $product->get_max_purchase_quantity() : 0;
    $min_qty = $can_cart ? max(1, (int) $product->get_min_purchase_quantity()) : 1;
    $copy = trim(wp_strip_all_tags((string) $product->get_short_description()));
    if ('' === $copy) {
        $copy = wp_trim_words(wp_strip_all_tags((string) $product->get_description()), 24, '…');
    }

    $current_amount = (float) $product->get_price();
    $regular_amount = (float) $product->get_regular_price();
    $on_sale = $product->is_on_sale() && $regular_amount > $current_amount && $current_amount >= 0;

    wp_send_json_success([
        'id' => (int) $product->get_id(),
        'name' => (string) $product->get_name(),
        'image' => (string) $image_url,
        'price' => rwb_quick_view_price_text($current_amount),
        'regular_price' => $on_sale ? rwb_quick_view_price_text($regular_amount) : '',
        'on_sale' => $on_sale,
        'stock' => $stock_text,
        'copy' => $copy,
        'url' => (string) $product->get_permalink(),
        'can_cart' => $can_cart,
        'min_qty' => $min_qty,
        'max_qty' => $max_qty < 0 ? 0 : (int) $max_qty,
    ]);
}

/* Remove the older late Quick View closure from checkout-quickview-fix.php. */
add_action('wp_loaded', static function (): void {
    if (function_exists('rwb_remove_closure_at_priority')) {
        rwb_remove_closure_at_priority('wp_footer', 50000, __DIR__ . '/checkout-quickview-fix.php');
    }
}, 99999);

/* The functional controller replaces product-card.js; card CSS remains untouched. */
add_action('wp_enqueue_scripts', static function (): void {
    if (! function_exists('rwb_master_card_runtime_surface') || (! is_front_page() && ! rwb_master_card_runtime_surface())) {
        return;
    }
    wp_dequeue_script('rwb-master-card-safe');
    wp_deregister_script('rwb-master-card-safe');
    wp_enqueue_script('wc-add-to-cart');
    wp_add_inline_style('rwb-theme', '.rhp-qv-copy [data-qv-price]{display:flex;flex-wrap:wrap;align-items:baseline;gap:10px}.rhp-qv-copy [data-qv-price] del{opacity:.55;font-weight:500}.rhp-qv-copy [data-qv-price] .rwb-qv-current{font-weight:700}');
}, 20000);

add_action('wp_footer', static function (): void {
    if (! function_exists('rwb_master_card_runtime_surface') || (! is_front_page() && ! rwb_master_card_runtime_surface())) {
        return;
    }
    $ajax_url = admin_url('admin-ajax.php');
    ?>
    <script id="rwb-quick-view-functional-v3">
    (()=>{'use strict';
      if(window.__rwbQuickViewFunctionalV3)return;window.__rwbQuickViewFunctionalV3=true;
      const AJAX=<?php echo wp_json_encode($ajax_url); ?>;
      let returnTo=null,loadToken=0,addInFlight=false,current=null;
      const dialog=()=>document.querySelector('[data-quick-dialog]');
      const setText=(dlg,sel,val)=>{const el=dlg.querySelector(sel);if(el)el.textContent=val||'';};
      const renderPrice=(dlg,data)=>{
        const el=dlg.querySelector('[data-qv-price]');if(!el)return;el.replaceChildren();
        if(data?.on_sale&&data?.regular_price){const old=document.createElement('del');old.textContent=data.regular_price;const now=document.createElement('span');now.className='rwb-qv-current';now.textContent=data.price||'';el.append(old,now);return;}
        const now=document.createElement('span');now.className='rwb-qv-current';now.textContent=data?.price||'';el.append(now);
      };
      const ensureControls=dlg=>{
        let qty=dlg.querySelector('[data-qv-qty]');
        if(!qty){
          const actions=dlg.querySelector('[data-qv-add]')?.parentElement;
          if(actions){
            const label=document.createElement('label');label.className='rwb-qv-quantity';label.setAttribute('data-qv-qty-wrap','');
            const sr=document.createElement('span');sr.className='screen-reader-text';sr.textContent='Quantity';
            qty=document.createElement('input');qty.type='number';qty.inputMode='numeric';qty.value='1';qty.min='1';qty.step='1';qty.setAttribute('data-qv-qty','');qty.setAttribute('aria-label','Quantity');
            label.append(sr,qty);actions.insertBefore(label,actions.firstChild);
          }
        }
        let status=dlg.querySelector('[data-qv-status]');
        if(!status){status=document.createElement('div');status.className='screen-reader-text';status.setAttribute('data-qv-status','');status.setAttribute('aria-live','polite');dlg.querySelector('.rhp-qv-copy')?.appendChild(status);}
        return{qty,status};
      };
      const applyFragments=fragments=>{Object.entries(fragments||{}).forEach(([selector,html])=>document.querySelectorAll(selector).forEach(node=>{const t=document.createElement('template');t.innerHTML=String(html||'').trim();const fresh=t.content.firstElementChild;if(fresh)node.replaceWith(fresh.cloneNode(true));}));};
      const setBusy=(add,busy)=>{addInFlight=busy;if(!add)return;add.setAttribute('aria-busy',busy?'true':'false');if(busy){add.setAttribute('aria-disabled','true');add.classList.add('loading');}else{add.removeAttribute('aria-disabled');add.classList.remove('loading');}};
      const closeDialog=dlg=>{if(!dlg)return;if(typeof dlg.close==='function'&&dlg.open)dlg.close();else{dlg.removeAttribute('open');dlg.classList.remove('is-open');returnTo?.focus?.();}};
      const fill=async(dlg,trigger)=>{
        const productId=Number.parseInt(trigger.dataset.qvProductId||'',10);if(!Number.isFinite(productId)||productId<1)return;
        const token=++loadToken;current=null;const {qty,status}=ensureControls(dlg);const add=dlg.querySelector('[data-qv-add]');
        if(status)status.textContent='Loading product details.';if(add){add.setAttribute('aria-disabled','true');add.removeAttribute('href');}if(qty)qty.disabled=true;
        try{
          const url=new URL(AJAX,window.location.origin);url.searchParams.set('action','rwb_quick_view_product');url.searchParams.set('product_id',String(productId));url.searchParams.set('_',String(Date.now()));
          const response=await fetch(url.toString(),{credentials:'same-origin',cache:'no-store',headers:{'X-Requested-With':'XMLHttpRequest'}});const payload=await response.json();
          if(token!==loadToken)return;if(!response.ok||!payload?.success)throw new Error(payload?.data?.message||'Unable to load this product.');
          const data=payload.data;current=data;setText(dlg,'[data-qv-name]',data.name);setText(dlg,'[data-qv-copy]',data.copy);renderPrice(dlg,data);setText(dlg,'[data-qv-stock]',data.stock);
          const img=dlg.querySelector('[data-qv-image]');if(img){img.src=data.image||'';img.alt=data.name||'';}const link=dlg.querySelector('[data-qv-link]');if(link)link.href=data.url||'#';
          if(qty){qty.min=String(data.min_qty||1);qty.value=String(data.min_qty||1);qty.step='1';if(data.max_qty>0)qty.max=String(data.max_qty);else qty.removeAttribute('max');qty.disabled=!data.can_cart;}
          if(add){add.textContent=data.can_cart?'Add to cart':'View product';if(data.can_cart){add.href='#';add.removeAttribute('aria-disabled');}else{add.href=data.url||'#';add.removeAttribute('aria-disabled');}}
          if(status)status.textContent='Product details loaded.';
        }catch(err){if(token!==loadToken)return;if(status)status.textContent=String(err?.message||'Unable to load this product.');if(add){add.textContent='View product';add.href=trigger.dataset.qvUrl||'#';add.removeAttribute('aria-disabled');}if(qty)qty.disabled=true;}
      };
      document.addEventListener('click',async e=>{
        const trigger=e.target.closest?.('[data-quick-view]');
        if(trigger){const dlg=dialog();if(!dlg)return;e.preventDefault();returnTo=trigger;const fallbackName=trigger.dataset.qvName||'';setText(dlg,'[data-qv-name]',fallbackName);setText(dlg,'[data-qv-stock]',trigger.dataset.qvStock||'');renderPrice(dlg,{price:''});const img=dlg.querySelector('[data-qv-image]');if(img){img.src=trigger.dataset.qvImage||'';img.alt=fallbackName;}if(typeof dlg.showModal==='function'){if(!dlg.open)dlg.showModal();}else{dlg.setAttribute('open','');dlg.classList.add('is-open');}setTimeout(()=>dlg.querySelector('[data-quick-close]')?.focus(),0);fill(dlg,trigger);return;}
        const close=e.target.closest?.('[data-quick-close]');if(close){e.preventDefault();closeDialog(dialog());return;}
        const add=e.target.closest?.('[data-qv-add]');if(add){
          const dlg=dialog();if(!dlg||!current||!current.can_cart)return;if(addInFlight){e.preventDefault();return;}e.preventDefault();
          const {qty,status}=ensureControls(dlg);let quantity=Number.parseInt(qty?.value||'1',10);const min=Number.parseInt(qty?.min||String(current.min_qty||1),10)||1;const max=Number.parseInt(qty?.max||'0',10)||0;
          if(!Number.isFinite(quantity)||quantity<min||(max>0&&quantity>max)){if(status)status.textContent=max>0?`Choose a quantity from ${min} to ${max}.`:`Choose a quantity of at least ${min}.`;qty?.focus();return;}
          const params=window.wc_add_to_cart_params;if(!params?.wc_ajax_url){window.location.href=current.url;return;}setBusy(add,true);if(qty)qty.disabled=true;if(status)status.textContent='Adding to cart.';
          try{
            const endpoint=String(params.wc_ajax_url).replace('%%endpoint%%','add_to_cart');const body=new URLSearchParams({product_id:String(current.id),quantity:String(quantity)});
            const response=await fetch(endpoint,{method:'POST',credentials:'same-origin',cache:'no-store',headers:{'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8','X-Requested-With':'XMLHttpRequest'},body:body.toString()});const data=await response.json();
            if(!response.ok||data?.error)throw new Error('WooCommerce could not add that quantity. Please check stock and try again.');applyFragments(data?.fragments||{});if(window.jQuery)window.jQuery(document.body).trigger('added_to_cart',[data?.fragments||{},data?.cart_hash||'',window.jQuery(add)]);if(status)status.textContent=`${quantity} × ${current.name} added to cart.`;
          }catch(err){if(status)status.textContent=String(err?.message||'Unable to add this product to the cart.');}
          finally{setBusy(add,false);if(qty)qty.disabled=!current?.can_cart;}
        }
      });
      const dlg=dialog();if(dlg){dlg.addEventListener('click',e=>{if(e.target===dlg)closeDialog(dlg);});dlg.addEventListener('cancel',()=>{});dlg.addEventListener('close',()=>{addInFlight=false;current=null;returnTo?.focus?.();});}
      document.addEventListener('keydown',e=>{const dlg=dialog();if(e.key==='Escape'&&dlg&&dlg.hasAttribute('open')&&typeof dlg.close!=='function'){e.preventDefault();closeDialog(dlg);}});
    })();
    </script>
    <?php
}, 50010);