document.addEventListener('DOMContentLoaded', () => {
  const body = document.body;
  const header = document.querySelector('[data-header]');
  const menuButton = document.querySelector('[data-menu-button]');
  const menu = document.querySelector('[data-menu]');
  const overlay = document.querySelector('[data-overlay]');
  const searchDialog = document.querySelector('[data-search-dialog]');
  const cartDrawer = document.querySelector('[data-cart-drawer]');
  let searchReturnFocus = null;
  let cartReturnFocus = null;

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

  const searchIsOpen = () => Boolean(searchDialog && !searchDialog.hidden && searchDialog.classList.contains('is-open'));
  const cartIsOpen = () => Boolean(cartDrawer && cartDrawer.classList.contains('is-open'));
  const syncBodyLock = () => {
    const anyLayerOpen = searchIsOpen() || cartIsOpen();
    body.classList.toggle('ruwa-layer-open', anyLayerOpen);
    body.classList.toggle('search-modal-open', searchIsOpen());
    body.classList.toggle('cart-drawer-open', cartIsOpen());
  };

  const closeSearch = ({ restoreFocus = true } = {}) => {
    if (!searchDialog) return;
    const target = searchReturnFocus;
    searchDialog.classList.remove('is-open');
    searchDialog.hidden = true;
    searchDialog.setAttribute('aria-hidden', 'true');
    searchReturnFocus = null;
    syncBodyLock();
    if (restoreFocus && target instanceof HTMLElement) target.focus({ preventScroll: true });
  };

  const closeCart = ({ restoreFocus = true } = {}) => {
    const target = cartReturnFocus;
    if (cartDrawer) {
      cartDrawer.classList.remove('is-open');
      cartDrawer.setAttribute('aria-hidden', 'true');
    }
    if (overlay) overlay.hidden = true;
    cartReturnFocus = null;
    syncBodyLock();
    if (restoreFocus && target instanceof HTMLElement) target.focus({ preventScroll: true });
  };

  const openSearch = event => {
    closeCart({ restoreFocus: false });
    if (!searchDialog) return;
    searchReturnFocus = event?.currentTarget instanceof HTMLElement ? event.currentTarget : document.activeElement;
    searchDialog.hidden = false;
    searchDialog.setAttribute('aria-hidden', 'false');
    requestAnimationFrame(() => {
      searchDialog.classList.add('is-open');
      syncBodyLock();
      searchDialog.querySelector('input[type="search"]')?.focus({ preventScroll: true });
    });
  };

  const openCart = event => {
    closeSearch({ restoreFocus: false });
    if (!cartDrawer || !overlay) return;
    cartReturnFocus = event?.currentTarget instanceof HTMLElement ? event.currentTarget : document.activeElement;
    cartDrawer.classList.add('is-open');
    cartDrawer.setAttribute('aria-hidden', 'false');
    overlay.hidden = false;
    syncBodyLock();
  };

  closeSearch({ restoreFocus: false });
  closeCart({ restoreFocus: false });
  document.querySelectorAll('[data-search-open]').forEach(el => el.addEventListener('click', openSearch));
  document.querySelectorAll('[data-search-close]').forEach(el => el.addEventListener('click', () => closeSearch()));
  document.querySelectorAll('[data-cart-close]').forEach(el => el.addEventListener('click', () => closeCart()));
  document.querySelectorAll('[data-cart-open]').forEach(el => el.addEventListener('click', openCart));
  overlay?.addEventListener('click', () => closeCart());
  searchDialog?.addEventListener('click', event => {
    if (event.target === searchDialog) closeSearch();
  });

  document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
      if (searchIsOpen()) closeSearch();
      else if (cartIsOpen()) closeCart();
      return;
    }
    if (event.key !== 'Tab' || !searchIsOpen() || !searchDialog) return;
    const focusable = [...searchDialog.querySelectorAll('button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled]), a[href], [tabindex]:not([tabindex="-1"])')]
      .filter(element => element instanceof HTMLElement && element.offsetParent !== null);
    if (!focusable.length) return;
    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  });

  const switcher = document.querySelector('[data-ritual-switcher]');
  const tabs = [...document.querySelectorAll('[data-ritual-tab]')];
  const panels = [...document.querySelectorAll('[data-ritual-panel]')];
  const images = [...document.querySelectorAll('[data-ritual-image]')];
  let active = 0;
  let switchTimer = 0;
  const selectRitual = index => {
    if (!tabs.length) return;
    active = (index + tabs.length) % tabs.length;
    const activeName = tabs[active]?.dataset.ritualTab || '';
    switcher?.classList.add('is-switching');
    switcher?.setAttribute('data-active-ritual', activeName);
    tabs.forEach((tab, i) => {
      const on = i === active;
      tab.classList.toggle('is-active', on);
      tab.setAttribute('aria-selected', String(on));
      tab.setAttribute('tabindex', on ? '0' : '-1');
    });
    panels.forEach((panel, i) => panel.classList.toggle('is-active', i === active));
    images.forEach((image, i) => image.classList.toggle('is-active', i === active));
    window.clearTimeout(switchTimer);
    switchTimer = window.setTimeout(() => switcher?.classList.remove('is-switching'), 460);
  };
  tabs.forEach((tab, index) => {
    tab.addEventListener('click', () => selectRitual(index));
    tab.addEventListener('keydown', event => {
      if (event.key === 'ArrowRight' || event.key === 'ArrowDown') {
        event.preventDefault();
        selectRitual(active + 1);
        tabs[(active + tabs.length) % tabs.length]?.focus();
      } else if (event.key === 'ArrowLeft' || event.key === 'ArrowUp') {
        event.preventDefault();
        selectRitual(active - 1);
        tabs[(active + tabs.length) % tabs.length]?.focus();
      }
    });
  });
  document.querySelector('[data-ritual-prev]')?.addEventListener('click', () => selectRitual(active - 1));
  document.querySelector('[data-ritual-next]')?.addEventListener('click', () => selectRitual(active + 1));
  if (tabs.length) selectRitual(0);

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

  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const heroVisual = document.querySelector('[data-ritual-visual]');
  if (!reducedMotion && switcher && heroVisual && window.matchMedia('(min-width: 1025px)').matches) {
    let ticking = false;
    const updateHeroMotion = () => {
      const rect = switcher.getBoundingClientRect();
      const travel = Math.max(1, rect.height + window.innerHeight);
      const progress = Math.max(0, Math.min(1, (window.innerHeight - rect.top) / travel));
      heroVisual.style.setProperty('--ruwa-parallax-y', `${(progress - 0.5) * 34}px`);
      heroVisual.style.setProperty('--ruwa-parallax-rotate', `${(progress - 0.5) * 5}deg`);
      heroVisual.style.setProperty('--ruwa-botanical-shift', `${(0.5 - progress) * 28}px`);
      heroVisual.style.setProperty('--ruwa-botanical-shift-two', `${(progress - 0.5) * 20}px`);
      ticking = false;
    };
    const requestHeroMotion = () => {
      if (!ticking) {
        ticking = true;
        window.requestAnimationFrame(updateHeroMotion);
      }
    };
    updateHeroMotion();
    window.addEventListener('scroll', requestHeroMotion, { passive: true });
    window.addEventListener('resize', requestHeroMotion, { passive: true });
  }
});