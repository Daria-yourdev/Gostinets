@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/catalog.css') }}">

<section class="cat-hero">
    <div class="container cat-hero__inner">
        <nav class="breadcrumbs" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Начало</a>
            <span aria-hidden="true">·</span>
            <span class="breadcrumbs__current">Кладовая</span>
        </nav>

        <div class="cat-hero__head">
            <div>
                <h1 class="cat-hero__title">
                    <span class="stretch">К</span>ладовая
                </h1>
                <p class="cat-hero__lede">
                    @if($filters['q'])
                    Нашли по запросу <em>«{{ $filters['q'] }}»</em> —
                    <strong>{{ $products->total() }}</strong> {{ plural($products->total(), 'банка', 'банки', 'банок') }}.
                    @else
                    в банках сегодня — <strong>{{ $totalActive }}</strong> {{ plural($totalActive, 'сорт', 'сорта', 'сортов') }} варенья.
                    Выбирай по ягоде, настроению или цене.
                    @endif
                </p>
            </div>

            {{-- Призыв к кастому, если ещё не уходил в /cauldron --}}
            <a href="{{ route('cauldron') }}" class="cat-hero__cauldron-cta">
                <span class="cat-hero__cauldron-emoji" aria-hidden="true">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.6"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10 22 L38 22 L36 38 Q36 42 32 42 L16 42 Q12 42 12 38 Z" />
                        <path d="M6 22 L42 22" stroke-width="2.2" />
                        <path d="M16 14 Q18 8 20 14 M22 12 Q24 6 26 12 M28 14 Q30 8 32 14" />
                    </svg>
                </span>
                <span class="cat-hero__cauldron-text">
                    <span class="cat-hero__cauldron-kicker">сваришь своё?</span>
                    <span class="cat-hero__cauldron-title">К котелку →</span>
                </span>
            </a>
        </div>
    </div>
</section>

<section class="cat-body">
    <div class="container cat-body__inner">

        {{-- ============================================================
             КНОПКА «Фильтры» — видна только на мобиле, открывает сайдбар
             ============================================================ --}}
        <button type="button" class="cat-filters__mobile-btn" id="cat-filters-open">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <line x1="4" y1="6" x2="20" y2="6" />
                <line x1="4" y1="12" x2="14" y2="12" />
                <line x1="4" y1="18" x2="10" y2="18" />
                <circle cx="18" cy="12" r="2" fill="currentColor" />
                <circle cx="14" cy="18" r="2" fill="currentColor" />
            </svg>
            <span>Фильтры</span>
            @php $activeCount = collect($filters)->filter(fn($v) => filled($v) && $v !== 'popular' && $v !== '')->count(); @endphp
            @if($activeCount > 0)
            <span class="cat-filters__mobile-count">{{ $activeCount }}</span>
            @endif
        </button>

        {{-- ============================================================
             САЙДБАР ФИЛЬТРОВ
             ============================================================ --}}
        <aside class="cat-filters" id="cat-filters">
            <div class="cat-filters__header">
                <h2 class="cat-filters__title">Просев</h2>
                <button type="button" class="cat-filters__close" id="cat-filters-close" aria-label="Закрыть">×</button>
            </div>

            <form id="cat-filters-form" method="GET" action="{{ route('catalog') }}" class="cat-filters__form">
                {{-- Сохраняем поисковый запрос при перестроении фильтров --}}
                @if($filters['q'])
                <input type="hidden" name="q" value="{{ $filters['q'] }}">
                @endif

                {{-- Активные фильтры с возможностью сбросить --}}
                @if($activeCount > 0)
                <div class="cat-filters__group cat-filters__active">
                    <div class="cat-filters__group-head">
                        <h3>Сейчас отобрано</h3>
                        <a href="{{ route('catalog') }}" class="cat-filters__reset">сбросить всё</a>
                    </div>
                </div>
                @endif

                {{-- ===================== Из чего варено ===================== --}}
                <details class="cat-filters__group" open>
                    <summary class="cat-filters__group-head">
                        <h3>Из чего варено</h3>
                        <span class="cat-filters__group-arrow" aria-hidden="true">›</span>
                    </summary>
                    <div class="cat-filters__chips">
                        @foreach($berries as $slug => $label)
                        <label class="cat-chip">
                            <input type="checkbox" name="berry[]" value="{{ $slug }}"
                                {{ in_array($slug, $filters['berries']) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </details>

                {{-- ===================== Настроение ===================== --}}
                <details class="cat-filters__group" open>
                    <summary class="cat-filters__group-head">
                        <h3>На какое настроение</h3>
                        <span class="cat-filters__group-arrow" aria-hidden="true">›</span>
                    </summary>
                    <div class="cat-filters__chips">
                        @foreach($moods as $slug => $label)
                        <label class="cat-chip cat-chip--mood">
                            <input type="checkbox" name="mood[]" value="{{ $slug }}"
                                {{ in_array($slug, $filters['moods']) ? 'checked' : '' }}>
                            <span>{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </details>

                {{-- ===================== Цена ===================== --}}
                <details class="cat-filters__group" open>
                    <summary class="cat-filters__group-head">
                        <h3>Цена за банку</h3>
                        <span class="cat-filters__group-arrow" aria-hidden="true">›</span>
                    </summary>
                    <div class="cat-filters__price">
                        <div class="cat-filters__price-inputs">
                            <label>
                                <span>от</span>
                                <input type="number" name="price_min"
                                    min="{{ $priceRange['min'] }}" max="{{ $priceRange['max'] }}"
                                    placeholder="{{ $priceRange['min'] }}"
                                    value="{{ $filters['price_min'] }}"
                                    inputmode="numeric">
                                <small>₽</small>
                            </label>
                            <label>
                                <span>до</span>
                                <input type="number" name="price_max"
                                    min="{{ $priceRange['min'] }}" max="{{ $priceRange['max'] }}"
                                    placeholder="{{ $priceRange['max'] }}"
                                    value="{{ $filters['price_max'] }}"
                                    inputmode="numeric">
                                <small>₽</small>
                            </label>
                        </div>
                        <div class="cat-filters__price-range"
                            data-min="{{ $priceRange['min'] }}"
                            data-max="{{ $priceRange['max'] }}">
                            <div class="cat-filters__price-track"></div>
                            <div class="cat-filters__price-fill" id="cat-price-fill"></div>
                        </div>
                    </div>
                </details>

                {{-- ===================== Спец. метки ===================== --}}
                <details class="cat-filters__group" open>
                    <summary class="cat-filters__group-head">
                        <h3>Особые приметы</h3>
                        <span class="cat-filters__group-arrow" aria-hidden="true">›</span>
                    </summary>
                    <div class="cat-filters__checks">
                        <label class="cat-check">
                            <input type="checkbox" name="sugar_free" value="1"
                                {{ $filters['sugar_free'] ? 'checked' : '' }}>
                            <span class="cat-check__box" aria-hidden="true"></span>
                            <span>Без сахара</span>
                        </label>
                        <label class="cat-check">
                            <input type="checkbox" name="gift" value="1"
                                {{ $filters['gift'] ? 'checked' : '' }}>
                            <span class="cat-check__box" aria-hidden="true"></span>
                            <span>В подарок</span>
                        </label>
                    </div>
                </details>

                {{-- ===================== Значки ===================== --}}
                <!-- <details class="cat-filters__group">
                    <summary class="cat-filters__group-head">
                        <h3>Метки</h3>
                        <span class="cat-filters__group-arrow" aria-hidden="true">›</span>
                    </summary>
                    <div class="cat-filters__chips">
                        @foreach($badges as $slug => $label)
                            <label class="cat-chip cat-chip--badge">
                                <input type="checkbox" name="badge[]" value="{{ $slug }}"
                                       {{ in_array($slug, $filters['badges']) ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </details> -->

                {{-- Кнопка submit для мобилы (на десктопе фильтры применяются автоматически) --}}
                <div class="cat-filters__submit-row">
                    <button type="submit" class="cat-filters__apply">Применить</button>
                    <a href="{{ route('catalog') }}" class="cat-filters__reset-link">Сбросить</a>
                </div>
            </form>
        </aside>

        {{-- ============================================================
             ОСНОВНАЯ КОЛОНКА — сортировка, сетка, пагинация
             ============================================================ --}}
        <div class="cat-main">

            {{-- Toolbar: количество + сортировка --}}
            <div class="cat-toolbar">
                <div class="cat-toolbar__count">
                    Найдено <strong>{{ $products->total() }}</strong>
                    {{ plural($products->total(), 'банка', 'банки', 'банок') }}
                </div>

                <div class="cat-toolbar__sort">
                    <label for="cat-sort">Порядок:</label>
                    <select id="cat-sort" name="sort" data-sort-control>
                        <option value="popular" {{ $filters['sort']==='season' ? 'selected' : '' }}>сначала сезонные</option>
                        <option value="new" {{ $filters['sort']==='new' ? 'selected' : '' }}>сначала новые</option>
                        <option value="cheap" {{ $filters['sort']==='cheap' ? 'selected' : '' }}>сначала дешёвые</option>
                        <option value="expensive" {{ $filters['sort']==='expensive' ? 'selected' : '' }}>сначала дорогие</option>
                    </select>
                </div>
            </div>

            {{-- Сетка товаров --}}
            @if($products->count() > 0)
            <div class="cat-grid">
                @foreach($products as $product)
                <article class="jar-card cat-card" style="--jam: {{ $product->jam_color }}">
                    @if($product->badge)
                    <div class="jar-card__tag">{{ $product->badge }}</div>
                    @endif

                    <a href="{{ route('product', $product->slug) }}" class="cat-card__visual-link"
                        aria-label="{{ $product->name }} — открыть">
                        <div class="jar-card__visual">
                            <img src="{{ asset($product->image_path ?: 'media/catalog/catalog-card-1.png') }}"
                                alt="{{ $product->name }}" class="jar-svg" loading="lazy">
                        </div>
                    </a>

                    <a href="{{ route('product', $product->slug) }}" class="cat-card__head">
                        <h3 class="jar-card__name">{{ $product->name }}</h3>
                        <div class="jar-card__hand">{{ $product->subtitle }}</div>
                    </a>

                    <div class="cat-card__meta">
                        <span class="cat-card__mood" title="настроение">{{ $product->moodLabel() }}</span>
                        <span class="cat-card__weight">{{ $product->weight }} г</span>
                    </div>

                    <div class="jar-card__row">
                        <div class="jar-card__price">
                            {{ $product->priceFormatted() }}
                        </div>
                        <button type="button" class="jar-card__add"
                            aria-label="Сложить в мешочек"
                            title="Сложить в мешочек"
                            data-add-to-cart="{{ $product->id }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                        </button>
                    </div>
                </article>
                @endforeach
            </div>

            {{-- Пагинация --}}
            <div class="cat-pagination">
                {{ $products->links('vendor.pagination.gostinets') }}
            </div>
            @else
            {{-- Пустое состояние --}}
            <div class="cat-empty">
                <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="1.5"
                    class="cat-empty__svg" aria-hidden="true">
                    <path d="M22 30 L58 30 L54 64 Q54 70 48 70 L32 70 Q26 70 26 64 Z" />
                    <path d="M18 30 L62 30" stroke-width="2.5" />
                    <path d="M30 24 L30 18 Q30 12 36 12 L44 12 Q50 12 50 18 L50 24" />
                    <text x="40" y="52" text-anchor="middle" font-size="14" fill="currentColor" stroke="none">?</text>
                </svg>
                <h3>Не нашлось</h3>
                <p>Под этот просев в кладовой нет банок. <br>Попробуй ослабить условия или начать заново.</p>
                <a href="{{ route('catalog') }}" class="btn-primary">
                    <span>Показать всё</span>
                </a>
            </div>
            @endif
        </div>
    </div>
</section>

{{-- Overlay для мобильной открытой панели фильтров --}}
<div class="cat-filters__backdrop" id="cat-filters-backdrop"></div>

<script src="{{ asset('js/catalog.js') }}" defer></script>

@endsection