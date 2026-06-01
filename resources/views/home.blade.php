@extends('layouts.app')
@section('content')

{{-- HERO --}}
<section class="hero" id="banner">
    <div class="container hero__inner">
        <div class="hero__left">
            <div>
                <!-- <div class="hero__top">
                        <span class="hero__chip --brick">Сезон клубники</span>
                        <span class="hero__chip">варено · 09.05.2026</span>
                    </div> -->
                <h1 class="hero__title">
                    <span class="stretch">Я</span>годы
                    <br>
                    <span class="accent">варили —</span>
                    <br>
                    а они
                    <br>
                    зашептали
                </h1>
                <p class="hero__lede">
                    <span class="pop">«Гостинецъ»</span> — банка варенья, в которой<br>
                    спит лето, сад, костёр и тёплый вечер.
                </p>
            </div>

            <div>
                <div class="hero__cta">
                    <a href="{{ route('catalog') }}" class="btn-primary">
                        <span>Выбрать гостинец</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6" />
                        </svg>
                    </a>
                    <a href="#about" class="btn-ghost">
                        Сказ хозяйки <span aria-hidden="true">↓</span>
                    </a>
                </div>
                {{-- Подпись под CTA: цена и количество сортов — снимает страхи новичков --}}
                <div class="hero__cta-hint">
                    23 сорта · от 290 ₽ · доставка по РФ
                </div>

                <!-- <div class="hero__meta">
                        <div class="hero__meta-item">
                            <div class="hero__meta-num">23</div>
                            <div class="hero__meta-lab">Сорта<br>в этом сезоне</div>
                        </div>
                        <div class="hero__meta-item">
                            <div class="hero__meta-num">100%</div>
                            <div class="hero__meta-lab">Ручная<br>варка</div>
                        </div>
                        <div class="hero__meta-item">
                            <div class="hero__meta-num">7</div>
                            <div class="hero__meta-lab">Лет<br>в медном тазу</div>
                        </div>
                    </div> -->
            </div>
        </div>

        {{-- Правый блок: вертикальный слайдер --}}
        <div class="hero__right">
            <div class="hero__decor-tag">
                Гостинецъ<br>
                сезон 2026
            </div>

            <div class="vslider" aria-roledescription="carousel">
                <div class="vslider__track">
                    <div class="vslider__slide" aria-label="Фото 1">
                        <img src="{{ asset('media/slider/slider-card-1.png') }}" alt="">
                    </div>
                    <div class="vslider__slide" aria-label="Фото 2">
                        <img src="{{ asset('media/slider/slider-card-2.png') }}" alt="">
                    </div>
                    <div class="vslider__slide" aria-label="Фото 3">
                        <img src="{{ asset('media/slider/slider-card-3.png') }}" alt="">
                    </div>
                    <div class="vslider__slide" aria-label="Фото 4">
                        <img src="{{ asset('media/slider/slider-card-4.png') }}" alt="">
                    </div>
                </div>

                <div class="vslider__dots" role="tablist" aria-label="Слайды">
                    <button type="button" class="vslider__dot --active" data-index="0" aria-label="Слайд 1"></button>
                    <button type="button" class="vslider__dot" data-index="1" aria-label="Слайд 2"></button>
                    <button type="button" class="vslider__dot" data-index="2" aria-label="Слайд 3"></button>
                    <button type="button" class="vslider__dot" data-index="3" aria-label="Слайд 4"></button>
                </div>

                <div class="vslider__nav">
                    <button type="button" class="vslider__btn vslider__btn--up" aria-label="Назад">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 15l-6-6-6 6" />
                        </svg>
                    </button>
                    <button type="button" class="vslider__btn vslider__btn--down" aria-label="Вперёд">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="hero__price-tag">от <b>290 ₽</b> банка</div>
        </div>
    </div>
</section>

{{-- MARQUEE — USP-полоса. Включена! Это якоря доверия. --}}
<!-- <div class="marquee" aria-hidden="true">
        <div class="marquee__track">
            @php
            $marqueeItems = [
            'ВАРИМ С 2019',
            'РУЧНАЯ ВАРКА',
            'БЕЗ КОНСЕРВАНТОВ',
            'ДОСТАВКА ПО РФ',
            'ОПЛАТА ПРИ ПОЛУЧЕНИИ',
            '23 СОРТА В СЕЗОН',
            'ЛЬНЯНОЙ МЕШОЧЕК В ПОДАРОК'
            ];
            @endphp
            @for ($i = 0; $i < 3; $i++)
                <span>
                @foreach ($marqueeItems as $item)
                {{ $item }} <span class="marquee__star">✦</span>
                @endforeach
                </span>
                @endfor
        </div>
    </div> -->

{{-- CATALOG --}}
<section class="section" id="catalog">
    <div class="container">
        <div class="section-head">
            <h2 class="section-head__title">Гостинцы <em>из закромов</em></h2>
            <p class="section-head__sub">банка тепла — древний обычай делиться летом и любовью</p>
        </div>

        @php
        // Берём 5 актуальных банок из БД для лендинга
        $jams = \App\Models\Product::active()->take(5)->get();
        @endphp

        <div class="catalog__grid">
            @foreach ($jams as $j)
            <article class="jar-card" style="--jam: {{ $j->jamColor() }}">
                @if ($j->badge)
                <div class="jar-card__tag">{{ $j->badge }}</div>
                @endif
                <a href="{{ route('product', $j->slug) }}" class="jar-card__visual">
                    <img src="{{ asset($j->image_path ?: 'media/catalog/catalog-card-1.png') }}" class="jar-svg" alt="{{ $j->name }}">
                </a>
                <a href="{{ route('product', $j->slug) }}" style="text-decoration: none; color: inherit;">
                    <h3 class="jar-card__name">{{ $j->name }}</h3>
                    <div class="jar-card__hand">{{ $j->subtitle }}</div>
                </a>
                <div class="jar-card__row">
                    <div class="jar-card__price">{{ $j->priceFormatted() }}<small>/ {{ $j->weight }} гр</small></div>
                    <button type="button" class="jar-card__add"
                        aria-label="В мешочек" title="В мешочек"
                        data-add-to-cart="{{ $j->id }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </button>
                </div>
            </article>
            @endforeach
        </div>

        <!-- <div class="catalog__foot">
            <div class="catalog__filters">
                @foreach (['Всё варево', 'Ягодное', 'Цитрус', 'Без сахара', 'В подарок', 'Сезон'] as $i => $f)
                <button type="button" class="filter-chip {{ $i === 0 ? '--on' : '' }}">{{ $f }}</button>
                @endforeach
            </div>
            <a href="{{ route('catalog') }}" class="btn-primary">
                <span>Все 23 сорта</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6" />
                </svg>
            </a>
        </div> -->
    </div>
</section>

{{-- ADVANTAGES — тёмный блок с парящими частицами (генерятся в home.js) --}}
<section class="section section--ink" id="advantages">
    <div class="container">
        <div class="section-head-white">
            <h2 class="section-head__title">В чем <em>колдовство?</em></h2>
            <p class="section-head__sub">пять заклятий, что зашиты в каждой банке</p>
        </div>

        <div class="adv__grid">
            <div class="adv__cell">
                <span class="adv__num">01</span>
                <div class="adv__icon">
                    <svg viewBox="0 0 48 48" fill="none">
                        <path d="M8 40 Q24 38 36 26 Q42 18 40 8 Q30 6 22 12 Q10 22 8 38 Z" stroke="currentColor" stroke-width="2" fill="none" />
                        <path d="M8 40 L24 24" stroke="currentColor" stroke-width="2" />
                        <path d="M28 14 L18 24 M30 18 L22 26 M32 22 L26 28" stroke="currentColor" stroke-width="1.4" />
                    </svg>
                </div>
                <h3 class="adv__title">Чистый <em>состав</em></h3>
                <p class="adv__desc">Ягода, сахар, капля лимона. Больше в банке ничего — ни тайных трав, ни химии.</p>
            </div>

            <div class="adv__cell">
                <span class="adv__num">02</span>
                <div class="adv__icon">
                    <svg viewBox="0 0 48 48" fill="none">
                        <path d="M10 22 L38 22 L36 38 Q36 42 32 42 L16 42 Q12 42 12 38 Z" stroke="currentColor" stroke-width="2" fill="none" />
                        <path d="M6 22 L42 22" stroke="currentColor" stroke-width="2.5" />
                        <path d="M28 6 L28 22 M24 12 L32 12" stroke="currentColor" stroke-width="2" />
                        <path d="M14 18 Q16 14 18 18 M22 16 Q24 12 26 16 M30 18 Q32 14 34 18" stroke="currentColor" stroke-width="1.4" fill="none" />
                    </svg>
                </div>
                <h3 class="adv__title">Ручное <em>варево</em></h3>
                <p class="adv__desc">В медном тазу, на живом огне, малыми партиями. Каждая ягода — целая.</p>
            </div>

            <div class="adv__cell">
                <span class="adv__num">03</span>
                <div class="adv__icon">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <path d="M24 24 m-2 0 a2 2 0 1 0 4 0 a2 2 0 1 0 -4 0
                                 M24 24 m-7 0 a7 7 0 1 0 14 0 a7 7 0 1 0 -14 0
                                 M24 24 m-12 0 a12 12 0 1 0 24 0 a12 12 0 1 0 -24 0
                                 M24 24 m-18 0 a18 18 0 1 0 36 0" fill="none" />
                    </svg>
                </div>
                <h3 class="adv__title">Глубина <em>вкуса</em></h3>
                <p class="adv__desc">Долгое томление — ягода раскрывается и отдаёт всё, что копила за лето.</p>
            </div>

            <div class="adv__cell">
                <span class="adv__num">04</span>
                <div class="adv__icon">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round">
                        <path d="M6 22 L24 8 L42 22 L42 42 L6 42 Z" fill="none" />
                        <path d="M18 42 L18 28 L30 28 L30 42" fill="none" />
                        <path d="M22 32 L26 32 M22 36 L26 36" stroke-width="1.5" />
                        <path d="M10 18 L14 18 M34 18 L38 18 M14 8 L34 8" stroke-width="1.5" />
                    </svg>
                </div>
                <h3 class="adv__title">Дух <em>традиции</em></h3>
                <p class="adv__desc">Рецепт от прабабки — записан в тетрадку, перелит в банку, дошёл до вас.</p>
            </div>

            <div class="adv__cell">
                <span class="adv__num">05</span>
                <div class="adv__icon">
                    <svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round">
                        <path d="M24 38 L10 24 Q4 18 10 12 Q16 6 24 14 Q32 6 38 12 Q44 18 38 24 Z" />
                        <path d="M24 4 L24 8 M44 24 L40 24 M4 24 L8 24 M38 8 L36 10 M10 8 L12 10" stroke-width="1.5" />
                    </svg>
                </div>
                <h3 class="adv__title">Эмоции <em>в банке</em></h3>
                <p class="adv__desc">Не еда — настроение. Открыл банку — и в кухне пахнет садом и бабушкиным домом.</p>
            </div>
        </div>
    </div>
</section>

{{-- WITCH STORY --}}
<section class="section witch" id="about">
    <div class="container">
        <div class="witch__grid">

            <div class="witch__copy">
                <h2>Хозяйка медного <em>котла</em></h2>
                <p>
                    Где-то под Казанью, в избе с медной крышей, живёт девушка,
                    что варит лето в банки. Не сердитая старуха из страшилки,
                    а внучка её внучки: волосы в косу, на плечах рушник,
                    на печи — котёл, что помнит руки трёх поколений.
                </p>
                <p>
                    <span class="hand">Каждую ягоду она знает по имени.</span>
                    Малину варит на закате — для тёплой терпкости.
                    Лимон — на рассвете, для ясности.
                    Ежевику — в полнолуние, для тайны.
                </p>
                <div class="witch__quote">
                    «Варенье — это память лета, посаженная в стекло.<br>
                    Открой банку зимой — и снова услышишь, как шумит сад.»
                </div>
                <div class="witch__sig">
                    <svg width="36" height="36" viewBox="0 0 36 36" aria-hidden="true">
                        <path d="M18 18 m-1 0 a1 1 0 1 0 2 0 a1 1 0 1 0 -2 0
                             M18 18 m-5 0 a5 5 0 1 0 10 0
                             M18 18 m-9 0 a9 9 0 1 0 18 0
                             M18 18 m-13 0 a13 13 0 1 0 26 0"
                            stroke="currentColor" stroke-width="1.6" fill="none" />
                    </svg>
                    <div>
                        <div class="witch__sig-name">Мария Кощеевна</div>
                        <div>хозяйка медного котла</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ORACLE — flip-карты с предсказанием + блокировка остальных + CTA на каталог --}}
<section class="section oracle" id="oracle">
    <svg class="oracle__bg" viewBox="0 0 1400 800" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
        <g stroke="#FFFAF0" stroke-width="1" fill="none">
            @for ($i = 0; $i
            < 12; $i++)
                <circle cx="700" cy="400" r="{{ 50 + $i * 60 }}" />
            @endfor
        </g>
    </svg>
    <div class="container oracle__inner">
        <h3>Что нашептают <br><em>тебе</em> ягоды?</h3>
        <p class="oracle__intro">
            Тяни одну карту. Ягода сама шепнёт, что обещает неделя —
            и какую банку забрать с собой.
        </p>

        <div class="oracle__deck" id="oracle-deck">
            @for ($i = 0; $i < 5; $i++)
                <button type="button" class="oracle-card" data-index="{{ $i }}" aria-label="Карта оракула">
                <div class="oracle-card__inner">
                    <div class="oracle-card__face oracle-card__back">
                        <img src="{{ asset('media/card.png') }}" alt="card">
                        <div class="oracle-card__back-text">
                            Гостинецъ
                            <small>тяни карту</small>
                        </div>
                    </div>
                    <div class="oracle-card__face oracle-card__front"></div>
                </div>
                </button>
                @endfor
        </div>

        <div class="oracle__result" id="oracle-result"></div>
        <button type="button" class="oracle__reset" id="oracle-reset">Тянуть заново</button>
    </div>
</section>

{{-- FAQ --}}
<section class="section section--paper" id="questions">
    <div class="container">
        <div class="section-head">
            <h2 class="section-head__title">Глаголят <em>люди</em> добрые</h2>
            <p class="section-head__sub">шесть вопросов, что задают чаще всего на ярмарке</p>
        </div>

        @php
        $faqs = [
        ['q' => 'Из каких ягод варите варенье?',
        'a' => 'Только сезонные ягоды из садов Татарстана и тайных полян Заволжья. Собираем вручную в момент полной зрелости — когда ягода сама просится в корзинку.'],
        ['q' => 'Сколько хранится одна банка?',
        'a' => 'До двух зим в прохладном тёмном месте. После того как банку отворили — лучше съесть за месяц, чтобы вкус не растерял голоса.'],
        ['q' => 'Есть ли консерванты в составе?',
        'a' => 'Нет. Только ягода, сахар и капля лимона для баланса. Никакой химии — варим в медном тазу, как варили прабабки.'],
        ['q' => 'Можно ли заказать в подарок?',
        'a' => 'Конечно — для того и название «Гостинецъ». Соберём в льняной мешочек, повяжем рушником, вложим открытку с пожеланием от вашего имени.'],
        ['q' => 'Как происходит варка?',
        'a' => 'Малыми партиями, в медном тазу на открытом огне. Ягода не разваривается — каждая остаётся целой и пускает сок только когда сама захочет.'],
        ['q' => 'Бывает ли варенье без сахара?',
        'a' => 'Да — линейка на меду и стевии. Каждую такую банку отмечаем особым клеймом, чтобы не перепутать.'],
        ];
        @endphp

        <div class="faq__grid">
            <div class="faq__list">
                @foreach ($faqs as $i => $f)
                <div class="faq-item {{ $i === 0 ? '--open' : '' }}">
                    <div class="faq-item__head">
                        <h3 class="faq-item__q">{{ $f['q'] }}</h3>
                        <button type="button" class="faq-item__toggle" aria-label="Развернуть"></button>
                    </div>
                    <div class="faq-item__body">
                        <p>{{ $f['a'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <aside class="faq__aside">
                <span class="faq__aside-eyebrow">остался вопрос?</span>
                <h3>Спросить у хозяйки лично</h3>
                <p>Если не нашли ответа — позвоните или напишите. <br>Отвечаем не роботами, а голосом.</p>
                <a class="faq__aside-tel" href="tel:+78123090934">+7 (812) 309-09-34</a>
                <a class="faq__aside-tel --small" href="tel:+79650842909">+7 (965) 084-29-09</a>
                <p class="faq__aside-hours">пн–вс · 9:00–21:00 по Москве</p>
                <a href="https://web.max.ru/" class="btn-primary">
                    <span>Написать в Макс</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </a>
                <!-- <button type="button" class="btn-primary">
                    <span>Написать в Макс</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M5 12h14M13 6l6 6-6 6" />
                    </svg>
                </button> -->
            </aside>
        </div>
    </div>
</section>
@endsection