/* ===========================================================
   ГОСТИНЕЦЪ — product.js
   Страница одного товара:
   1. Табы (Сказ / О ягоде / Хранение / Доставка)
   2. Счётчик количества —/+
   3. Кнопка «В мешочек» — анимация + добавление в корзину
   =========================================================== */
(function () {
    'use strict';

    /* ============================================================
       1. ТАБЫ
    ============================================================ */
    const tabsRoot = document.getElementById('product-tabs');
    if (tabsRoot) {
        const tabs    = tabsRoot.querySelectorAll('.product-tabs__tab');
        const panels  = tabsRoot.querySelectorAll('.product-tabs__panel');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;

                // Переключаем классы
                tabs.forEach(t => {
                    t.classList.toggle('--active', t === tab);
                    t.setAttribute('aria-selected', t === tab ? 'true' : 'false');
                });

                panels.forEach(p => {
                    p.classList.toggle('--active', p.id === `tab-${target}`);
                });

                // Плавно скроллим к табам если уже прокручено ниже
                const rect = tabsRoot.getBoundingClientRect();
                if (rect.top < 0) {
                    tabsRoot.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    }

    /* ============================================================
       2. СЧЁТЧИК КОЛИЧЕСТВА
    ============================================================ */
    const qtyRoot  = document.getElementById('product-qty');
    const qtyMinus = document.getElementById('qty-minus');
    const qtyPlus  = document.getElementById('qty-plus');
    const qtyValue = document.getElementById('qty-value');

    let qty = 1;
    const MAX_QTY = 99;

    function setQty(n) {
        qty = Math.max(1, Math.min(MAX_QTY, n));
        if (qtyValue) {
            qtyValue.textContent = qty;
            qtyValue.classList.remove('--pulse');
            void qtyValue.offsetWidth;
            qtyValue.classList.add('--pulse');
            setTimeout(() => qtyValue.classList.remove('--pulse'), 200);
        }
        // Блокируем кнопку «минус» на единице
        if (qtyMinus) qtyMinus.disabled = qty <= 1;
    }

    qtyMinus?.addEventListener('click', () => setQty(qty - 1));
    qtyPlus?.addEventListener('click',  () => setQty(qty + 1));

    // Начальное состояние
    if (qtyMinus) qtyMinus.disabled = true;

    /* ============================================================
       3. ДОБАВЛЕНИЕ В КОРЗИНУ
       Обработка кнопок [data-add-to-cart] делегирована в cart-actions.js
       (подключается глобально в layouts/app.blade.php).
       Основная кнопка «В мешочек» имеет data-add-to-cart="<id>",
       а данные о qty берутся из соседнего .product-qty в общем
       контейнере с data-cart-context.
    ============================================================ */

})();
