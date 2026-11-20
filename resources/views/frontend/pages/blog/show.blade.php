@extends('frontend.layouts.app')

@php
    /** @var \App\Models\BlogPost $post */
    $seo = $post->getSeoMetaTags();
    $url = $post->publicUrl();
    $image = $post->featuredImageUrl();
    $locale = app()->getLocale();
    $date = $post->published_at?->locale($locale)->translatedFormat('j F Y');
    $shareText = rawurlencode($post->title);
    $shareUrl = rawurlencode($url);
    $shares = [
        ['icon' => 'whatsapp', 'label' => 'WhatsApp', 'tone' => 'whatsapp', 'href' => 'https://wa.me/?text='.$shareText.'%20'.$shareUrl],
        ['icon' => 'facebook', 'label' => 'Facebook', 'tone' => 'facebook', 'href' => 'https://www.facebook.com/sharer/sharer.php?u='.$shareUrl],
        ['icon' => 'x', 'label' => 'X', 'tone' => 'x', 'href' => 'https://twitter.com/intent/tweet?url='.$shareUrl.'&text='.$shareText],
        ['icon' => 'telegram', 'label' => 'Telegram', 'tone' => 'telegram', 'href' => 'https://t.me/share/url?url='.$shareUrl.'&text='.$shareText],
    ];
@endphp

@section('title', $seo['title'])
@section('description', (string) $seo['description'])
@section('og_type', 'article')
@section('canonical', $seo['canonical'])
@if ($seo['og:image'])
    @section('og_image', $seo['og:image'])
@endif

@push('head')
    <meta name="robots" content="{{ $seo['robots'] }}">
    @if ($seo['keywords'])
        <meta name="keywords" content="{{ $seo['keywords'] }}">
    @endif
    <script type="application/ld+json">{!! json_encode($post->getSchemaJsonLd(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endpush

@section('content')
    <article class="fe-article" data-article>
        {{-- Compact two-column hero: the headline beside the article's picture. --}}
        <header class="fe-article__hero">
            <div class="fe-hero__bg" aria-hidden="true">
                <span class="fe-glow fe-glow--sea"></span>
                <span class="fe-glow fe-glow--mint"></span>
                @include('frontend.partials.contour', ['class' => 'fe-hero__contour'])
            </div>

            <div class="fe-shell fe-article__headgrid">
                <div class="fe-article__head">
                    <nav class="fe-crumbs" aria-label="{{ __('frontend.ui.breadcrumb') }}">
                        <a href="{{ route('frontend.home') }}">{{ __('frontend.nav.home') }}</a>
                        <span aria-hidden="true">/</span>
                        <a href="{{ route('frontend.blog') }}">{{ __('frontend.nav.blog') }}</a>
                    </nav>

                    @if ($post->category)
                        <a class="fe-kicker fe-article__category" href="{{ route('frontend.blog.category', ['slug' => $post->category->slug]) }}">
                            <span class="fe-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                            {{ $post->category->name }}
                        </a>
                    @endif

                    <h1 class="fe-article__title" data-split>{{ $post->title }}</h1>

                    @if ($post->excerpt)
                        <p class="fe-article__lead">{{ $post->excerpt }}</p>
                    @endif

                    <ul class="fe-article__meta">
                        @if ($post->author)
                            <li class="fe-article__author">
                                <span class="fe-avatar" aria-hidden="true">{{ mb_substr($post->author->name, 0, 1) }}</span>
                                <span><small>{{ __('frontend.blog.by') }}</small> {{ $post->author->name }}</span>
                            </li>
                        @endif
                        @if ($date)
                            <li><x-frontend::icon name="calendar" :size="16" /><time datetime="{{ $post->published_at->toDateString() }}">{{ $date }}</time></li>
                        @endif
                        <li><x-frontend::icon name="clock" :size="16" />{{ trans_choice('frontend.blog.minutes', $post->readingMinutes()) }}</li>
                        <li><x-frontend::icon name="eye" :size="16" />{{ __('frontend.blog.views', ['count' => number_format($post->views_count)]) }}</li>
                    </ul>
                </div>

                <figure class="fe-article__visual @if (! $image) fe-article__visual--empty @endif">
                    @if ($image)
                        <img src="{{ $image }}" alt="{{ $post->featured_image_alt ?: $post->title }}">
                    @else
                        <span class="fe-medal" aria-hidden="true">
                            <svg class="fe-medal__ring" viewBox="0 0 200 200">
                                <circle cx="100" cy="100" r="96" />
                                <circle cx="100" cy="100" r="80" />
                                <circle cx="100" cy="4" r="4" class="fe-medal__bead" />
                            </svg>
                            <span class="fe-medal__core"><x-frontend::icon :name="$post->category ? 'blog' : 'knowledge'" :size="60" /></span>
                        </span>
                    @endif
                </figure>
            </div>

            <svg class="fe-hero__curve" viewBox="0 0 1440 70" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0 70V38C220 6 470 0 720 26s500 44 720 6v38z" />
            </svg>
        </header>

        <div class="fe-shell fe-article__layout">
            <div class="fe-article__main">
                {{-- Written by staff in the admin editor, rendered as HTML as before. --}}
                <div class="fe-article__content" data-article-content>
                    {!! $post->content !!}
                </div>

                @if ($post->tags->isNotEmpty())
                    <div class="fe-article__tags">
                        <span>{{ __('frontend.blog.tags') }}</span>
                        @foreach ($post->tags as $tag)
                            <a href="{{ route('frontend.blog.tag', ['slug' => $tag->slug]) }}">#{{ $tag->name }}</a>
                        @endforeach
                    </div>
                @endif

                @if ($post->author)
                    <section class="fe-authorbox" aria-label="{{ __('frontend.blog.about_author') }}">
                        @include('frontend.partials.contour', ['class' => 'fe-authorbox__dots'])
                        <span class="fe-authorbox__avatar" aria-hidden="true">{{ mb_substr($post->author->name, 0, 1) }}</span>
                        <div>
                            <p class="fe-authorbox__label">{{ __('frontend.blog.about_author') }}</p>
                            <p class="fe-authorbox__name">{{ $post->author->name }}</p>
                            <p class="fe-authorbox__note">{{ __('frontend.blog.author_note') }}</p>
                        </div>
                    </section>
                @endif

                @if ($previous || $next)
                    <nav class="fe-postnav" aria-label="{{ __('frontend.nav.blog') }}">
                        @foreach (['previous' => $previous, 'next' => $next] as $direction => $neighbour)
                            @if ($neighbour)
                                <a class="fe-postnav__link fe-postnav__link--{{ $direction }}" href="{{ $neighbour->publicUrl() }}" rel="{{ $direction === 'previous' ? 'prev' : 'next' }}">
                                    <span class="fe-postnav__dir">
                                        <svg width="18" height="18" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                                        {{ __('frontend.blog.'.$direction) }}
                                    </span>
                                    <span class="fe-postnav__title">{{ $neighbour->title }}</span>
                                </a>
                            @else
                                <span class="fe-postnav__spacer" aria-hidden="true"></span>
                            @endif
                        @endforeach
                    </nav>
                @endif
            </div>

            <aside class="fe-article__aside">
                <div class="fe-article__sticky">
                    {{-- Reading progress ring + numbered contents (filled by app.js). --}}
                    <nav class="fe-sidecard fe-readcard" aria-label="{{ __('frontend.blog.toc') }}" data-toc hidden>
                        <div class="fe-readcard__head">
                            <span class="fe-readring" aria-hidden="true">
                                <svg viewBox="0 0 44 44"><circle cx="22" cy="22" r="19" /><circle cx="22" cy="22" r="19" class="fe-readring__bar" data-read-ring /></svg>
                                <span data-read-percent>0%</span>
                            </span>
                            <p class="fe-sidecard__title">{{ __('frontend.blog.toc') }}</p>
                        </div>
                        <ol class="fe-readcard__list" data-toc-list></ol>
                    </nav>

                    <div class="fe-sidecard fe-share">
                        <p class="fe-sidecard__title">{{ __('frontend.blog.share') }}</p>
                        <div class="fe-share__grid">
                            @foreach ($shares as $share)
                                <a class="fe-share__btn fe-share__btn--{{ $share['tone'] }}" href="{{ $share['href'] }}" target="_blank" rel="noopener noreferrer">
                                    <x-frontend::icon :name="$share['icon']" :size="18" />
                                    <span>{{ $share['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                        <button class="fe-share__copy" type="button" data-copy-link="{{ $url }}" data-copied="{{ __('frontend.blog.copied') }}">
                            <x-frontend::icon name="link" :size="18" />
                            <span>{{ __('frontend.blog.copy_link') }}</span>
                        </button>
                        <p class="fe-share__toast" role="status" data-copy-toast></p>
                    </div>

                    @if ($latest->isNotEmpty())
                        <div class="fe-sidecard fe-latest">
                            <p class="fe-sidecard__title">{{ __('frontend.blog.latest') }}</p>
                            <ul>
                                @foreach ($latest as $item)
                                    @php $thumb = $item->featuredImageUrl(); @endphp
                                    <li>
                                        <a href="{{ $item->publicUrl() }}">
                                            <span class="fe-latest__thumb">
                                                @if ($thumb)
                                                    <img src="{{ $thumb }}" alt="" loading="lazy">
                                                @else
                                                    <x-frontend::icon name="blog" :size="20" />
                                                @endif
                                            </span>
                                            <span>
                                                <span class="fe-latest__title">{{ $item->title }}</span>
                                                <span class="fe-latest__date">{{ $item->published_at?->locale($locale)->translatedFormat('j F Y') }}</span>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </article>

    @if ($related->isNotEmpty())
        <section class="fe-section fe-band">
            <div class="fe-shell">
                <x-frontend::section-head :title="__('frontend.blog.related')" data-reveal>
                    <a class="fe-textlink" href="{{ route('frontend.blog') }}">{{ __('frontend.blog.all_posts') }}</a>
                </x-frontend::section-head>
                <div class="fe-posts">
                    @foreach ($related as $item)
                        <x-frontend::post-card :post="$item" data-reveal style="--reveal-delay: {{ $loop->index * 100 }}ms" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
