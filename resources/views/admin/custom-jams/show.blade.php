@extends('admin.layout')

@section('title', "Кастом #{$jam->id}")
@section('heading', "Кастом #{$jam->id}")

@section('content')

<a href="{{ route('admin.custom-jams.index') }}" class="admin-back">← к котлу</a>

@php
$statuses = [
    'draft'     => 'Черновик',
    'ordered'   => 'Заказан',
    'cooking'   => 'Варится',
    'ready'     => 'Готов',
    'delivered' => 'Доставлен',
];
$extras = is_array($jam->berry_extras) ? $jam->berry_extras : json_decode($jam->berry_extras ?? '[]', true);
$spices = is_array($jam->spices) ? $jam->spices : json_decode($jam->spices ?? '[]', true);
@endphp

<div class="admin-order">
    <div class="admin-order__main">

        <section class="admin-block">
            <header class="admin-block__head">
                <h2>«{{ $jam->label_name ?: 'Без имени' }}»</h2>
                <span class="admin-block__hint">{{ $jam->jar_size }} мл</span>
            </header>

            <dl class="admin-dl">
                <div>
                    <dt>Главная ягода</dt>
                    <dd><strong>{{ $jam->berry_main }}</strong></dd>
                </div>
                @if(!empty($extras))
                    <div>
                        <dt>Дополнительно</dt>
                        <dd>{{ implode(', ', $extras) }}</dd>
                    </div>
                @endif
                @if(!empty($spices))
                    <div>
                        <dt>Специи</dt>
                        <dd>{{ implode(' · ', $spices) }}</dd>
                    </div>
                @endif
                <div>
                    <dt>Подсластитель</dt>
                    <dd>{{ $jam->sweetener ?: 'не указан' }}</dd>
                </div>
                @if($jam->dedication)
                    <div>
                        <dt>Посвящение</dt>
                        <dd><em>«{{ $jam->dedication }}»</em></dd>
                    </div>
                @endif
                @if($jam->whisper)
                    <div>
                        <dt>Шёпот ягоды</dt>
                        <dd class="admin-mute"><em>{{ $jam->whisper }}</em></dd>
                    </div>
                @endif
            </dl>
        </section>

        <section class="admin-block">
            <header class="admin-block__head"><h2>Заказчик</h2></header>
            @if($jam->user)
                <dl class="admin-dl">
                    <div>
                        <dt>Имя</dt>
                        <dd>
                            <a href="{{ route('admin.users.show', $jam->user) }}" class="admin-link">
                                {{ $jam->user->name }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt>Email</dt>
                        <dd><a href="mailto:{{ $jam->user->email }}" class="admin-link">{{ $jam->user->email }}</a></dd>
                    </div>
                </dl>
            @else
                <p class="admin-mute">Гость без аккаунта</p>
            @endif
        </section>
    </div>

    <aside class="admin-order__side">

        <section class="admin-block">
            <header class="admin-block__head"><h2>Цена</h2></header>
            <div class="admin-bigprice admin-mono">
                {{ number_format($jam->price, 0, '.', ' ') }} ₽
            </div>
        </section>

        <section class="admin-block">
            <header class="admin-block__head"><h2>Статус</h2></header>

            <div class="admin-status-current admin-status admin-status--{{ $jam->status }}">
                {{ $statuses[$jam->status] ?? $jam->status }}
            </div>

            <form action="{{ route('admin.custom-jams.status', $jam) }}" method="POST" class="admin-status-form">
                @csrf
                @method('PATCH')
                <label class="admin-field">
                    <span class="admin-field__label">Изменить</span>
                    <select name="status">
                        @foreach($statuses as $key => $label)
                            <option value="{{ $key }}" {{ $jam->status === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </label>
                <button type="submit" class="admin-btn admin-btn--primary">Сохранить</button>
            </form>
        </section>

        <!-- <section class="admin-block">
            <header class="admin-block__head"><h2>Когда</h2></header>
            <dl class="admin-dl">
                <div>
                    <dt>Создан</dt>
                    <dd>{{ $jam->created_at->format('d.m.Y H:i') }}</dd>
                </div>
                <div>
                    <dt>Изменён</dt>
                    <dd>{{ $jam->updated_at->format('d.m.Y H:i') }}</dd>
                </div>
            </dl>
        </section> -->
    </aside>
</div>

@endsection