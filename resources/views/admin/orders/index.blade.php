@extends('admin.layout')

@section('title', 'Заказы')
@section('heading', 'Заказы')

@section('content')

<div class="admin-toolbar">
    <nav class="admin-tabs" aria-label="Статус заказа">
        <a href="{{ route('admin.orders.index', request()->except('status')) }}"
           class="admin-tab {{ !$currentStatus ? '--active' : '' }}">
            Все <em>{{ $counts['all'] }}</em>
        </a>
        @foreach($statusLabels as $key => $label)
            <a href="{{ route('admin.orders.index', array_merge(request()->except('status'), ['status' => $key])) }}"
               class="admin-tab admin-tab--{{ $key }} {{ $currentStatus === $key ? '--active' : '' }}">
                {{ $label }} <em>{{ $counts[$key] }}</em>
            </a>
        @endforeach
    </nav>

    <form action="{{ route('admin.orders.index') }}" method="GET" class="admin-search">
        @if($currentStatus)
            <input type="hidden" name="status" value="{{ $currentStatus }}">
        @endif
        <input type="search" name="q" value="{{ $q }}"
               placeholder="Номер, email, телефон или имя…" class="admin-search__input">
        <button type="submit" class="admin-search__btn" aria-label="Найти">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
            </svg>
        </button>
    </form>
</div>

@if($orders->isEmpty())
    <div class="admin-empty admin-empty--large">
        <p>По выбранным условиям ничего не нашлось.</p>
        @if($q || $currentStatus)
            <a href="{{ route('admin.orders.index') }}" class="admin-link">сбросить фильтры</a>
        @endif
    </div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Номер</th>
                    <th>Покупатель</th>
                    <th>Доставка</th>
                    <th>Позиции</th>
                    <th class="admin-th-right">Сумма</th>
                    <th>Статус</th>
                    <th>Создан</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $o)
                    <tr>
                        <td>
                            <a href="{{ route('admin.orders.show', $o) }}" class="admin-link admin-mono">
                                {{ $o->number }}
                            </a>
                            @if($o->yookassa_payment_id)
                                <div class="admin-mute admin-tiny">ЮКасса</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $o->contact_name }}</div>
                            <small class="admin-mute">{{ $o->contact_email }}</small><br>
                            <small class="admin-mute">{{ $o->contact_phone }}</small>
                        </td>
                        <td>
                            <strong>{{ $o->deliveryLabel() }}</strong><br>
                            <small class="admin-mute">{{ $o->delivery_city }}</small>
                        </td>
                        <td class="admin-mono">{{ $o->items->sum('qty') }} шт.</td>
                        <td class="admin-th-right admin-mono">
                            <strong>{{ number_format($o->total, 0, '.', ' ') }} ₽</strong>
                        </td>
                        <td>
                            <span class="admin-status admin-status--{{ $o->status }}">{{ $o->statusLabel() }}</span>
                        </td>
                        <td class="admin-mute admin-mono">
                            {{ $o->created_at->format('d.m.y') }}<br>
                            <small>{{ $o->created_at->format('H:i') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.show', $o) }}" class="admin-iconbtn" title="Открыть">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M9 18l6-6-6-6"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $orders->links() }}</div>
@endif

@endsection