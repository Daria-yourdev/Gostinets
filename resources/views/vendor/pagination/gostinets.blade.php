@if ($paginator->hasPages())
    <nav class="paginator" role="navigation" aria-label="{{ __('Pagination Navigation') }}">

        {{-- Назад --}}
        @if ($paginator->onFirstPage())
            <span class="paginator__btn paginator__btn--disabled" aria-disabled="true">
                <span aria-hidden="true">←</span>
                <span>назад</span>
            </span>
        @else
            <a class="paginator__btn" href="{{ $paginator->previousPageUrl() }}" rel="prev">
                <span aria-hidden="true">←</span>
                <span>назад</span>
            </a>
        @endif

        {{-- Номера страниц --}}
        <span class="paginator__pages">
            @foreach ($elements as $element)
                {{-- «Многоточие» --}}
                @if (is_string($element))
                    <span class="paginator__dots">{{ $element }}</span>
                @endif

                {{-- Массив страниц --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="paginator__page paginator__page--current" aria-current="page">{{ $page }}</span>
                        @else
                            <a class="paginator__page" href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach
        </span>

        {{-- Вперёд --}}
        @if ($paginator->hasMorePages())
            <a class="paginator__btn" href="{{ $paginator->nextPageUrl() }}" rel="next">
                <span>вперёд</span>
                <span aria-hidden="true">→</span>
            </a>
        @else
            <span class="paginator__btn paginator__btn--disabled" aria-disabled="true">
                <span>вперёд</span>
                <span aria-hidden="true">→</span>
            </span>
        @endif
    </nav>
@endif
