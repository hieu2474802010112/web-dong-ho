(() => {
  const reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
  document.querySelectorAll('[data-watch-gallery]').forEach((preview) => {
    let urls;
    try { urls = JSON.parse(preview.dataset.watchGallery); } catch (_) { return; }
    if (!Array.isArray(urls) || !urls.length) return;
    urls = [...new Set(urls.filter((url) => typeof url === 'string' && url.trim()))];
    if (!urls.length) return;
    const image = preview.querySelector('.lw-watch-preview__alternate');
    const hint = preview.querySelector('.lw-watch-preview__hint');
    const link = preview.closest('a');
    if (!image || !link) return;
    let timer = null;
    let index = 0;
    let active = false;
    let loadToken = 0;
    urls.forEach((url) => { const preload = new Image(); preload.src = url; });
    const show = () => {
      const current = index++ % urls.length;
      const src = urls[current];
      const token = ++loadToken;
      image.classList.remove('is-visible');
      image.onload = () => {
        if (active && token === loadToken) image.classList.add('is-visible');
      };
      image.onerror = () => image.classList.remove('is-visible');
      image.src = src;
      if (image.complete && image.naturalWidth) image.classList.add('is-visible');
      if (hint) hint.textContent = `Góc ${current + 1}/${urls.length}`;
    };
    const start = () => {
      if (active) return;
      active = true;
      show();
      if (!reduced.matches && urls.length > 1) timer = window.setInterval(show, 1500);
    };
    const stop = () => {
      active = false;
      window.clearInterval(timer);
      timer = null;
      image.classList.remove('is-visible');
      index = 0;
      if (hint) hint.textContent = 'Nhiều góc nhìn';
    };
    preview.addEventListener('mouseenter', start);
    preview.addEventListener('mouseleave', stop);
    link.addEventListener('focus', start);
    link.addEventListener('blur', stop);
  });
})();
