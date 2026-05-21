/* ===========================================================
   ГОСТИНЕЦЪ — cart-actions.js
   Единый глобальный обработчик добавления в корзину.

   Работает на всех страницах через event delegation:
     document.addEventListener('click', ...)
   Так что не зависит ни от какой структуры страницы и не падает,
   если каких-то элементов нет (форм фильтров, основной кнопки и т.п.).

   Обрабатывает любые кнопки с атрибутом [data-add-to-cart="<id>"].
   =========================================================== */
(function () {
    'use strict';

    // Базовый URL берём из мета-тега — работает и на localhost:8000,
    // и на localhost/gostinets/, и на любом другом домене
    const BASE     = (document.querySelector('meta[name="base-url"]')?.content || '').replace(/\/$/, '');
    const ENDPOINT = BASE + '/cart/add';

    /* ============================================================
       Глобальный делегированный клик
    ============================================================ */
    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-add-to-cart]');
        if (!btn) return;

        // Если уже в процессе — игнорируем
        if (btn.disabled || btn.classList.contains('--loading')) return;

        e.preventDefault();
        const id = parseInt(btn.dataset.addToCart, 10);
        if (!id) return;

        // Берём qty из соседнего счётчика, если есть. Иначе — 1.
        let qty = 1;
        const qtyEl = btn.closest('[data-cart-context]')?.querySelector('[data-qty-value]')
                    || document.getElementById('qty-value');
        if (qtyEl) {
            qty = parseInt(qtyEl.textContent, 10) || 1;
        }

        await sendAdd(btn, id, qty);
    });

    /* ============================================================
       Отправка запроса + UX
    ============================================================ */
    async function sendAdd(btn, productId, qty) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrf) {
            console.error('[cart-actions] CSRF meta tag missing in <head>');
            return;
        }

        // Сохраняем оригинальное содержимое кнопки — чтобы вернуть потом
        const originalHTML = btn.innerHTML;

        btn.classList.add('--loading');
        btn.disabled = true;

        try {
            const res = await fetch(ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ product_id: productId, qty }),
            });

            // Сразу ловим HTTP-ошибки чтобы было видно в консоли
            if (!res.ok) {
                const txt = await res.text().catch(() => '');
                console.error('[cart-actions] HTTP', res.status, txt.slice(0, 200));
                throw new Error('HTTP ' + res.status);
            }

            const json = await res.json().catch(() => ({}));
            if (!json.ok) throw new Error(json.message || 'cart server error');

            // ВИЗУАЛ: галочка вместо «+»
            btn.classList.remove('--loading');
            btn.classList.add('--added');
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" '
                + 'stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" '
                + 'aria-hidden="true"><path d="M20 6 L9 17 L4 12"/></svg>';

            // Обновляем счётчик в шапке
            updateHeaderCount(json.count);

            // Через 1.4 с возвращаем исходный вид кнопки
            setTimeout(() => {
                btn.classList.remove('--added');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }, 1400);

        } catch (err) {
            console.error('[cart-actions] add failed:', err);
            // Восстанавливаем кнопку
            btn.classList.remove('--loading');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
            // Показываем флэш-тост, если на странице есть контейнер для него
            flashToast('Не вышло положить в мешочек. Попробуй ещё раз.');
        }
    }

    /* ============================================================
       Обновление счётчика в шапке
    ============================================================ */
    function updateHeaderCount(count) {
        const el = document.querySelector('.action__count');
        if (!el) return;
        el.textContent = count;
        el.classList.remove('--pulse');
        void el.offsetWidth; // restart animation
        el.classList.add('--pulse');
        setTimeout(() => el.classList.remove('--pulse'), 600);
    }

    /* ============================================================
       Мини-тост для ошибок (без зависимости от страницы)
    ============================================================ */
    let toastEl = null;
    function flashToast(text) {
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.className = 'cart-toast';
            toastEl.setAttribute('role', 'alert');
            document.body.appendChild(toastEl);
        }
        toastEl.textContent = text;
        toastEl.classList.add('--show');
        clearTimeout(toastEl._timer);
        toastEl._timer = setTimeout(() => toastEl.classList.remove('--show'), 3000);
    }

})();