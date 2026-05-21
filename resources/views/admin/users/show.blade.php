@extends('admin.layout')

@section('title', $user->name)
@section('heading', $user->name)

@section('content')

<a href="{{ route('admin.users.index') }}" class="admin-back">← к списку гостей</a>

<div class="admin-order">
    <div class="admin-order__main">

        <section class="admin-block">
            <header class="admin-block__head"><h2>Заказы гостя</h2></header>

            @if($user->orders->isEmpty())
                <p class="admin-empty admin-empty--small">Заказов пока нет.</p>
            @else
                <div class="admin-table-wrap">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Номер</th>
                                <th class="admin-th-right">Сумма</th>
                                <th>Статус</th>
                                <th>Когда</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($user->orders as $o)
                                <tr>
                                    <td class="admin-mono">
                                        <a href="{{ route('admin.orders.show', $o) }}" class="admin-link">
                                            {{ $o->number }}
                                        </a>
                                    </td>
                                    <td class="admin-th-right admin-mono">
                                        {{ number_format($o->total, 0, '.', ' ') }} ₽
                                    </td>
                                    <td>
                                        <span class="admin-status admin-status--{{ $o->status }}">
                                            {{ $o->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="admin-mute admin-mono">
                                        {{ $o->created_at->format('d.m.y H:i') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $o) }}" class="admin-iconbtn">
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
            @endif
        </section>
    </div>

    <aside class="admin-order__side">

        <section class="admin-block">
            <header class="admin-block__head"><h2>Контакты</h2></header>
            <dl class="admin-dl">
                <div><dt>Имя</dt><dd>{{ $user->name }}</dd></div>
                <div>
                    <dt>Email</dt>
                    <dd><a href="mailto:{{ $user->email }}" class="admin-link admin-tiny">{{ $user->email }}</a></dd>
                </div>
                <div>
                    <dt>Зарегистрирован</dt>
                    <dd>{{ $user->created_at->format('d.m.Y H:i') }}</dd>
                </div>
            </dl>
        </section>

        <section class="admin-block">
            <header class="admin-block__head"><h2>Сводка</h2></header>
            <dl class="admin-dl">
                <div><dt>Всего заказов</dt><dd class="admin-mono">{{ $stats['orders_count'] }}</dd></div>
                <div><dt>Оплачено</dt><dd class="admin-mono">{{ $stats['orders_paid'] }}</dd></div>
                <div><dt>Потрачено</dt><dd class="admin-mono"><strong>{{ number_format($stats['total_spent'], 0, '.', ' ') }} ₽</strong></dd></div>
                <div><dt>В котле</dt><dd class="admin-mono">{{ $stats['customs_count'] }}</dd></div>
            </dl>
        </section>

        <section class="admin-block">
            <header class="admin-block__head"><h2>Роль</h2></header>

            <div class="admin-status-current">
                @if($user->role === 'admin')
                    <span class="admin-tag admin-tag--gold">Хозяин</span>
                @else
                    <span class="admin-tag">Гость</span>
                @endif
            </div>

            @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.role', $user) }}" method="POST" class="admin-status-form">
                    @csrf
                    @method('PATCH')
                    <label class="admin-field">
                        <span class="admin-field__label">Изменить</span>
                        <select name="role">
                            <option value="user"  {{ $user->role === 'user'  ? 'selected' : '' }}>Гость</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Хозяин</option>
                        </select>
                    </label>
                    <button type="submit" class="admin-btn admin-btn--primary">Сохранить</button>
                </form>
                @error('role')
                    <div class="admin-field__err">{{ $message }}</div>
                @enderror
            @else
                <p class="admin-mute admin-tiny">Это твой аккаунт — менять роль нельзя.</p>
            @endif
        </section>
    </aside>
</div>

@endsection