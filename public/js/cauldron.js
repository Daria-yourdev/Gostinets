/* ===========================================================
   ГОСТИНЕЦЪ — cauldron.js v2
   Изменения:
   - overflow-x: clip в CSS снял sticky, теперь котёл прилипает
   - Основная ягода блокируется в списке «компании» (step 2)
   - Состояние формы сохраняется в localStorage → обновление страницы
     не сбрасывает выбор
   - Добавлены цвета для новых ягод: klukva, grusha, yabloko, shishka
   =========================================================== */
(function () {
    'use strict';

    const root = document.getElementById('cauldron');
    if (!root) return;

    const form = document.getElementById('cauldron-form');
    if (!form) return;

    const priceMap = JSON.parse(root.dataset.priceMap || '{}');
    const canCook  = root.dataset.canCook === '1';
    const STORE_KEY = 'gostinets_cauldron_v2';

    /* ----------------------------------------------------------
       DOM
    ---------------------------------------------------------- */
    const dom = {
        priceValue:  document.getElementById('cauldron-price-value'),
        priceEchoes: document.querySelectorAll('[data-price-display]'),
        liquid:      document.getElementById('cauldron-liquid'),
        drops:       document.getElementById('cauldron-drops'),
        labelName:   document.getElementById('cauldron-label-name'),
        labelDesc:   document.getElementById('cauldron-label-desc'),
        labelSize:   document.getElementById('cauldron-label-size'),
        steps:       document.querySelectorAll('.cauldron-step'),
        flash:       document.querySelector('[data-flash]'),
    };

    /* ============================================================
       1. PAYLOAD
    ============================================================ */
    function readPayload() {
        const fd = new FormData(form);
        return {
            label_name:  (fd.get('label_name')  || '').toString().trim(),
            berry_main:   fd.get('berry_main')   || null,
            berry_extras: fd.getAll('berry_extras[]'),
            spices:       fd.getAll('spices[]'),
            sweetener:   (fd.get('sweetener')    || 'sugar').toString(),
            jar_size: parseInt(fd.get('jar_size'), 10) || 250,
            dedication:  (fd.get('dedication')   || '').toString().trim(),
            whisper:     (fd.get('whisper')      || '').toString().trim(),
        };
    }

    /* ============================================================
       2. РАСЧЁТ ЦЕНЫ
    ============================================================ */
    function calculatePrice(data) {
        let price = priceMap.base || 0;
        price += priceMap.berries?.[data.berry_main] || 0;
        price += data.berry_extras.length * (priceMap.extraBerry || 0);
        price += data.spices.length * (priceMap.spice || 0);
        price += priceMap.sweeteners?.[data.sweetener] || 0;
        if (data.dedication) price += priceMap.dedication || 0;
        const mul = priceMap.sizes?.[data.jar_size] ?? 1.0;
        return Math.round(price * mul);
    }

    function applyPrice(newPrice) {
        if (!dom.priceValue) return;
        if (parseInt(dom.priceValue.textContent, 10) === newPrice) return;
        dom.priceValue.textContent = newPrice;
        dom.priceEchoes.forEach(el => (el.textContent = newPrice));
        dom.priceValue.classList.remove('--pulse');
        void dom.priceValue.offsetWidth;
        dom.priceValue.classList.add('--pulse');
        setTimeout(() => dom.priceValue.classList.remove('--pulse'), 420);
    }

    /* ============================================================
       3. ОГРАНИЧЕНИЕ ЧЕКБОКСОВ ПО data-max
    ============================================================ */
    function enforceLimit() {
        const groups = {};
        form.querySelectorAll('input[type="checkbox"][data-max]').forEach(inp => {
            const key = inp.name;
            (groups[key] = groups[key] || []).push(inp);
        });

        Object.values(groups).forEach(boxes => {
            const max = parseInt(boxes[0].dataset.max, 10);
            const checked = boxes.filter(b => b.checked).length;
            const atLimit = checked >= max;

            boxes.forEach(b => {
                if (b.hasAttribute('data-main-blocked')) return; // этим управляет другая функция
                const label = b.closest('label');
                if (b.checked || !atLimit) {
                    label?.classList.remove('--disabled');
                    b.disabled = false;
                } else {
                    label?.classList.add('--disabled');
                    b.disabled = true;
                }
            });
        });
    }

    /* ============================================================
       4. БЛОКИРОВКА MAIN-ЯГОДЫ В EXTRAS
          Вызывается при каждой смене berry_main.
    ============================================================ */
    function syncExtrasWithMain(mainBerry) {
        form.querySelectorAll('input[name="berry_extras[]"]').forEach(cb => {
            const label = cb.closest('label');
            if (cb.value === mainBerry) {
                cb.checked = false;           // снимаем галочку если стояла
                cb.disabled = true;
                cb.setAttribute('data-main-blocked', '1');
                label?.classList.add('--disabled');
            } else {
                cb.removeAttribute('data-main-blocked');
                // Разблокируем только если не заблокировано лимитом
                // enforceLimit() разберётся с этим следующим вызовом
                if (!cb.disabled || !cb.closest('label')?.classList.contains('--disabled')) {
                    cb.disabled = false;
                    label?.classList.remove('--disabled');
                }
            }
        });
    }

    /* ============================================================
       5. ВИЗУАЛ КОТЛА
    ============================================================ */
    const BERRY_COLORS = {
        vishnya:  '#7E1A1A',
        malina:   '#6B1818',
        ezhevika: '#2E1414',
        limon:    '#C9A961',
        abrikos:  '#D4A24C',
        klubnika: '#A02020',
        klukva:   '#8B1520',   // клюква — тёмно-малиновый
        grusha:   '#8B9A2C',   // груша — зеленоватый
        yabloko:  '#B52020',   // яблоко — красный
        shishka:  '#5C3B1F',   // шишка — смолистый коричневый
    };

    const BERRY_LABELS = {
        vishnya: 'вишня', malina: 'малина', ezhevika: 'ежевика',
        limon: 'лимон', abrikos: 'абрикос', klubnika: 'клубника',
        klukva: 'клюква', grusha: 'груша', yabloko: 'яблоко', shishka: 'шишка',
    };

    function paintLiquid(berry) {
        if (dom.liquid) dom.liquid.setAttribute('fill', BERRY_COLORS[berry] || '#5B1C1C');
    }

    function dropBerry(berry) {
        if (!dom.drops) return;
        const color = BERRY_COLORS[berry] || '#7E1A1A';
        const drop = document.createElement('div');
        drop.className = 'cauldron__drop';
        drop.style.left = (30 + Math.random() * 40) + '%';
        drop.innerHTML = `<svg viewBox="0 0 28 28" fill="none">
            <circle cx="14" cy="14" r="11" fill="${color}" stroke="#1A0A0A" stroke-width="1.5"/>
            <ellipse cx="11" cy="10" rx="2.5" ry="2" fill="rgba(255,255,255,0.3)"/>
        </svg>`;
        dom.drops.appendChild(drop);
        setTimeout(() => drop.remove(), 900);
    }

    /* ============================================================
       6. ПРЕВЬЮ ЭТИКЕТКИ
    ============================================================ */
    function updateLabel(data) {
        if (dom.labelName) {
            dom.labelName.textContent = data.label_name || 'Своё варенье';
        }
        if (dom.labelDesc) {
            const parts = [];
            if (data.berry_main) parts.push(BERRY_LABELS[data.berry_main] || data.berry_main);
            parts.push(...data.berry_extras.map(x => BERRY_LABELS[x] || x));
            if (data.spices.length) {
                const sm = { ginger:'имбирь', cinnamon:'корица', vanilla:'ваниль',
                             cardamom:'кардамон', mint:'мята', lemon_zest:'цедра' };
                parts.push('· ' + data.spices.map(x => sm[x] || x).join(', '));
            }
            dom.labelDesc.textContent = parts.length ? parts.join(', ') : 'собери в котле слева';
        }
        if (dom.labelSize) dom.labelSize.textContent = data.jar_size + ' г';
    }

    /* ============================================================
       7. ШАГИ ПРОГРЕССА
    ============================================================ */
    function updateSteps(data) {
        const done = {
            1: !!data.berry_main,
            2: data.berry_extras.length > 0,
            3: data.spices.length > 0,
            4: !!data.sweetener,
            5: !!data.jar_size,
            6: data.label_name.length >= 2,
        };
        dom.steps.forEach(s => s.classList.toggle('--done', !!done[+s.dataset.step]));
    }

    /* ============================================================
       8. СОХРАНЕНИЕ В localStorage
    ============================================================ */
    function saveState(data) {
        try { localStorage.setItem(STORE_KEY, JSON.stringify(data)); } catch (_) {}
    }

    function clearState() {
        try { localStorage.removeItem(STORE_KEY); } catch (_) {}
    }

    function restoreState() {
        let data;
        try {
            const raw = localStorage.getItem(STORE_KEY);
            if (!raw) return;
            data = JSON.parse(raw);
        } catch (_) { return; }

        const esc = s => CSS.escape(String(s));

        // berry_main
        if (data.berry_main) {
            const rb = form.querySelector(`input[name="berry_main"][value="${esc(data.berry_main)}"]`);
            if (rb) rb.checked = true;
        }

        // Сначала применяем блокировку main, потом ставим extras
        if (data.berry_main) syncExtrasWithMain(data.berry_main);

        // berry_extras
        (data.berry_extras || []).forEach(val => {
            if (val === data.berry_main) return; // заблокирована
            const cb = form.querySelector(`input[name="berry_extras[]"][value="${esc(val)}"]`);
            if (cb) cb.checked = true;
        });

        // spices
        (data.spices || []).forEach(val => {
            const cb = form.querySelector(`input[name="spices[]"][value="${esc(val)}"]`);
            if (cb) cb.checked = true;
        });

        // sweetener
        if (data.sweetener) {
            const rb = form.querySelector(`input[name="sweetener"][value="${esc(data.sweetener)}"]`);
            if (rb) rb.checked = true;
        }

        // jar_size
        if (data.jar_size) {
            const rb = form.querySelector(`input[name="jar_size"][value="${data.jar_size}"]`);
            if (rb) rb.checked = true;
        }

        // Текстовые поля
        [
            ['input[name="label_name"]', data.label_name],
            ['input[name="dedication"]', data.dedication],
            ['textarea[name="whisper"]', data.whisper],
        ].forEach(([sel, val]) => {
            const el = form.querySelector(sel);
            if (el && val) el.value = val;
        });

        // Красим котёл
        if (data.berry_main) paintLiquid(data.berry_main);
    }

    /* ============================================================
       9. ГЛАВНЫЙ ОБРАБОТЧИК
    ============================================================ */
    let previousMain = null;

    function handleChange() {
        const data = readPayload();

        // Блокируем main в extras при каждой смене
        if (data.berry_main !== previousMain) {
            syncExtrasWithMain(data.berry_main || '');
            previousMain = data.berry_main;
        }

        enforceLimit();
        applyPrice(calculatePrice(data));
        updateLabel(data);
        updateSteps(data);
        saveState(data);
    }

    // Капля + перекраска при смене main ягоды
    form.querySelectorAll('input[name="berry_main"]').forEach(rb => {
        rb.addEventListener('change', () => {
            if (rb.checked) { paintLiquid(rb.value); dropBerry(rb.value); }
        });
    });

    // Капля при добавлении доп.ягоды
    form.querySelectorAll('input[name="berry_extras[]"]').forEach(cb => {
        cb.addEventListener('change', () => { if (cb.checked) dropBerry(cb.value); });
    });

    form.addEventListener('change', handleChange);
    form.addEventListener('input', e => {
        if (e.target.matches('input[type="text"], textarea')) handleChange();
    });

    /* ============================================================
       10. AUTH-КНОПКИ ДЛЯ ГОСТЕЙ
    ============================================================ */
    document.querySelectorAll('[data-open-auth]').forEach(btn => {
        btn.addEventListener('click', () => {
            const mode = btn.dataset.openAuth || 'login';
            if (typeof window.openAuthModal === 'function') {
                window.openAuthModal(mode); return;
            }
            const fallback = document.getElementById('open-auth-btn');
            if (fallback) {
                fallback.click();
                setTimeout(() => {
                    const tab = document.querySelector(`[data-switch="${mode}"]`);
                    if (tab && mode === 'register') tab.click();
                }, 50);
            }
        });
    });

    /* ============================================================
       11. AJAX-СУБМИТ
    ============================================================ */
    if (canCook) {
        form.addEventListener('submit', async e => {
            e.preventDefault();
            const submitter = e.submitter;
            const action = submitter?.value || 'order';

            form.querySelectorAll('.cauldron-error').forEach(el => {
                el.textContent = ''; el.classList.remove('--show');
            });
            hideFlash();
            submitter?.classList.add('--loading');
            submitter?.setAttribute('disabled', 'disabled');

            try {
                const fd = new FormData(form);
                fd.set('action', action);
                const csrf = document.querySelector('meta[name="csrf-token"]')?.content
                           || form.querySelector('input[name="_token"]')?.value;

                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: fd,
                });

                const json = await res.json().catch(() => ({}));

                if (res.status === 422 && json.errors) {
                    Object.entries(json.errors).forEach(([field, msgs]) => {
                        const el = form.querySelector(`.cauldron-error[data-error="${field}"]`);
                        if (el) { el.textContent = msgs[0]; el.classList.add('--show'); }
                    });
                    showFlash('error', json.message || 'Где-то не сходится.');
                    return;
                }

                if (!res.ok || !json.ok) {
                    showFlash('error', json.message || 'Котёл закашлял. Попробуй снова.');
                    return;
                }

                clearState(); // успех — чистим localStorage
                showFlash('success', json.message);
                if (json.redirect) setTimeout(() => (window.location.href = json.redirect), 1200);

            } catch (err) {
                showFlash('error', 'Связь прервалась. Попробуй через минуту.');
            } finally {
                submitter?.classList.remove('--loading');
                submitter?.removeAttribute('disabled');
            }
        });
    }

    /* ============================================================
       Утилиты
    ============================================================ */
    function showFlash(type, msg) {
        if (!dom.flash) return;
        dom.flash.className = `cauldron-flash --show --${type}`;
        dom.flash.textContent = msg;
    }
    function hideFlash() {
        if (!dom.flash) return;
        dom.flash.className = 'cauldron-flash';
        dom.flash.textContent = '';
    }

    /* ============================================================
       ИНИЦИАЛИЗАЦИЯ
    ============================================================ */
    restoreState();  // сначала восстанавливаем
    handleChange();  // потом пересчитываем

})();