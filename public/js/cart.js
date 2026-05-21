/* ===========================================================
   ГОСТИНЕЦЪ — cart.js
   Управление корзиной:
   - Изменение количества (+/−)
   - Удаление позиции с анимацией
   - Обновление сводки и счётчика в шапке
   =========================================================== */
(function () {
    'use strict';

    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const BASE = (document.querySelector('meta[name="base-url"]')?.content || '').replace(/\/$/, '');

    /* ============================================================
       Утилиты для AJAX
    ============================================================ */
    async function api(url, method = 'POST', body = null) {
        const res = await fetch(url, {
            method,
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: body ? JSON.stringify(body) : null,
        });
        return res.json().catch(() => ({}));
    }

    /* ============================================================
       Форматирование числа: 1234 → "1 234 ₽"
    ============================================================ */
    function fmtPrice(n) {
        return new Intl.NumberFormat('ru-RU').format(n) + ' ₽';
    }

    /* ============================================================
       Обновление UI после изменения корзины
    ============================================================ */
    function updateHeaderCounter(count) {
        const el = document.querySelector('.action__count');
        if (!el) return;
        el.textContent = count;
        el.classList.remove('--pulse');
        void el.offsetWidth;
        el.classList.add('--pulse');
        setTimeout(() => el.classList.remove('--pulse'), 600);
    }

    function updateSubtotal(subtotal) {
        document.querySelectorAll('[data-summary-subtotal]').forEach(el => {
            el.textContent = fmtPrice(subtotal);
        });
        document.querySelectorAll('[data-summary-total]').forEach(el => {
            el.textContent = fmtPrice(subtotal);
        });
    }

    function updateItemSubtotal(itemEl, qty, pricePerUnit) {
        const subtotalEl = itemEl.querySelector('[data-cart-subtotal]');
        if (!subtotalEl) return;
        const sum = qty * pricePerUnit;
        const each = subtotalEl.querySelector('.cart-item__price-each');
        const total = subtotalEl.querySelector('.cart-item__price-sum');
        if (each) each.textContent = fmtPrice(pricePerUnit) + ' × ' + qty;
        if (total) total.textContent = fmtPrice(sum);
    }

    function pulseQty(itemEl) {
        const val = itemEl.querySelector('[data-qty-value]');
        if (!val) return;
        val.classList.remove('--pulse');
        void val.offsetWidth;
        val.classList.add('--pulse');
        setTimeout(() => val.classList.remove('--pulse'), 220);
    }

    /* ============================================================
       Извлечение цены за единицу из DOM
       (price_each = price_sum / qty)
    ============================================================ */
    function extractPricePerUnit(itemEl) {
        const eachText = itemEl.querySelector('.cart-item__price-each')?.textContent || '';
        // "320 ₽ × 2" → 320
        const m = eachText.match(/([\d\s]+)\s*₽/);
        return m ? parseInt(m[1].replace(/\s/g, ''), 10) : 0;
    }

    /* ============================================================
       Изменение количества
    ============================================================ */
    async function changeQty(itemEl, delta) {
        const productId = itemEl.dataset.productId;
        const valEl = itemEl.querySelector('[data-qty-value]');
        const currentQty = parseInt(valEl.textContent, 10) || 1;
        const newQty = Math.max(0, currentQty + delta);

        // Оптимистичное обновление
        if (newQty === 0) {
            return removeItem(itemEl);
        }

        valEl.textContent = newQty;
        pulseQty(itemEl);

        const pricePerUnit = extractPricePerUnit(itemEl);
        updateItemSubtotal(itemEl, newQty, pricePerUnit);

        // AJAX
        try {
            const json = await api(`${BASE}/cart/${productId}`, 'PATCH', { qty: newQty });
            if (!json.ok) throw new Error('server');

            // Если сервер вернул меньше (например, не хватило на складе) — корректируем
            if (json.qty !== newQty) {
                valEl.textContent = json.qty;
                updateItemSubtotal(itemEl, json.qty, pricePerUnit);
            }

            updateHeaderCounter(json.count);
            updateSubtotal(json.subtotal);

        } catch (err) {
            console.error('[cart] update failed:', err);
            // Откатываем
            valEl.textContent = currentQty;
            updateItemSubtotal(itemEl, currentQty, pricePerUnit);
        }
    }

    /* ============================================================
       Удаление позиции
    ============================================================ */
    async function removeItem(itemEl) {
        const productId = itemEl.dataset.productId;

        itemEl.classList.add('--removing');

        try {
            const json = await api(`${BASE}/cart/${productId}`, 'DELETE');

            // Анимация удаления → реальное удаление из DOM
            setTimeout(() => {
                itemEl.remove();
                updateHeaderCounter(json.count);
                updateSubtotal(json.subtotal);

                // Если корзина опустела — перезагружаем чтобы показать пустое состояние
                if (json.empty) {
                    window.location.reload();
                }
            }, 280);

        } catch (err) {
            console.error('[cart] remove failed:', err);
            itemEl.classList.remove('--removing');
        }
    }

    /* ============================================================
       ОБРАБОТЧИКИ
    ============================================================ */
    document.addEventListener('click', (e) => {
        const action = e.target.closest('[data-cart-action]');
        if (!action) return;

        const item = action.closest('.cart-item');
        if (!item) return;

        const type = action.dataset.cartAction;
        if (type === 'plus')          changeQty(item, +1);
        if (type === 'minus')         changeQty(item, -1);
        if (type === 'remove')        removeItem(item);
        if (type === 'remove-custom') removeCustomItem(item);
    });

    /* ============================================================
       Удаление кастомного варенья
    ============================================================ */
    async function removeCustomItem(itemEl) {
        const customId = itemEl.dataset.customId;
        itemEl.classList.add('--removing');

        try {
            const json = await api(`${BASE}/cart/custom/${customId}`, 'DELETE');

            setTimeout(() => {
                itemEl.remove();
                updateHeaderCounter(json.count);
                updateSubtotal(json.subtotal);
                if (json.empty) window.location.reload();
            }, 280);

        } catch (err) {
            console.error('[cart] remove-custom failed:', err);
            itemEl.classList.remove('--removing');
        }
    }

})();
