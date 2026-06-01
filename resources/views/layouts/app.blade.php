<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ rtrim(url('/'), '/') }}">
    <meta name="description" content="Варенье ручной варки от молодой Яги — натуральные ягоды, медный таз, без консервантов. Доставка по РФ.">

    {{-- OG-теги для соцсетей (рекомендация продакшена) --}}
    <meta property="og:title" content="Гостинецъ — варенье ручной варки">
    <meta property="og:description" content="Банка варенья, в которой спит лето, сад, костёр и тёплый вечер. 23 сорта, ручная варка, доставка по РФ.">
    <meta property="og:image" content="{{ asset('media/og-image.png') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ru_RU">

    <title>Гостинецъ — варенье ручной варки</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marko+One&family=Yeseva+One&family=Spectral:ital,wght@0,400;0,500;0,600;1,400&family=Caveat:wght@500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <link rel="shortcut icon" href="{{ asset('media/favicon.png') }}" type="image/x-icon">
</head>

<body class="grain" id="top">

    <div class="site-top">

        {{-- Верхняя плашка с локацией и навигацией соцсетей --}}
        <div class="top-bar">
            <div class="container top-bar__inner">
                <div class="top-bar__loc">
                    <svg width="12" height="14" viewBox="0 0 12 14" fill="none" aria-hidden="true">
                        <path d="M6 1 C9 1 11 3 11 6 C11 9 6 13 6 13 C6 13 1 9 1 6 C1 3 3 1 6 1 Z" stroke="currentColor" stroke-width="1.2" />
                        <circle cx="6" cy="6" r="2" stroke="currentColor" stroke-width="1.2" />
                    </svg>
                    <span class="top-bar__loc-text">
                        Доставка: <strong id="user-city">По России</strong>
                    </span>
                    <button type="button" class="top-bar__city-btn" id="change-city-btn">
                        Изменить способ доставки
                    </button>
                </div>
                <nav class="top-bar__nav" aria-label="Социальные сети">
                    <a class="live" href="#">Огонь горит · сегодня варится клубника</a>
                    <a href="https://web.max.ru/">Mакс</a>
                    <a href="https://vk.com/club239216674">ВК</a>
                    <a href="https://yandex.ru/chat#/">Яндекс</a>
                </nav>
            </div>
        </div>

        {{-- Основная шапка --}}
        <header class="header">
            <div class="container header__inner">
                <a class="logo" href="{{ route('home') }}" aria-label="Гостинецъ — на главную">
                    <div class="logo__mark">
                        <img src="{{ asset('media/logo.png') }}" alt="logo">
                    </div>
                    <div class="logo__text">
                        <span class="logo__name">Гостинецъ</span>
                        <span class="logo__tag">Варенье ручной варки</span>
                    </div>
                </a>

                <form class="search" role="search" method="GET" action="{{ route('catalog') }}">
                    <svg class="search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <circle cx="11" cy="11" r="6" />
                        <path d="m20 20-4-4" />
                    </svg>
                    <input type="text" name="q" value="{{ request('q') }}"
                        placeholder="Что ищешь, добрый гость? Варенье на зиму, гостинец к чаю..."
                        aria-label="Поиск по кладовой">
                    <button type="submit" class="search__btn">
                        <span>Искать</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </button>
                </form>

                <!-- <div class="header__actions">
                    <button type="button" class="action" aria-label="Корзина">
                        <span class="action__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h2l2 12h11l2-9H6" />
                                <circle cx="9" cy="20" r="1.4" />
                                <circle cx="17" cy="20" r="1.4" />
                            </svg>
                            <span class="action__count">{{ session('cart_count', 3) }}</span>
                        </span>
                        <span class="action__label">Запасы</span>
                    </button>
                    <button type="button" class="action" aria-label="Заказы">
                        <span class="action__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 4h11l3 3v13H5z" />
                                <path d="M9 10h6M9 14h6M9 18h4" />
                            </svg>
                            <span class="action__count">{{ auth()->user()?->orders_count ?? 2 }}</span>
                        </span>
                        <span class="action__label">Заказы</span>
                    </button>
                    <button type="button" class="action" aria-label="Войти">
                        <span class="action__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="9" r="4" />
                                <path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6" />
                            </svg>
                        </span>
                        <span class="action__label">Войти</span>
                    </button>
                </div> -->
                <div class="header__actions">
                    {{-- Корзина в шапке — ссылка на /cart, счётчик из сессии --}}
                    <a href="{{ route('cart') }}" class="action" aria-label="Корзина">
                        <span class="action__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M3 5h2l2 12h11l2-9H6" />
                                <circle cx="9" cy="20" r="1.4" />
                                <circle cx="17" cy="20" r="1.4" />
                            </svg>
                            <span class="action__count">{{ app(\App\Services\CartService::class)->count() }}</span>
                        </span>
                        <span class="action__label">Мешочек</span>
                    </a>

                    @auth
                    {{-- Заказы — только авторизованным --}}
                    <a href="{{ route('orders') }}" class="action" aria-label="Заказы">
                        <span class="action__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 4h11l3 3v13H5z" />
                                <path d="M9 10h6M9 14h6M9 18h4" />
                            </svg>

                        </span>
                        <span class="action__label">Заказы</span>
                    </a>

                    {{-- Меню пользователя --}}
                    <div class="user-menu" data-user-menu>
                        <button type="button" class="action user-menu__btn" aria-haspopup="menu" aria-expanded="false">
                            <span class="action__icon">
                                @if(auth()->user()->isAdmin())
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 2 L14.5 8 L21 8.5 L16 13 L17.5 20 L12 16.5 L6.5 20 L8 13 L3 8.5 L9.5 8 Z" />
                                </svg>
                                @else
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                    stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="9" r="4" />
                                    <path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6" />
                                </svg>
                                @endif
                            </span>
                            <span class="action__label">{{ \Illuminate\Support\Str::limit(auth()->user()->name, 10, '…') }}</span>
                        </button>

                        <div class="user-menu__panel" role="menu">
                            <div class="user-menu__head">
                                <div class="user-menu__name">{{ auth()->user()->name }}</div>
                                <div class="user-menu__email">{{ auth()->user()->email }}</div>
                                @if(auth()->user()->isAdmin())
                                <span class="user-menu__badge">хозяйка котла</span>
                                @else
                                <span class="user-menu__badge --soft">добрый гость</span>
                                @endif
                            </div>

                            <a href="{{ route('profile') }}" class="user-menu__item" role="menuitem">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <circle cx="12" cy="9" r="4" />
                                    <path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6" />
                                </svg>
                                Личное
                            </a>
                            <a href="{{ route('orders') }}" class="user-menu__item" role="menuitem">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M5 4h11l3 3v13H5z" />
                                    <path d="M9 10h6M9 14h6" />
                                </svg>
                                Мои заказы
                            </a>

                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="user-menu__item --admin" role="menuitem">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M12 2 L14.5 8 L21 8.5 L16 13 L17.5 20 L12 16.5 L6.5 20 L8 13 L3 8.5 L9.5 8 Z" />
                                </svg>
                                Управление котлом
                            </a>
                            @endif

                            <form method="POST" action="{{ route('logout') }}" class="user-menu__logout-form">
                                @csrf
                                <button type="submit" class="user-menu__item --danger" role="menuitem">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                        stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                                        <path d="M16 17l5-5-5-5" />
                                        <path d="M21 12H9" />
                                    </svg>
                                    Затворить избу
                                </button>
                            </form>
                        </div>
                    </div>
                    @else
                    {{-- Гость — кнопка открывает auth-modal --}}
                    <button type="button" class="action" id="open-auth-btn" aria-label="Войти">
                        <span class="action__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="12" cy="9" r="4" />
                                <path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6" />
                            </svg>
                        </span>
                        <span class="action__label">Войти</span>
                    </button>
                    @endauth
                </div>
            </div>
        </header>
    </div>

    {{-- ====================== МОДАЛКА ВЫБОРА СПОСОБА ДОСТАВКИ ====================== --}}
    <div class="delivery-modal" id="delivery-modal" hidden role="dialog" aria-modal="true" aria-labelledby="delivery-modal-title">
        <div class="delivery-modal__overlay" id="delivery-modal-overlay"></div>
        <div class="delivery-modal__inner">
            <button type="button" class="delivery-modal__close" id="delivery-modal-close" aria-label="Закрыть">×</button>

            <h3 id="delivery-modal-title">Куда нести гостинец?</h3>
            <p class="delivery-modal__hint">Выбери, как удобнее. Это сохранится — при оформлении заказа подставим автоматически.</p>

            <div class="delivery-modal__list">
                {{-- 1. По России --}}
                <button type="button" class="delivery-option" data-mode="russia" data-method="cdek">
                    <div class="delivery-option__head">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 7l9-4 9 4M3 7v10l9 4 9-4V7M3 7l9 4M21 7l-9 4M12 11v10" />
                        </svg>
                        <div>
                            <strong>По России</strong>
                            <span>СДЭК или Почта России · 350–250 ₽</span>
                        </div>
                    </div>
                    <div class="delivery-option__sub">
                        <label class="delivery-suboption">
                            <input type="radio" name="russia-method" value="cdek" checked>
                            <span><strong>СДЭК</strong> — 350 ₽ · 3–7 дней</span>
                        </label>
                        <label class="delivery-suboption">
                            <input type="radio" name="russia-method" value="post">
                            <span><strong>Почта России</strong> — 250 ₽ · 7–21 день</span>
                        </label>
                    </div>
                </button>

                {{-- 2. Самовывоз Казань --}}
                <button type="button" class="delivery-option" data-mode="pickup">
                    <div class="delivery-option__head">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                        <div>
                            <strong>Самовывоз</strong>
                            <span>Казань · бесплатно</span>
                        </div>
                    </div>
                    <div class="delivery-option__sub">
                        <p class="delivery-suboption__text">
                            Адрес: г. Казань, ул. Мавлютова, д. 15. Согласуем время по телефону после оплаты.
                        </p>
                    </div>
                </button>

                {{-- 3. В подарок --}}
                <button type="button" class="delivery-option" data-mode="gift" data-method="cdek">
                    <div class="delivery-option__head">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M20 12v10H4V12M2 7h20v5H2zM12 22V7M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7" />
                        </svg>
                        <div>
                            <strong>В подарок</strong>
                            <span>Доставка или самовывоз — на выбор</span>
                        </div>
                    </div>
                    <div class="delivery-option__sub">
                        <label class="delivery-suboption">
                            <input type="radio" name="gift-method" value="cdek" checked>
                            <span><strong>СДЭК</strong> — 350 ₽</span>
                        </label>
                        <label class="delivery-suboption">
                            <input type="radio" name="gift-method" value="post">
                            <span><strong>Почта России</strong> — 250 ₽</span>
                        </label>
                        <label class="delivery-suboption">
                            <input type="radio" name="gift-method" value="pickup">
                            <span><strong>Самовывоз</strong> — Казань, бесплатно</span>
                        </label>
                    </div>
                </button>
            </div>

            <button type="button" class="delivery-modal__save" id="delivery-modal-save">
                Сохранить выбор
            </button>
        </div>
    </div>

    {{-- CITY MODAL --}}
    <!-- <div class="city-modal" id="city-modal" hidden role="dialog" aria-modal="true" aria-labelledby="city-modal-title">
        <div class="city-modal__overlay" id="city-modal-overlay"></div>
        <div class="city-modal__inner">
            <button type="button" class="city-modal__close" id="city-modal-close" aria-label="Закрыть">×</button>
            <h3 id="city-modal-title">Выберите ваш город</h3>
            <p>Это поможет рассчитать стоимость и сроки доставки.</p>
            <div class="city-modal__list">
                <button type="button" class="city-modal__item" data-city="Казань">
                    Казань <small>по умолчанию</small>
                </button>
                <button type="button" class="city-modal__item" data-city="Москва">Москва</button>
                <button type="button" class="city-modal__item" data-city="Санкт-Петербург">Санкт-Петербург</button>
                <button type="button" class="city-modal__item" data-city="Tula">Тула</button>
                <button type="button" class="city-modal__item" data-city="Нижний Новгород">Нижний Новгород</button>
                <button type="button" class="city-modal__item" data-city="Екатеринбург">Екатеринбург</button>
                <button type="button" class="city-modal__item" data-city="Новосибирск">Новосибирск</button>
                <button type="button" class="city-modal__item" data-city="Самара">Самара</button>
                <button type="button" class="city-modal__item" data-city="Уфа">Уфа</button>
                <button type="button" class="city-modal__item" data-city="Краснодар">Краснодар</button>
            </div>
            <div class="city-modal__form">
                <input type="text" class="city-modal__input" id="city-modal-input"
                    placeholder="Или введите ваш город..." aria-label="Свой город">
                <button type="button" class="city-modal__save" id="city-modal-save">Сохранить</button>
            </div>
        </div>
    </div> -->

    @yield('content')

    {{-- FOOTER --}}
    <footer class="footer" id="contacts">
        <svg class="footer__bg" viewBox="0 0 1400 600" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <g stroke="#FFFAF0" stroke-width="1" fill="none">
                @for ($i = 0; $i
                < 10; $i++)
                    <circle cx="{{ $i * 180 }}" cy="300" r="120" />
                @endfor
            </g>
        </svg>
        <div class="container">
            <div class="footer__inner">
                <div class="footer__col footer__brand">
                    <a class="logo" href="#top">
                        <div class="logo__mark">
                            <img src="{{ asset('media/logo-light.png') }}" alt="logo">
                        </div>
                        <div class="logo__text">
                            <span class="logo__name">Гостинецъ</span>
                            <span class="logo__tag">Варенье ручной варки</span>
                        </div>
                    </a>
                    <p>Семейная варня в Татарстане. Варим малыми партиями, с уважением к ягоде и тому, кто её ест.</p>
                    <div class="footer__contact">
                        <span>тел.</span>
                        <a href="tel:+78123090934">+7 (812) 309-09-34</a>
                        <a href="tel:+79650842909">+7 (965) 084-29-09</a>
                        <span>почта</span>
                        <a href="mailto:gost@gostinec.ru">gost@gostinec.ru</a>
                    </div>
                </div>

                <div class="footer__col">
                    <h4>Тропинки</h4>
                    <nav class="footer__nav">
                        <a href="{{ route('home') }}#banner">Начало</a>
                        <a href="{{ route('catalog') }}">Закрома</a>
                        <a href="{{ route('home') }}#about">Сказ хозяйки</a>
                        <a href="{{ route('home') }}#questions">Вопросы</a>
                        <a href="{{ route('home') }}#oracle">Оракул ягод</a>
                        <a href="{{ route('catalog') }}">Обрести банку</a>
                    </nav>
                </div>

                <div class="footer__col">
                    <h4>В социальном лесу</h4>
                    <nav class="footer__nav">
                        <a href="https://web.max.ru/">Макс · @gostinets</a>
                        <a href="https://vk.com/club239216674">ВК · gostinets.jam</a>
                        <a href="https://yandex.ru/chat#/">Яндекс · @gostinets</a>
                        <a href="https://rutube.ru/">RuTube · сказы из варни</a>
                    </nav>

                    {{-- ===== ПОДПИСКА В ФУТЕРЕ ===== --}}
                    <!-- <div class="newsletter" id="newsletter-block">
                        <h3>Первый заказ?</h3>
                        <p>Подпишись — пришлём весточку о новых сортах, ярмарках и сезонных скидках.</p>
                        <form class="newsletter__form" id="subscribe-form" novalidate>
                            @csrf
                            <input type="email"
                                name="email"
                                id="subscribe-email"
                                placeholder="Введите email..."
                                aria-label="Email для рассылки"
                                autocomplete="email"
                                required>
                            <button type="submit" id="subscribe-btn">Подписаться</button>
                        </form>

                        <label class="newsletter__consent">
                            <input type="checkbox" id="subscribe-consent" required>
                            <span>Согласен на обработку персональных данных</span>
                        </label>

                        <p class="newsletter__result" id="subscribe-result" hidden></p>
                    </div> -->
                </div>
            </div>

            <div class="footer__bottom">
                <span>© 2025–2026 · Гостинецъ</span>
                <div class="footer__bottom-nav">
                    <a href="{{ route('legal.privacy') }}">Политика конфиденциальности</a>
                    <a href="{{ route('legal.oferta') }}">Договор оферты</a>
                </div>
                <!-- <span>дипломный проект · Пирогова Д. Д.</span> -->
            </div>
        </div>
    </footer>

    {{-- Кнопка "наверх" --}}
    <button type="button" class="scroll-top" aria-label="Наверх">↑</button>

    <div class="auth-modal" id="auth-modal" hidden role="dialog" aria-modal="true" aria-labelledby="auth-modal-title">
        <div class="auth-modal__overlay" id="auth-modal-overlay"></div>

        <div class="auth-modal__inner">
            <button type="button" class="auth-modal__close" id="auth-modal-close" aria-label="Закрыть">×</button>

            {{-- Табы --}}
            <div class="auth-modal__tabs" role="tablist" id="auth-modal-title">
                <button type="button" class="auth-modal__tab --active" data-tab="login" role="tab" aria-selected="true">
                    Войти в избу
                </button>
                <button type="button" class="auth-modal__tab" data-tab="register" role="tab" aria-selected="false">
                    Зарегистрироваться
                </button>
            </div>

            {{-- ============= ЛОГИН ============= --}}
            <form class="auth-modal__panel --active"
                id="auth-login-form"
                data-panel="login"
                method="POST"
                action="{{ route('login') }}"
                novalidate>
                @csrf
                <div class="auth-modal__head">
                    <h3>В избу — добрый гость</h3>
                    <p>введи тайное слово — и кладовая откроется</p>
                </div>

                <label class="auth-field">
                    <span class="auth-field__label">Электронная почта</span>
                    <input type="email" name="email" class="auth-field__input"
                        autocomplete="email" autocapitalize="none" spellcheck="false" required>
                    <span class="auth-field__error" data-error="email"></span>
                </label>

                <label class="auth-field">
                    <span class="auth-field__label">Тайное слово</span>
                    <span class="auth-field__wrap">
                        <input type="password" name="password" class="auth-field__input"
                            autocomplete="current-password" required>
                        <button type="button" class="auth-field__eye" data-toggle-pass aria-label="Показать пароль">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </span>
                    <span class="auth-field__error" data-error="password"></span>
                </label>

                <label class="auth-field --check">
                    <input type="checkbox" name="remember" value="1">
                    <span>Запомнить — не спрашивай каждый раз</span>
                </label>

                <button type="submit" class="auth-modal__submit">
                    <span class="auth-modal__submit-text">Войти в избу</span>
                    <svg class="auth-modal__submit-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                    <span class="auth-modal__spinner" aria-hidden="true"></span>
                </button>

                <div class="auth-modal__alert" data-alert role="alert"></div>

                <div class="auth-modal__footer">
                    Впервые здесь?
                    <button type="button" class="auth-modal__switch" data-switch="register">
                        Завести грамоту →
                    </button>
                </div>
            </form>

            {{-- ============= РЕГИСТРАЦИЯ ============= --}}
            <form class="auth-modal__panel"
                id="auth-register-form"
                data-panel="register"
                method="POST"
                action="{{ route('register') }}"
                novalidate>
                @csrf
                <div class="auth-modal__head">
                    <h3>Гостевая грамота</h3>
                    <p>впиши имя в книгу — и Мария приготовит банку</p>
                </div>

                <label class="auth-field">
                    <span class="auth-field__label">Как звать?</span>
                    <input type="text" name="name" class="auth-field__input"
                        autocomplete="name" maxlength="60" required>
                    <span class="auth-field__error" data-error="name"></span>
                </label>

                <label class="auth-field">
                    <span class="auth-field__label">Электронная почта</span>
                    <input type="email" name="email" class="auth-field__input"
                        autocomplete="email" autocapitalize="none" spellcheck="false" required>
                    <span class="auth-field__error" data-error="email"></span>
                </label>

                <label class="auth-field">
                    <span class="auth-field__label">Тайное слово</span>
                    <span class="auth-field__wrap">
                        <input type="password" name="password" class="auth-field__input"
                            autocomplete="new-password" required>
                        <button type="button" class="auth-field__eye" data-toggle-pass aria-label="Показать пароль">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"
                                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </span>
                    <span class="auth-field__hint">не короче 6 знаков, с буквой и цифрой</span>
                    <span class="auth-field__error" data-error="password"></span>
                </label>

                <label class="auth-field">
                    <span class="auth-field__label">Повтори тайное слово</span>
                    <input type="password" name="password_confirmation" class="auth-field__input"
                        autocomplete="new-password" required>
                    <span class="auth-field__error" data-error="password_confirmation"></span>
                </label>

                <label class="auth-field --check">
                    <input type="checkbox" name="agree" value="1" required>
                    <span>Согласен на обработку личных данных</span>
                </label>

                <button type="submit" class="auth-modal__submit">
                    <span class="auth-modal__submit-text">Завести грамоту</span>
                    <svg class="auth-modal__submit-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                    <span class="auth-modal__spinner" aria-hidden="true"></span>
                </button>

                <div class="auth-modal__alert" data-alert role="alert"></div>

                <div class="auth-modal__footer">
                    Уже бывали?
                    <button type="button" class="auth-modal__switch" data-switch="login">
                        Войти →
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('js/home.js') }}" defer></script>
    <script src="{{ asset('js/auth.js') }}" defer></script>
    <script src="{{ asset('js/form-masks.js') }}" defer></script>
    {{-- Корзина: глобальный обработчик [data-add-to-cart] — работает на ВСЕХ страницах --}}
    <script src="{{ asset('js/cart-actions.js') }}" defer></script>
    <script src="{{ asset('js/delivery-modal.js') }}" defer></script>
</body>

</html>