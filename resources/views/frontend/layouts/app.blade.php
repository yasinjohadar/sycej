@php
    $locale = app()->getLocale();
    $isRtl = $locale === 'ar';
    // A blog post has no translation, so it declares no language alternates.
    $hasAlternates = ! request()->routeIs('frontend.blog.show');
    $pageTitle = trim($__env->yieldContent('title'));
    $fullTitle = $pageTitle !== '' ? $pageTitle.' · '.__('frontend.brand.short') : __('frontend.brand.name');
    $description = trim($__env->yieldContent('description')) ?: __('frontend.brand.description');
    $shareImage = trim($__env->yieldContent('og_image')) ?: $logoUrl;
    $ogType = trim($__env->yieldContent('og_type')) ?: 'website';
    $canonical = trim($__env->yieldContent('canonical')) ?: url()->current();
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}" dir="{{ $isRtl ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#17324d" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#0c1c28" media="(prefers-color-scheme: dark)">

    <title>{{ $fullTitle }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    @if ($hasAlternates)
        @foreach (config('frontend.locales') as $alternate)
            <link rel="alternate" hreflang="{{ $alternate }}" href="{{ \App\Support\FrontendSite::alternateUrl($alternate) }}">
        @endforeach
        <link rel="alternate" hreflang="x-default" href="{{ \App\Support\FrontendSite::alternateUrl(config('frontend.default_locale')) }}">
    @endif

    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:site_name" content="{{ __('frontend.brand.name') }}">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:image" content="{{ $shareImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta property="og:locale" content="{{ $isRtl ? 'ar_SY' : 'en_US' }}">

    <link rel="icon" type="image/png" href="{{ $logoUrl }}">
    @stack('head')

    {{-- Runs before first paint: a saved dark preference never flashes light, and
         reveal-on-scroll content is only hidden once we know JS will show it again. --}}
    <script>
        (function () {
            document.documentElement.classList.add('js');
            try {
                var saved = localStorage.getItem('sycej-theme');
                if (saved === 'dark' || saved === 'light') {
                    document.documentElement.setAttribute('data-theme', saved);
                }
            } catch (e) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Alexandria:wght@400;500;600;700;800&family=Readex+Pro:wght@300;400;500;600&display=swap">

    @vite(['resources/css/frontend/app.css', 'resources/js/frontend/app.js'])
</head>
<body class="fe-body fe-has-hero">
    <a class="fe-skip" href="#content">{{ __('frontend.ui.skip') }}</a>
    <div class="fe-progress" aria-hidden="true"><span data-progress></span></div>

    @include('frontend.partials.header')

    <main id="content" tabindex="-1">
        @yield('content')
    </main>

    @include('frontend.partials.newsletter')
    @include('frontend.partials.footer')

    @include('frontend.partials.whatsapp-float')

    <a class="fe-totop" href="#content" data-totop>
        <span class="fe-sr">{{ __('frontend.ui.back_to_top') }}</span>
        <svg width="20" height="20" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 19V5M6 11l6-6 6 6"/></svg>
    </a>
</body>
</html>
