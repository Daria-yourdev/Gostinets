@extends('admin.layout')

@section('title', 'Котёл')
@section('heading', 'Котёл')

@section('content')

@php
$statuses = [
    'draft'     => 'Черновик',
    'ordered'   => 'Заказан',
    'cooking'   => 'Варится',
    'ready'     => 'Готов',
    'delivered' => 'Доставлен',
];
@endphp

<div class="admin-toolbar">
    <nav class="admin-tabs">
        <a href="{{ route('admin.custom-jams.index', request()->except('status')) }}"
           class="admin-tab {{ !$currentStatus ? '--active' : '' }}">
            Все <em>{{ $counts['all'] }}</em>
        </a>
        @foreach($statuses as $key => $label)
            <a href="{{ route('admin.custom-jams.index', array_merge(request()->except('status'), ['status' => $key])) }}"
               class="admin-tab admin-tab--{{ $key }} {{ $currentStatus === $key ? '--active' : '' }}">
                {{ $label }} <em>{{ $counts[$key] }}</em>
            </a>
        @endforeach
    </nav>

    <form action="{{ route('admin.custom-jams.index') }}" method="GET" class="admin-search">
        @if($currentStatus)<input type="hidden" name="status" value="{{ $currentStatus }}">@endif
        <input type="search" name="q" value="{{ $q }}" placeholder="Имя, ягода или email…" class="admin-search__input">
        <button type="submit" class="admin-search__btn" aria-label="Найти">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
            </svg>
        </button>
    </form>
</div>

@if($jams->isEmpty())
    <div class="admin-empty admin-empty--large">
        <p>В котле пусто.</p>
    </div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Имя банки</th>
                    <th>Кто заказал</th>
                    <th>Состав</th>
                    <th class="admin-th-right">Цена</th>
                    <th>Статус</th>
                    <th>Создан</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($jams as $j)
                    <tr>
                        <td class="admin-mono">#{{ $j->id }}</td>
                        <td>
                            <a href="{{ route('admin.custom-jams.show', $j) }}" class="admin-link">
                                <strong>{{ $j->label_name ?: '— без имени —' }}</strong>
                            </a>
                            <div class="admin-mute">{{ $j->jar_size }} мл</div>
                        </td>
                        <td>
                            @if($j->user)
                                <a href="{{ route('admin.users.show', $j->user) }}" class="admin-link">
                                    {{ $j->user->name }}
                                </a><br>
                                <small class="admin-mute">{{ $j->user->email }}</small>
                            @else
                                <span class="admin-mute">гость</span>
                            @endif
                        </td>
                        <td>
                            <strong>{{ $j->berry_main }}</strong>
                            @php
                                $extras = is_array($j->berry_extras) ? $j->berry_extras : json_decode($j->berry_extras ?? '[]', true);
                                $spices = is_array($j->spices) ? $j->spices : json_decode($j->spices ?? '[]', true);
                            @endphp
                            @if(!empty($extras))
                                <br><small class="admin-mute">+ {{ implode(', ', $extras) }}</small>
                            @endif
                            @if(!empty($spices))
                                <br><small class="admin-mute">{{ implode(' · ', $spices) }}</small>
                            @endif
                        </td>
                        <td class="admin-th-right admin-mono">
                            <strong>{{ number_format($j->price, 0, '.', ' ') }} ₽</strong>
                        </td>
                        <td>
                            <span class="admin-status admin-status--{{ $j->status }}">
                                {{ $statuses[$j->status] ?? $j->status }}
                            </span>
                        </td>
                        <td class="admin-mute admin-mono">
                            {{ $j->created_at->format('d.m H:i') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.custom-jams.show', $j) }}" class="admin-iconbtn" title="Открыть">
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

    <div class="admin-pagination">{{ $jams->links() }}</div>
@endif

@endsection