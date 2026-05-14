{{-- Партиал с SVG-иконками ягод для конструктора кастомного варенья.
     Используется в cauldron.blade.php
     Принимает: $berry (slug ягоды) --}}

@switch($berry)
@case('vishnya')
{{-- Вишня — две ягоды на черенках --}}
<img src="{{ asset('media/cotel/icon-vishnya.svg') }}" alt="">
<!-- <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M22 8 Q26 18 28 28" stroke="#5C5238" stroke-width="2" stroke-linecap="round" fill="none"/>
            <path d="M42 8 Q38 18 36 28" stroke="#5C5238" stroke-width="2" stroke-linecap="round" fill="none"/>
            <path d="M22 8 Q32 4 42 8" stroke="#7E1A1A" stroke-width="2" stroke-linecap="round" fill="none"/>
            <circle cx="22" cy="40" r="12" fill="#7E1A1A"/>
            <circle cx="42" cy="42" r="12" fill="#5B1212"/>
            <ellipse cx="18" cy="36" rx="3" ry="2" fill="#A04444" opacity="0.6"/>
            <ellipse cx="38" cy="38" rx="3" ry="2" fill="#7E2A2A" opacity="0.6"/>
        </svg> -->
@break

@case('malina')
{{-- Малина — гроздь точек --}}
<img src="{{ asset('media/cotel/icon-malina.svg') }}" alt="">
<!-- <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M28 8 L32 18 L36 8" stroke="#5C5238" stroke-width="2" stroke-linecap="round" fill="none"/>
            <path d="M24 14 Q32 8 40 14" stroke="#5C5238" stroke-width="1.5" fill="none"/>
            <g fill="#6B1818">
                <circle cx="22" cy="28" r="7"/>
                <circle cx="32" cy="26" r="7"/>
                <circle cx="42" cy="28" r="7"/>
                <circle cx="26" cy="38" r="7"/>
                <circle cx="38" cy="38" r="7"/>
                <circle cx="32" cy="48" r="7"/>
            </g>
            <g fill="#8B2828" opacity="0.7">
                <circle cx="20" cy="26" r="2"/>
                <circle cx="30" cy="24" r="2"/>
                <circle cx="40" cy="26" r="2"/>
            </g>
        </svg> -->
@break

@case('ezhevika')
{{-- Ежевика — крупная тёмная гроздь --}}
<img src="{{ asset('media/cotel/icon-ezhevika.svg') }}" alt="">
<!-- <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M32 6 L32 16" stroke="#5C5238" stroke-width="2" stroke-linecap="round"/>
            <path d="M22 10 L32 14 L42 10" stroke="#5C5238" stroke-width="1.5" fill="none"/>
            <g fill="#2E1414">
                <circle cx="22" cy="26" r="8"/>
                <circle cx="32" cy="22" r="8"/>
                <circle cx="42" cy="26" r="8"/>
                <circle cx="24" cy="38" r="8"/>
                <circle cx="40" cy="38" r="8"/>
                <circle cx="32" cy="48" r="8"/>
            </g>
            <g fill="#5C2828" opacity="0.6">
                <circle cx="20" cy="24" r="2.5"/>
                <circle cx="30" cy="20" r="2.5"/>
                <circle cx="40" cy="24" r="2.5"/>
            </g>
        </svg> -->
@break

@case('limon')
{{-- Лимон — овальный плод --}}
<img src="{{ asset('media/cotel/icon-limon.svg') }}" alt="">
<!-- <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <ellipse cx="32" cy="34" rx="20" ry="22" fill="#E8C77A"/>
            <ellipse cx="32" cy="34" rx="20" ry="22" stroke="#C9A961" stroke-width="2" fill="none"/>
            <path d="M32 14 Q28 8 32 6 Q36 8 32 14" fill="#5C5238"/>
            <path d="M30 12 L34 14 M28 16 L32 18" stroke="#A88A40" stroke-width="1" opacity="0.5"/>
            <ellipse cx="26" cy="28" rx="4" ry="3" fill="#F0D894" opacity="0.7"/>
        </svg> -->
@break

@case('abrikos')
{{-- Абрикос — округлый плод с косточкой --}}
<img src="{{ asset('media/cotel/icon-abrikos.svg') }}" alt="">
<!-- <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <circle cx="32" cy="34" r="22" fill="#D4A24C"/>
            <circle cx="32" cy="34" r="22" stroke="#A87830" stroke-width="2" fill="none"/>
            <path d="M32 12 Q30 6 32 10 Q34 6 32 12" stroke="#5C5238" stroke-width="2" fill="none"/>
            <path d="M32 16 L32 50" stroke="#A87830" stroke-width="1.5" opacity="0.4"/>
            <ellipse cx="26" cy="28" rx="4" ry="3" fill="#E8C088" opacity="0.7"/>
        </svg> -->
@break

@case('klubnika')
{{-- Клубника — треугольный плод с зелёным хвостиком --}}
<img src="{{ asset('media/cotel/icon-klubnika.svg') }}" alt="">
<!-- <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M32 14 L20 24 Q16 32 20 42 Q28 56 32 56 Q36 56 44 42 Q48 32 44 24 Z"
                  fill="#A02020"/>
            <path d="M32 14 L20 24 Q16 32 20 42 Q28 56 32 56 Q36 56 44 42 Q48 32 44 24 Z"
                  stroke="#7E1818" stroke-width="2" fill="none"/>
            {{-- Семечки --}}
            <g fill="#F5D080">
                <ellipse cx="26" cy="28" rx="1.2" ry="2" transform="rotate(15 26 28)"/>
                <ellipse cx="34" cy="26" rx="1.2" ry="2" transform="rotate(-10 34 26)"/>
                <ellipse cx="40" cy="30" rx="1.2" ry="2" transform="rotate(20 40 30)"/>
                <ellipse cx="22" cy="34" rx="1.2" ry="2" transform="rotate(-5 22 34)"/>
                <ellipse cx="30" cy="36" rx="1.2" ry="2" transform="rotate(10 30 36)"/>
                <ellipse cx="38" cy="38" rx="1.2" ry="2" transform="rotate(-15 38 38)"/>
                <ellipse cx="26" cy="44" rx="1.2" ry="2" transform="rotate(5 26 44)"/>
                <ellipse cx="34" cy="46" rx="1.2" ry="2" transform="rotate(-20 34 46)"/>
            </g>
            {{-- Зелёный хвостик --}}
            <path d="M22 14 L32 16 L42 14 L36 20 L40 22 L32 22 L24 22 L28 20 Z" fill="#5C7820"/>
            <path d="M30 8 L34 12 L32 16" stroke="#5C7820" stroke-width="2" stroke-linecap="round" fill="none"/>
        </svg> -->
@break

@case('klukva')
{{-- Клюква — гроздь мелких ягод на тонких ножках --}}
<img src="{{ asset('media/cotel/icon-klukva.svg') }}" alt="">
@break

@case('grusha')
{{-- Груша — классическая грушевидная форма, желтовато-зелёная --}}
<img src="{{ asset('media/cotel/icon-grusha.svg') }}" alt="">
@break

@case('yabloko')
{{-- Яблоко — круглое, красное, с листиком --}}
<img src="{{ asset('media/cotel/icon-yabloko.svg') }}" alt="">
@break

@case('shishka')
{{-- Шишка — сосновая, психоделический ингредиент --}}
<img src="{{ asset('media/cotel/icon-shishka.svg') }}" alt="">
@break


@default
{{-- Дефолтная ягода --}}
<svg viewBox="0 0 64 64" fill="none" aria-hidden="true">
    <circle cx="32" cy="34" r="20" fill="#7E1A1A" />
</svg>
@endswitch