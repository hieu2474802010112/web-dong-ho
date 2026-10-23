(() => {
  const nav = document.querySelector('.main-navigation .primary-navigation');
  const menu = nav && nav.querySelector(':scope > ul');
  if (!menu || !window.matchMedia('(min-width: 768px)').matches) return;
  const items = [...menu.children].filter(item => item.querySelector(':scope > a'));
  if (!items.length) return;
  const pill = document.createElement('span');
  pill.className = 'lw-nav-indicator';
  pill.setAttribute('aria-hidden', 'true');
  menu.prepend(pill);
  const normalize = path => path.replace(/\/+$/, '') || '/';
  const current = normalize(location.pathname);
  const active = items.find(item => {
    const link = item.querySelector(':scope > a');
    try { const target = new URL(link.href, location.href); return target.origin === location.origin && normalize(target.pathname) === current; } catch { return false; }
  });
  function move(item, animate = true) {
    if (!item) { pill.classList.remove('is-visible'); return; }
    const link = item.querySelector(':scope > a');
    const parent = menu.getBoundingClientRect(), rect = link.getBoundingClientRect();
    if (!rect.width || !rect.height) return;
    pill.classList.toggle('no-transition', !animate);
    pill.style.width = `${rect.width}px`;
    pill.style.height = `${rect.height}px`;
    pill.style.transform = `translate(${rect.left - parent.left}px, ${rect.top - parent.top}px)`;
    pill.classList.add('is-visible');
  }
  items.forEach(item => {
    item.addEventListener('pointerenter', () => move(item));
    item.addEventListener('focusin', () => move(item));
  });
  menu.addEventListener('pointerleave', () => move(active));
  menu.addEventListener('focusout', event => { if (!menu.contains(event.relatedTarget)) move(active); });
  window.addEventListener('resize', () => move(active, false), {passive:true});
  move(active, false);
})();
