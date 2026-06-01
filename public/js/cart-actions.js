/* ===========================================================
   ГОСТИНЕЦЪ — cart-actions.js
   Глобальный обработчик кнопок [data-add-to-cart].
   =========================================================== */
(function () {
    'use strict';

    const BASE     = (document.querySelector('meta[name="base-url"]')?.content || '').replace(/\/$/, '');
    const ENDPOINT = BASE + '/cart/add';

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('[data-add-to-cart]');
        if (!btn) return;
        if (btn.disabled || btn.classList.contains('--loading')) return;

        e.preventDefault();
        const id = parseInt(btn.dataset.addToCart, 10);
        if (!id) return;

        let qty = 1;
        const qtyEl = btn.closest('[data-cart-context]')?.querySelector('[data-qty-value]')
                   || document.getElementById('qty-value');
        if (qtyEl) qty = parseInt(qtyEl.textContent, 10) || 1;

        // Имя товара (для тоста) — из ближайшей карточки
        const card = btn.closest('.jar-card, .product-info, [data-cart-context]');
        const name = card?.querySelector('.jar-card__name, .product-info__title')?.textContent?.trim()
                   || 'Банка';

        await sendAdd(btn, id, qty, name);
    });

    async function sendAdd(btn, productId, qty, name) {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrf) { console.error('[cart-actions] CSRF meta tag missing'); return; }

        const originalHTML = btn.innerHTML;
        btn.classList.add('--loading');
        btn.disabled = true;

        try {
            const res = await fetch(ENDPOINT, {
                method: 'POST',
                headers: {
                    'Content-Type':     'application/json',
                    'Accept':           'application/json',
                    'X-CSRF-TOKEN':     csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ product_id: productId, qty }),
            });
            if (!res.ok) throw new Error('HTTP ' + res.status);
            const json = await res.json().catch(() => ({}));
            if (!json.ok) throw new Error(json.message || 'cart server error');

            // Кнопка превращается в галочку
            btn.classList.remove('--loading');
            btn.classList.add('--added');
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" '
                + 'stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'
                + '<path d="M20 6 L9 17 L4 12"/></svg>';

            // Тост-подтверждение — внизу справа, с превью и ссылкой
            showCartToast(name, json.count);

            // Анимация счётчика в шапке
            updateHeaderCount(json.count);

            // Через 2 с возвращаем кнопку в исходное состояние
            setTimeout(() => {
                btn.classList.remove('--added');
                btn.disabled  = false;
                btn.innerHTML = originalHTML;
            }, 2000);

        } catch (err) {
            console.error('[cart-actions] add failed:', err);
            btn.classList.remove('--loading');
            btn.disabled  = false;
            btn.innerHTML = originalHTML;
            flashToast('Не вышло положить в мешочек. Попробуй ещё раз.', 'error');
        }
    }

    function updateHeaderCount(count) {
        const el = document.querySelector('.action__count');
        if (!el) return;
        el.textContent = count;
        el.classList.remove('--pulse');
        void el.offsetWidth;
        el.classList.add('--pulse');
        setTimeout(() => el.classList.remove('--pulse'), 600);
    }

    /* ==== Большой тост: «Банка положена» + кнопка «К мешочку» ==== */
    let bigToast = null;
    let bigToastTimer = null;

    function showCartToast(name, count) {
        if (!bigToast) {
            bigToast = document.createElement('div');
            bigToast.className = 'cart-toast-big';
            bigToast.innerHTML = `
                <div class="cart-toast-big__icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6 L9 17 L4 12"/>
                    </svg>
                </div>
                <div class="cart-toast-big__body">
                    <strong class="cart-toast-big__title"></strong>
                    <span class="cart-toast-big__sub"></span>
                </div>
                <a href="${BASE}/cart" class="cart-toast-big__link">К мешочку →</a>
            `;
            document.body.appendChild(bigToast);
        }
        bigToast.querySelector('.cart-toast-big__title').textContent = '«' + name + '» в мешочке';
        bigToast.querySelector('.cart-toast-big__sub').textContent   = 'Всего ' + count
            + ' ' + plural(count, 'банка', 'банки', 'банок');

        bigToast.classList.add('--show');

        clearTimeout(bigToastTimer);
        bigToastTimer = setTimeout(() => bigToast.classList.remove('--show'), 3500);
    }

    function plural(n, one, few, many) {
        const m10 = n % 10, m100 = n % 100;
        if (m100 >= 11 && m100 <= 19) return many;
        if (m10 === 1)                return one;
        if (m10 >= 2 && m10 <= 4)     return few;
        return many;
    }

    /* ==== Мини-тост ошибок ==== */
    let toastEl = null;
    function flashToast(text, type = 'error') {
        if (!toastEl) {
            toastEl = document.createElement('div');
            toastEl.className = 'cart-toast';
            toastEl.setAttribute('role', 'alert');
            document.body.appendChild(toastEl);
        }
        toastEl.textContent = text;
        toastEl.classList.toggle('--error', type === 'error');
        toastEl.classList.add('--show');
        clearTimeout(toastEl._timer);
        toastEl._timer = setTimeout(() => toastEl.classList.remove('--show'), 3000);
    }

})();