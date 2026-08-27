(()=>{'use strict';
document.documentElement.classList.add('rwb-js');
const b=document.body,q=(s,c=document)=>c.querySelector(s),qa=(s,c=document)=>[...c.querySelectorAll(s)];
let last=null;
const open=e=>{if(!e)return;last=document.activeElement;e.hidden=false;requestAnimationFrame(()=>e.classList.add('is-open'));b.classList.add('rwb-lock');setTimeout(()=>q('button,input,a[href]',e)?.focus(),30)};
const close=e=>{if(!e||e.hidden)return;e.classList.remove('is-open');setTimeout(()=>e.hidden=true,220);b.classList.remove('rwb-lock');last?.focus?.()};
const menu=q('[data-menu]'),search=q('[data-search]'),cart=q('[data-cart]'),mega=q('[data-shop-menu]');
q('[data-menu-open]')?.addEventListener('click',()=>open(menu));
q('[data-menu-close]')?.addEventListener('click',()=>close(menu));
qa('[data-search-open]').forEach(x=>x.addEventListener('click',()=>open(search)));
qa('[data-search-close]').forEach(x=>x.addEventListener('click',()=>close(search)));
qa('[data-cart-open]').forEach(x=>x.addEventListener('click',()=>open(cart)));
qa('[data-cart-close]').forEach(x=>x.addEventListener('click',()=>close(cart)));
q('[data-shop-toggle]')?.addEventListener('click',()=>{mega.hidden=!mega.hidden});
document.addEventListener('keydown',e=>{if(e.key==='Escape'){close(menu);close(search);close(cart);if(mega)mega.hidden=true}});
q('[data-header]')&&addEventListener('scroll',()=>q('[data-header]').classList.toggle('compact',scrollY>20),{passive:true});
if(window.jQuery)window.jQuery(document.body).on('added_to_cart',()=>open(cart));
const reduced=matchMedia('(prefers-reduced-motion: reduce)').matches,els=qa('[data-reveal]');
if(!reduced&&'IntersectionObserver'in window){const o=new IntersectionObserver(es=>es.forEach(e=>{if(e.isIntersecting){e.target.classList.add('visible');o.unobserve(e.target)}}),{threshold:.1});els.forEach(e=>o.observe(e))}else els.forEach(e=>e.classList.add('visible'));

const socialLinks={
  Facebook:'https://www.facebook.com/share/1BNAdjWpYW/',
  Instagram:'https://www.instagram.com/rawah.beauty?utm_source=qr&igsh=ZjMzazdrNmk1aTVt',
  TikTok:'https://vt.tiktok.com/ZSX6WqwS2/'
};
const wireFooterSocials=()=>{
  qa('.rwb-dieux-footer-socials span[aria-label]').forEach(span=>{
    if(span.closest('a'))return;
    const label=span.getAttribute('aria-label')||'';
    const href=socialLinks[label];
    if(!href)return;
    const link=document.createElement('a');
    link.href=href;
    link.target='_blank';
    link.rel='noopener noreferrer';
    link.className='rwb-dieux-social-link';
    link.setAttribute('aria-label',`${label} — Ruwah Beauty`);
    link.title=`Follow Ruwah Beauty on ${label}`;
    span.parentNode.insertBefore(link,span);
    link.appendChild(span);
  });
};

const installContactDock=()=>{
  if(q('#rwb-contact-dock'))return;
  const style=document.createElement('style');
  style.id='rwb-contact-dock-styles';
  style.textContent=`
    .rwb-contact-dock{position:fixed;right:22px;bottom:max(22px,env(safe-area-inset-bottom));z-index:125;display:flex;flex-direction:column;align-items:flex-end;gap:10px;font-family:Inter,Arial,sans-serif}
    .rwb-contact-dock-item{position:relative;width:56px;height:56px;display:grid;place-items:center;border-radius:50%;box-shadow:0 7px 24px rgba(0,0,0,.18);transition:transform .18s ease,box-shadow .18s ease;text-decoration:none!important}
    .rwb-contact-dock-item:hover,.rwb-contact-dock-item:focus-visible{transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,0,0,.24)}
    .rwb-contact-dock-item--whatsapp{background:#25D366;color:#fff!important}
    .rwb-contact-dock-item--gmail{border:1px solid rgba(17,17,17,.12);background:#fff;color:#EA4335!important}
    .rwb-contact-dock-item svg{width:28px;height:28px;display:block}
    .rwb-contact-dock-item--gmail svg{width:27px;height:27px}
    .rwb-contact-dock-label{position:absolute;right:66px;top:50%;min-width:max-content;padding:7px 10px;border-radius:4px;background:#111;color:#fff;font-size:11px;font-weight:600;line-height:1;letter-spacing:.02em;opacity:0;pointer-events:none;transform:translate(7px,-50%);transition:opacity .18s ease,transform .18s ease;box-shadow:0 5px 18px rgba(0,0,0,.15)}
    .rwb-contact-dock-item:hover .rwb-contact-dock-label,.rwb-contact-dock-item:focus-visible .rwb-contact-dock-label{opacity:1;transform:translate(0,-50%)}
    body.rwb-lock .rwb-contact-dock{opacity:0;pointer-events:none}
    .rwb-dieux-footer-socials .rwb-dieux-social-link{display:block;border-radius:50%;line-height:0;transition:transform .18s ease}
    .rwb-dieux-footer-socials .rwb-dieux-social-link:hover,.rwb-dieux-footer-socials .rwb-dieux-social-link:focus-visible{transform:translateY(-2px)}
    @media(max-width:820px){.rwb-contact-dock{right:14px;bottom:max(14px,env(safe-area-inset-bottom));gap:8px}.rwb-contact-dock-item{width:50px;height:50px}.rwb-contact-dock-item svg{width:25px;height:25px}.rwb-contact-dock-label{display:none}}
  `;
  document.head.appendChild(style);
  const dock=document.createElement('nav');
  dock.id='rwb-contact-dock';
  dock.className='rwb-contact-dock';
  dock.setAttribute('aria-label','Contact Ruwah Beauty');
  dock.innerHTML=`
    <a class="rwb-contact-dock-item rwb-contact-dock-item--whatsapp" href="https://wa.me/923713923279" target="_blank" rel="noopener noreferrer" aria-label="Chat with Ruwah Beauty on WhatsApp at +92 371 3923279" title="WhatsApp: +92 371 3923279">
      <span class="rwb-contact-dock-label">WhatsApp</span>
      <svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path fill="currentColor" d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.93 7.93 0 0 0 3.79.965h.004c4.366 0 7.926-3.558 7.93-7.93a7.9 7.9 0 0 0-2.327-5.607m-5.607 12.2a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.25a6.56 6.56 0 0 1-1.007-3.505c0-3.64 2.963-6.601 6.591-6.601a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.003 3.64-2.963 6.605-6.592 6.608m3.615-4.943c-.197-.099-1.17-.578-1.352-.643-.182-.065-.315-.099-.445.099-.133.197-.513.643-.629.775-.116.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.984-.59-.525-.985-1.174-1.101-1.372-.116-.197-.013-.304.086-.402.089-.088.197-.232.296-.348.099-.116.132-.197.197-.33.066-.132.033-.247-.016-.346-.05-.099-.445-1.074-.61-1.47-.16-.389-.323-.336-.445-.342l-.378-.007a.72.72 0 0 0-.527.247c-.182.197-.692.676-.692 1.65s.708 1.916.807 2.049c.099.132 1.394 2.128 3.377 2.984.471.203.839.324 1.125.414.472.15.902.129 1.242.078.379-.057 1.17-.478 1.335-.94.164-.462.164-.858.115-.94-.049-.083-.182-.132-.38-.231"/></svg>
    </a>
    <a class="rwb-contact-dock-item rwb-contact-dock-item--gmail" href="https://mail.google.com/mail/?view=cm&fs=1&to=rawahbeauty783@gmail.com" target="_blank" rel="noopener noreferrer" aria-label="Email Ruwah Beauty at rawahbeauty783@gmail.com using Gmail" title="Gmail: rawahbeauty783@gmail.com">
      <span class="rwb-contact-dock-label">Gmail</span>
      <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="#EA4335" d="M3.2 5.1 12 11.7l8.8-6.6A2.7 2.7 0 0 0 18.6 4H5.4c-.9 0-1.7.4-2.2 1.1Z"/><path fill="#4285F4" d="M2.7 6.8V18c0 1.1.9 2 2 2h1.7V9.6L2.7 6.8Z"/><path fill="#34A853" d="M17.6 9.6V20h1.7c1.1 0 2-.9 2-2V6.8l-3.7 2.8Z"/><path fill="#FBBC04" d="m6.4 9.6 5.6 4.2 5.6-4.2V20H6.4V9.6Z"/><path fill="#C5221F" d="M3.2 5.1 12 11.7l8.8-6.6c.3.5.5 1.1.5 1.7v.1L12 13.9 2.7 6.9v-.1c0-.6.2-1.2.5-1.7Z"/></svg>
    </a>`;
  document.body.appendChild(dock);
};

const protectContactDock=()=>{
  const dock=q('#rwb-contact-dock');
  if(!dock)return;
  if(!q('#rwb-contact-dock-overlap-fix')){
    const style=document.createElement('style');
    style.id='rwb-contact-dock-overlap-fix';
    style.textContent=`
      #rwb-contact-dock{transition:opacity .18s ease,visibility .18s ease,transform .18s ease;right:max(18px,env(safe-area-inset-right))}
      #rwb-contact-dock.rwb-contact-dock--footer{opacity:0!important;visibility:hidden!important;pointer-events:none!important;transform:translateY(10px)}
      @media(max-width:820px){#rwb-contact-dock{right:max(12px,env(safe-area-inset-right));bottom:max(12px,env(safe-area-inset-bottom))}}
      @media(max-width:520px){#rwb-contact-dock{gap:7px}#rwb-contact-dock .rwb-contact-dock-item{width:44px;height:44px}#rwb-contact-dock .rwb-contact-dock-item svg{width:23px;height:23px}}
      @media(max-width:380px){#rwb-contact-dock{right:max(9px,env(safe-area-inset-right));bottom:max(9px,env(safe-area-inset-bottom))}#rwb-contact-dock .rwb-contact-dock-item{width:42px;height:42px}}
    `;
    document.head.appendChild(style);
  }
  const footer=q('.rwb-ref-footer')||q('#rwb-reference-footer')||q('footer');
  if(!footer)return;
  const toggle=visible=>dock.classList.toggle('rwb-contact-dock--footer',visible);
  if('IntersectionObserver'in window){
    const observer=new IntersectionObserver(entries=>entries.forEach(entry=>toggle(entry.isIntersecting)),{root:null,threshold:0,rootMargin:'0px 0px 72px 0px'});
    observer.observe(footer);
  }else{
    const check=()=>{const rect=footer.getBoundingClientRect();toggle(rect.top<innerHeight&&rect.bottom>0)};
    addEventListener('scroll',check,{passive:true});addEventListener('resize',check,{passive:true});check();
  }
};

wireFooterSocials();
installContactDock();
protectContactDock();
})();