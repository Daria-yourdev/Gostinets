/* ===========================================================
   ГОСТИНЕЦЪ — catalog.js
   Логика страницы каталога: фильтры, сортировка, ценовой бар,
   мобильная панель, добавление в корзину.
   =========================================================== */
(function () {
    'use strict';

    const form = document.getElementById('cat-filters-form');
    if (!form) return;

    /* ============================================================
       1. АВТО-САБМИТ ФИЛЬТРОВ (только на десктопе)
       На мобиле — только по кнопке «Применить»
       ============================================================ */
    const isMobile = () => window.matchMedia('(max-width: 768px)').matches;

    let submitTimer = null;
    function scheduleSubmit() {
        if (isMobile()) return; // на мобиле — ручной submit
        clearTimeout(submitTimer);
        submitTimer = setTimeout(() => form.submit(), 350);
    }

    // Чекбоксы и select-ы — реагируют сразу
    form.querySelectorAll('input[type="checkbox"]').forEach(inp => {
        inp.addEventListener('change', scheduleSubmit);
    });

    // Цена — отложенный submit (debounce)
    form.querySelectorAll('input[type="number"]').forEach(inp => {
        inp.addEventListener('input', () => {
            updatePriceFill();
            scheduleSubmit();
        });
    });

    /* ============================================================
       2. СОРТИРОВКА — submit формы с поля sort
       Поле находится вне формы, надо подсунуть его в URL
       ============================================================ */
    const sortControl = document.querySelector('[data-sort-control]');
    if (sortControl) {
        sortControl.addEventListener('change', () => {
            const url = new URL(window.location.href);
            url.searchParams.set('sort', sortControl.value);
            url.searchParams.delete('page'); // сброс пагинации при смене сортировки
            window.location.href = url.toString();
        });
    }

    /* ============================================================
       3. ВИЗУАЛИЗАЦИЯ ДИАПАЗОНА ЦЕНЫ
       Подкрашиваем заполненный отрезок между min и max
       ============================================================ */
    const priceRange = document.querySelector('.cat-filters__price-range');
    const priceFill = document.getElementById('cat-price-fill');

    function updatePriceFill() {
        if (!priceRange || !priceFill) return;
        const min = Number(priceRange.dataset.min || 0);
        const max = Number(priceRange.dataset.max || 1000);
        const minIn = form.querySelector('input[name="price_min"]');
        const maxIn = form.querySelector('input[name="price_max"]');
        const cur1 = Number(minIn?.value || min);
        const cur2 = Number(maxIn?.value || max);

        const span = Math.max(max - min, 1);
        const leftPct = Math.max(0, Math.min(100, ((cur1 - min) / span) * 100));
        const rightPct = Math.max(0, Math.min(100, ((cur2 - min) / span) * 100));

        priceFill.style.left = leftPct + '%';
        priceFill.style.right = (100 - rightPct) + '%';
    }
    updatePriceFill();

    /* ============================================================
       4. МОБИЛЬНАЯ ПАНЕЛЬ ФИЛЬТРОВ
       ============================================================ */
    const filtersPanel = document.getElementById('cat-filters');
    const openBtn = document.getElementById('cat-filters-open');
    const closeBtn = document.getElementById('cat-filters-close');
    const backdrop = document.getElementById('cat-filters-backdrop');

    function openMobileFilters() {
        if (!filtersPanel) return;
        filtersPanel.classList.add('--open');
        backdrop?.classList.add('--show');
        document.body.style.overflow = 'hidden';
    }
    function closeMobileFilters() {
        if (!filtersPanel) return;
        filtersPanel.classList.remove('--open');
        backdrop?.classList.remove('--show');
        document.body.style.overflow = '';
    }

    openBtn?.addEventListener('click', openMobileFilters);
    closeBtn?.addEventListener('click', closeMobileFilters);
    backdrop?.addEventListener('click', closeMobileFilters);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && filtersPanel?.classList.contains('--open')) {
            closeMobileFilters();
        }
    });

    /* ============================================================
       5. ДОБАВЛЕНИЕ В КОРЗИНУ
       Корзина живёт в сессии — работает для всех (гость + авторизованный).
       ============================================================ */
    document.querySelectorAll('[data-add-to-cart]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const id = btn.dataset.addToCart;

            btn.classList.add('--loading');
            btn.disabled = true;

            try {
                const res = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ product_id: parseInt(id, 10), qty: 1 })
                });
                const json = await res.json().catch(() => ({}));

                if (!json.ok) throw new Error(json.message || 'add failed');

                btn.classList.remove('--loading');
                btn.classList.add('--added');
                btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 L9 17 L4 12"/></svg>';

                // Обновляем счётчик в шапке
                const counter = document.querySelector('.action__count');
                if (counter) {
                    counter.textContent = json.count;
                    counter.classList.add('--pulse');
                    setTimeout(() => counter.classList.remove('--pulse'), 600);
                }

                // Через секунду возвращаем плюс
                setTimeout(() => {
                    btn.classList.remove('--added');
                    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>';
                    btn.disabled = false;
                }, 1400);
            } catch (err) {
                console.error('[catalog] add-to-cart failed:', err);
                btn.classList.remove('--loading');
                btn.disabled = false;
            }
        });
    });

    /* ============================================================
       6. ПОДСТРАХОВКА: если на десктопе пользователь меняет цену,
       а потом ресайзит окно в мобилу — submit не должен срабатывать
       ============================================================ */
    window.addEventListener('resize', () => {
        if (isMobile() && submitTimer) {
            clearTimeout(submitTimer);
            submitTimer = null;
        }
    });
})();
