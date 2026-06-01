@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/cauldron.css') }}">

<section class="cauldron-page">
    <div class="container">
        <nav class="breadcrumbs" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Начало</a>
            <span aria-hidden="true">·</span>
            <a href="{{ route('catalog') }}">Кладовая</a>
            <span aria-hidden="true">·</span>
            <span class="breadcrumbs__current">Котелок</span>
        </nav>

        <header class="cauldron-page__head">
            <h1>
                Котелок <em>для варки</em>
            </h1>
            <p>
                {{-- Заговорный лид: "вари своё варенье" --}}
                Бросай в котёл ягоды, специи и пожелания
            </p>
        </header>

        {{-- ============================================================
             БАННЕР ДЛЯ ГОСТЕЙ — приглашение зарегистрироваться
             ============================================================ --}}
        @guest
        <div class="cauldron-gate">
            <!-- <div class="cauldron-gate__seal" aria-hidden="true">
                <img src="{{ asset('media/cotel/cotel-gif2.gif') }}" alt="">
                <svg viewBox="0 0 80 80" fill="none">
                    <circle cx="40" cy="40" r="36" stroke="currentColor" stroke-width="1" opacity="0.4"/>
                    <circle cx="40" cy="40" r="28" stroke="currentColor" stroke-width="1" opacity="0.6"/>
                    <circle cx="40" cy="40" r="20" stroke="currentColor" stroke-width="1.5"/>
                    <path d="M40 12 L40 68 M12 40 L68 40" stroke="currentColor" stroke-width="0.8" opacity="0.4"/>
                    <text x="40" y="46" text-anchor="middle" font-family="Marko One" font-size="20" fill="currentColor" stroke="none">К</text>
                </svg>
            </div> -->

            <div class="cauldron-gate__copy">
                <span class="cauldron-gate__kicker">только для гостей с грамотой</span>
                <h2>Котелок открывают <em>своим</em></h2>
                <p>
                    Положить в котёл собственную банку — особая радость:
                    выбрать ягоду, добавить специй, шепнуть мастеру пожелание.
                    Чтобы Мария знала, кому варить и куда отправить —
                    заведи гостевую грамоту. Это <strong>бесплатно</strong>
                    и займёт меньше минуты.
                </p>

                <div class="cauldron-gate__cta">
                    <button type="button" class="btn-primary" data-open-auth="register">
                        <span>Завести грамоту</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </button>
                    <button type="button" class="btn-ghost" data-open-auth="login">
                        Уже бывали? Войти
                    </button>
                </div>

                <!-- <ul class="cauldron-gate__perks">
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M20 6 L9 17 L4 12"/>
                        </svg>
                        Сохранять рецепты в гримуар — возвращаться и менять
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M20 6 L9 17 L4 12"/>
                        </svg>
                        Получать первой свежие сезонные новинки
                    </li>
                    <li>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path d="M20 6 L9 17 L4 12"/>
                        </svg>
                        Видеть статус своих заказов и историю варок
                    </li>
                </ul> -->
            </div>
        </div>

        <div class="cauldron-gate__preview">
            <p class="cauldron-gate__preview-note">
                А пока — потрогай конструктор. Цена пересчитывается прямо при выборе.
            </p>
        </div>
        @endguest

        {{-- ============================================================
             КОНСТРУКТОР — доступен всем; гостям финальные кнопки заменены
             ============================================================ --}}
        <div class="cauldron"
            id="cauldron"
            data-can-cook="{{ $canCook ? '1' : '0' }}"
            data-price-map='@json($priceMap)'>

            {{-- =========== ЛЕВЫЙ СТОЛБЕЦ — сам котелок =========== --}}
            <div class="cauldron__stage" style="position: sticky;
    top: 150px;">
                <div class="cauldron__vessel" id="cauldron-vessel" aria-hidden="true">
                    {{-- Пар --}}
                    <!-- <div class="cauldron__steam">
                        <span class="cauldron__steam-bubble"></span>
                        <span class="cauldron__steam-bubble"></span>
                        <span class="cauldron__steam-bubble"></span>
                        <span class="cauldron__steam-bubble"></span>
                        <span class="cauldron__steam-bubble"></span>
                    </div> -->

                    {{-- Сам котёл (SVG) --}}
                    <!-- <svg class="cauldron__svg" viewBox="0 0 320 280" fill="none" xmlns="http://www.w3.org/2000/svg">
                        {{-- Обод --}}
                        <ellipse cx="160" cy="78" rx="135" ry="22" fill="#3A2818" stroke="#1A0A0A" stroke-width="2" />
                        <ellipse cx="160" cy="74" rx="125" ry="18" fill="#1A0A0A" />

                        {{-- Тело котла --}}
                        <path d="M30 78 L50 220 Q55 250 90 255 L230 255 Q265 250 270 220 L290 78 Z"
                            fill="url(#cauldronGrad)" stroke="#1A0A0A" stroke-width="2.5" />

                        {{-- Блик --}}
                        <path d="M45 90 L60 200 Q62 215 75 220"
                            stroke="rgba(232, 199, 122, 0.35)" stroke-width="3" fill="none" stroke-linecap="round" />

                        {{-- Заклёпки --}}
                        @for ($i = 0; $i
                        < 5; $i++)
                            <circle cx="{{ 50 + $i * 55 }}" cy="100" r="3" fill="#1A0A0A" />
                        @endfor

                        {{-- Внутренняя жидкость — цвет меняется JS-ом --}}
                        <ellipse cx="160" cy="78" rx="118" ry="14"
                            fill="#5B1C1C" id="cauldron-liquid"
                            style="transition: fill 0.6s ease;" />

                        {{-- Огонь снизу (декор) --}}
                        <g transform="translate(110, 250)">
                            <path d="M0 20 Q5 0 12 10 Q18 -5 25 8 Q32 0 40 15 Q48 5 55 18 Q62 10 70 20 L75 30 L-5 30 Z"
                                fill="#C9A961" opacity="0.7">
                                <animate attributeName="d"
                                    values="M0 20 Q5 0 12 10 Q18 -5 25 8 Q32 0 40 15 Q48 5 55 18 Q62 10 70 20 L75 30 L-5 30 Z;
                                            M0 18 Q5 2 12 12 Q18 -3 25 10 Q32 2 40 17 Q48 7 55 16 Q62 8 70 18 L75 30 L-5 30 Z;
                                            M0 20 Q5 0 12 10 Q18 -5 25 8 Q32 0 40 15 Q48 5 55 18 Q62 10 70 20 L75 30 L-5 30 Z"
                                    dur="0.6s" repeatCount="indefinite" />
                            </path>
                        </g>

                        <defs>
                            <linearGradient id="cauldronGrad" x1="0" y1="0" x2="0" y2="1">
                                <stop offset="0%" stop-color="#8B6535" />
                                <stop offset="50%" stop-color="#5C3B1F" />
                                <stop offset="100%" stop-color="#3A2818" />
                            </linearGradient>
                        </defs>
                    </svg> -->

                    <img src="{{ asset('media//cotel/cotel.png') }}" alt="">

                    {{-- Контейнер для падающих ингредиентов (заполняется JS-ом) --}}
                    <div class="cauldron__drops" id="cauldron-drops" aria-hidden="true"></div>
                </div>

                {{-- Превью этикетки --}}
                <div class="cauldron__preview">
                    <div class="cauldron__label-preview" id="cauldron-label">
                        <div class="cauldron__label-frame">
                            <div class="cauldron__label-kicker">Гостинецъ · ручная варка</div>
                            <div class="cauldron__label-name" id="cauldron-label-name">Своё варенье</div>
                            <div class="cauldron__label-desc" id="cauldron-label-desc">
                                собери в котле слева
                            </div>
                            <div class="cauldron__label-foot">
                                <span id="cauldron-label-size">250 г</span>
                                <span id="cauldron-label-date">{{ now()->format('d.m.Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="cauldron__price" id="cauldron-price">
                        <span class="cauldron__price-kicker">итого</span>
                        <span class="cauldron__price-value" id="cauldron-price-value">{{ $priceMap['base'] }}</span>
                        <span class="cauldron__price-currency">₽</span>
                    </div>
                </div>
            </div>

            {{-- =========== ПРАВЫЙ СТОЛБЕЦ — форма выбора =========== --}}
            <form class="cauldron__form" id="cauldron-form"
                method="POST" action="{{ route('cauldron.store') }}" novalidate>
                @csrf

                {{-- Прогресс шагов --}}
                <ol class="cauldron-steps">
                    <li class="cauldron-step --done" data-step="1"><span>1</span>Ягода</li>
                    <li class="cauldron-step" data-step="2"><span>2</span>Компания</li>
                    <li class="cauldron-step" data-step="3"><span>3</span>Приправы</li>
                    <li class="cauldron-step" data-step="4"><span>4</span>Сладость</li>
                    <li class="cauldron-step" data-step="5"><span>5</span>Размер</li>
                    <li class="cauldron-step" data-step="6"><span>6</span>Этикетка</li>
                </ol>

                {{-- ===== ШАГ 1: главная ягода ===== --}}
                <section class="cauldron-block" data-block="1">
                    <header class="cauldron-block__head">
                        <span class="cauldron-block__num">01</span>
                        <h3 class="cauldron-block__title">Какая ягода <em>главная</em>?</h3>
                        <p class="cauldron-block__hint">одна — без неё котёл не варит</p>
                    </header>

                    <div class="cauldron-grid cauldron-grid--berries">
                        @foreach($berries as $slug => $name)
                        <label class="berry-card">
                            <input type="radio" name="berry_main" value="{{ $slug }}" required>
                            <span class="berry-card__inner">
                                <span class="berry-card__visual berry-card__visual--{{ $slug }}">
                                    @include('partials.berry-svg', ['berry' => $slug])
                                </span>
                                <span class="berry-card__name">{{ $name }}</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                    <div class="cauldron-error" data-error="berry_main"></div>
                </section>

                {{-- ===== ШАГ 2: дополнительные ягоды ===== --}}
                <section class="cauldron-block" data-block="2">
                    <header class="cauldron-block__head">
                        <span class="cauldron-block__num">02</span>
                        <h3 class="cauldron-block__title">Кого позовём <em>в компанию</em>?</h3>
                        <p class="cauldron-block__hint">до двух дополнительных ягод · +80 ₽ за каждую</p>
                    </header>

                    <div class="cauldron-grid cauldron-grid--berries cauldron-grid--small">
                        @foreach($berries as $slug => $name)
                        <label class="berry-card berry-card--small">
                            <input type="checkbox" name="berry_extras[]" value="{{ $slug }}" data-max="2">
                            <span class="berry-card__inner">
                                <span class="berry-card__visual berry-card__visual--{{ $slug }} berry-card__visual--small">
                                    @include('partials.berry-svg', ['berry' => $slug])
                                </span>
                                <span class="berry-card__name">{{ $name }}</span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </section>

                {{-- ===== ШАГ 3: специи ===== --}}
                <section class="cauldron-block" data-block="3">
                    <header class="cauldron-block__head">
                        <span class="cauldron-block__num">03</span>
                        <h3 class="cauldron-block__title">Что <em>добавим</em>?</h3>
                        <p class="cauldron-block__hint">до трёх приправ · +40 ₽ за каждую</p>
                    </header>

                    <div class="cauldron-chips">
                        @foreach($spices as $slug => $name)
                        <label class="cauldron-chip">
                            <input type="checkbox" name="spices[]" value="{{ $slug }}" data-max="3">
                            <span>{{ $name }}</span>
                        </label>
                        @endforeach
                    </div>
                </section>

                {{-- ===== ШАГ 4: подсластитель ===== --}}
                <section class="cauldron-block" data-block="4">
                    <header class="cauldron-block__head">
                        <span class="cauldron-block__num">04</span>
                        <h3 class="cauldron-block__title">Чем <em>сладить</em>?</h3>
                        <p class="cauldron-block__hint">мёд и стевия — небольшая доплата</p>
                    </header>

                    <div class="cauldron-radios">
                        @foreach($sweeteners as $slug => $name)
                        @php
                        $fee = \App\Models\CustomJam::SWEETENER_FEE[$slug] ?? 0;
                        @endphp
                        <label class="cauldron-radio">
                            <input type="radio" name="sweetener" value="{{ $slug }}"
                                {{ $slug === 'sugar' ? 'checked' : '' }} required>
                            <span class="cauldron-radio__inner">
                                <span class="cauldron-radio__dot" aria-hidden="true"></span>
                                <span class="cauldron-radio__text">
                                    <strong>{{ $name }}</strong>
                                    <!-- @if($fee > 0)
                                            <small>+{{ $fee }} ₽</small>
                                        @endif -->
                                </span>
                            </span>
                        </label>
                        @endforeach
                    </div>
                </section>

                {{-- ===== ШАГ 5: размер банки ===== --}}
                <section class="cauldron-block" data-block="5">
                    <header class="cauldron-block__head">
                        <span class="cauldron-block__num">05</span>
                        <h3 class="cauldron-block__title">Какая <em>банка</em>?</h3>
                        <p class="cauldron-block__hint">маленькая — для пробы, большая — на зиму</p>
                    </header>

                    <div class="cauldron-sizes">
                        <label class="cauldron-size">
                            <input type="radio" name="jar_size" value="250" checked required>
                            <span class="cauldron-size__inner">
                                <span class="cauldron-size__jar cauldron-size__jar--s"></span>
                                <span class="cauldron-size__label">
                                    <strong>250 г</strong>
                                    <small>стандартная</small>
                                </span>
                            </span>
                        </label>
                        <label class="cauldron-size">
                            <input type="radio" name="jar_size" value="500">
                            <span class="cauldron-size__inner">
                                <span class="cauldron-size__jar cauldron-size__jar--m"></span>
                                <span class="cauldron-size__label">
                                    <strong>500 г</strong>
                                    <small>×1.85</small>
                                </span>
                            </span>
                        </label>
                        <label class="cauldron-size">
                            <input type="radio" name="jar_size" value="750">
                            <span class="cauldron-size__inner">
                                <span class="cauldron-size__jar cauldron-size__jar--l"></span>
                                <span class="cauldron-size__label">
                                    <strong>750 г</strong>
                                    <small>×2.6</small>
                                </span>
                            </span>
                        </label>
                    </div>
                </section>

                {{-- ===== ШАГ 6: этикетка + пожелание ===== --}}
                <section class="cauldron-block" data-block="6">
                    <header class="cauldron-block__head">
                        <span class="cauldron-block__num">06</span>
                        <h3 class="cauldron-block__title">Как <em>назовём</em>?</h3>
                        <p class="cauldron-block__hint">это будет на этикетке</p>
                    </header>

                    <div class="cauldron-fields">
                        <label class="cauldron-field">
                            <span class="cauldron-field__label">Имя варенья</span>
                            <input type="text" name="label_name" maxlength="60"
                                placeholder="Лето у бабушки в Заволжье" required>
                            <span class="cauldron-field__hint">от 2 до 60 знаков</span>
                            <span class="cauldron-error" data-error="label_name"></span>
                        </label>

                        <label class="cauldron-field">
                            <span class="cauldron-field__label">
                                Посвящение <small>(не обязательно, +60 ₽)</small>
                            </span>
                            <input type="text" name="dedication" maxlength="160"
                                placeholder="Маме — на 60 лет">
                            <span class="cauldron-field__hint">короткая подпись на этикетке</span>
                        </label>

                        <label class="cauldron-field">
                            <span class="cauldron-field__label">Шепнуть котлу</span>
                            <textarea name="whisper" maxlength="280" rows="3"
                                placeholder="Любые пожелания мастеру: «погуще», «без косточек», «упакуйте в льняной мешочек с открыткой»…"></textarea>
                            <span class="cauldron-field__hint">до 280 знаков · Мария прочитает лично</span>
                        </label>
                    </div>
                </section>

                {{-- ===== ИТОГ — кнопки действия ===== --}}
                <section class="cauldron-block cauldron-block--actions">
                    @auth
                    {{-- Для авторизованных — реальные кнопки --}}
                    <button type="submit" class="btn-primary cauldron-submit" name="action" value="order">
                        <span class="cauldron-submit__text">Бросить в котёл</span>
                        <span class="cauldron-submit__price">
                            · <span data-price-display>{{ $priceMap['base'] }}</span> ₽
                        </span>
                        <svg class="cauldron-submit__arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                        <span class="cauldron-submit__spinner" aria-hidden="true"></span>
                    </button>
                    <!-- <button type="submit" class="btn-ghost cauldron-draft" name="action" value="draft">
                        Записать в гримуар
                    </button> -->
                    <p class="cauldron-note">
                        Мария возьмётся в течение дня. О каждом шаге — варка, остывание, отправка — придёт весточка.
                    </p>
                    @else
                    {{-- Для гостей — призыв к регистрации --}}
                    <button type="button" class="btn-primary cauldron-submit cauldron-submit--locked"
                        data-open-auth="register">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
                            class="cauldron-submit__lock">
                            <rect x="5" y="11" width="14" height="10" rx="1" />
                            <path d="M8 11V7a4 4 0 0 1 8 0v4" />
                        </svg>
                        <span class="cauldron-submit__text">Войти, чтобы сварить</span>
                        <span class="cauldron-submit__price">
                            · <span data-price-display>{{ $priceMap['base'] }}</span> ₽
                        </span>
                    </button>
                    <p class="cauldron-note">
                        Гостям с грамотой котёл откроется сразу.
                        Регистрация — бесплатная, заведёт за минуту.
                    </p>
                    @endauth

                    <div class="cauldron-flash" data-flash></div>
                </section>
            </form>
        </div>
    </div>
</section>

<script src="{{ asset('js/cauldron.js') }}" defer></script>

@endsection