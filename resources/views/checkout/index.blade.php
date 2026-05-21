@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">

<div class="checkout-page">
    <div class="container">

        <nav class="breadcrumbs" aria-label="Хлебные крошки">
            <a href="{{ route('home') }}">Начало</a>
            <span aria-hidden="true">·</span>
            <a href="{{ route('cart') }}">Запасы</a>
            <span aria-hidden="true">·</span>
            <span class="breadcrumbs__current">Оформление</span>
        </nav>

        <header class="checkout-head">
            <h1>Гостинцу — <em>путь</em></h1>
            <p>
                Куда нести, кому передать. Мария уложит банки в льняной мешочек
                и отправит выбранной службой.
            </p>
        </header>

        <form class="checkout" method="POST" action="{{ route('checkout.store') }}" id="checkout-form" novalidate>
            @csrf

            <div class="checkout__grid">

                {{-- ==================== ЛЕВАЯ КОЛОНКА — ФОРМА ==================== --}}
                <div class="checkout__main">

                    {{-- ----- КОНТАКТЫ ----- --}}
                    <section class="checkout-block">
                        <header class="checkout-block__head">
                            <span class="checkout-block__num">01</span>
                            <h2 class="checkout-block__title">Кто гость?</h2>
                            <p class="checkout-block__hint">контакты получателя</p>
                        </header>

                        <div class="checkout-fields checkout-fields--2col">
                            <label class="checkout-field">
                                <span class="checkout-field__label">Имя</span>
                                <input type="text" name="contact_name" maxlength="100"
                                       value="{{ old('contact_name', $user?->name) }}"
                                       placeholder="Дарья" required>
                                @error('contact_name')
                                    <span class="checkout-field__error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="checkout-field">
                                <span class="checkout-field__label">Почта</span>
                                <input type="email" name="contact_email" maxlength="160"
                                       value="{{ old('contact_email', $user?->email) }}"
                                       placeholder="daria@example.ru" required>
                                @error('contact_email')
                                    <span class="checkout-field__error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="checkout-field checkout-field--wide">
                                <span class="checkout-field__label">Телефон</span>
                                <input type="tel" name="contact_phone" maxlength="32"
                                       value="{{ old('contact_phone') }}"
                                       placeholder="+7 999 123-45-67" required>
                                <span class="checkout-field__hint">для курьера, мы не звоним без надобности</span>
                                @error('contact_phone')
                                    <span class="checkout-field__error">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </section>

                    {{-- ----- ДОСТАВКА ----- --}}
                    <section class="checkout-block">
                        <header class="checkout-block__head">
                            <span class="checkout-block__num">02</span>
                            <h2 class="checkout-block__title">Как везти?</h2>
                            <p class="checkout-block__hint">
                                @if($subtotal >= $freeShipFrom)
                                    доставка <strong>бесплатно</strong> — заказ от {{ number_format($freeShipFrom, 0, '.', ' ') }} ₽
                                @else
                                    от {{ number_format($freeShipFrom, 0, '.', ' ') }} ₽ — доставка бесплатна
                                @endif
                            </p>
                        </header>

                        <div class="checkout-delivery">
                            @foreach($deliveryMethods as $slug => $info)
                                @php
                                    $isFree = $subtotal >= $freeShipFrom && $info['cost'] > 0;
                                    $displayCost = $isFree ? 0 : $info['cost'];
                                @endphp
                                <label class="checkout-delivery__option">
                                    <input type="radio" name="delivery_method" value="{{ $slug }}"
                                           data-cost="{{ $displayCost }}"
                                           {{ old('delivery_method', 'cdek') === $slug ? 'checked' : '' }}
                                           required>
                                    <span class="checkout-delivery__inner">
                                        <span class="checkout-delivery__dot" aria-hidden="true"></span>
                                        <span class="checkout-delivery__text">
                                            <strong>{{ $info['label'] }}</strong>
                                            <small>{{ $info['eta'] }}</small>
                                        </span>
                                        <span class="checkout-delivery__cost">
                                            @if($displayCost === 0)
                                                <em>бесплатно</em>
                                            @else
                                                {{ $displayCost }} ₽
                                            @endif
                                        </span>
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <div class="checkout-fields checkout-fields--2col" style="margin-top: 20px;">
                            <label class="checkout-field">
                                <span class="checkout-field__label">Город</span>
                                <input type="text" name="delivery_city" maxlength="80"
                                       value="{{ old('delivery_city') }}"
                                       placeholder="Казань" required>
                                @error('delivery_city')
                                    <span class="checkout-field__error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="checkout-field">
                                <span class="checkout-field__label">Индекс</span>
                                <input type="text" name="delivery_zip" maxlength="16"
                                       value="{{ old('delivery_zip') }}"
                                       placeholder="420012" inputmode="numeric">
                            </label>

                            <label class="checkout-field checkout-field--wide">
                                <span class="checkout-field__label">Адрес</span>
                                <input type="text" name="delivery_address" maxlength="200"
                                       value="{{ old('delivery_address') }}"
                                       placeholder="ул. Баумана, 12, кв. 34" required>
                                @error('delivery_address')
                                    <span class="checkout-field__error">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="checkout-field checkout-field--wide">
                                <span class="checkout-field__label">
                                    Комментарий для курьера <small>(не обязательно)</small>
                                </span>
                                <textarea name="delivery_note" maxlength="500" rows="2"
                                          placeholder="Код домофона, как лучше передать, особые пожелания…">{{ old('delivery_note') }}</textarea>
                            </label>
                        </div>
                    </section>

                    {{-- ----- ОПЛАТА ----- --}}
                    <section class="checkout-block">
                        <header class="checkout-block__head">
                            <span class="checkout-block__num">03</span>
                            <h2 class="checkout-block__title">Чем расплатимся?</h2>
                            <p class="checkout-block__hint">оплата через ЮКассу — безопасно</p>
                        </header>

                        <div class="checkout-payment">
                            <div class="checkout-payment__option checkout-payment__option--active">
                                <div class="checkout-payment__icon" aria-hidden="true">
                                    <svg viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <rect x="1" y="1" width="30" height="20" rx="2"
                                              fill="var(--paper)" stroke="var(--ink)" stroke-width="1.5"/>
                                        <rect x="1" y="5" width="30" height="4" fill="var(--burgundy)"/>
                                        <rect x="5" y="14" width="8" height="2" fill="var(--ink)"/>
                                        <rect x="5" y="17" width="14" height="1.5" fill="var(--ink-2)"/>
                                    </svg>
                                </div>
                                <div class="checkout-payment__text">
                                    <strong>Банковская карта</strong>
                                    <small>Visa, Mastercard, МИР · через ЮКассу</small>
                                </div>
                                <div class="checkout-payment__check" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 6 L9 17 L4 12"/>
                                    </svg>
                                </div>
                            </div>

                            <p class="checkout-payment__note">
                                После нажатия «Расплатиться» откроется защищённая страница ЮКассы
                                для ввода данных карты. Гостинецъ не хранит реквизиты.
                            </p>
                        </div>
                    </section>

                    {{-- ----- СОГЛАСИЕ ----- --}}
                    <section class="checkout-block checkout-block--agreement">
                        <label class="checkout-agree">
                            <input type="checkbox" name="agree" value="1"
                                   {{ old('agree') ? 'checked' : '' }} required>
                            <span class="checkout-agree__box" aria-hidden="true"></span>
                            <span class="checkout-agree__text">
                                Согласен с <a href="#" target="_blank">правилами заказа</a>
                                и <a href="#" target="_blank">обработкой персональных данных</a>
                            </span>
                        </label>
                        @error('agree')
                            <span class="checkout-field__error" style="display: block; margin-top: 8px;">{{ $message }}</span>
                        @enderror
                    </section>

                </div>

                {{-- ==================== ПРАВАЯ КОЛОНКА — СВОДКА ==================== --}}
                <aside class="checkout-summary" id="checkout-summary">
                    <h2 class="checkout-summary__title">В мешочке</h2>

                    <ul class="checkout-summary__items">
                        @foreach($items as $row)
                            @php $p = $row['product']; @endphp
                            <li class="checkout-summary__item" style="--jam: {{ $p->jamColor() }}">
                                <div class="checkout-summary__visual">
                                    <img src="{{ asset($p->image_path ?: 'media/catalog/catalog-card-1.png') }}"
                                         alt="{{ $p->name }}" loading="lazy">
                                    <span class="checkout-summary__qty">{{ $row['qty'] }}</span>
                                </div>
                                <div class="checkout-summary__item-info">
                                    <strong>{{ $p->name }}</strong>
                                    <small>{{ $p->subtitle }}</small>
                                </div>
                                <div class="checkout-summary__item-price">
                                    {{ number_format($row['subtotal'], 0, '.', ' ') }} ₽
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <hr class="checkout-summary__divider">

                    <div class="checkout-summary__row">
                        <span>За банки</span>
                        <strong>{{ number_format($subtotal, 0, '.', ' ') }} ₽</strong>
                    </div>

                    <div class="checkout-summary__row" id="summary-delivery-row">
                        <span>Доставка</span>
                        <strong id="summary-delivery">—</strong>
                    </div>

                    <hr class="checkout-summary__divider">

                    <div class="checkout-summary__total">
                        <span>Итого к оплате</span>
                        <strong id="summary-total">{{ number_format($subtotal, 0, '.', ' ') }} ₽</strong>
                    </div>

                    <button type="submit" class="btn-primary checkout-submit" id="checkout-submit">
                        <span class="checkout-submit__text">Расплатиться</span>
                        <span class="checkout-submit__amount" id="submit-amount">
                            · <span id="submit-total">{{ number_format($subtotal, 0, '.', ' ') }}</span> ₽
                        </span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M5 12h14M13 6l6 6-6 6"/>
                        </svg>
                        <span class="checkout-submit__spinner" aria-hidden="true"></span>
                    </button>

                    <div class="checkout-summary__safety">
                        <svg viewBox="0 0 16 16" fill="none" stroke="currentColor"
                             stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M8 14.6s5.3-2.7 5.3-6.6V3.3L8 1.3 2.7 3.3v4.7c0 3.9 5.3 6.6 5.3 6.6z"/>
                            <path d="M6 8l1.3 1.3 2.7-2.6"/>
                        </svg>
                        <span>Защищённая оплата ЮКассы</span>
                    </div>
                </aside>

            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/checkout.js') }}" defer></script>

@endsection
