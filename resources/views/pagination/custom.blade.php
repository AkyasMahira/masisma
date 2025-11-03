@if ($paginator->hasPages())
    <nav class="custom-pagination-wrapper mt-3">
        <ul class="custom-pagination">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="pg-item disabled"><span>‹</span></li>
            @else
                <li class="pg-item"><a href="{{ $paginator->previousPageUrl() }}">‹</a></li>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                {{-- "..." --}}
                @if (is_string($element))
                    <li class="pg-item disabled"><span>{{ $element }}</span></li>
                @endif

                {{-- Page Numbers --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="pg-item active"><span>{{ $page }}</span></li>
                        @else
                            <li class="pg-item"><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="pg-item"><a href="{{ $paginator->nextPageUrl() }}">›</a></li>
            @else
                <li class="pg-item disabled"><span>›</span></li>
            @endif

        </ul>
    </nav>
@endif

<style>
    .custom-pagination-wrapper {
        display: flex;
        justify-content: center;
    }

    .custom-pagination {
        display: flex;
        gap: 0.45rem;
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .pg-item {
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        transition: all 0.25s ease;
        min-width: 38px;
        text-align: center;
    }

    .pg-item a,
    .pg-item span {
        display: block;
        padding: 7px 13px;
        text-decoration: none;
        font-weight: 600;
        color: #5c0f11;
        /* text dark maroon */
    }

    .pg-item.active {
        background-color: #7c1316;
        box-shadow: 0 0 12px rgba(124, 19, 22, 0.6);
    }

    .pg-item.active span {
        color: #fff;
    }

    .pg-item:hover:not(.active):not(.disabled) {
        background: rgba(255, 255, 255, 0.28);
        transform: translateY(-2px);
    }

    .pg-item.disabled {
        opacity: 0.45;
        pointer-events: none;
    }

    .pg-item.disabled span {
        color: #5c0f11;
        /* text dark maroon */
    }
</style>
