(() => {
  const navigation = performance.getEntriesByType('navigation')[0];
  if (navigation && navigation.type === 'reload') {
    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    window.addEventListener('pageshow', () => {
      window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
    }, { once: true });
    window.addEventListener('pagehide', () => {
      if ('scrollRestoration' in history) history.scrollRestoration = 'auto';
    }, { once: true });
  }
  const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
  if (reducedMotion.matches || !('IntersectionObserver' in window) || !Element.prototype.animate) return;

  // Keep the page readable if JavaScript or animation is unavailable.
  const targets = document.querySelectorAll('.lw-section__head, .lw-auction-feature__visual, .lw-auction-market__header, .lw-footer-cta');
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(({target, isIntersecting}) => {
      if (!isIntersecting) return;
      observer.unobserve(target);
      if (reducedMotion.matches) return;
      if (target.classList.contains('lw-auction-feature__visual')) {
        target.animate([{clipPath:'inset(0 4% 0 0)'}, {clipPath:'inset(0 0 0 0)'}], {duration:750,easing:'cubic-bezier(.16,1,.3,1)'});
      } else {
        target.animate([{opacity:.82,transform:'translateY(10px)'}, {opacity:1,transform:'translateY(0)'}], {duration:580,easing:'cubic-bezier(.16,1,.3,1)'});
      }
    });
  }, {threshold:0.12, rootMargin:'0px 0px -24px 0px'});
  targets.forEach((target) => {
    // Never animate content the visitor already sees on initial load.
    if (target.getBoundingClientRect().top > window.innerHeight) observer.observe(target);
  });
  reducedMotion.addEventListener?.('change', (event) => { if (event.matches) observer.disconnect(); });
})();
