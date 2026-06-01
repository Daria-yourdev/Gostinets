@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="{{ asset('css/account.css') }}">

<div class="account-page">
    <div class="container">

        <header class="account-head">
            <h1>
                <span class="account-head__title">Моя грамота</span>
                <span class="account-head__sub">личные данные и адрес</span>
            </h1>
        </header>

        @if(session('flash'))
        <div style="background: rgba(92, 82, 56, 0.1); border-left: 4px solid var(--moss); padding: 12px 18px; margin-bottom: 24px; font-family: var(--serif); font-style: italic; color: var(--moss);">
            ✓ {{ session('flash') }}
        </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" class="profile-form">
            @csrf
            @method('PATCH')

            <div class="profile-grid">
                <section class="profile-section">
                    <h2 class="profile-section__title">Кто ты</h2>

                    <label class="profile-field">
                        <span class="profile-field__label">Имя <em>*</em></span>
                        <input type="text" name="name" required maxlength="60"
                            value="{{ old('name', $user->name) }}">
                        @error('name')<span class="field-error">{{ $message }}</span>@enderror
                    </label>

                    <label class="profile-field">
                        <span class="profile-field__label">Email</span>
                        <input type="email" value="{{ $user->email }}" readonly
                            style="background: var(--bg-warm); opacity: 0.7;">
                        <span style="font-family: var(--mono); font-size: 10px; color: var(--ink-2); opacity: 0.6;">Менять не можем — почта это твой ключ</span>
                    </label>

                    <label class="profile-field">
                        <span class="profile-field__label">Телефон</span>
                        <input type="tel" name="phone" maxlength="20"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="+7 (___) ___-__-__">
                        @error('phone')<span class="field-error">{{ $message }}</span>@enderror
                    </label>
                </section>

                <section class="profile-section">
                    <h2 class="profile-section__title">Куда нести гостинцы</h2>
                    <p class="profile-section__hint">
                        Это подставится автоматически при оформлении заказа — не нужно вводить каждый раз.
                    </p>

                    <div class="profile-row">
                        <label class="profile-field">
                            <span class="profile-field__label">Город</span>
                            <input type="text" name="delivery_city" maxlength="80"
                                value="{{ old('delivery_city', $user->delivery_city) }}"
                                placeholder="Казань">
                        </label>

                        <label class="profile-field">
                            <span class="profile-field__label">Индекс</span>
                            <input type="text" name="delivery_zip" maxlength="6"
                                value="{{ old('delivery_zip', $user->delivery_zip) }}"
                                placeholder="420012">
                        </label>
                    </div>

                    <label class="profile-field">
                        <span class="profile-field__label">Адрес</span>
                        <input type="text" name="delivery_address" maxlength="250"
                            value="{{ old('delivery_address', $user->delivery_address) }}"
                            placeholder="ул. Баумана, д. 15, кв. 7">
                    </label>

                    <label class="profile-field">
                        <span class="profile-field__label">Комментарий (домофон, этаж и т.п.)</span>
                        <textarea name="delivery_note" maxlength="250" rows="2"
                            placeholder="Например: код домофона 1234#, 3 этаж">{{ old('delivery_note', $user->delivery_note) }}</textarea>
                    </label>
                </section>
            </div>

            <button type="submit" class="btn-primary" style="margin-top: 20px;">
                <span>Сохранить</span>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M20 6 L9 17 L4 12" />
                </svg>
            </button>
        </form>

    </div>
</div>

<style>
    .profile-form {
        /*  max-width: 620px; */
        margin: 0 auto;
    }

    .profile-grid {
        display: flex;
        justify-content: flex-start;
        gap: 20px;
    }

    .profile-section {
        max-width: 620px;
        background: var(--paper);
        border: 1.5px solid var(--ink);
        padding: 24px 28px;
        margin-bottom: 18px;
/*         box-shadow: 4px 4px 0 rgba(73, 14, 14, 0.15); */
    }

    .profile-section__title {
        font-family: var(--local);
        font-size: 22px;
        color: var(--burgundy);
        margin: 0 0 6px;
        font-weight: normal;
    }

    .profile-section__hint {
        font-family: var(--serif);
        font-style: italic;
        font-size: 14px;
        color: var(--ink-2);
        opacity: 0.7;
        margin: 0 0 18px;
    }

    .profile-field {
        display: block;
        margin-bottom: 14px;
    }

    .profile-field__label {
        display: block;
        font-family: var(--mono);
        font-size: 10px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--ink-2);
        margin-bottom: 5px;
    }

    .profile-field__label em {
        color: var(--burgundy);
        font-style: normal;
        margin-left: 2px;
    }

    .profile-field input,
    .profile-field textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--line-strong, rgba(0, 0, 0, 0.2));
        background: var(--paper);
        color: var(--ink);
        font: inherit;
        font-size: 14px;
        box-sizing: border-box;
    }

    .profile-field textarea {
        resize: vertical;
        font-family: var(--serif);
    }

    .profile-row {
        display: grid;
        grid-template-columns: 1fr 200px;
        gap: 14px;
    }

    @media (max-width: 640px) {
        .profile-row {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection