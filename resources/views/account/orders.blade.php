@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/account.css') }}">

<div class="account-page">
    <div class="container">

        <header class="account-head">
            <h1>
                <span class="account-head__title">Мои запасы</span>
                <span class="account-head__sub">история заказов</span>
            </h1>
        </header>

        {{-- ОБЫЧНЫЕ ЗАКАЗЫ --}}
        @if($orders->isEmpty() && $jams->isEmpty())

        <div class="account-empty">
            <svg viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                <path d="M22 28 L58 28 L54 64 Q54 70 48 70 L32 70 Q26 70 26 64 Z" />
                <path d="M18 28 L62 28" stroke-width="2.5" />
                <path d="M30 22 L30 16 Q30 10 36 10 L44 10 Q50 10 50 16 L50 22" />
            </svg>
            <h2>Пока ничего</h2>
            <p>Загляни в кладовую — там ждут варенья.</p>
            <a href="{{ route('catalog') }}" class="btn-primary">
                <span>В кладовую</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" aria-hidden="true">
                    <path d="M5 12h14M13 6l6 6-6 6" />
                </svg>
            </a>
        </div>

        @else

        @if($orders->count() > 0)
        <section class="account-section">
            <h2 class="account-section__title">Заказы</h2>

            <div class="account-orders">
                @foreach($orders as $o)
                <article class="account-order">
                    <header class="account-order__head">
                        <span class="account-order__number">{{ $o->number }}</span>
                        <span class="account-status account-status--{{ $o->status }}">
                            {{ $o->statusLabel() }}
                        </span>
                        <time class="account-order__date">
                            {{ $o->created_at->format('d.m.Y') }}
                        </time>
                    </header>

                    {{-- Позиции --}}
                    <ul class="account-order__items">
                        @foreach($o->items as $item)
                        <li class="account-order__item">
                            <div class="account-order__item-img">
                                <img src="{{ asset($item->product_image ?: 'media/cotel/kastom_card.jpg') }}"
                                    alt="{{ $item->product_name }}" loading="lazy">
                            </div>
                            <div class="account-order__item-info">
                                <strong>{{ $item->product_name }}</strong>
                                @if($item->product_subtitle)
                                <span>{{ $item->product_subtitle }}</span>
                                @endif
                            </div>
                            <div class="account-order__item-qty">{{ $item->qty }} шт.</div>
                            <div class="account-order__item-price">{{ $item->subtotalFormatted() }}</div>
                        </li>
                        @endforeach
                    </ul>

                    <footer class="account-order__foot">
                        <div class="account-order__delivery">
                            {{ $o->deliveryLabel() }} · {{ $o->delivery_city }}
                        </div>
                        <div class="account-order__total">
                            Итого: <strong>{{ $o->totalFormatted() }}</strong>
                        </div>
                    </footer>

                    @if($o->isPending())
                    <div class="account-order__actions">
                        <a href="{{ route('checkout.return', $o) }}" class="account-link">
                            → Вернуться к оплате
                        </a>
                    </div>
                    @endif
                </article>
                @endforeach
            </div>

            <div class="account-pagination">
                {{ $orders->links() }}
            </div>
        </section>
        @endif

        {{-- КАСТОМНЫЕ ВАРЕНЬЯ --}}
        @if($jams->count() > 0)
        @php
        $jamStatuses = [
        'ordered' => 'В очереди',
        'cooking' => 'Варится',
        'ready' => 'Готово',
        'delivered' => 'Доставлено',
        ];
        @endphp
        <section class="account-section">
            <h2 class="account-section__title">Котёл</h2>
            <p class="account-section__hint">Кастомные варенья, сваренные специально для тебя</p>

            <div class="account-jams">
                @foreach($jams as $j)
                @php
                $extras = is_array($j->berry_extras) ? $j->berry_extras : json_decode($j->berry_extras ?? '[]', true);
                $spices = is_array($j->spices) ? $j->spices : json_decode($j->spices ?? '[]', true);
                @endphp
                <article class="account-jam">
                    <div class="account-jam__head">
                        <strong class="account-jam__name">
                            «{{ $j->label_name ?: 'Безымянный котёл' }}»
                        </strong>
                        <span class="account-status account-status--{{ $j->status }}">
                            {{ $jamStatuses[$j->status] ?? $j->status }}
                        </span>
                    </div>
                    <ul class="account-jam__props">
                        <li><span>Ягода</span> {{ $j->berry_main }}</li>
                        @if(!empty($extras))
                        <li><span>Дополнительно</span> {{ implode(', ', $extras) }}</li>
                        @endif
                        @if(!empty($spices))
                        <li><span>Специи</span> {{ implode(' · ', $spices) }}</li>
                        @endif
                        <li><span>Объём</span> {{ $j->jar_size }} мл</li>
                        @if($j->dedication)
                        <li><span>Посвящение</span> <em>«{{ $j->dedication }}»</em></li>
                        @endif
                    </ul>
                    <div class="account-jam__foot">
                        <span class="account-jam__price">{{ number_format($j->price, 0, '.', ' ') }} ₽</span>
                        <span class="account-jam__date">{{ $j->created_at->format('d.m.Y') }}</span>
                    </div>
                </article>
                @endforeach
            </div>
        </section>
        @endif

        @endif

    </div>
</div>

@endsection