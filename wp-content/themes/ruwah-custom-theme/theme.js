(()=>{
  const q=(selector,context=document)=>context.querySelector(selector);
  const qa=(selector,context=document)=>[...context.querySelectorAll(selector)];
  const body=document.body;
  const overlay=q('[data-overlay]');
  const drawer=q('[data-cart-drawer]');
  const search=q('[data-search]');
  const menu=q('[data-menu]');

  const isSearchOpen=()=>Boolean(search&&search.getAttribute('aria-hidden')==='false');
  const isCartOpen=()=>Boolean(drawer&&drawer.classList.contains('is-open'));

  const sync=()=>{
    const open=isCartOpen()||isSearchOpen();
    if(overlay){
      overlay.hidden=!open;
      overlay.style.display=open?'block':'none';
    }
    body.style.overflow=open?'hidden':'';
  };

  const openSearch=()=>{
    if(!search)return;
    search.hidden=false;
    search.style.display='grid';
    search.setAttribute('aria-hidden','false');
    sync();
    window.setTimeout(()=>q('input[type="search"]',search)?.focus(),50);
  };

  const closeSearch=()=>{
    if(!search)return;
    search.hidden=true;
    search.style.display='none';
    search.setAttribute('aria-hidden','true');
    sync();
  };

  const closeCart=()=>{
    drawer?.classList.remove('is-open');
    drawer?.setAttribute('aria-hidden','true');
    sync();
  };

  qa('[data-cart-open]').forEach(button=>button.addEventListener('click',()=>{
    closeSearch();
    drawer?.classList.add('is-open');
    drawer?.setAttribute('aria-hidden','false');
    sync();
  }));

  q('[data-cart-close]')?.addEventListener('click',closeCart);
  qa('[data-search-open]').forEach(button=>button.addEventListener('click',openSearch));
  q('[data-search-close]')?.addEventListener('click',closeSearch);

  search?.addEventListener('click',event=>{
    if(event.target===search)closeSearch();
  });

  overlay?.addEventListener('click',()=>{
    closeCart();
    closeSearch();
  });

  q('[data-menu-toggle]')?.addEventListener('click',()=>menu?.classList.toggle('is-open'));

  document.addEventListener('keydown',event=>{
    if(event.key==='Escape'){
      closeCart();
      closeSearch();
    }
  });

  if(search){
    search.hidden=true;
    search.style.display='none';
    search.setAttribute('aria-hidden','true');
  }
  sync();
})();
