(() => {
  'use strict';

  const formatRemaining = (milliseconds) => {
    const total = Math.max(0, Math.floor(milliseconds / 1000));
    const days = Math.floor(total / 86400);
    const hours = Math.floor((total % 86400) / 3600);
    const minutes = Math.floor((total % 3600) / 60);
    const seconds = total % 60;
    if (days > 0) return `${days} ngày ${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
  };

  const clocks = Array.from(document.querySelectorAll('.lw-countdown[data-end]'));
  const updateClocks = () => {
    const now = Date.now();
    clocks.forEach((clock) => {
      const output = clock.querySelector('[data-countdown-output]');
      const end = Date.parse(clock.dataset.end || '');
      if (!output || Number.isNaN(end) || clock.dataset.state === 'ended') return;
      const remaining = end - now;
      output.textContent = remaining > 0 ? formatRemaining(remaining) : 'Phiên vừa chuyển trạng thái';
      if (remaining <= 0 && clock.dataset.expired !== 'true') {
        clock.dataset.expired = 'true';
        window.setTimeout(() => window.location.reload(), 1800);
      }
    });
  };
  if (clocks.length) {
    updateClocks();
    window.setInterval(updateClocks, 1000);
  }

  document.querySelectorAll('.lw-auction-filters').forEach((filters) => {
    const market = filters.closest('.lw-auction-market');
    const cards = market ? Array.from(market.querySelectorAll('.lw-auction-card')) : [];
    const empty = market ? market.querySelector('.lw-auction-filter-empty') : null;
    filters.addEventListener('click', (event) => {
      const button = event.target.closest('[data-auction-filter]');
      if (!button) return;
      const filter = button.dataset.auctionFilter;
      filters.querySelectorAll('[data-auction-filter]').forEach((item) => {
        const active = item === button;
        item.classList.toggle('is-active', active);
        item.setAttribute('aria-pressed', String(active));
      });
      let visible = 0;
      cards.forEach((card) => {
        const show = filter === 'all' || card.dataset.auctionState === filter;
        card.hidden = !show;
        if (show) visible += 1;
      });
      if (empty) empty.hidden = visible !== 0;
    });
  });

  document.querySelectorAll('[data-bid-form]').forEach((form) => {
    const input = form.querySelector('input[name="amount"]');
    form.querySelectorAll('[data-bid-value]').forEach((button) => {
      button.addEventListener('click', () => {
        if (!input) return;
        input.value = button.dataset.bidValue || '';
        input.focus();
      });
    });
    form.addEventListener('submit', () => {
      const submit = form.querySelector('button[type="submit"]');
      if (!submit || submit.disabled) return;
      submit.disabled = true;
      submit.textContent = 'Đang kiểm tra giá…';
    });
  });
})();
