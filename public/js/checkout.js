/* ===========================================================
   ГОСТИНЕЦЪ — checkout.js
   - Пересчёт доставки и итога при смене способа доставки
   - Лоадер на кнопке «Расплатиться» при сабмите
   =========================================================== */
(function () {
    'use strict';

    const form = document.getElementById('checkout-form');
    if (!form) return;

    /* ============================================================
       Форматирование числа
    ============================================================ */
    function fmt(n) {
        return new Intl.NumberFormat('ru-RU').format(n);
    }

    /* ============================================================
       Подсчёт подытога из суммы позиций (она зашита в DOM)
    ============================================================ */
    function getSubtotal() {
        const el = document.querySelector('.checkout-summary__row strong');
        if (!el) return 0;
        return parseInt(el.textContent.replace(/[^\d]/g, ''), 10) || 0;
    }

    /* ============================================================
       Пересчёт сводки
    ============================================================ */
    function updateSummary() {
        const selected = form.querySelector('input[name="delivery_method"]:checked');
        const deliveryCost = selected ? parseInt(selected.dataset.cost, 10) || 0 : 0;
        const subtotal = getSubtotal();
        const total = subtotal + deliveryCost;

        // Доставка
        const dEl = document.getElementById('summary-delivery');
        if (dEl) {
            dEl.textContent = deliveryCost === 0
                ? 'бесплатно'
                : fmt(deliveryCost) + ' ₽';
        }

        // Итого
        const tEl = document.getElementById('summary-total');
        if (tEl) tEl.textContent = fmt(total) + ' ₽';

        // Кнопка submit
        const sEl = document.getElementById('submit-total');
        if (sEl) sEl.textContent = fmt(total);
    }

    // Слушаем смену доставки
    form.querySelectorAll('input[name="delivery_method"]').forEach(r => {
        r.addEventListener('change', updateSummary);
    });

    // Инициализация
    updateSummary();

    /* ============================================================
       Лоадер при сабмите формы
    ============================================================ */
    form.addEventListener('submit', (e) => {
        // Базовая HTML-валидация
        if (!form.checkValidity()) {
            // Браузер сам покажет ошибки
            return;
        }

        const submitBtn = document.getElementById('checkout-submit');
        if (submitBtn) {
            submitBtn.classList.add('--loading');
            submitBtn.disabled = true;
            // На случай если ничего не происходит — снимем через 10 сек
            setTimeout(() => {
                submitBtn.classList.remove('--loading');
                submitBtn.disabled = false;
            }, 10000);
        }
    });

})();
