@props(['logoUrl'])

@php
    $titleWords = preg_split('/\s+/u', trim(__('frontend.home.hero.title')));
    $lastWord = array_pop($titleWords);
    $elements = __('frontend.home.hero.elements');
@endphp

{{--
    The home page's own hero: a full-height ink field with a live "scene" — the logo
    at the centre, the logo's three dots set in orbit around it, pulsing contour rings
    and the four elements (water, soil, air, climate) floating at different depths.
--}}
<section class="fe-homehero" data-hero>
    <div class="fe-hero__bg" aria-hidden="true">
        <span class="fe-glow fe-glow--sea"></span>
        <span class="fe-glow fe-glow--mint"></span>
        <span class="fe-glow fe-glow--ink"></span>
        <span class="fe-glow fe-glow--sea fe-glow--late"></span>
        @include('frontend.partials.contour', ['class' => 'fe-hero__contour'])
    </div>

    <div class="fe-shell fe-homehero__grid">
        <div class="fe-homehero__copy">
            <p class="fe-kicker">
                <span class="fe-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                {{ __('frontend.brand.short') }} — {{ \App\Support\FrontendSite::contact()['location'] }}
            </p>

            <h1 class="fe-homehero__title" data-split>{{ implode(' ', $titleWords) }} <span class="fe-mark">{{ $lastWord }}<svg viewBox="0 0 120 14" preserveAspectRatio="none" aria-hidden="true"><path pathLength="1" d="M3 10C30 3 70 2 117 7" /></svg></span></h1>

            <p class="fe-homehero__lead">{{ __('frontend.home.hero.lead') }}</p>

            <div class="fe-actions">
                <a class="fe-btn fe-btn--mint" href="{{ route('frontend.areas') }}">{{ __('frontend.ui.explore_areas') }}</a>
                <a class="fe-btn fe-btn--glass" href="{{ route('frontend.contact') }}">{{ __('frontend.nav.contact') }}</a>
            </div>
        </div>

        <div class="fe-scene" data-parallax aria-hidden="true">
            <svg class="fe-scene__rings" data-depth="8" viewBox="0 0 400 400">
                <circle cx="200" cy="200" r="190" />
                <circle cx="200" cy="200" r="150" />
                <circle cx="200" cy="200" r="110" />
            </svg>

            <span class="fe-orbit fe-orbit--1"><i></i></span>
            <span class="fe-orbit fe-orbit--2"><i></i></span>
            <span class="fe-orbit fe-orbit--3"><i></i></span>

            <span class="fe-scene__core" data-depth="16">
                <img src="{{ $logoUrl }}" alt="" width="200" height="196">
            </span>

            @foreach (['water', 'soil', 'air', 'climate'] as $index => $element)
                <span class="fe-chip fe-chip--{{ $element }}" data-depth="{{ [26, 34, 22, 30][$index] }}" style="--chip-i: {{ $index }}">
                    <x-frontend::icon :name="$element" :size="20" />
                    {{ $elements[$element] }}
                </span>
            @endforeach
        </div>
    </div>

    <div class="fe-shell">
        <nav class="fe-homehero__areas" aria-label="{{ __('frontend.nav.areas') }}">
            @foreach (__('frontend.areas.items') as $area)
                <a class="fe-glasscard" href="{{ route('frontend.areas') }}#{{ $area['key'] }}" data-spotlight style="--reveal-delay: {{ 700 + $loop->index * 120 }}ms">
                    <x-frontend::icon :name="$area['key']" :size="30" />
                    <span>{{ $area['title'] }}</span>
                </a>
            @endforeach
        </nav>
    </div>

    <a class="fe-scrollcue" href="#home-intro">
        <span class="fe-sr">{{ __('frontend.ui.scroll') }}</span>
        <i aria-hidden="true"></i>
    </a>
</section>
