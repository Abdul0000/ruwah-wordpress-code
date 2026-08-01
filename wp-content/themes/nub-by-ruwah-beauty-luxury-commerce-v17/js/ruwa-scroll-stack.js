document.addEventListener('DOMContentLoaded', function () {
  if (typeof window.gsap === 'undefined' || typeof window.ScrollTrigger === 'undefined') return;

  const gsap = window.gsap;
  const ScrollTrigger = window.ScrollTrigger;
  gsap.registerPlugin(ScrollTrigger);

  const stackSection = document.querySelector('.ritual-claims-stack');
  if (!stackSection) return;

  const cards = gsap.utils.toArray('.ritual-claims-stack .claim-card');
  if (!cards.length) return;

  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReducedMotion) {
    cards.forEach(function (card) {
      gsap.set(card, { y: 0, opacity: 1, scale: 1, clearProps: 'willChange' });
    });
    return;
  }

  cards.forEach(function (card) {
    gsap.fromTo(card, { y: 80, opacity: 0.4, scale: 0.96 }, {
      y: 0,
      opacity: 1,
      scale: 1,
      ease: 'power2.out',
      scrollTrigger: {
        trigger: card,
        start: 'top 80%',
        end: 'top 30%',
        scrub: true,
        invalidateOnRefresh: true
      }
    });
  });

  const refresh = function () { ScrollTrigger.refresh(); };
  window.addEventListener('load', refresh, { once: true });
  if (document.fonts && document.fonts.ready) {
    document.fonts.ready.then(refresh).catch(function () {});
  }
});
