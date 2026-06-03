@extends('admin.layout')

@section('title', 'Пользователи')
@section('heading', 'Пользователи')

@section('content')

<div class="admin-toolbar">
    <form action="{{ route('admin.users.index') }}" method="GET" class="admin-toolbar__filters">
        <input type="search" name="q" value="{{ $q }}"
               placeholder="Имя или email…" class="admin-search__input">

        <select name="role" class="admin-select" onchange="this.form.submit()">
            <option value="">Все роли</option>
            <option value="user"  {{ $role === 'user'  ? 'selected' : '' }}>Гости</option>
            <option value="admin" {{ $role === 'admin' ? 'selected' : '' }}>Хозяева</option>
        </select>

        <button type="submit" class="admin-search__btn" aria-label="Найти">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
            </svg>
        </button>
    </form>
</div>

@if($users->isEmpty())
    <div class="admin-empty admin-empty--large"><p>Ничего не нашлось.</p></div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th class="admin-th-right">Заказов</th>
                    <th>Регистрация</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $u)
                    <tr>
                        <td>
                            <a href="{{ route('admin.users.show', $u) }}" class="admin-link">
                                <strong>{{ $u->name }}</strong>
                            </a>
                        </td>
                        <td class="admin-mono admin-tiny">{{ $u->email }}</td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="admin-tag admin-tag--gold">Хозяин</span>
                            @else
                                <span class="admin-tag">Гость</span>
                            @endif
                        </td>
                        <td class="admin-th-right admin-mono">{{ $u->orders_count }}</td>
                        <td class="admin-mute admin-mono">{{ $u->created_at->format('d.m.y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $u) }}" class="admin-iconbtn" title="Открыть">
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

    <div class="admin-pagination">{{ $users->links() }}</div>
@endif

@endsection