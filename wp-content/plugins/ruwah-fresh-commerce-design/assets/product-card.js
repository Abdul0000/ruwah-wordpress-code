(()=>{'use strict';
const quickDialog=document.querySelector('[data-quick-dialog]');
const qName=quickDialog?.querySelector('[data-qv-name]'),qImage=quickDialog?.querySelector('[data-qv-image]'),qCopy=quickDialog?.querySelector('[data-qv-copy]'),qPrice=quickDialog?.querySelector('[data-qv-price]'),qStock=quickDialog?.querySelector('[data-qv-stock]'),qAdd=quickDialog?.querySelector('[data-qv-add]'),qLink=quickDialog?.querySelector('[data-qv-link]');let qReturn=null;
document.addEventListener('click',e=>{const trigger=e.target.closest('[data-quick-view]');if(trigger&&quickDialog){qReturn=trigger;if(qName)qName.textContent=trigger.dataset.qvName||'';if(qCopy)qCopy.textContent=trigger.dataset.qvCopy||'';if(qPrice)qPrice.textContent=trigger.dataset.qvPrice||'';if(qStock)qStock.textContent=trigger.dataset.qvStock||'';if(qImage){qImage.src=trigger.dataset.qvImage||'';qImage.alt=trigger.dataset.qvName||'';}if(qLink)qLink.href=trigger.dataset.qvUrl||'#';if(qAdd){qAdd.href=trigger.dataset.qvAdd||'#';qAdd.textContent=trigger.dataset.qvCanCart==='1'?'Add to cart':'View product';}quickDialog.showModal();quickDialog.querySelector('[data-quick-close]')?.focus();}});
quickDialog?.querySelector('[data-quick-close]')?.addEventListener('click',()=>quickDialog.close());
quickDialog?.addEventListener('click',e=>{if(e.target===quickDialog)quickDialog.close();});
quickDialog?.addEventListener('close',()=>qReturn?.focus());
})();
