<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ rtrim(url('/'), '/') }}">

    <title>@yield('title', 'Хозяйская') · Гостинецъ</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Marko+One&family=Spectral:ital,wght@0,400;0,600;1,400&family=JetBrains+Mono:wght@400;500;600;700&family=Caveat:wght@400;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body class="admin">

    {{-- БОКОВАЯ ПАНЕЛЬ --}}
    <aside class="admin-side" id="admin-side">
        <div class="admin-side__brand">
            <a href="{{ route('admin.dashboard') }}" class="admin-side__logo">
                <span class="admin-side__logo-mark">Г</span>
                <span class="admin-side__logo-text">
                    <strong>Хозяйская</strong>
                    <small>Гостинецъ</small>
                </span>
            </a>
            <button type="button" class="admin-side__close" id="admin-side-close"
                    aria-label="Закрыть меню">×</button>
        </div>

        <nav class="admin-nav" aria-label="Навигация">
            @php $r = request()->route()?->getName(); @endphp

            <a href="{{ route('admin.dashboard') }}"
               class="admin-nav__item {{ $r === 'admin.dashboard' ? '--active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 12L12 3l9 9M5 10v10h14V10"/>
                </svg>
                <span>Сводка</span>
            </a>

            <a href="{{ route('admin.orders.index') }}"
               class="admin-nav__item {{ str_starts_with($r ?? '', 'admin.orders') ? '--active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M6 2L4 6v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V6l-2-4z M4 6h16 M10 12h4"/>
                </svg>
                <span>Заказы</span>
                @isset($adminBadges['orders_pending'])
                    @if($adminBadges['orders_pending'] > 0)
                        <em class="admin-nav__badge">{{ $adminBadges['orders_pending'] }}</em>
                    @endif
                @endisset
            </a>

            <a href="{{ route('admin.products.index') }}"
               class="admin-nav__item {{ str_starts_with($r ?? '', 'admin.products') ? '--active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M9 7 L15 7 L14 19 Q14 21 12 21 L12 21 Q10 21 10 19 Z M8 7 L16 7 M11 4 L11 2 L13 2 L13 4"/>
                </svg>
                <span>Кладовая</span>
            </a>

            <a href="{{ route('admin.custom-jams.index') }}"
               class="admin-nav__item {{ str_starts_with($r ?? '', 'admin.custom-jams') ? '--active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 11h14l-1 8q0 2-2 2H8q-2 0-2-2zM3 11h18M10 7q1-3 2 0M14 6q1-3 2 0"/>
                </svg>
                <span>Котёл</span>
                @isset($adminBadges['customs_pending'])
                    @if($adminBadges['customs_pending'] > 0)
                        <em class="admin-nav__badge">{{ $adminBadges['customs_pending'] }}</em>
                    @endif
                @endisset
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="admin-nav__item {{ str_starts_with($r ?? '', 'admin.users') ? '--active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M4 21v-1a8 8 0 0 1 16 0v1"/>
                </svg>
                <span>Гости</span>
            </a>
        </nav>

        <div class="admin-side__foot">
            <a href="{{ route('home') }}" class="admin-side__leave">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M15 6l-6 6 6 6"/>
                </svg>
                к витрине
            </a>
        </div>
    </aside>

    {{-- ОСНОВНОЙ КОНТЕНТ --}}
    <main class="admin-main">
        <header class="admin-top">
            <button type="button" class="admin-top__burger" id="admin-side-open"
                    aria-label="Открыть меню">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <h1 class="admin-top__title">@yield('heading', 'Сводка')</h1>

            <div class="admin-top__user">
                @auth
                    <span class="admin-top__user-name">{{ auth()->user()->name }}</span>
                    <form action="{{ route('logout') }}" method="POST" class="admin-top__logout-form">
                        @csrf
                        <button type="submit" class="admin-top__logout" title="Затворить избу">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4 M16 17l5-5-5-5 M21 12H9"/>
                            </svg>
                        </button>
                    </form>
                @endauth
            </div>
        </header>

        @if(session('flash'))
            <div class="admin-flash admin-flash--ok">{{ session('flash') }}</div>
        @endif
        @if(session('flash-error'))
            <div class="admin-flash admin-flash--err">{{ session('flash-error') }}</div>
        @endif

        <div class="admin-content">
            @yield('content')
        </div>
    </main>

    <div class="admin-side-overlay" id="admin-side-overlay"></div>

    <script src="{{ asset('js/admin.js') }}" defer></script>
</body>
</html>