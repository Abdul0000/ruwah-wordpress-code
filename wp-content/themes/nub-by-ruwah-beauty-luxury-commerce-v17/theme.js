document.addEventListener('DOMContentLoaded',()=>{
 const header=document.querySelector('[data-header]');
 const menuButton=document.querySelector('[data-menu-button]');
 const menu=document.querySelector('[data-menu]');
 const updateHeader=()=>header&&header.classList.toggle('is-scrolled',window.scrollY>16);
 updateHeader(); window.addEventListener('scroll',updateHeader,{passive:true});
 if(menuButton&&menu){menuButton.addEventListener('click',()=>{const open=menuButton.getAttribute('aria-expanded')==='true';menuButton.setAttribute('aria-expanded',String(!open));menu.classList.toggle('is-open',!open);});}
 const tabs=[...document.querySelectorAll('[data-ritual-tab]')];
 const panels=[...document.querySelectorAll('[data-ritual-panel]')];
 const images=[...document.querySelectorAll('[data-ritual-image]')];
 let active=0;
 const selectRitual=index=>{active=(index+tabs.length)%tabs.length;tabs.forEach((tab,i)=>{const on=i===active;tab.classList.toggle('is-active',on);tab.setAttribute('aria-selected',String(on));});panels.forEach((panel,i)=>panel.classList.toggle('is-active',i===active));images.forEach((image,i)=>image.classList.toggle('is-active',i===active));};
 tabs.forEach((tab,index)=>tab.addEventListener('click',()=>selectRitual(index)));
 document.querySelector('[data-ritual-prev]')?.addEventListener('click',()=>selectRitual(active-1));
 document.querySelector('[data-ritual-next]')?.addEventListener('click',()=>selectRitual(active+1));
 const observer='IntersectionObserver'in window?new IntersectionObserver(entries=>entries.forEach(entry=>{if(entry.isIntersecting){entry.target.classList.add('is-visible');observer.unobserve(entry.target);}}),{threshold:.12}):null;
 document.querySelectorAll('.ruwa-reveal').forEach((el,index)=>{el.style.setProperty('--delay',`${(index%4)*80}ms`);observer?observer.observe(el):el.classList.add('is-visible');});
 document.querySelectorAll('.ruwa-faq h2,.ruwa-faq h3').forEach(title=>{title.tabIndex=0;title.setAttribute('role','button');title.setAttribute('aria-expanded','false');const toggle=()=>{const open=title.getAttribute('aria-expanded')==='true';title.setAttribute('aria-expanded',String(!open));title.parentElement?.classList.toggle('is-open',!open);};title.addEventListener('click',toggle);title.addEventListener('keydown',event=>{if(event.key==='Enter'||event.key===' '){event.preventDefault();toggle();}});});
 document.querySelectorAll('.ruwa-bundle-options button').forEach(button=>button.addEventListener('click',()=>{button.parentElement.querySelectorAll('button').forEach(item=>item.classList.remove('is-active'));button.classList.add('is-active');}));
 if(matchMedia('(min-width:1025px)').matches){const visual=document.querySelector('[data-ritual-visual]');window.addEventListener('scroll',()=>{if(visual)visual.style.transform=`translate3d(0,${Math.min(window.scrollY*.035,24)}px,0)`},{passive:true});}
});