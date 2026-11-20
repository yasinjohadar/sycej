{{-- Utility strip: scrolls away with the page; the island header below stays. --}}
<div class="fe-topbar">
    <div class="fe-shell fe-topbar__inner">
        <ul class="fe-topbar__info">
            <li>
                <x-frontend::icon name="location" :size="15" />
                {{ $contact['location'] }}
            </li>
            <li>
                <a href="mailto:{{ $contact['email'] }}">
                    <x-frontend::icon name="mail" :size="15" />
                    <span dir="ltr">{{ $contact['email'] }}</span>
                </a>
            </li>
            <li>
                <a href="tel:{{ $contact['phone_dial'] }}">
                    <x-frontend::icon name="phone" :size="15" />
                    <span dir="ltr">{{ $contact['phone'] }}</span>
                </a>
            </li>
        </ul>
        <div class="fe-topbar__social">
            @foreach ($socialLinks as $social)
                <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social->label }}" title="{{ $social->label }}"><x-frontend::icon :name="$social->frontendIcon()" :size="16" /></a>
            @endforeach
            <a href="{{ $contact['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" title="WhatsApp"><x-frontend::icon name="whatsapp" :size="16" /></a>
        </div>
    </div>
</div>

<header class="fe-header" data-header>
    <div class="fe-island">
        <div class="fe-shell">
            <div class="fe-island__top">
                <a class="fe-brand" href="{{ route('frontend.home') }}">
                    <span class="fe-brand__mark"><img src="{{ $logoUrl }}" alt="" width="44" height="43"></span>
                    <span class="fe-brand__text">
                        <strong>{{ __('frontend.brand.name') }}</strong>
                        <span>{{ __('frontend.brand.short') }}<i class="fe-brand__place"> · {{ $contact['location'] }}</i></span>
                    </span>
                </a>

                <div class="fe-header__tools">
                    @include('frontend.partials.theme-toggle')
                    @include('frontend.partials.lang-switch')

                    <a class="fe-btn fe-btn--mint fe-header__cta" href="{{ route('frontend.contact') }}"
                       @if (request()->routeIs('frontend.contact*')) aria-current="page" @endif>
                        <x-frontend::icon name="mail" :size="18" />
                        <span>{{ __('frontend.nav.contact') }}</span>
                    </a>

                    <button class="fe-iconbtn fe-menu-btn" type="button" aria-controls="fe-nav" aria-expanded="false" data-menu-toggle>
                        <span class="fe-sr">{{ __('frontend.ui.menu') }}</span>
                        <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" fill="none"/></svg>
                    </button>
                </div>
            </div>

            @include('frontend.partials.nav')
        </div>
    </div>
</header>
