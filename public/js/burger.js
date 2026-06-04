/* ===========================================================
   ГОСТИНЕЦЪ — burger.js
   Логика открытия/закрытия мобильного меню (drawer)
   Подключить: <script src="{{ asset('js/burger.js') }}" defer></script>
   =========================================================== */
(function () {
    'use strict';

    const btn     = document.getElementById('burger-btn');
    const drawer  = document.getElementById('mob-drawer');
    const overlay = document.getElementById('mob-overlay');
    const closeBtn = document.getElementById('mob-close');

    if (!btn || !drawer) return;

    function open() {
        drawer.classList.add('--open');
        drawer.setAttribute('aria-hidden', 'false');
        overlay.classList.add('--visible');
        btn.classList.add('--open');
        btn.setAttribute('aria-expanded', 'true');
        document.body.classList.add('--menu-open');
    }

    function close() {
        drawer.classList.remove('--open');
        drawer.setAttribute('aria-hidden', 'true');
        overlay.classList.remove('--visible');
        btn.classList.remove('--open');
        btn.setAttribute('aria-expanded', 'false');
        document.body.classList.remove('--menu-open');
    }

    btn.addEventListener('click', () => {
        drawer.classList.contains('--open') ? close() : open();
    });

    closeBtn?.addEventListener('click', close);
    overlay?.addEventListener('click', close);

    // ESC закрывает
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drawer.classList.contains('--open')) close();
    });

    // Закрываем при переходе на другую страницу (для SPA-like навигации)
    drawer.querySelectorAll('a[href]').forEach(link => {
        link.addEventListener('click', () => {
            setTimeout(close, 150);
        });
    });

    // Синхронизируем текст доставки с шапкой
    const mobDelivery = document.getElementById('mob-delivery-text');
    const headerDelivery = document.getElementById('user-city');
    if (mobDelivery && headerDelivery) {
        const sync = () => { mobDelivery.textContent = headerDelivery.textContent; };
        sync();
        new MutationObserver(sync).observe(headerDelivery, { childList: true, characterData: true, subtree: true });
    }

})();
