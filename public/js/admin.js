/* ===========================================================
   ГОСТИНЕЦЪ — admin.js
   - Мобильный бургер сайдбара
   - Подтверждение удаления (формы с data-confirm)
   =========================================================== */
(function () {
    'use strict';

    /* === Бургер мобильного сайдбара === */
    const side    = document.getElementById('admin-side');
    const openBtn = document.getElementById('admin-side-open');
    const closeBtn= document.getElementById('admin-side-close');
    const overlay = document.getElementById('admin-side-overlay');

    function openSide()  { side?.classList.add('--open'); overlay?.classList.add('--show'); }
    function closeSide() { side?.classList.remove('--open'); overlay?.classList.remove('--show'); }

    openBtn?.addEventListener('click', openSide);
    closeBtn?.addEventListener('click', closeSide);
    overlay?.addEventListener('click', closeSide);

    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeSide();
    });

    /* === Подтверждение опасных действий === */
    document.querySelectorAll('[data-confirm]').forEach(form => {
        form.addEventListener('submit', e => {
            const message = form.dataset.confirm || 'Уверен?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });

})();
