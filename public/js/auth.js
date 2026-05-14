/* ===========================================================
   ГОСТИНЕЦЪ — auth.js
   Модалка входа/регистрации, валидация на JS, AJAX-отправка,
   выпадашка меню пользователя.
   Подключать ПОСЛЕ home.js: <script src="{{ asset('js/auth.js') }}" defer></script>
   =========================================================== */
(function () {
    'use strict';

    /* ============================================================
       0. CSRF
       ============================================================ */
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    const CSRF_TOKEN = csrfMeta ? csrfMeta.content : '';

    /* ============================================================
       1. ВАЛИДАТОРЫ — клиентская проверка перед AJAX
       Серверные сообщения в стиле «фольклор», поэтому и тут так же.
       ============================================================ */
    const RE_EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    const VALIDATORS = {
        name(v) {
            const s = (v || '').trim();
            if (!s) return 'Без имени в книгу не записать.';
            if (s.length < 2) return 'Имя слишком короткое.';
            if (s.length > 60) return 'Имя слишком длинное.';
            return null;
        },
        email(v) {
            const s = (v || '').trim();
            if (!s) return 'Введите почту.';
            if (!RE_EMAIL.test(s)) return 'Похоже, это не почта.';
            return null;
        },
        password(v, ctx) {
            if (!v) return ctx === 'login' ? 'Введите тайное слово.' : 'Придумайте тайное слово.';
            if (ctx === 'login') return null;
            if (v.length < 6) return 'Тайное слово — не короче 6 знаков.';
            if (!/[A-Za-zА-Яа-яЁё]/.test(v)) return 'В тайном слове должна быть буква.';
            if (!/\d/.test(v)) return 'В тайном слове должна быть цифра.';
            return null;
        },
        password_confirmation(v, ctx, all) {
            if (!v) return 'Повторите тайное слово.';
            if (v !== all.password) return 'Тайные слова не сходятся.';
            return null;
        },
        agree(v) {
            return v ? null : 'Без согласия в книгу не запишем.';
        },
        remember() { return null; }
    };

    function validateForm(form) {
        const ctx = form.dataset.panel; // 'login' | 'register'
        const data = collectFormData(form);
        const errors = {};

        Object.keys(data).forEach(key => {
            if (!VALIDATORS[key]) return;
            const err = VALIDATORS[key](data[key], ctx, data);
            if (err) errors[key] = err;
        });

        return { data, errors, valid: Object.keys(errors).length === 0 };
    }

    function collectFormData(form) {
        const fd = new FormData(form);
        const out = {};
        // FormData сохраняет только отмеченные чекбоксы — для unchecked получаем undefined
        const fields = form.querySelectorAll('input[name]');
        fields.forEach(input => {
            const name = input.name;
            if (input.type === 'checkbox') {
                out[name] = input.checked;
            } else {
                out[name] = fd.get(name) ?? '';
            }
        });
        return out;
    }

    /* ============================================================
       2. РЕНДЕР ОШИБОК В ИНТЕРФЕЙСЕ
       ============================================================ */
    function clearFieldError(field) {
        if (!field) return;
        const wrap = field.closest('.auth-field');
        if (!wrap) return;
        wrap.classList.remove('--invalid');
        const errEl = wrap.querySelector('.auth-field__error');
        if (errEl) errEl.textContent = '';
    }

    function setFieldError(form, name, message) {
        const field = form.querySelector('[name="' + name + '"]');
        if (!field) return;
        const wrap = field.closest('.auth-field');
        if (!wrap) return;
        wrap.classList.add('--invalid');
        const errEl = wrap.querySelector('[data-error="' + name + '"]')
                   || wrap.querySelector('.auth-field__error');
        if (errEl) errEl.textContent = message;
    }

    function clearAllErrors(form) {
        form.querySelectorAll('.auth-field.--invalid').forEach(w => {
            w.classList.remove('--invalid');
            const e = w.querySelector('.auth-field__error');
            if (e) e.textContent = '';
        });
        setAlert(form, '', '');
    }

    function setAlert(form, type, text) {
        const alert = form.querySelector('[data-alert]');
        if (!alert) return;
        alert.classList.remove('--show', '--error', '--success');
        if (!text) { alert.textContent = ''; return; }
        alert.textContent = text;
        alert.classList.add('--show', type === 'success' ? '--success' : '--error');
    }

    /* ============================================================
       3. МОДАЛКА — открытие/закрытие, табы, переключение
       ============================================================ */
    const modal = document.getElementById('auth-modal');
    const overlay = document.getElementById('auth-modal-overlay');
    const closeBtn = document.getElementById('auth-modal-close');
    const tabs = modal ? modal.querySelectorAll('.auth-modal__tab') : [];
    const panels = modal ? modal.querySelectorAll('.auth-modal__panel') : [];

    function openAuthModal(panel) {
        if (!modal) return;
        modal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';
        switchPanel(panel || 'login');
        setTimeout(() => {
            const active = modal.querySelector('.auth-modal__panel.--active input');
            if (active) active.focus();
        }, 200);
    }

    function closeAuthModal() {
        if (!modal) return;
        modal.setAttribute('hidden', '');
        document.body.style.overflow = '';
        // Сбросим формы и ошибки, чтобы при повторном открытии было чисто
        modal.querySelectorAll('form').forEach(f => {
            clearAllErrors(f);
            // Не сбрасываем поля целиком (мб юзер просто закрыл по ошибке)
        });
    }

    function switchPanel(target) {
        tabs.forEach(t => {
            const on = t.dataset.tab === target;
            t.classList.toggle('--active', on);
            t.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        panels.forEach(p => p.classList.toggle('--active', p.dataset.panel === target));
        const firstInput = modal.querySelector('.auth-modal__panel.--active input');
        if (firstInput) setTimeout(() => firstInput.focus(), 50);
    }

    // Открытие из шапки
    const openBtn = document.getElementById('open-auth-btn');
    if (openBtn) openBtn.addEventListener('click', () => openAuthModal('login'));

    // Открытие из любых кнопок с data-requires-auth (например, корзина гостя)
    document.querySelectorAll('[data-requires-auth]').forEach(el => {
        el.addEventListener('click', e => {
            if (window.__USER_AUTHENTICATED__) return; // глобальный флаг — выставляется в blade
            e.preventDefault();
            openAuthModal('login');
        });
    });

    // Закрытие
    if (closeBtn) closeBtn.addEventListener('click', closeAuthModal);
    if (overlay) overlay.addEventListener('click', closeAuthModal);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal && !modal.hasAttribute('hidden')) closeAuthModal();
    });

    // Переключение по табам
    tabs.forEach(t => t.addEventListener('click', () => switchPanel(t.dataset.tab)));

    // Переключение по ссылке-свитчу в подвале
    if (modal) {
        modal.querySelectorAll('.auth-modal__switch').forEach(s => {
            s.addEventListener('click', () => switchPanel(s.dataset.switch));
        });
    }

    // Если в URL ?auth=login или ?auth=register — открываем
    (() => {
        const params = new URLSearchParams(window.location.search);
        const want = params.get('auth');
        if (want === 'login' || want === 'register') openAuthModal(want);
    })();

    /* ============================================================
       4. TOGGLE ПАРОЛЯ (глаз)
       ============================================================ */
    document.querySelectorAll('[data-toggle-pass]').forEach(btn => {
        btn.addEventListener('click', () => {
            const wrap = btn.closest('.auth-field__wrap');
            if (!wrap) return;
            const input = wrap.querySelector('input');
            if (!input) return;
            const isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';
            btn.classList.toggle('--on', isPass);
        });
    });

    /* ============================================================
       5. ОЧИСТКА ОШИБКИ ПРИ ВВОДЕ
       ============================================================ */
    if (modal) {
        modal.querySelectorAll('input').forEach(inp => {
            inp.addEventListener('input', () => clearFieldError(inp));
            inp.addEventListener('change', () => clearFieldError(inp));
        });
    }

    /* ============================================================
       6. SUBMIT — клиентская валидация + AJAX
       ============================================================ */
    if (modal) {
        modal.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', async (e) => {
                e.preventDefault();
                clearAllErrors(form);

                const { data, errors, valid } = validateForm(form);

                if (!valid) {
                    Object.entries(errors).forEach(([name, msg]) => setFieldError(form, name, msg));
                    // Скроллим к первой ошибке
                    const firstErr = form.querySelector('.auth-field.--invalid');
                    if (firstErr) firstErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }

                await submitForm(form, data);
            });
        });
    }

    async function submitForm(form, data) {
        const submit = form.querySelector('.auth-modal__submit');
        if (submit) {
            submit.disabled = true;
            submit.classList.add('--loading');
        }

        try {
            const res = await fetch(form.action, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(data)
            });

            // Сессия истекла или CSRF протух
            if (res.status === 419) {
                setAlert(form, 'error', 'Грамота просрочена. Обновите страницу и попробуйте снова.');
                return;
            }

            const json = await safeJson(res);

            // Серверная валидация (Laravel вернёт 422)
            if (res.status === 422 && json && json.errors) {
                Object.entries(json.errors).forEach(([name, list]) => {
                    setFieldError(form, name, Array.isArray(list) ? list[0] : list);
                });
                if (json.message) setAlert(form, 'error', json.message);
                return;
            }

            // Прочие ошибки
            if (!res.ok) {
                setAlert(form, 'error', (json && json.message) || 'Что-то пошло не так. Попробуйте ещё раз.');
                return;
            }

            // Успех
            setAlert(form, 'success', (json && json.message) || 'Готово.');
            const redirect = (json && json.redirect) || null;
            // Небольшая пауза, чтобы пользователь увидел сообщение
            setTimeout(() => {
                if (redirect) window.location.href = redirect;
                else window.location.reload();
            }, 900);
        } catch (err) {
            console.error('[auth] submit failed:', err);
            setAlert(form, 'error', 'Связь оборвалась. Проверьте сеть и попробуйте снова.');
        } finally {
            if (submit) {
                submit.disabled = false;
                submit.classList.remove('--loading');
            }
        }
    }

    async function safeJson(res) {
        try { return await res.json(); } catch (e) { return null; }
    }

    /* ============================================================
       7. USER MENU — выпадашка авторизованного юзера
       ============================================================ */
    const userMenu = document.querySelector('[data-user-menu]');
    if (userMenu) {
        const btn = userMenu.querySelector('.user-menu__btn');
        const panel = userMenu.querySelector('.user-menu__panel');

        function closeMenu() {
            userMenu.classList.remove('--open');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }
        function toggleMenu() {
            const open = userMenu.classList.toggle('--open');
            if (btn) btn.setAttribute('aria-expanded', open ? 'true' : 'false');
        }

        if (btn) btn.addEventListener('click', e => { e.stopPropagation(); toggleMenu(); });

        // Закрытие по клику вне
        document.addEventListener('click', e => {
            if (!userMenu.contains(e.target)) closeMenu();
        });
        // Закрытие по Escape
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeMenu();
        });
        // Не закрывать при клике внутри панели (нужно для logout-формы)
        if (panel) panel.addEventListener('click', e => e.stopPropagation());
    }
})();
