/* ===========================================================
   ГОСТИНЕЦЪ — delivery-modal.js
   Модалка выбора способа доставки (сохраняется в сессию через AJAX)
   =========================================================== */
(function () {
    'use strict';

    const modal = document.getElementById('delivery-modal');
    if (!modal) return;

    const openBtn = document.getElementById('change-city-btn'); // существующая кнопка в шапке
    const closeBtn = document.getElementById('delivery-modal-close');
    const overlay = document.getElementById('delivery-modal-overlay');
    const saveBtn = document.getElementById('delivery-modal-save');
    const options = modal.querySelectorAll('.delivery-option');
    const labelEl = document.getElementById('user-city'); // оставим тот же id для шапки

    const BASE = (document.querySelector('meta[name="base-url"]')?.content || '').replace(/\/$/, '');

    /* Открытие/закрытие */
    function open() {
        modal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
    }
    function close() {
        modal.setAttribute('hidden', '');
        document.body.style.overflow = '';
    }

    openBtn?.addEventListener('click', open);
    closeBtn?.addEventListener('click', close);
    overlay?.addEventListener('click', close);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.hasAttribute('hidden')) close();
    });

    /* Выбор опции */
    options.forEach(opt => {
        opt.addEventListener('click', (e) => {
            // Не реагируем на клик по радио внутри
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'LABEL') return;
            options.forEach(o => o.classList.remove('--active'));
            opt.classList.add('--active');
        });
    });

    /* Сохранение выбора */
    saveBtn?.addEventListener('click', async () => {
        const active = modal.querySelector('.delivery-option.--active');
        if (!active) {
            alert('Выбери способ доставки');
            return;
        }

        const mode = active.dataset.mode;
        let method = active.dataset.method || null;

        // Для russia и gift — берём радио
        if (mode === 'russia') {
            method = modal.querySelector('input[name="russia-method"]:checked')?.value || 'cdek';
        } else if (mode === 'gift') {
            method = modal.querySelector('input[name="gift-method"]:checked')?.value || 'cdek';
        } else if (mode === 'pickup') {
            method = 'pickup';
        }

        saveBtn.disabled = true;
        saveBtn.textContent = 'Сохраняем...';

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const res = await fetch(`${BASE}/delivery/save`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ mode, method }),
            });
            const json = await res.json().catch(() => ({}));

            if (json.ok) {
                // Обновляем подпись в шапке
                if (labelEl) labelEl.textContent = json.label || 'Доставка выбрана';
                // Сохраняем в localStorage для следующего захода
                try { localStorage.setItem('gostinets-delivery', JSON.stringify({ mode, method })); } catch (e) { }
                close();
            } else {
                alert(json.message || 'Ошибка сохранения');
            }
        } catch (err) {
            console.error(err);
            alert('Не удалось сохранить выбор');
        } finally {
            saveBtn.disabled = false;
            saveBtn.textContent = 'Сохранить выбор';
        }
    });

    /* Восстановление выбора из localStorage при загрузке */
    try {
        const saved = JSON.parse(localStorage.getItem('gostinets-delivery') || 'null');
        if (saved?.mode) {
            const opt = modal.querySelector(`.delivery-option[data-mode="${saved.mode}"]`);
            if (opt) {
                opt.classList.add('--active');
                if (saved.method) {
                    const radio = modal.querySelector(`input[name="${saved.mode}-method"][value="${saved.method}"]`);
                    if (radio) radio.checked = true;
                }
            }
        }
    } catch (e) { }

})();