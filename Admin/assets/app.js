(function () {
  const onReady = () => {
    document.body.classList.add('is-loaded');

    const revealEls = document.querySelectorAll('.card, .table-card, .graph-container, .report-card, .form-card, .detail-group');
    revealEls.forEach((el) => el.classList.add('reveal'));

    if (typeof IntersectionObserver === 'undefined') {
      revealEls.forEach((el) => el.classList.add('is-visible'));
    } else {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            observer.unobserve(entry.target);
          }
        });
      }, { threshold: 0.2 });

      revealEls.forEach((el) => observer.observe(el));
    }

    const searchInputs = document.querySelectorAll('input[data-table-search]');
    searchInputs.forEach((input) => {
      const tableId = input.getAttribute('data-table-search');
      const table = document.getElementById(tableId);
      if (!table) return;
      input.addEventListener('input', () => {
        const filter = input.value.toLowerCase();
        table.querySelectorAll('tbody tr').forEach((row) => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(filter) ? '' : 'none';
        });
      });
    });

    const countEls = document.querySelectorAll('[data-count]');
    countEls.forEach((el) => {
      const target = parseInt(el.getAttribute('data-count'), 10);
      if (Number.isNaN(target)) return;
      let current = 0;
      const step = Math.max(1, Math.floor(target / 30));
      const tick = () => {
        current = Math.min(target, current + step);
        el.textContent = current.toString();
        if (current < target) requestAnimationFrame(tick);
      };
      tick();
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', onReady);
  } else {
    onReady();
  }
})();
