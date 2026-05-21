@extends('layouts.app')

@section('content')

<div style="text-align: center; padding: 80px 20px; min-height: 50vh;">
    <div style="font-family: var(--display); font-size: clamp(80px, 12vw, 140px); color: var(--burgundy); line-height: 1;">
        403
    </div>
    <h1 style="font-family: var(--display); font-size: 32px; color: var(--ink); margin: 18px 0;">
        Дверь <em style="color: var(--burgundy);">заперта</em>
    </h1>
    <p style="font-family: var(--serif); font-style: italic; font-size: 18px; color: var(--ink-2); max-width: 460px; margin: 0 auto 32px;">
        Сюда хозяйка не пускает. Может, нужно войти под своим именем.
    </p>
    <a href="{{ route('home') }}" class="btn-primary">На главную</a>
</div>

@endsection