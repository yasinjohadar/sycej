@if ($paginator->hasPages())
    <nav class="fe-pager" role="navigation" aria-label="{{ __('frontend.ui.pagination') }}">
        @if ($paginator->onFirstPage())
            <span class="fe-pager__step is-disabled" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
            </span>
        @else
            <a class="fe-pager__step" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="{{ __('frontend.ui.previous') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M11 6l-6 6 6 6"/></svg>
            </a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="fe-pager__gap">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="fe-pager__page is-current" aria-current="page">{{ $page }}</span>
                    @else
                        <a class="fe-pager__page" href="{{ $url }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a class="fe-pager__step" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="{{ __('frontend.ui.next') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
        @else
            <span class="fe-pager__step is-disabled" aria-hidden="true">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </span>
        @endif
    </nav>
@endif
