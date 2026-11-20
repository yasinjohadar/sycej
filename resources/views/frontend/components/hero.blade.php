@props(['title', 'lead' => null, 'icon' => null, 'kicker' => null])

{{--
    The shared hero for every inner page: deep ink field with drifting brand-colour
    glows, flowing contour lines, the logo's three dots as a pulsing signature, and a
    glass medallion holding the page's icon. The home page has its own (home-hero).
--}}
<section {{ $attributes->class('fe-hero') }} data-hero>
    <div class="fe-hero__bg" aria-hidden="true">
        <span class="fe-glow fe-glow--sea"></span>
        <span class="fe-glow fe-glow--mint"></span>
        <span class="fe-glow fe-glow--ink"></span>
        @include('frontend.partials.contour', ['class' => 'fe-hero__contour'])
    </div>

    <div class="fe-shell fe-hero__inner">
        <div class="fe-hero__copy">
            <nav class="fe-crumbs" aria-label="{{ __('frontend.ui.breadcrumb') }}">
                <a href="{{ route('frontend.home') }}">{{ __('frontend.nav.home') }}</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page">{{ $title }}</span>
            </nav>

            <p class="fe-kicker">
                <span class="fe-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                {{ $kicker ?? __('frontend.brand.short').' — '.\App\Support\FrontendSite::contact()['location'] }}
            </p>

            <h1 class="fe-hero__title" data-split>{{ $title }}</h1>

            @if ($lead)
                <p class="fe-hero__lead">{{ $lead }}</p>
            @endif

            @isset($actions)
                <div class="fe-actions fe-hero__actions">{{ $actions }}</div>
            @endisset
        </div>

        @if ($icon)
            <div class="fe-medal" aria-hidden="true">
                <svg class="fe-medal__ring" viewBox="0 0 200 200">
                    <circle cx="100" cy="100" r="96" />
                    <circle cx="100" cy="100" r="80" />
                    <circle cx="100" cy="4" r="4" class="fe-medal__bead" />
                </svg>
                <span class="fe-medal__core">
                    <x-frontend::icon :name="$icon" :size="64" />
                </span>
            </div>
        @endif
    </div>

    <svg class="fe-hero__curve" viewBox="0 0 1440 70" preserveAspectRatio="none" aria-hidden="true">
        <path d="M0 70V38C220 6 470 0 720 26s500 44 720 6v38z" />
    </svg>
</section>
