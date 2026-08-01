document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const header = document.querySelector('[data-header]');
  const menuButton = document.querySelector('[data-menu-button]');
  const menu = document.querySelector('[data-menu]');
  const overlay = document.querySelector('[data-overlay]');
  const searchDialog = document.querySelector('[data-search-dialog]');
  const cartDrawer = document.querySelector('[data-cart-drawer]');

  const updateHeader = () => header && header.classList.toggle('is-scrolled', window.scrollY > 80);
  updateHeader();
  window.addEventListener('scroll', updateHeader, { passive: true });

  if (menuButton && menu) {
    menuButton.addEventListener('click', () => {
      const open = menuButton.getAttribute('aria-expanded') === 'true';
      menuButton.setAttribute('aria-expanded', String(!open));
      menu.classList.toggle('is-open', !open);
    });
  }

  const closeLayers = () => {
    if (searchDialog) searchDialog.hidden = true;
    if (cartDrawer) {
      cartDrawer.classList.remove('is-open');
      cartDrawer.setAttribute('aria-hidden', 'true');
    }
    if (overlay) overlay.hidden = true;
    body.classList.remove('ruwa-layer-open');
  };
  const openSearch = () => {
    closeLayers();
    if (!searchDialog || !overlay) return;
    searchDialog.hidden = false;
    overlay.hidden = false;
    body.classList.add('ruwa-layer-open');
    requestAnimationFrame(() => searchDialog.querySelector('input[type="search"]')?.focus());
  };
  const openCart = () => {
    closeLayers();
    if (!cartDrawer || !overlay) return;
    cartDrawer.classList.add('is-open');
    cartDrawer.setAttribute('aria-hidden', 'false');
    overlay.hidden = false;
    body.classList.add('ruwa-layer-open');
  };
  document.querySelectorAll('[data-search-open]').forEach(el => el.addEventListener('click', openSearch));
  document.querySelectorAll('[data-search-close],[data-cart-close]').forEach(el => el.addEventListener('click', closeLayers));
  document.querySelectorAll('[data-cart-open]').forEach(el => el.addEventListener('click', openCart));
  overlay?.addEventListener('click', closeLayers);
  document.addEventListener('keydown', event => { if (event.key === 'Escape') closeLayers(); });

  const tabs = [...document.querySelectorAll('[data-ritual-tab]')];
  const panels = [...document.querySelectorAll('[data-ritual-panel]')];
  const images = [...document.querySelectorAll('[data-ritual-image]')];
  let active = 0;
  const selectRitual = index => {
    if (!tabs.length) return;
    active = (index + tabs.length) % tabs.length;
    tabs.forEach((tab, i) => {
      const on = i === active;
      tab.classList.toggle('is-active', on);
      tab.setAttribute('aria-selected', String(on));
    });
    panels.forEach((panel, i) => panel.classList.toggle('is-active', i === active));
    images.forEach((image, i) => image.classList.toggle('is-active', i === active));
  };
  tabs.forEach((tab, index) => tab.addEventListener('click', () => selectRitual(index)));
  document.querySelector('[data-ritual-prev]')?.addEventListener('click', () => selectRitual(active - 1));
  document.querySelector('[data-ritual-next]')?.addEventListener('click', () => selectRitual(active + 1));

  const observer = 'IntersectionObserver' in window
    ? new IntersectionObserver(entries => entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          observer.unobserve(entry.target);
        }
      }), { threshold: 0.12 })
    : null;
  document.querySelectorAll('.ruwa-reveal').forEach((el, index) => {
    el.style.setProperty('--delay', `${(index % 4) * 70}ms`);
    observer ? observer.observe(el) : el.classList.add('is-visible');
  });

  document.querySelectorAll('.ruwa-accordion section > button').forEach(button => {
    button.addEventListener('click', () => {
      const panel = button.nextElementSibling;
      const open = button.getAttribute('aria-expanded') === 'true';
      button.setAttribute('aria-expanded', String(!open));
      if (panel) panel.hidden = open;
    });
  });

  document.querySelectorAll('[data-bundle]').forEach(button => {
    button.addEventListener('click', () => {
      const count = Number(button.dataset.bundle || 3);
      document.querySelectorAll('[data-bundle]').forEach(item => item.classList.toggle('is-active', item === button));
      document.querySelectorAll('[data-bundle-item]').forEach(item => {
        item.hidden = Number(item.dataset.bundleItem || 0) > count;
      });
    });
  });

  if (matchMedia('(min-width:1025px)').matches) {
    const visual = document.querySelector('[data-ritual-visual]');
    window.addEventListener('scroll', () => {
      if (visual) visual.style.transform = `translate3d(0,${Math.min(window.scrollY * 0.025, 22)}px,0)`;
    }, { passive: true });
  }
});