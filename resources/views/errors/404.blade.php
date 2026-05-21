@extends('layouts.app')

@section('content')

<div style="text-align: center; padding: 80px 20px; min-height: 50vh;">
    <div style="font-family: var(--display); font-size: clamp(80px, 12vw, 140px); color: var(--burgundy); line-height: 1;">
        404
    </div>
    <h1 style="font-family: var(--display); font-size: 32px; color: var(--ink); margin: 18px 0;">
        Тропка <em style="color: var(--burgundy);">потерялась</em>
    </h1>
    <p style="font-family: var(--serif); font-style: italic; font-size: 18px; color: var(--ink-2); max-width: 460px; margin: 0 auto 32px;">
        Такой страницы нет. Может, перепутан адрес, а может — страницу увели лесные духи.
    </p>
    <a href="{{ route('home') }}" class="btn-primary">
        <span>На главную</span>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M5 12h14M13 6l6 6-6 6"/>
        </svg>
    </a>
</div>

@endsection