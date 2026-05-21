@extends('admin.layout')

@section('title', 'Сводка')
@section('heading', 'Сводка')

@section('content')

{{-- Карточки с метриками --}}
<div class="admin-cards">
    <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="admin-card">
        <span class="admin-card__label">Ждут оплаты</span>
        <strong class="admin-card__value">{{ $ordersPending }}</strong>
        <span class="admin-card__hint">за всё время</span>
    </a>

    <a href="{{ route('admin.orders.index', ['status' => 'paid']) }}" class="admin-card">
        <span class="admin-card__label">Оплачены</span>
        <strong class="admin-card__value">{{ $ordersPaid }}</strong>
        <span class="admin-card__hint">ждут варки и упаковки</span>
    </a>

    <div class="admin-card">
        <span class="admin-card__label">Заказов сегодня</span>
        <strong class="admin-card__value">{{ $ordersToday }}</strong>
        <span class="admin-card__hint">за 7 дней — {{ $ordersWeek }}</span>
    </div>

    <div class="admin-card admin-card--accent">
        <span class="admin-card__label">Выручка за 30 дней</span>
        <strong class="admin-card__value">{{ number_format($revenueMonth, 0, '.', ' ') }} <small>₽</small></strong>
        <span class="admin-card__hint">сегодня — {{ number_format($revenueToday, 0, '.', ' ') }} ₽</span>
    </div>
</div>

{{-- Двухколоночный блок: последние заказы + сайдбар с прочим --}}
<div class="admin-row">

    {{-- Последние заказы --}}
    <section class="admin-block">
        <header class="admin-block__head">
            <h2>Последние заказы</h2>
            <a href="{{ route('admin.orders.index') }}" class="admin-block__more">все →</a>
        </header>

        @if($latestOrders->isEmpty())
            <p class="admin-empty">Пока никто не заказывал. Подожди.</p>
        @else
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Номер</th>
                            <th>Покупатель</th>
                            <th>Сумма</th>
                            <th>Статус</th>
                            <th>Когда</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($latestOrders as $o)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.orders.show', $o) }}" class="admin-link admin-mono">
                                        {{ $o->number }}
                                    </a>
                                </td>
                                <td>
                                    <div>{{ $o->contact_name }}</div>
                                    <small class="admin-mute">{{ $o->contact_email }}</small>
                                </td>
                                <td class="admin-mono">{{ number_format($o->total, 0, '.', ' ') }} ₽</td>
                                <td>
                                    <span class="admin-status admin-status--{{ $o->status }}">
                                        {{ $o->statusLabel() }}
                                    </span>
                                </td>
                                <td class="admin-mute admin-mono">
                                    {{ $o->created_at->format('d.m H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Боковой блок --}}
    <aside class="admin-row__side">

        {{-- Топ товаров --}}
        <section class="admin-block">
            <header class="admin-block__head">
                <h2>Лучше всего идут</h2>
                <span class="admin-block__hint">за 30 дней</span>
            </header>

            @if($topProducts->isEmpty())
                <p class="admin-empty admin-empty--small">Пока тихо.</p>
            @else
                <ol class="admin-toplist">
                    @foreach($topProducts as $i => $p)
                        <li>
                            <span class="admin-toplist__rank">{{ $i + 1 }}</span>
                            <span class="admin-toplist__name">{{ $p->product_name }}</span>
                            <span class="admin-toplist__qty">{{ $p->total_qty }} шт.</span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </section>

        {{-- Низкие остатки --}}
        @if($lowStock->count() > 0)
            <section class="admin-block admin-block--warn">
                <header class="admin-block__head">
                    <h2>Заканчивается</h2>
                    <span class="admin-block__hint">меньше 5 банок</span>
                </header>
                <ul class="admin-toplist admin-toplist--warn">
                    @foreach($lowStock as $p)
                        <li>
                            <a href="{{ route('admin.products.edit', $p) }}" class="admin-toplist__link">
                                <span class="admin-toplist__name">{{ $p->name }}</span>
                                <span class="admin-toplist__qty admin-toplist__qty--low">
                                    {{ $p->stock }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        {{-- Сводка по магазину --}}
        <section class="admin-block">
            <header class="admin-block__head">
                <h2>Всего в магазине</h2>
            </header>
            <dl class="admin-stats">
                <div>
                    <dt>В кладовой</dt>
                    <dd>{{ $productsActive }} <small>из {{ $productsTotal }}</small></dd>
                </div>
                <div>
                    <dt>Гостей зарегистрировано</dt>
                    <dd>{{ $usersTotal }}</dd>
                </div>
                <div>
                    <dt>В котле ждут</dt>
                    <dd>{{ $customsPending }}</dd>
                </div>
            </dl>
        </section>

    </aside>
</div>

@endsection