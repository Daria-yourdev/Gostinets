/* ===========================================================
   ГОСТИНЕЦЪ — главная: интерактив (vanilla JS, no deps)
   =========================================================== */
(function () {
    'use strict';

    /* Шаблоны рун (SVG-строки для flip-карточек) */
    const RUNES = {
        berry: '<svg viewBox="0 0 64 64" fill="none"><circle cx="22" cy="36" r="9" fill="currentColor"/><circle cx="42" cy="36" r="9" fill="currentColor"/><circle cx="32" cy="48" r="9" fill="currentColor"/><path d="M32 8 Q28 18 22 28 M32 8 Q36 18 42 28 M32 8 L32 22" stroke="currentColor" stroke-width="2" fill="none"/><path d="M28 6 Q32 4 36 6" stroke="currentColor" stroke-width="2.2" fill="none"/></svg>',
        sun: '<svg viewBox="0 0 64 64" fill="none"><circle cx="32" cy="32" r="10" fill="currentColor"/>' +
            Array.from({ length: 12 }, (_, i) =>
                `<line x1="32" y1="32" x2="32" y2="6" stroke="currentColor" stroke-width="2.5" transform="rotate(${i * 30} 32 32)"/>`
            ).join('') + '</svg>',
        diamond: '<svg viewBox="0 0 64 64" fill="none"><path d="M32 4 L60 32 L32 60 L4 32 Z" stroke="currentColor" stroke-width="2.5"/><path d="M32 16 L48 32 L32 48 L16 32 Z" stroke="currentColor" stroke-width="2"/><circle cx="32" cy="32" r="4" fill="currentColor"/><path d="M32 4 L32 60 M4 32 L60 32" stroke="currentColor" stroke-width="1.5" opacity="0.5"/></svg>',
        tree: '<svg viewBox="0 0 64 64" fill="none"><path d="M32 6 L32 58" stroke="currentColor" stroke-width="2.5"/><path d="M32 12 L22 6 M32 12 L42 6" stroke="currentColor" stroke-width="2"/><path d="M32 22 L18 14 M32 22 L46 14" stroke="currentColor" stroke-width="2"/><path d="M32 34 L14 24 M32 34 L50 24" stroke="currentColor" stroke-width="2"/><path d="M32 46 L20 40 M32 46 L44 40" stroke="currentColor" stroke-width="2"/><circle cx="32" cy="58" r="3" fill="currentColor"/></svg>',
        spiral: '<svg viewBox="0 0 64 64" fill="none"><path d="M32 32 m-2 0 a2 2 0 1 0 4 0 a2 2 0 1 0 -4 0 M32 32 m-8 0 a8 8 0 1 0 16 0 M32 32 m-16 0 a16 16 0 1 0 32 0 M32 32 m-24 0 a24 24 0 1 0 48 0" stroke="currentColor" stroke-width="2.4" fill="none"/></svg>',
        bird: '<svg viewBox="0 0 64 64" fill="none"><path d="M14 36 Q22 24 32 28 Q42 32 52 22 Q50 38 38 42 Q32 44 26 42 Q20 40 14 36 Z" fill="currentColor"/><circle cx="48" cy="26" r="2" fill="white"/><path d="M14 36 L8 38 M14 36 L8 42" stroke="currentColor" stroke-width="2"/><path d="M30 44 L28 52 M34 44 L36 52" stroke="currentColor" stroke-width="2"/></svg>'
    };

    /* Карты-варенья для оракула:
       prediction — короткое пророчество (психоделика+фольклор).
       buyLabel  — название сорта, которое подставится в CTA «Забрать ___ →». */
    const ORACLE_BERRIES = [
        {
            name: 'Вишня', lab: 'Страсть', rune: 'berry',
            prediction: 'Вишня сулит встречу, что обожжёт. Спрячь сердце за косточкой — или впусти.',
            buyLabel: 'вишнёвое'
        },
        {
            name: 'Малина', lab: 'Радость', rune: 'sun',
            prediction: 'Малина шепчет: эта неделя пахнет солнцем. Жди весточки от того, по кому скучаешь.',
            buyLabel: 'малиновое'
        },
        {
            name: 'Ежевика', lab: 'Тайна', rune: 'diamond',
            prediction: 'Ежевика прячет ответ. То, что искал давно, найдётся не там, где смотришь.',
            buyLabel: 'ежевичное'
        },
        {
            name: 'Лимон', lab: 'Ясность', rune: 'tree',
            prediction: 'Лимон зовёт начать заново. Время на разговор, что давно откладываешь.',
            buyLabel: 'лимонное'
        },
        {
            name: 'Абрикос', lab: 'Покой', rune: 'spiral',
            prediction: 'Абрикос обещает тишину. Позвони бабушке. Налей чай. Ничего не объясняй.',
            buyLabel: 'абрикосовое'
        },
        {
            name: 'Клубника', lab: 'Любовь', rune: 'bird',
            prediction: 'Клубника знает: о тебе думают. Позволь себе быть в центре чьего-то лета.',
            buyLabel: 'клубничное'
        }
    ];

    /* CITY: смена города + сохранение в localStorage */
    const cityModal = document.getElementById('city-modal');
    const cityBtn = document.getElementById('change-city-btn');
    const cityClose = document.getElementById('city-modal-close');
    const cityOverlay = document.getElementById('city-modal-overlay');
    const userCityEl = document.getElementById('user-city');
    const citySave = document.getElementById('city-modal-save');
    const cityInput = document.getElementById('city-modal-input');
    const cityItems = document.querySelectorAll('.city-modal__item');
    const DEFAULT_CITY = 'Казань';

    function getSavedCity() {
        try { return localStorage.getItem('gostinets-city'); } catch (e) { return null; }
    }
    function saveCity(city) {
        try { localStorage.setItem('gostinets-city', city); } catch (e) { }
    }
    function paintActiveCity(city) {
        cityItems.forEach(it => {
            it.classList.toggle('--active', it.dataset.city === city);
        });
    }
    function setCity(city) {
        if (!city) return;
        userCityEl.textContent = city;
        saveCity(city);
        paintActiveCity(city);
        closeCityModal();
    }
    function openCityModal() {
        if (!cityModal) return;
        cityModal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
        paintActiveCity(userCityEl.textContent.trim());
        setTimeout(() => cityInput && cityInput.focus(), 200);
    }
    function closeCityModal() {
        if (!cityModal) return;
        cityModal.setAttribute('hidden', '');
        document.body.style.overflow = '';
        if (cityInput) cityInput.value = '';
    }

    const saved = getSavedCity();
    if (saved && userCityEl) userCityEl.textContent = saved;
    else if (userCityEl) userCityEl.textContent = DEFAULT_CITY;

    if (cityBtn) cityBtn.addEventListener('click', openCityModal);
    if (cityClose) cityClose.addEventListener('click', closeCityModal);
    if (cityOverlay) cityOverlay.addEventListener('click', closeCityModal);
    cityItems.forEach(it => it.addEventListener('click', () => setCity(it.dataset.city)));
    if (citySave && cityInput) {
        citySave.addEventListener('click', () => {
            const v = cityInput.value.trim();
            if (v) setCity(v);
        });
        cityInput.addEventListener('keydown', e => {
            if (e.key === 'Enter') {
                const v = cityInput.value.trim();
                if (v) setCity(v);
            }
        });
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && cityModal && !cityModal.hasAttribute('hidden')) closeCityModal();
    });

    /* VERTICAL SLIDER (Hero) */
    (() => {
        const root = document.querySelector('.vslider');
        if (!root) return;
        const track = root.querySelector('.vslider__track');
        const dots = Array.from(root.querySelectorAll('.vslider__dot'));
        const btnUp = root.querySelector('.vslider__btn--up');
        const btnDown = root.querySelector('.vslider__btn--down');
        const total = dots.length || track.children.length || 1;
        const AUTOPLAY_MS = 8000; // было 5500 — слишком быстро для рассматривания
        let index = 0;
        let timer = null;

        function go(i) {
            index = ((i % total) + total) % total;
            track.style.transform = `translateY(-${index * 100}%)`;
            dots.forEach((d, k) => d.classList.toggle('--active', k === index));
        }
        function start() { stop(); timer = setInterval(() => go(index + 1), AUTOPLAY_MS); }
        function stop() { if (timer) { clearInterval(timer); timer = null; } }

        if (btnUp) btnUp.addEventListener('click', () => { go(index - 1); start(); });
        if (btnDown) btnDown.addEventListener('click', () => { go(index + 1); start(); });
        dots.forEach((d, i) => d.addEventListener('click', () => { go(i); start(); }));
        root.addEventListener('mouseenter', stop);
        root.addEventListener('mouseleave', start);

        let touchY = null;
        root.addEventListener('touchstart', e => { touchY = e.touches[0].clientY; }, { passive: true });
        root.addEventListener('touchend', e => {
            if (touchY === null) return;
            const diff = touchY - e.changedTouches[0].clientY;
            if (Math.abs(diff) > 40) go(index + (diff > 0 ? 1 : -1));
            touchY = null;
            start();
        });

        go(0);
        start();
    })();

    /* FAQ accordion */
    document.querySelectorAll('.faq-item').forEach(item => {
        item.addEventListener('click', () => {
            const isOpen = item.classList.contains('--open');
            document.querySelectorAll('.faq-item.--open').forEach(o => {
                if (o !== item) o.classList.remove('--open');
            });
            item.classList.toggle('--open', !isOpen);
        });
    });

    /* Catalog filter chips */
    document.querySelectorAll('.filter-chip').forEach(chip => {
        chip.addEventListener('click', () => {
            document.querySelectorAll('.filter-chip').forEach(c => c.classList.remove('--on'));
            chip.classList.add('--on');
        });
    });

    /* ===========================================================
       ORACLE — flip-карты, случайный выбор, блокировка остальных,
       CTA на покупку выбранного сорта
       =========================================================== */
    const oracleDeck = document.getElementById('oracle-deck');
    const oracleResult = document.getElementById('oracle-result');
    const oracleReset = document.getElementById('oracle-reset');

    function pickRandomBerry() {
        return ORACLE_BERRIES[Math.floor(Math.random() * ORACLE_BERRIES.length)];
    }

    function flipCard(card) {
        if (!oracleDeck) return;
        // Если в колоде уже есть выбранная карта — игнорируем повторные клики
        if (oracleDeck.classList.contains('--locked')) return;
        if (card.classList.contains('--flipped')) return;

        const pick = pickRandomBerry();
        const front = card.querySelector('.oracle-card__front');
        const rune = RUNES[pick.rune] || RUNES.berry;
        front.innerHTML =
            '<div class="oracle-card__rune">' + rune + '</div>' +
            '<div>' +
            '<div class="oracle-card__name">' + pick.name + '</div>' +
            '<div class="oracle-card__lab">' + pick.lab + '</div>' +
            '</div>';
        card.classList.add('--flipped');

        // Блокируем остальные карты
        oracleDeck.classList.add('--locked');
        oracleDeck.querySelectorAll('.oracle-card:not(.--flipped)').forEach(c => {
            c.setAttribute('aria-disabled', 'true');
            c.tabIndex = -1;
        });

        // Заполняем результат: вердикт + предсказание + CTA-кнопка к каталогу
        if (oracleResult) {
            oracleResult.innerHTML =
                '<div class="oracle__verdict">Сегодня тебе шепчет — <b>' + pick.name + '</b>.</div>' +
                '<div class="oracle__pred">' + pick.prediction + '</div>' +
                '<a href="#catalog" class="oracle__cta btn-primary" data-berry="' + pick.buyLabel + '">' +
                '<span>Забрать ' + pick.buyLabel + ' →</span>' +
                '</a>';

            // Плавная прокрутка к каталогу при клике на CTA (с подсветкой нужной карточки, если найдём)
            const cta = oracleResult.querySelector('.oracle__cta');
            if (cta) {
                cta.addEventListener('click', e => {
                    e.preventDefault();
                    const target = document.querySelector('#catalog');
                    if (!target) return;
                    const top = target.getBoundingClientRect().top + window.scrollY - 12;
                    window.scrollTo({ top, behavior: 'smooth' });

                    // Попытка подсветить карточку выбранного сорта
                    setTimeout(() => {
                        document.querySelectorAll('.jar-card').forEach(jc => {
                            const name = (jc.querySelector('.jar-card__name')?.textContent || '').toLowerCase();
                            if (name.includes(pick.buyLabel.toLowerCase())) {
                                jc.classList.add('--highlight');
                                setTimeout(() => jc.classList.remove('--highlight'), 2400);
                            }
                        });
                    }, 700);
                });
            }
        }

        if (oracleReset) oracleReset.classList.add('--show');
    }

    if (oracleDeck) {
        oracleDeck.querySelectorAll('.oracle-card').forEach(card => {
            card.addEventListener('click', () => flipCard(card));
            card.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    flipCard(card);
                }
            });
        });
    }

    if (oracleReset) {
        oracleReset.addEventListener('click', () => {
            if (!oracleDeck) return;
            oracleDeck.classList.remove('--locked');
            document.querySelectorAll('.oracle-card').forEach(c => {
                c.removeAttribute('aria-disabled');
                c.tabIndex = 0;
                if (c.classList.contains('--flipped')) {
                    c.classList.remove('--flipped');
                    setTimeout(() => {
                        const front = c.querySelector('.oracle-card__front');
                        if (front) front.innerHTML = '';
                    }, 600);
                }
            });
            oracleReset.classList.remove('--show');
            if (oracleResult) oracleResult.innerHTML = '';
        });
    }

    /* ===========================================================
       PARTICLES — парящие частицы на тёмном блоке преимуществ
       (атмосфера колдовства / искры из медного таза)
       =========================================================== */
    (() => {
        const ink = document.querySelector('.section--ink');
        if (!ink) return;
        // Уважаем prefers-reduced-motion
        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

        const layer = document.createElement('div');
        layer.className = 'adv-particles';
        layer.setAttribute('aria-hidden', 'true');
        ink.prepend(layer);

        const COUNT = 32;
        for (let i = 0; i < COUNT; i++) {
            const p = document.createElement('span');
            p.className = 'adv-particle';
            // случайный размер 2–6px
            const size = 2 + Math.random() * 4;
            p.style.width = size + 'px';
            p.style.height = size + 'px';
            // случайная горизонтальная позиция
            p.style.left = (Math.random() * 100) + '%';
            // длительность подъёма 14–28 сек
            p.style.animationDuration = (14 + Math.random() * 14) + 's';
            // отрицательная задержка, чтобы при загрузке частицы уже летели
            p.style.animationDelay = (-Math.random() * 28) + 's';
            // 25% частиц — ярко-золотые с свечением, 10% — тёмно-красные (вишнёвые искры)
            const r = Math.random();
            if (r < 0.25) p.classList.add('--gold');
            else if (r < 0.35) p.classList.add('--red');
            layer.appendChild(p);
        }
    })();

    /* SCROLL TOP button */
    const scrollTopBtn = document.querySelector('.scroll-top');
    if (scrollTopBtn) {
        const onScroll = () => {
            scrollTopBtn.classList.toggle('--show', window.scrollY > 600);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();
        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    /* Smooth-scroll для внутренних якорей */
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const href = a.getAttribute('href');
            if (!href || href === '#') return;
            // оракульный CTA сам обработает скролл с подсветкой
            if (a.classList.contains('oracle__cta')) return;
            const target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();
            const top = target.getBoundingClientRect().top + window.scrollY - 12;
            window.scrollTo({ top, behavior: 'smooth' });
        });
    });
})();

/* ===========================================================
   ПОДПИСКА НА РАССЫЛКУ — футер
   =========================================================== */
(function () {
    const form = document.getElementById('subscribe-form');
    const btn = document.getElementById('subscribe-btn');
    const result = document.getElementById('subscribe-result');
    const consent = document.getElementById('subscribe-consent');

    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        // Проверяем согласие
        if (!consent?.checked) {
            showResult('Поставь галочку согласия.', 'error');
            return;
        }

        const email = form.querySelector('input[type="email"]').value.trim();
        if (!email) {
            showResult('Введи адрес почты.', 'error');
            return;
        }

        // Отправляем
        btn.disabled = true;
        btn.textContent = '...';

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
            const base = document.querySelector('meta[name="base-url"]')?.content || '';

            const res = await fetch(`${base}/subscribe`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ email, source: 'footer' }),
            });

            const json = await res.json().catch(() => ({}));

            if (res.ok && json.ok) {
                // Успех — скрываем форму, показываем сообщение
                form.hidden = true;
                consent.closest('label').hidden = true;
                showResult(
                    json.already
                        ? '✓ Ты уже в нашем списке. Весточки придут.'
                        : '✓ Добавили! Будем писать только о важном.',
                    'ok'
                );
            } else {
                showResult(json.message || 'Что-то пошло не так, попробуй позже.', 'error');
                btn.disabled = false;
                btn.textContent = 'Подписаться';
            }
        } catch (err) {
            showResult('Нет связи. Попробуй чуть позже.', 'error');
            btn.disabled = false;
            btn.textContent = 'Подписаться';
        }
    });

    function showResult(text, type) {
        result.textContent = text;
        result.hidden = false;
        result.className = 'newsletter__result newsletter__result--' + type;
    }
})();