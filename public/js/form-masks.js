/* ===========================================================
   ГОСТИНЕЦЪ — form-masks.js
   Маски и валидация для форм регистрации, входа, checkout
   =========================================================== */
(function () {
    'use strict';

    /* === ТЕЛЕФОН === Маска: +7 (XXX) XXX-XX-XX */
    function maskPhone(input) {
        input.addEventListener('input', () => {
            // Берём только цифры
            let digits = input.value.replace(/\D/g, '');

            // Если начинается с 8 — заменяем на 7
            if (digits.startsWith('8')) digits = '7' + digits.slice(1);
            // Если не начинается с 7 — добавляем
            if (!digits.startsWith('7')) digits = '7' + digits;
            // Не больше 11 цифр
            digits = digits.slice(0, 11);

            // Форматируем
            let result = '+7';
            if (digits.length > 1) result += ' (' + digits.slice(1, 4);
            if (digits.length >= 5) result += ') ' + digits.slice(4, 7);
            if (digits.length >= 8) result += '-' + digits.slice(7, 9);
            if (digits.length >= 10) result += '-' + digits.slice(9, 11);

            input.value = result;
        });

        // При фокусе подставляем "+7 (" если пусто
        input.addEventListener('focus', () => {
            if (!input.value || input.value === '+7') {
                input.value = '+7 (';
            }
        });

        // На blur — если в поле только префикс, очищаем
        input.addEventListener('blur', () => {
            if (input.value.replace(/\D/g, '').length < 2) {
                input.value = '';
            }
        });
    }

    /* === EMAIL — валидация в реальном времени === */
    function validateEmail(input) {
        const errEl = input.parentElement?.querySelector('.field-error') || createErrorEl(input);

        function check() {
            const v = input.value.trim();
            if (!v) {
                errEl.textContent = '';
                input.classList.remove('--invalid', '--valid');
                return;
            }
            const ok = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/.test(v);
            if (ok) {
                input.classList.add('--valid');
                input.classList.remove('--invalid');
                errEl.textContent = '';
            } else {
                input.classList.add('--invalid');
                input.classList.remove('--valid');
                errEl.textContent = 'Похоже на неправильный адрес';
            }
        }

        input.addEventListener('blur', check);
        input.addEventListener('input', () => {
            if (input.classList.contains('--invalid')) check();
        });
    }

    /* === ИНДЕКС === Маска: 6 цифр */
    function maskZip(input) {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '').slice(0, 6);
        });
    }

    /* === ИМЯ === Только буквы, пробел, дефис */
    function validateName(input) {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/[^a-zA-Zа-яА-ЯёЁ\s\-]/g, '');
        });
    }

    /* === ПАРОЛЬ — индикатор силы === */
    function passwordStrength(input) {
        const indicator = input.parentElement?.querySelector('.password-strength');
        if (!indicator) return;

        input.addEventListener('input', () => {
            const v = input.value;
            let score = 0;
            if (v.length >= 6) score++;
            if (v.length >= 10) score++;
            if (/[a-zA-Zа-яА-Я]/.test(v)) score++;
            if (/\d/.test(v)) score++;
            if (/[^a-zA-Z0-9а-яА-Я]/.test(v)) score++;

            indicator.className = 'password-strength password-strength--' + score;
            indicator.textContent = ['', 'слабый', 'не очень', 'нормально', 'хорошо', 'крепко'][score];
        });
    }

    /* === Вспомогательно === */
    function createErrorEl(input) {
        const el = document.createElement('span');
        el.className = 'field-error';
        input.parentElement?.appendChild(el);
        return el;
    }

    /* === ПРИМЕНЯЕМ === */
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('input[type="tel"], input[name="phone"], input[name="contact_phone"]').forEach(maskPhone);
        document.querySelectorAll('input[type="email"]').forEach(validateEmail);
        document.querySelectorAll('input[name="delivery_zip"], input[name="zip"]').forEach(maskZip);
        document.querySelectorAll('input[name="name"], input[name="contact_name"]').forEach(validateName);
        document.querySelectorAll('input[name="password"]:not([data-no-strength])').forEach(passwordStrength);
    });

})();