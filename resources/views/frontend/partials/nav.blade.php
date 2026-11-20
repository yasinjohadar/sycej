<nav class="fe-nav" id="fe-nav" aria-label="{{ __('frontend.ui.main_nav') }}" data-nav>
    <div class="fe-nav__head">
        <span>{{ __('frontend.ui.menu') }}</span>
        <button class="fe-iconbtn" type="button" data-menu-close>
            <span class="fe-sr">{{ __('frontend.ui.close') }}</span>
            <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
        </button>
    </div>
    <ul class="fe-nav__list">
        @foreach (config('frontend.pages') as $pageKey)
            @php $isCurrent = request()->routeIs('frontend.'.$pageKey) || ($pageKey === 'contact' && request()->routeIs('frontend.contact.*')); @endphp
            {{-- On desktop "contact" lives in the header's call-to-action button instead. --}}
            <li @class(['fe-nav__item--contact' => $pageKey === 'contact', 'fe-nav__item--home' => $pageKey === 'home'])>
                <a href="{{ route('frontend.'.$pageKey) }}" @if ($isCurrent) aria-current="page" @endif>
                    {{ __('frontend.nav.'.$pageKey) }}
                </a>
            </li>
        @endforeach
    </ul>
</nav>
<div class="fe-nav-scrim" data-menu-close hidden></div>
