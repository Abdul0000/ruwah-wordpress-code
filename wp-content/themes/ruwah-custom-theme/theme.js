(()=>{
  const q=(selector,context=document)=>context.querySelector(selector);
  const qa=(selector,context=document)=>[...context.querySelectorAll(selector)];
  const body=document.body;
  const overlay=q('[data-overlay]');
  const drawer=q('[data-cart-drawer]');
  const search=q('[data-search]');
  const menu=q('[data-menu]');

  if(body.classList.contains('home')){
    const heroStyle=document.createElement('style');
    heroStyle.id='ruwah-hero-type-refinement';
    heroStyle.textContent=`
      .home .rb-hero-copy{padding-top:52px!important;padding-bottom:52px!important}
      .home .rb-hero-copy .rb-kicker{margin-bottom:16px!important;font-size:12px!important;letter-spacing:.17em!important}
      .home .rb-hero h1{max-width:640px!important;margin:0 0 24px!important;font-size:clamp(50px,4.75vw,70px)!important;line-height:.94!important;letter-spacing:-.025em!important;text-wrap:balance!important}
      .home .rb-hero p{max-width:560px!important;margin:0!important;color:#624d50!important;font-size:18px!important;line-height:1.55!important;letter-spacing:-.01em!important;text-wrap:pretty!important}
      .home .rb-hero .rb-actions{margin-top:28px!important}
      @media(max-width:1180px){
        .home .rb-hero h1{max-width:540px!important;font-size:clamp(48px,5.25vw,64px)!important}
        .home .rb-hero p{max-width:500px!important;font-size:17px!important}
      }
      @media(max-width:900px){
        .home .rb-hero h1{max-width:450px!important;font-size:52px!important;line-height:.96!important}
        .home .rb-hero p{max-width:430px!important}
      }
      @media(max-width:760px){
        .home .rb-hero-copy{padding-top:36px!important;padding-bottom:30px!important}
        .home .rb-hero-copy .rb-kicker{margin-bottom:12px!important;font-size:10px!important}
        .home .rb-hero h1{max-width:100%!important;margin-bottom:18px!important;font-size:clamp(40px,11.5vw,52px)!important;line-height:.98!important;letter-spacing:-.02em!important}
        .home .rb-hero p{max-width:100%!important;font-size:15.5px!important;line-height:1.5!important}
        .home .rb-hero .rb-actions{margin-top:22px!important}
      }
    `;
    document.head.appendChild(heroStyle);
  }

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