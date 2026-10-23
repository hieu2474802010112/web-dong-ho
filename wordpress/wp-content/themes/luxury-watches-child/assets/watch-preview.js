(() => {
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  document.querySelectorAll('[data-watch-gallery]').forEach((preview) => {
    let urls;
    try { urls = JSON.parse(preview.dataset.watchGallery); } catch (_) { return; }
    if (!Array.isArray(urls) || !urls.length) return;
    const image = preview.querySelector('.lw-watch-preview__alternate');
    const link = preview.closest('a');
    if (!image || !link) return;
    let timer = null;
    let index = 0;
    let active = false;
    const show = () => {
      const src = urls[index++ % urls.length];
      image.classList.remove('is-visible');
      image.onload = () => { if (active) image.classList.add('is-visible'); };
      image.onerror = () => image.classList.remove('is-visible');
      image.src = src;
      if (image.complete && image.naturalWidth) image.classList.add('is-visible');
    };
    const start = () => {
      if (active) return;
      active = true;
      show();
      if (!reduced.matches && urls.length > 1) timer = window.setInterval(show, 1800);
    };
    const stop = () => {
      active = false;
      window.clearInterval(timer);
      timer = null;
      image.classList.remove('is-visible');
    };
    preview.addEventListener('mouseenter', start);
    preview.addEventListener('mouseleave', stop);
    link.addEventListener('focus', start);
    link.addEventListener('blur', stop);
  });
})();
