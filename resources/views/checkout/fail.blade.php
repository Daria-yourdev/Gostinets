@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">

<div class="checkout-result checkout-result--fail">
    <div class="container">

        <div class="checkout-result__inner">
            <div class="checkout-result__seal" aria-hidden="true">
                <svg viewBox="0 0 120 120" fill="none">
                    <circle cx="60" cy="60" r="56" stroke="currentColor" stroke-width="0.8" opacity="0.3"/>
                    <circle cx="60" cy="60" r="46" stroke="currentColor" stroke-width="1" opacity="0.55"/>
                    <circle cx="60" cy="60" r="34" stroke="currentColor" stroke-width="2"/>
                    <path d="M48 48 L72 72 M72 48 L48 72" stroke="currentColor"
                          stroke-width="3" stroke-linecap="round" fill="none"/>
                    <text x="60" y="98" text-anchor="middle" font-family="JetBrains Mono"
                          font-size="6" letter-spacing="1.4" fill="currentColor" stroke="none">ОТМЕНЕНО</text>
                </svg>
            </div>

            <span class="checkout-result__kicker">не сложилось</span>
            <h1 class="checkout-result__title">
                Оплата <em>не прошла</em>.
            </h1>

            <p class="checkout-result__lede">
                @if(session('flash'))
                    {{ session('flash') }}
                @else
                    Может, на карте не хватило, может — банк отказал. Заказ
                    <strong>{{ $order->number }}</strong> сохранён,
                    можно попробовать оплатить снова.
                @endif
                <br><br>
                Банки в твоём мешочке мы не тронули — они ждут.
            </p>

            <div class="checkout-result__actions">
                <a href="{{ route('checkout') }}" class="btn-primary">
                    <span>Попробовать снова</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M3 12a9 9 0 1 0 3-6.7"/>
                        <path d="M3 4v5h5"/>
                    </svg>
                </a>
                <a href="{{ route('cart') }}" class="btn-ghost">
                    Вернуться в мешочек
                </a>
            </div>

            <p class="checkout-result__hint">
                Если что-то странное с оплатой — напиши:
                <a href="mailto:hello@gostinec.ru">hello@gostinec.ru</a>
            </p>
        </div>
    </div>
</div>

@endsection
