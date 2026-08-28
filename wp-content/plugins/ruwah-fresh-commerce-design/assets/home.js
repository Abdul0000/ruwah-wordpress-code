(()=>{'use strict';
const body=document.body;
const menu=document.querySelector('[data-premium-menu]');
const menuOpen=document.querySelector('[data-premium-menu-open]');
const menuClose=document.querySelector('[data-premium-menu-close]');
const menuBackdrop=document.querySelector('[data-premium-menu-backdrop]');
let menuReturnFocus=null;
const focusables=root=>[...root.querySelectorAll('a[href],button:not([disabled]),input:not([disabled]),select:not([disabled]),textarea:not([disabled]),[tabindex]:not([tabindex="-1"])')].filter(el=>!el.hidden&&el.offsetParent!==null);
const closeMenu=()=>{if(!menu||menu.hidden)return;menu.hidden=true;if(menuBackdrop)menuBackdrop.hidden=true;body.classList.remove('rhp-menu-open');menuOpen?.setAttribute('aria-expanded','false');menuReturnFocus?.focus();};
const openMenu=()=>{if(!menu)return;menuReturnFocus=document.activeElement;menu.hidden=false;if(menuBackdrop)menuBackdrop.hidden=false;body.classList.add('rhp-menu-open');menuOpen?.setAttribute('aria-expanded','true');requestAnimationFrame(()=>focusables(menu)[0]?.focus());};
menuOpen?.addEventListener('click',openMenu);menuClose?.addEventListener('click',closeMenu);menuBackdrop?.addEventListener('click',closeMenu);
menu?.addEventListener('click',e=>{if(e.target.closest('a'))closeMenu();});
menu?.addEventListener('keydown',e=>{if(e.key==='Escape'){e.preventDefault();closeMenu();return;}if(e.key!=='Tab')return;const items=focusables(menu);if(!items.length)return;const first=items[0],last=items[items.length-1];if(e.shiftKey&&document.activeElement===first){e.preventDefault();last.focus();}else if(!e.shiftKey&&document.activeElement===last){e.preventDefault();first.focus();}});
document.addEventListener('keydown',e=>{if(e.key==='Escape'&&menu&&!menu.hidden)closeMenu();});

const quickDialog=document.querySelector('[data-quick-dialog]');const qName=quickDialog?.querySelector('[data-qv-name]'),qImage=quickDialog?.querySelector('[data-qv-image]'),qCopy=quickDialog?.querySelector('[data-qv-copy]'),qPrice=quickDialog?.querySelector('[data-qv-price]'),qStock=quickDialog?.querySelector('[data-qv-stock]'),qAdd=quickDialog?.querySelector('[data-qv-add]'),qLink=quickDialog?.querySelector('[data-qv-link]');let qReturn=null;
document.addEventListener('click',e=>{const trigger=e.target.closest('[data-quick-view]');if(trigger&&quickDialog){qReturn=trigger;if(qName)qName.textContent=trigger.dataset.qvName||'';if(qCopy)qCopy.textContent=trigger.dataset.qvCopy||'';if(qPrice)qPrice.textContent=trigger.dataset.qvPrice||'';if(qStock)qStock.textContent=trigger.dataset.qvStock||'';if(qImage){qImage.src=trigger.dataset.qvImage||'';qImage.alt=trigger.dataset.qvName||'';}if(qLink)qLink.href=trigger.dataset.qvUrl||'#';if(qAdd){qAdd.href=trigger.dataset.qvAdd||'#';qAdd.textContent=trigger.dataset.qvCanCart==='1'?'Add to cart':'View product';}quickDialog.showModal();quickDialog.querySelector('[data-quick-close]')?.focus();}});
quickDialog?.querySelector('[data-quick-close]')?.addEventListener('click',()=>quickDialog.close());quickDialog?.addEventListener('click',e=>{if(e.target===quickDialog)quickDialog.close();});quickDialog?.addEventListener('close',()=>qReturn?.focus());

const searchLayer=document.querySelector('[data-search]');const searchOpen=document.querySelector('[data-search-open]');const closeSearch=()=>{if(searchLayer){searchLayer.hidden=true;body.classList.remove('rhp-layer-open');searchOpen?.focus();}};searchOpen?.addEventListener('click',()=>{if(!searchLayer)return;searchLayer.hidden=false;body.classList.add('rhp-layer-open');requestAnimationFrame(()=>searchLayer.querySelector('input[type="search"]')?.focus());});searchLayer?.querySelectorAll('[data-search-close]').forEach(el=>el.addEventListener('click',closeSearch));

const cartLayer=document.querySelector('[data-cart]');const cartOpen=document.querySelector('[data-cart-open]');const closeCart=()=>{if(cartLayer){cartLayer.hidden=true;body.classList.remove('rhp-layer-open');cartOpen?.focus();}};cartOpen?.addEventListener('click',()=>{if(!cartLayer)return;cartLayer.hidden=false;body.classList.add('rhp-layer-open');requestAnimationFrame(()=>cartLayer.querySelector('button,a,input')?.focus());});cartLayer?.querySelectorAll('[data-cart-close]').forEach(el=>el.addEventListener('click',closeCart));
document.addEventListener('keydown',e=>{if(e.key==='Escape'){if(searchLayer&&!searchLayer.hidden)closeSearch();if(cartLayer&&!cartLayer.hidden)closeCart();}});
})();
