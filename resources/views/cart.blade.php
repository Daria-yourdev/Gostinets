@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cart.css') }}">

<div class="cart-page">
    <div class="container">

        <nav class="breadcrumbs" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Начало</a>
            <span aria-hidden="true">·</span>
            <a href="{{ route('catalog') }}">Кладовая</a>
            <span aria-hidden="true">·</span>
            <span class="breadcrumbs__current">Запасы</span>
        </nav>

        <header class="cart-head">
            <h1>
                <span class="cart-head__title">Запасы</span>
                <span class="cart-head__sub">
                    @if($count > 0)
                    в мешочке {{ $count }} {{ plural($count, 'банка', 'банки', 'банок') }}
                    @else
                    пока ничего нет
                    @endif
                </span>
            </h1>
        </header>

        {{-- Flash от middleware (например, после редиректа из checkout) --}}
        @if(session('flash'))
        <div class="cart-flash">{{ session('flash') }}</div>
        @endif

        @if($items->isEmpty() && (!isset($customItems) || $customItems->isEmpty()))
        {{-- ============ ПУСТАЯ КОРЗИНА ============ --}}
        <div class="cart-empty">
            <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="1.5"
                class="cart-empty__svg" aria-hidden="true">
                <path d="M22 28 L58 28 L54 64 Q54 70 48 70 L32 70 Q26 70 26 64 Z" />
                <path d="M18 28 L62 28" stroke-width="2.5" />
                <path d="M30 22 L30 16 Q30 10 36 10 L44 10 Q50 10 50 16 L50 22" />
            </svg>
            <h2>Мешочек пуст</h2>
            <p>
                Зайди в кладовую — там <br>
                @php $total = \App\Models\Product::active()->count(); @endphp
                {{ $total }} {{ plural($total, 'банка', 'банки', 'банок') }} ждут гостя.
            </p>
            <div class="cart-empty__actions">
                <a href="{{ route('catalog') }}" class="btn-primary">
                    <span>В кладовую</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </a>
                <a href="{{ route('cauldron') }}" class="btn-ghost">
                    Или свари своё в котле
                </a>
            </div>
        </div>
        @else
        {{-- ============ ЕСТЬ ПОЗИЦИИ ============ --}}
        <div class="cart-body">

            {{-- Левая: список банок --}}
            <div class="cart-list">
                @foreach($items as $row)
                @php
                $p = $row['product'];
                $qty = $row['qty'];
                $sub = $row['subtotal'];
                @endphp
                <article class="cart-item" data-product-id="{{ $p->id }}"
                    style="--jam: {{ $p->jamColor() }}">

                    <a href="{{ route('product', $p->slug) }}" class="cart-item__visual">
                        <!-- <div class="cart-item__jambar" aria-hidden="true"></div> -->
                        <img src="{{ asset($p->image_path ?: 'media/catalog/catalog-card-1.png') }}"
                            alt="{{ $p->name }}" loading="lazy">
                    </a>

                    <div class="cart-item__info">
                        <a href="{{ route('product', $p->slug) }}" class="cart-item__name-link">
                            <h3 class="cart-item__name">{{ $p->name }}</h3>
                            @if($p->subtitle)
                            <p class="cart-item__subtitle">{{ $p->subtitle }}</p>
                            @endif
                        </a>
                        <div class="cart-item__meta">
                            <span>{{ $p->moodLabel() }}</span>
                            <span aria-hidden="true">·</span>
                            <span>{{ $p->weight }} г</span>
                            @if($p->is_sugar_free)
                            <span aria-hidden="true">·</span>
                            <span>без сахара</span>
                            @endif
                        </div>
                    </div>

                    <div class="cart-item__qty" data-cart-qty="{{ $p->id }}">
                        <button type="button" class="cart-item__qty-btn"
                            data-cart-action="minus"
                            aria-label="Уменьшить">−</button>
                        <output class="cart-item__qty-value" data-qty-value>{{ $qty }}</output>
                        <button type="button" class="cart-item__qty-btn"
                            data-cart-action="plus"
                            aria-label="Увеличить">+</button>
                    </div>

                    <div class="cart-item__price" data-cart-subtotal>
                        <span class="cart-item__price-each">{{ $p->priceFormatted() }} × {{ $qty }}</span>
                        <span class="cart-item__price-sum">{{ number_format($sub, 0, '.', ' ') }} ₽</span>
                    </div>

                    <button type="button" class="cart-item__remove"
                        data-cart-action="remove"
                        aria-label="Снять со стола" title="Снять со стола">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 6L18 18M18 6L6 18" />
                        </svg>
                    </button>
                </article>
                @endforeach

                {{-- Из котла: кастомные варенья --}}
                @if(isset($customItems) && $customItems->count() > 0)
                <div class="cart-list cart-list--customs">
                    <h3 class="cart-list__section-title">Из котла</h3>
                    @foreach($customItems as $row)
                    @php $jam = $row['custom']; @endphp
                    <article class="cart-item cart-item--custom" data-custom-id="{{ $jam->id }}"
                        style="--jam: var(--burgundy)">

                        <div class="cart-item__visual cart-item__visual--custom">
                            <!-- <div class="cart-item__jambar" aria-hidden="true"></div> -->
                            <img src="{{ asset('media/cotel/kastom_card.jpg') }}"
                                alt="" loading="lazy">
                        </div>

                        <div class="cart-item__info">
                            <h3 class="cart-item__name">«{{ $jam->label_name ?: 'Своё варенье' }}»</h3>
                            <p class="cart-item__subtitle">{{ $jam->berry_main }}</p>
                            <div class="cart-item__meta">
                                <span>{{ $jam->jar_size }} мл</span>
                                @php
                                $extras = is_array($jam->berry_extras)
                                ? $jam->berry_extras
                                : json_decode($jam->berry_extras ?? '[]', true);
                                @endphp
                                @if(!empty($extras))
                                <span aria-hidden="true">·</span>
                                <span>+ {{ implode(', ', $extras) }}</span>
                                @endif
                            </div>
                        </div>

                        {{-- Кастом нельзя изменить по qty — только удалить --}}
                        <div class="cart-item__qty-placeholder">× 1</div>

                        <div class="cart-item__price">
                            <span class="cart-item__price-each">1 котёл</span>
                            <span class="cart-item__price-sum">{{ number_format($jam->price, 0, '.', ' ') }} ₽</span>
                        </div>

                        <button type="button" class="cart-item__remove"
                            data-cart-action="remove-custom"
                            aria-label="Убрать из мешочка" title="Убрать">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M6 6L18 18M18 6L6 18" />
                            </svg>
                        </button>
                    </article>
                    @endforeach
                </div>
                @endif
            </div>



            {{-- Правая: сводка и кнопка checkout --}}
            <aside class="cart-summary" id="cart-summary">
                <h2 class="cart-summary__title">Что выходит</h2>

                <div class="cart-summary__row">
                    <span>За банки</span>
                    <strong data-summary-subtotal>{{ number_format($subtotal, 0, '.', ' ') }} ₽</strong>
                </div>

                <div class="cart-summary__row cart-summary__row--hint">
                    <span>Доставка</span>
                    <em>
                        @if($subtotal >= 3000)
                        бесплатно (от 3 000 ₽)
                        @else
                        посчитаем дальше
                        @endif
                    </em>
                </div>

                <hr class="cart-summary__divider">

                <div class="cart-summary__total">
                    <span>Итого</span>
                    <strong data-summary-total>{{ number_format($subtotal, 0, '.', ' ') }} ₽</strong>
                </div>

                @if($subtotal < 3000)
                    <p class="cart-summary__progress-note">
                    До бесплатной доставки осталось <strong>{{ 3000 - $subtotal }} ₽</strong>
                    </p>
                    <div class="cart-summary__progress" aria-hidden="true">
                        <div class="cart-summary__progress-fill"
                            style="width: {{ min(100, round($subtotal / 30)) }}%"></div>
                    </div>
                    @endif

                    @auth
                    <a href="{{ route('checkout') }}" class="btn-primary cart-checkout-btn">
                        <span>Унести с собой</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </a>
                    @else
                    <button type="button" class="btn-primary cart-checkout-btn" data-requires-auth>
                        <span>Войти и оформить</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </button>
                    <p style="font-family: var(--serif); font-style: italic; font-size: 13px; color: var(--ink-2); margin: 8px 0 0; text-align: center;">
                        Заказ оформляется только для гостей из книги
                    </p>
                    @endauth

                    <a href="{{ route('catalog') }}" class="cart-summary__continue">
                        ← вернуться к выбору
                    </a>

                    <div class="cart-summary__safety">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <path d="M9 12l2 2 4-4" />
                        </svg>
                        <span>Оплата через ЮКассу — банковской картой, безопасно</span>
                    </div>
            </aside>
        </div>
        @endif

    </div>
</div>

<script src="{{ asset('js/cart.js') }}" defer></script>

@endsection