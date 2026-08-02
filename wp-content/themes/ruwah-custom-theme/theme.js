(()=>{
  const currentScript=document.currentScript;
  if(!document.getElementById('ruwah-global-design-system')){
    const design=document.createElement('link');
    design.id='ruwah-global-design-system';
    design.rel='stylesheet';
    design.href=new URL('design-system.css?ver=20260802-1',currentScript?.src||window.location.href).href;
    document.head.appendChild(design);
  }

  const q=(selector,context=document)=>context.querySelector(selector);
  const qa=(selector,context=document)=>[...context.querySelectorAll(selector)];
  const body=document.body;
  const overlay=q('[data-overlay]');
  const drawer=q('[data-cart-drawer]');
  const search=q('[data-search]');
  const menu=q('[data-menu]');

  if(body.classList.contains('home')){
    const heroImage=q('.rb-hero-media>img');
    if(heroImage){
      const originalSource=heroImage.currentSrc||heroImage.src;
      const pngSource=new URL('assets/hero-product.php?v=png-20260802-1',currentScript?.src||window.location.href).href;
      heroImage.addEventListener('error',()=>{
        if(heroImage.src!==originalSource){heroImage.src=originalSource;}
      },{once:true});
      heroImage.src=pngSource;
      heroImage.removeAttribute('srcset');
      heroImage.removeAttribute('sizes');
      heroImage.alt='Mineral Shield Sunscreen SPF 50+';
      heroImage.loading='eager';
      heroImage.decoding='async';
    }

    const heroStyle=document.createElement('style');
    heroStyle.id='ruwah-hero-type-refinement';
    heroStyle.textContent=`
      .home .rb-hero-copy{padding-top:52px!important;padding-bottom:52px!important}
      .home .rb-hero-copy .rb-kicker{margin-bottom:16px!important;font-size:12px!important;letter-spacing:.17em!important}
      .home .rb-hero h1{max-width:640px!important;margin:0 0 24px!important;font-size:clamp(50px,4.75vw,70px)!important;line-height:.94!important;letter-spacing:-.025em!important;text-wrap:balance!important}
      .home .rb-hero p{max-width:560px!important;margin:0!important;color:#625c66!important;font-size:18px!important;line-height:1.55!important;letter-spacing:-.01em!important;text-wrap:pretty!important}
      .home .rb-hero .rb-actions{margin-top:28px!important}
      @media(max-width:1180px){.home .rb-hero h1{max-width:540px!important;font-size:clamp(48px,5.25vw,64px)!important}.home .rb-hero p{max-width:500px!important;font-size:17px!important}}
      @media(max-width:900px){.home .rb-hero h1{max-width:450px!important;font-size:52px!important;line-height:.96!important}.home .rb-hero p{max-width:430px!important}}
      @media(max-width:760px){.home .rb-hero-copy{padding-top:36px!important;padding-bottom:30px!important}.home .rb-hero-copy .rb-kicker{margin-bottom:12px!important;font-size:10px!important}.home .rb-hero h1{max-width:100%!important;margin-bottom:18px!important;font-size:clamp(40px,11.5vw,52px)!important;line-height:.98!important;letter-spacing:-.02em!important}.home .rb-hero p{max-width:100%!important;font-size:15.5px!important;line-height:1.5!important}.home .rb-hero .rb-actions{margin-top:22px!important}}
    `;
    document.head.appendChild(heroStyle);

    const categoryStyle=document.createElement('style');
    categoryStyle.id='ruwah-category-slider-refinement';
    categoryStyle.textContent=`
      .home .rb-category-slider-section{padding:44px 0 50px!important;background:#fff!important;border-top:1px solid #e8e0ed!important;border-bottom:1px solid #e8e0ed!important}
      .home .rb-category-slider{padding:0 66px!important}
      .home .rb-category-slider .rb-category-row{gap:24px!important;align-items:start!important}
      .home .rb-category-slider .rb-category-card{flex:0 0 calc((100% - 96px)/5)!important;display:block!important;min-width:0!important;padding:0 4px 8px!important;text-align:center!important}
      .home .rb-category-slider .rb-category-card span{position:relative!important;width:100%!important;aspect-ratio:1.34/1!important;display:flex!important;align-items:center!important;justify-content:center!important;padding:12px 12px 0!important;border:1px solid #e8e0ed!important;border-radius:26px 26px 54% 54%!important;background:linear-gradient(180deg,#fff 0 22%,#f6f1fb 22% 100%)!important;box-shadow:0 8px 24px rgba(42,22,56,.055)!important;overflow:hidden!important;transition:transform .28s ease,box-shadow .28s ease!important}
      .home .rb-category-slider .rb-category-card img{width:88%!important;height:94%!important;object-fit:contain!important;object-position:center bottom!important;transform:none!important;filter:saturate(.96) contrast(1.02)!important}
      .home .rb-category-slider .rb-category-card b{display:block!important;margin-top:13px!important;color:#151218!important;font-family:Inter,Arial,sans-serif!important;font-size:15px!important;font-weight:650!important;line-height:1.25!important;letter-spacing:-.015em!important}
      .home .rb-category-slider .rb-category-card:hover span{transform:translateY(-4px)!important;box-shadow:0 14px 34px rgba(42,22,56,.10)!important}
      .home .rb-category-arrow{top:42%!important;width:42px!important;height:42px!important;border:1px solid #e8e0ed!important;background:rgba(255,255,255,.96)!important;color:#9638d5!important;font-size:25px!important;font-weight:400!important;box-shadow:0 8px 22px rgba(42,22,56,.09)!important;backdrop-filter:blur(8px)!important}
      .home .rb-category-arrow:hover{background:#9638d5!important;color:#fff!important;border-color:#9638d5!important;transform:translateY(-50%) scale(1.04)!important}
      @media(max-width:980px){.home .rb-category-slider{padding:0 52px!important}.home .rb-category-slider .rb-category-row{gap:18px!important}.home .rb-category-slider .rb-category-card{flex-basis:calc((100% - 36px)/3)!important}.home .rb-category-slider .rb-category-card span{border-radius:22px 22px 50% 50%!important}}
      @media(max-width:560px){.home .rb-category-slider-section{padding:30px 0 34px!important}.home .rb-category-slider{padding:0 36px!important}.home .rb-category-slider .rb-category-row{gap:12px!important}.home .rb-category-slider .rb-category-card{flex-basis:calc((100% - 12px)/2)!important;padding:0 2px 5px!important}.home .rb-category-slider .rb-category-card span{padding:8px 8px 0!important;border-radius:16px 16px 48% 48%!important}.home .rb-category-slider .rb-category-card b{margin-top:9px!important;font-size:12px!important}.home .rb-category-arrow{width:32px!important;height:32px!important;font-size:20px!important}}
    `;
    document.head.appendChild(categoryStyle);
  }

  const isSearchOpen=()=>Boolean(search&&search.getAttribute('aria-hidden')==='false');
  const isCartOpen=()=>Boolean(drawer&&drawer.classList.contains('is-open'));
  const sync=()=>{const open=isCartOpen()||isSearchOpen();if(overlay){overlay.hidden=!open;overlay.style.display=open?'block':'none'}body.style.overflow=open?'hidden':''};
  const openSearch=()=>{if(!search)return;search.hidden=false;search.style.display='grid';search.setAttribute('aria-hidden','false');sync();window.setTimeout(()=>q('input[type="search"]',search)?.focus(),50)};
  const closeSearch=()=>{if(!search)return;search.hidden=true;search.style.display='none';search.setAttribute('aria-hidden','true');sync()};
  const closeCart=()=>{drawer?.classList.remove('is-open');drawer?.setAttribute('aria-hidden','true');sync()};
  qa('[data-cart-open]').forEach(button=>button.addEventListener('click',()=>{closeSearch();drawer?.classList.add('is-open');drawer?.setAttribute('aria-hidden','false');sync()}));
  q('[data-cart-close]')?.addEventListener('click',closeCart);
  qa('[data-search-open]').forEach(button=>button.addEventListener('click',openSearch));
  q('[data-search-close]')?.addEventListener('click',closeSearch);
  search?.addEventListener('click',event=>{if(event.target===search)closeSearch()});
  overlay?.addEventListener('click',()=>{closeCart();closeSearch()});
  q('[data-menu-toggle]')?.addEventListener('click',()=>menu?.classList.toggle('is-open'));
  document.addEventListener('keydown',event=>{if(event.key==='Escape'){closeCart();closeSearch()}});
  if(search){search.hidden=true;search.style.display='none';search.setAttribute('aria-hidden','true')}
  sync();
})();