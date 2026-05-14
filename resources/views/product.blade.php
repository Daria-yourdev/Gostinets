@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/product.css') }}">

{{-- CSS-переменная jam_color передаётся через style на корневой элемент --}}
<div class="product-page" style="--jam: {{ $product->jamColor() }}">

    <div class="container">

        {{-- ============================================================
             ХЛЕБНЫЕ КРОШКИ
             ============================================================ --}}
        <nav class="breadcrumbs product-breadcrumbs" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Начало</a>
            <span aria-hidden="true">·</span>
            <a href="{{ route('catalog') }}">Кладовая</a>
            <span aria-hidden="true">·</span>
            @if($product->berry_type)
            <a href="{{ route('catalog', ['berry[]' => $product->berry_type]) }}">
                {{ $product->berryLabel() }}
            </a>
            <span aria-hidden="true">·</span>
            @endif
            <span class="breadcrumbs__current">{{ $product->name }}</span>
        </nav>

        {{-- ============================================================
             ОСНОВНОЙ БЛОК — 2 колонки: галерея + инфо
             ============================================================ --}}
        <div class="product-hero">

            {{-- ========== ЛЕВАЯ: Галерея ========== --}}
            <aside class="product-gallery">
                <div class="product-gallery__frame">

                    {{-- Вертикальная полоска цвета варенья --}}
                    <div class="product-gallery__jam-bar"></div>

                    {{-- Badge как наклейка --}}
                    @if($product->badge)
                    <div class="product-gallery__badge">
                        {{ \App\Models\Product::BADGES[$product->badge] ?? $product->badge }}
                    </div>
                    @endif

                    {{-- Изображение --}}
                    <div class="product-gallery__img-wrap">
                        <img
                            src="{{ asset($product->image_path ?: 'media/catalog/catalog-card-1.png') }}"
                            alt="{{ $product->name }}"
                            class="product-gallery__img"
                            loading="eager">
                    </div>
                </div>

                {{-- Цветовые кружки настроения —
                     маленькая легенда под фото --}}
                <div class="product-gallery__mood">
                    <span class="product-gallery__mood-dot"
                        style="background: var(--jam)"></span>
                    <span class="product-gallery__mood-label">
                        {{ $product->moodLabel() }}
                    </span>
                    <span class="product-gallery__mood-sep">·</span>
                    <span class="product-gallery__mood-berry">
                        {{ $product->berryLabel() }}
                    </span>
                </div>
            </aside>

            {{-- ========== ПРАВАЯ: Информация ========== --}}
            <section class="product-info" aria-label="Карточка товара">

                {{-- Шапка карточки: метки + заголовок --}}
                <div class="product-info__head">
                    <div class="product-info__labels">
                        {{-- Mood-лейбл --}}
                        <span class="product-label product-label--mood">
                            {{ $product->moodLabel() }}
                        </span>

                        {{-- Метки «Без сахара» и «В подарок» --}}
                        @if($product->is_sugar_free)
                        <span class="product-label product-label--special">Без сахара</span>
                        @endif
                        @if($product->is_gift)
                        <span class="product-label product-label--gift">В подарок</span>
                        @endif
                    </div>

                    <h1 class="product-info__title">{{ $product->name }}</h1>

                    @if($product->subtitle)
                    <p class="product-info__subtitle">{{ $product->subtitle }}</p>
                    @endif

                    @if($product->short_description)
                    <p class="product-info__desc">{{ $product->short_description }}</p>
                    @endif
                </div>

                <hr class="product-divider">

                {{-- Цена + наличие --}}
                <div class="product-info__commerce">
                    <div class="product-info__price-row">
                        <div class="product-info__price">
                            {{ $product->priceFormatted() }}
                        </div>
                        <div class="product-info__weight">
                            {{ $product->weight }} г
                        </div>
                    </div>

                    <div class="product-info__stock
                        @if(!$product->inStock()) product-info__stock--out @endif">
                        @if($product->inStock())
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="4" fill="currentColor" />
                        </svg>
                        В кладовой: {{ $product->stock }} {{ trans_choice('банка|банки|банок', $product->stock) }}
                        @else
                        <svg viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <circle cx="8" cy="8" r="4" fill="currentColor" />
                        </svg>
                        Разобрали. Следующая варка — скоро
                        @endif
                    </div>
                </div>

                {{-- Количество + кнопки --}}
                @if($product->inStock())
                <div class="product-info__actions" data-cart-context>
                    {{-- Счётчик qty --}}
                    <div class="product-qty" id="product-qty" role="group" aria-label="Количество">
                        <button type="button" class="product-qty__btn" id="qty-minus"
                            aria-label="Уменьшить">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                <path d="M5 12h14" />
                            </svg>
                        </button>
                        <output class="product-qty__value" id="qty-value"
                            data-qty-value
                            aria-live="polite">1</output>
                        <button type="button" class="product-qty__btn" id="qty-plus"
                            aria-label="Увеличить">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                        </button>
                    </div>

                    <div class="product-info__btns">
                        {{-- Основная кнопка — обрабатывается cart-actions.js --}}
                        <button type="button" class="btn-primary product-add-btn"
                            id="product-add"
                            data-add-to-cart="{{ $product->id }}">
                            <span class="product-add-btn__text">В мешочек</span>
                            <svg class="product-add-btn__arrow" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" aria-hidden="true">
                                <path d="M5 12h14M13 6l6 6-6 6" />
                            </svg>
                            <span class="product-add-btn__spinner" aria-hidden="true"></span>
                        </button>

                        {{-- Кнопка-ссылка на кастом --}}
                        <a href="{{ route('cauldron') }}" class="btn-ghost product-cauldron-btn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"
                                aria-hidden="true">
                                <path d="M6 11L18 11L17 19Q17 21 15 21L9 21Q7 21 7 19Z" />
                                <path d="M4 11L20 11" stroke-width="2.2" />
                                <path d="M10 8Q11 5 12 8M13 7Q14 4 15 7" />
                            </svg>
                            Своё в котелке
                        </a>
                    </div>
                </div>
                @else
                {{-- Нет в наличии — предложить подписку или похожее --}}
                <div class="product-info__nostock">
                    <p>Пока нет, но Дарина варит по расписанию.</p>
                    <a href="{{ route('catalog') }}" class="btn-ghost">
                        Другие банки в кладовой →
                    </a>
                </div>
                @endif

                <hr class="product-divider">

                {{-- Блок предсказания — самое психоделическое место --}}
                @if($product->moodPrediction())
                <div class="product-whispering">
                    <div class="product-whispering__jar" aria-hidden="true">
                        <svg viewBox="0 0 40 48" fill="none">
                            <path d="M10 14 L30 14 L28 40 Q28 44 24 44 L16 44 Q12 44 12 40 Z"
                                fill="var(--jam)" opacity="0.7" />
                            <path d="M8 14 L32 14" stroke="var(--jam)" stroke-width="2.5" />
                            <path d="M14 10 L14 6 Q14 4 16 4 L24 4 Q26 4 26 6 L26 10"
                                stroke="var(--jam)" stroke-width="1.5" fill="none" />
                        </svg>
                    </div>
                    <div class="product-whispering__text">
                        <span class="product-whispering__kicker">ягода нашептала</span>
                        <blockquote class="product-whispering__quote">
                            {{ $product->moodPrediction() }}
                        </blockquote>
                    </div>
                </div>
                @endif

                {{-- Мини-инфо о доставке --}}
                <ul class="product-info__hints">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                        Доставка по России — СДЭК и Почта
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                            <path d="M20 6 L9 17 L4 12" />
                        </svg>
                        Упакуем в льняной мешочек бесплатно
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true">
                            <path d="M20 6 L9 17 L4 12" />
                        </svg>
                        Можно добавить открытку с посвящением
                    </li>
                </ul>

            </section>
        </div>

        {{-- ============================================================
             ТАБЫ — Подробная информация
             ============================================================ --}}
        <div class="product-tabs" id="product-tabs">
            <div class="product-tabs__nav" role="tablist">
                <button class="product-tabs__tab --active" role="tab"
                    data-tab="story" aria-selected="true">
                    Сказ о варенье
                </button>
                <button class="product-tabs__tab" role="tab"
                    data-tab="berry" aria-selected="false">
                    О ягоде
                </button>
                <button class="product-tabs__tab" role="tab"
                    data-tab="store" aria-selected="false">
                    Как хранить
                </button>
                <button class="product-tabs__tab" role="tab"
                    data-tab="delivery" aria-selected="false">
                    Доставка
                </button>
            </div>

            {{-- Сказ о варенье --}}
            <div class="product-tabs__panel --active" id="tab-story"
                role="tabpanel" aria-labelledby="tab-btn-story">
                @if($product->description)
                <div class="product-tabs__content">
                    {!! nl2br(e($product->description)) !!}
                </div>
                @else
                <p class="product-tabs__empty">Дарина ещё не написала сказ об этой банке.</p>
                @endif
            </div>

            {{-- О ягоде --}}
            <div class="product-tabs__panel" id="tab-berry"
                role="tabpanel" aria-labelledby="tab-btn-berry">
                <div class="product-tabs__content">
                    <div class="product-berry-info">
                        <div class="product-berry-info__visual" aria-hidden="true">
                            @include('partials.berry-svg', ['berry' => $product->berry_type])
                        </div>
                        <div class="product-berry-info__text">
                            <h3>{{ $product->berryLabel() }}</h3>
                            <p>{{ $product->berryLore() }}</p>
                        </div>
                    </div>
                    @if($product->moodPrediction())
                    <div class="product-berry-mood">
                        <span class="product-berry-mood__kicker">настроение — {{ $product->moodLabel() }}</span>
                        <p>{{ $product->moodPrediction() }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Хранение --}}
            <div class="product-tabs__panel" id="tab-store"
                role="tabpanel" aria-labelledby="tab-btn-store">
                <div class="product-tabs__content">
                    <div class="product-storage">
                        <div class="product-storage__item">
                            <strong>Срок хранения</strong>
                            <span>24 месяца с даты варки, при соблюдении условий</span>
                        </div>
                        <div class="product-storage__item">
                            <strong>До вскрытия</strong>
                            <span>В тёмном, прохладном месте (погреб, кладовая, нижняя полка шкафа). Температура от +5 до +20 °C.</span>
                        </div>
                        <div class="product-storage__item">
                            <strong>После вскрытия</strong>
                            <span>В холодильнике, не дольше 3 месяцев. Закрывайте крышкой после каждого использования.</span>
                        </div>
                        <div class="product-storage__item">
                            <strong>Состав</strong>
                            <span>{{ $product->berryLabel() }}, сахар{{ $product->is_sugar_free ? ' (заменён натуральным подсластителем)' : '' }}. Без красителей, консервантов и ароматизаторов.</span>
                        </div>
                        <p class="product-storage__note">
                            Если банка вздулась или крышка не держится — не открывайте.
                            Напишите нам, заменим без вопросов.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Доставка --}}
            <div class="product-tabs__panel" id="tab-delivery"
                role="tabpanel" aria-labelledby="tab-btn-delivery">
                <div class="product-tabs__content">
                    <div class="product-delivery">
                        <div class="product-delivery__item">
                            <span class="product-delivery__icon">📦</span>
                            <div>
                                <strong>СДЭК</strong>
                                <p>Доставка в пункты выдачи и курьером. Срок — 3–7 рабочих дней. Стоимость рассчитывается при оформлении.</p>
                            </div>
                        </div>
                        <div class="product-delivery__item">
                            <span class="product-delivery__icon">✉️</span>
                            <div>
                                <strong>Почта России</strong>
                                <p>До любого населённого пункта. Срок — 7–21 день. Подходит для подарков в отдалённые места.</p>
                            </div>
                        </div>
                        <div class="product-delivery__item">
                            <span class="product-delivery__icon">🎁</span>
                            <div>
                                <strong>Подарочная упаковка</strong>
                                <p>По запросу упакуем в льняной мешочек с сургучной печатью. Добавим открытку с вашим текстом — бесплатно.</p>
                            </div>
                        </div>
                        <div class="product-delivery__item">
                            <span class="product-delivery__icon">🔄</span>
                            <div>
                                <strong>Возврат</strong>
                                <p>Если банка пришла повреждённой — пришлите фото, заменим или вернём деньги. Без сложностей.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================================
             ПОХОЖИЕ ВАРЕНЬЯ
             ============================================================ --}}
        @if($related->count() > 0)
        <section class="product-related" aria-labelledby="related-heading">
            <h2 class="product-related__title" id="related-heading">
                Ещё из кладовой
            </h2>
            <p class="product-related__sub">
                та же ягода или то же настроение
            </p>

            <div class="product-related__grid">
                @foreach($related as $rel)
                <article class="jar-card" style="--jam: {{ $rel->jamColor() }}">
                    @if($rel->badge)
                    <div class="jar-card__tag">{{ $rel->badge }}</div>
                    @endif

                    <a href="{{ route('product', $rel->slug) }}" class="product-related__visual-link">
                        <div class="jar-card__visual">
                            <img src="{{ asset($rel->image_path ?: 'media/catalog/catalog-card-1.png') }}"
                                alt="{{ $rel->name }}" class="jar-svg" loading="lazy">
                        </div>
                    </a>

                    <a href="{{ route('product', $rel->slug) }}" class="product-related__card-head">
                        <h3 class="jar-card__name">{{ $rel->name }}</h3>
                        <div class="jar-card__hand">{{ $rel->subtitle }}</div>
                    </a>

                    <div class="jar-card__row">
                        <div class="jar-card__price">{{ $rel->priceFormatted() }}</div>
                        <button type="button" class="jar-card__add"
                            aria-label="Сложить в мешочек"
                            data-add-to-cart="{{ $rel->id }}">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                        </button>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endif

    </div>{{-- /container --}}
</div>{{-- /product-page --}}

<script src="{{ asset('js/product.js') }}" defer></script>

@endsection