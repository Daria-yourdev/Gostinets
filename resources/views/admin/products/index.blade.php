@extends('admin.layout')

@section('title', 'Кладовая')
@section('heading', 'Кладовая')

@section('content')

<div class="admin-toolbar">
    <form action="{{ route('admin.products.index') }}" method="GET" class="admin-toolbar__filters">
        <input type="search" name="q" value="{{ $q }}"
               placeholder="Название или slug…" class="admin-search__input">

        <select name="berry" class="admin-select" onchange="this.form.submit()">
            <option value="">Любая ягода</option>
            @foreach($berries as $key => $label)
                <option value="{{ $key }}" {{ $berry === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>

        <select name="status" class="admin-select" onchange="this.form.submit()">
            <option value="">Все</option>
            <option value="active"   {{ $status === 'active'   ? 'selected' : '' }}>Активные</option>
            <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Скрытые</option>
        </select>

        <button type="submit" class="admin-search__btn" aria-label="Найти">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>
            </svg>
        </button>
    </form>

    <a href="{{ route('admin.products.create') }}" class="admin-btn admin-btn--primary">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Новая банка
    </a>
</div>

@if($products->isEmpty())
    <div class="admin-empty admin-empty--large">
        <p>Ничего не нашлось.</p>
        @if($q || $berry || $status)
            <a href="{{ route('admin.products.index') }}" class="admin-link">сбросить фильтры</a>
        @endif
    </div>
@else
    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th></th>
                    <th>Название</th>
                    <th>Ягода / настроение</th>
                    <th class="admin-th-right">Цена</th>
                    <th class="admin-th-right">Остаток</th>
                    <th>Метка</th>
                    <th>Статус</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                    <tr class="{{ !$p->is_active ? 'admin-row--mute' : '' }}">
                        <td>
                            <div class="admin-thumb" style="--jam: {{ $p->jamColor() }}">
                                <img src="{{ asset($p->image_path ?: 'media/catalog/catalog-card-1.png') }}"
                                     alt="" loading="lazy">
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.products.edit', $p) }}" class="admin-link">
                                <strong>{{ $p->name }}</strong>
                            </a>
                            @if($p->subtitle)
                                <div class="admin-mute">{{ $p->subtitle }}</div>
                            @endif
                            <div class="admin-mono admin-tiny admin-mute">{{ $p->slug }}</div>
                        </td>
                        <td>
                            <strong>{{ $p->berryLabel() }}</strong><br>
                            <small class="admin-mute">{{ $p->moodLabel() }}</small>
                        </td>
                        <td class="admin-th-right admin-mono">
                            <strong>{{ $p->priceFormatted() }}</strong><br>
                            <small class="admin-mute">{{ $p->weight }} г</small>
                        </td>
                        <td class="admin-th-right admin-mono">
                            @if($p->stock <= 0)
                                <span class="admin-pill admin-pill--err">нет</span>
                            @elseif($p->stock < 5)
                                <span class="admin-pill admin-pill--warn">{{ $p->stock }}</span>
                            @else
                                {{ $p->stock }}
                            @endif
                        </td>
                        <td>
                            @if($p->badge)
                                <span class="admin-tag admin-tag--gold">{{ $p->badge }}</span>
                            @endif
                        </td>
                        <td>
                            @if($p->is_active)
                                <span class="admin-pill admin-pill--ok">в кладовой</span>
                            @else
                                <span class="admin-pill admin-pill--mute">скрыто</span>
                            @endif
                        </td>
                        <td class="admin-table__actions">
                            <a href="{{ route('admin.products.edit', $p) }}" class="admin-iconbtn" title="Редактировать">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.products.destroy', $p) }}" method="POST"
                                  class="admin-iconform" data-confirm="Удалить «{{ $p->name }}»?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="admin-iconbtn admin-iconbtn--danger" title="Удалить">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M3 6h18 M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2 M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="admin-pagination">{{ $products->links() }}</div>
@endif

@endsection