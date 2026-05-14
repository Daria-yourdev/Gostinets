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
    ============================================================ */
    const addBtn = document.getElementById('product-add');

    if (addBtn) {
        addBtn.addEventListener('click', async () => {
            // Гость с флагом — auth.js откроет модалку (обрабатывает сам)
            if (addBtn.hasAttribute('data-requires-auth')) return;

            addBtn.classList.add('--loading');
            addBtn.disabled = true;

            try {
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                const res = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        product_id: parseInt(addBtn.dataset.productId, 10),
                        qty,
                    })
                });
                const json = await res.json().catch(() => ({}));
                if (!json.ok) throw new Error(json.message || 'add failed');

                // Успех
                addBtn.classList.remove('--loading');
                addBtn.classList.add('--added');
                addBtn.innerHTML = `
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 6 L9 17 L4 12"/>
                    </svg>
                    <span class="product-add-btn__text">В мешочке</span>`;

                // Обновляем счётчик в шапке
                const counter = document.querySelector('.action__count');
                if (counter) {
                    counter.textContent = json.count;
                    counter.classList.add('--pulse');
                    setTimeout(() => counter.classList.remove('--pulse'), 600);
                }

                // Через 2 сек возвращаем кнопку
                setTimeout(() => {
                    addBtn.classList.remove('--added');
                    addBtn.disabled = false;
                    addBtn.innerHTML = `
                        <span class="product-add-btn__text">В мешочек</span>
                        <svg class="product-add-btn__arrow" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                        <span class="product-add-btn__spinner" aria-hidden="true"></span>`;
                }, 2000);

            } catch (err) {
                console.error('[product] add-to-cart:', err);
                addBtn.classList.remove('--loading');
                addBtn.disabled = false;
            }
        });
    }

    /* ============================================================
       4. КНОПКИ + в related-карточках
          (та же логика что в catalog.js)
    ============================================================ */
    document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
        if (btn === addBtn) return; // основная кнопка — уже обработана выше

        btn.addEventListener('click', async () => {
            if (btn.hasAttribute('data-requires-auth')) return;

            btn.classList.add('--loading');
            btn.disabled = true;

            await new Promise(r => setTimeout(r, 400));

            btn.classList.remove('--loading');
            btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2.5" stroke-linecap="round" aria-hidden="true">
                <path d="M20 6 L9 17 L4 12"/>
            </svg>`;

            setTimeout(() => {
                btn.disabled = false;
                btn.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                    <path d="M12 5v14M5 12h14"/>
                </svg>`;
            }, 1400);
        });
    });

})();
