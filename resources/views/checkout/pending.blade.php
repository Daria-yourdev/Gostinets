@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">

<div class="checkout-result checkout-result--pending">
    <div class="container">

        <div class="checkout-result__inner">
            <div class="checkout-result__spinner" aria-hidden="true"></div>

            <span class="checkout-result__kicker">обрабатываем</span>
            <h1 class="checkout-result__title">
                Подтверждаем <em>оплату</em>...
            </h1>

            <p class="checkout-result__lede">
                Платёж по заказу <strong>{{ $order->number }}</strong> проверяется банком.
                Это занимает до минуты — страница обновится сама.
            </p>

            <div class="checkout-result__actions">
                <a href="{{ route('checkout.return', $order) }}" class="btn-ghost">
                    Обновить вручную
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Auto-refresh через 8 секунд --}}
<meta http-equiv="refresh" content="8;url={{ route('checkout.return', $order) }}">

@endsection
