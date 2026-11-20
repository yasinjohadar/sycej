@extends('frontend.layouts.app')

@section('description', __('frontend.home.meta'))

@section('content')
    <x-frontend::home-hero :logo-url="$logoUrl" />

    {{-- Topics ribbon along the hero's lower edge. The second copy only exists so
         the loop is seamless. --}}
    <section class="fe-marquee" aria-label="{{ __('frontend.ui.themes') }}">
        <div class="fe-marquee__band">
        <div class="fe-marquee__track">
            @foreach ([false, true] as $isClone)
                <ul class="fe-marquee__list" @if ($isClone) aria-hidden="true" @endif>
                    @foreach (__('frontend.home.themes') as $theme)
                        <li>{{ $theme }}</li>
                    @endforeach
                </ul>
            @endforeach
        </div>
        </div>
    </section>

    <section class="fe-section fe-intro" id="home-intro">
        <div class="fe-shell fe-intro__grid">
            {{-- The statement, set on its own lit ink card. --}}
            <div class="fe-intro__card" data-reveal>
                <span class="fe-glow fe-glow--sea" aria-hidden="true"></span>
                <span class="fe-glow fe-glow--mint" aria-hidden="true"></span>
                @include('frontend.partials.contour', ['class' => 'fe-intro__contour'])
                <p class="fe-kicker">
                    <span class="fe-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                    {{ __('frontend.brand.short') }}
                </p>
                <h2>{{ __('frontend.home.intro.title') }}</h2>
                <a class="fe-btn fe-btn--glass" href="{{ route('frontend.about') }}">{{ __('frontend.nav.about') }}</a>
            </div>

            <div class="fe-intro__body">
                @foreach (__('frontend.home.intro.paragraphs') as $paragraph)
                    <p @class(['fe-intro__lead' => $loop->first]) data-reveal style="--reveal-delay: {{ $loop->index * 120 }}ms">{{ $paragraph }}</p>
                @endforeach

                {{-- The approach the second paragraph describes, in order. --}}
                <ol class="fe-intro__path" data-reveal style="--reveal-delay: 240ms">
                    @foreach (__('frontend.home.intro.path') as $step)
                        <li style="--step: {{ $loop->index }}">
                            <span class="fe-intro__pathicon"><x-frontend::icon :name="$step['icon']" :size="26" /></span>
                            <span>{{ $step['title'] }}</span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </div>
    </section>

    <section class="fe-section fe-section--ink">
        @include('frontend.partials.contour', ['class' => 'fe-section__contour'])
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.home.offer.title')" :lead="__('frontend.areas.lead')" data-reveal />
            <div class="fe-offer">
                @foreach (__('frontend.home.offer.items') as $index => $offer)
                    @php $area = __('frontend.areas.items')[$index]; @endphp
                    {{-- Each card carries one brand tone (mint, sea, ink-light) as its accent. --}}
                    <a class="fe-offer__card fe-offer__card--{{ $index + 1 }}" href="{{ route('frontend.areas') }}#{{ $area['key'] }}" data-reveal data-spotlight style="--reveal-delay: {{ $index * 140 }}ms">
                        <span class="fe-offer__glow" aria-hidden="true"></span>
                        <x-frontend::icon :name="$area['key']" :size="190" class="fe-offer__ghost" />

                        <span class="fe-offer__icon">
                            <x-frontend::icon :name="$area['key']" :size="34" />
                        </span>
                        <h3>{{ $offer['title'] }}</h3>
                        <p>{{ $offer['body'] }}</p>

                        <ul class="fe-offer__points">
                            @foreach (array_slice($area['points'], 0, 3) as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>

                        <span class="fe-offer__more">
                            <span>{{ __('frontend.ui.learn_more') }}</span>
                            <span class="fe-offer__go" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    @php
        $audiences = __('frontend.home.audience.items');
        // Nodes sit evenly on a circle around the logo, the first one at the top.
        $hubNodes = collect($audiences)->keys()->map(function (int $index) use ($audiences): array {
            $angle = deg2rad(-90 + $index * 360 / count($audiences));

            return ['x' => round(50 + 38 * cos($angle), 2), 'y' => round(50 + 38 * sin($angle), 2)];
        });
    @endphp
    {{-- Who we work for: a hub of the five audiences around the Center, linked to the list. --}}
    <section class="fe-section fe-audience" id="home-audience" data-audience>
        <div class="fe-shell fe-audience__grid">
            <div class="fe-audience__side">
                <x-frontend::section-head :title="__('frontend.home.audience.title')" :lead="__('frontend.home.audience.lead')" data-reveal />

                <div class="fe-hub" data-reveal aria-hidden="true">
                    <svg class="fe-hub__web" viewBox="0 0 100 100">
                        <circle cx="50" cy="50" r="38" class="fe-hub__ring" />
                        <circle cx="50" cy="50" r="24" class="fe-hub__ring fe-hub__ring--inner" />
                        @foreach ($hubNodes as $index => $node)
                            <line x1="50" y1="50" x2="{{ $node['x'] }}" y2="{{ $node['y'] }}" class="fe-hub__line" data-audience-key="{{ $index }}" />
                        @endforeach
                    </svg>
                    <span class="fe-hub__core"><img src="{{ $logoUrl }}" alt="" width="64" height="63"></span>
                    @foreach ($hubNodes as $index => $node)
                        <span class="fe-hub__node" data-audience-key="{{ $index }}" style="--x: {{ $node['x'] }}%; --y: {{ $node['y'] }}%">
                            <x-frontend::icon :name="$audiences[$index]['icon']" :size="24" />
                        </span>
                    @endforeach
                </div>
            </div>

            <ul class="fe-audience__list">
                @foreach ($audiences as $index => $audience)
                    <li class="fe-audience__item" data-audience-key="{{ $index }}" tabindex="0" data-reveal style="--reveal-delay: {{ $index * 90 }}ms">
                        <span class="fe-audience__icon"><x-frontend::icon :name="$audience['icon']" :size="24" /></span>
                        <span class="fe-audience__text">
                            <strong>{{ $audience['who'] }}</strong>
                            <span>{{ $audience['how'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    @if (count($mediaItems))
    <section class="fe-section fe-band">
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.home.media.title')" data-reveal>
                <a class="fe-textlink" href="{{ route('frontend.media') }}">{{ __('frontend.ui.media_archive') }}</a>
            </x-frontend::section-head>
            <x-frontend::notice data-reveal>{{ __('frontend.ui.founder_media') }}</x-frontend::notice>
            {{-- Items marked for the home page first (admin panel), three cards to a row. --}}
            <div class="fe-mediagrid fe-mediagrid--three">
                @foreach ($mediaItems as $item)
                    <x-frontend::media-card :item="$item" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 120 }}ms" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="fe-section fe-library" id="home-library">
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.home.knowledge.title')" :lead="__('frontend.home.knowledge.lead')" data-reveal />
            <div class="fe-shelves">
                @foreach (__('frontend.knowledge.collections') as $collection)
                    {{-- Each collection as a lit shelf of books; the books rise in turn on hover. --}}
                    <a class="fe-shelf fe-shelf--{{ $loop->iteration }}" href="{{ route('frontend.knowledge') }}" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 140 }}ms">
                        <span class="fe-shelf__scene" aria-hidden="true">
                            @include('frontend.partials.contour', ['class' => 'fe-shelf__contour'])
                            <span class="fe-shelf__books">
                                @foreach ([72, 88, 64, 94, 78, 58, 84] as $height)
                                    <i style="--h: {{ $height }}%; --b: {{ $loop->index }}"></i>
                                @endforeach
                            </span>
                            <span class="fe-shelf__icon"><x-frontend::icon :name="$loop->first ? 'research' : 'justice'" :size="30" /></span>
                        </span>
                        <span class="fe-shelf__body">
                            <span class="fe-shelf__title">{{ $collection['title'] }}</span>
                            <span class="fe-shelf__text">{{ $collection['body'] }}</span>
                            <span class="fe-shelf__foot">
                                <span class="fe-shelf__status">{{ __('frontend.ui.in_preparation') }}</span>
                                <span class="fe-shelf__go">
                                    {{ __('frontend.ui.browse') }}
                                    <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </span>
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
        {{-- From the blog: the newest post as a tall feature, the next ones listed beside it.
             Shown even before anything is published, as a "coming soon" card. --}}
        <section class="fe-section fe-section--ink fe-bloghome" id="home-blog">
            @include('frontend.partials.contour', ['class' => 'fe-section__contour'])
            <div class="fe-shell">
                <x-frontend::section-head :title="__('frontend.blog.home_title')" :lead="__('frontend.blog.home_lead')" data-reveal>
                    <a class="fe-btn fe-btn--glass" href="{{ route('frontend.blog') }}">{{ __('frontend.blog.all_posts') }}</a>
                </x-frontend::section-head>

                @if ($latestPosts->isNotEmpty())
                @php $leadPost = $latestPosts->first(); $leadImage = $leadPost->featuredImageUrl(); @endphp
                <div class="fe-bloghome__grid">
                    <article class="fe-feature" data-reveal>
                        @if ($leadImage)
                            <img class="fe-feature__image" src="{{ $leadImage }}" alt="{{ $leadPost->featured_image_alt ?: '' }}" loading="lazy">
                        @else
                            <span class="fe-post__placeholder fe-feature__image" aria-hidden="true">
                                @include('frontend.partials.contour', ['class' => 'fe-post__contour'])
                                <x-frontend::icon name="blog" :size="72" />
                            </span>
                        @endif
                        <div class="fe-feature__body">
                            @if ($leadPost->category)
                                <span class="fe-feature__category">{{ $leadPost->category->name }}</span>
                            @endif
                            <h3 class="fe-feature__title"><a href="{{ $leadPost->publicUrl() }}">{{ $leadPost->title }}</a></h3>
                            @if ($leadPost->excerpt)
                                <p>{{ $leadPost->excerpt }}</p>
                            @endif
                            <span class="fe-feature__meta">
                                <span>{{ $leadPost->published_at?->locale(app()->getLocale())->translatedFormat('j F Y') }}</span>
                                <span>{{ trans_choice('frontend.blog.minutes', $leadPost->readingMinutes()) }}</span>
                            </span>
                        </div>
                    </article>

                    @if ($latestPosts->count() > 1)
                        <div class="fe-bloghome__list">
                            @foreach ($latestPosts->skip(1) as $post)
                                <x-frontend::post-card :post="$post" variant="row" data-reveal style="--reveal-delay: {{ $loop->index * 110 }}ms" />
                            @endforeach
                        </div>
                    @endif
                </div>
                @else
                <div class="fe-soon" data-reveal>
                    <span class="fe-soon__icon"><x-frontend::icon name="blog" :size="38" /></span>
                    <div>
                        <h3>{{ __('frontend.blog.empty_title') }}</h3>
                        <p>{{ __('frontend.blog.empty_body') }}</p>
                    </div>
                    <a class="fe-btn fe-btn--mint" href="{{ route('frontend.blog') }}">{{ __('frontend.nav.blog') }}</a>
                </div>
                @endif
            </div>
        </section>
@endsection
