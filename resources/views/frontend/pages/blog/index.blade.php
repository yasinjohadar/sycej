@extends('frontend.layouts.app')

@section('title', $heading)
@section('description', $lead ?: __('frontend.blog.meta'))

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $posts */
    $isFiltered = $search !== '' || $activeCategory || $activeTag;
    // The cover story is shown on its own, so the grid below skips it.
    $gridPosts = $cover ? $posts->getCollection()->reject(fn ($post) => $post->is($cover)) : $posts->getCollection();
@endphp

@section('content')
    <x-frontend::hero :title="$heading" :lead="$search !== '' ? __('frontend.blog.results_for', ['q' => $search]) : $lead" icon="blog">
        <x-slot:actions>
            <form class="fe-search" action="{{ route('frontend.blog') }}" method="GET" role="search">
                <x-frontend::icon name="search" :size="20" class="fe-search__icon" />
                <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('frontend.blog.search_placeholder') }}" aria-label="{{ __('frontend.blog.search') }}">
                <button class="fe-btn fe-btn--mint" type="submit">{{ __('frontend.blog.search') }}</button>
            </form>
        </x-slot:actions>
    </x-frontend::hero>

    <section class="fe-section fe-section--tight fe-blog">
        <div class="fe-shell">
            @if ($categories->isNotEmpty())
                <nav class="fe-chips" aria-label="{{ __('frontend.nav.blog') }}" data-reveal>
                    <a href="{{ route('frontend.blog') }}" @if (! $isFiltered) aria-current="page" @endif>{{ __('frontend.blog.all') }}</a>
                    @foreach ($categories as $category)
                        <a href="{{ route('frontend.blog.category', ['slug' => $category->slug]) }}" @if ($activeCategory?->is($category)) aria-current="page" @endif>{{ $category->name }}</a>
                    @endforeach
                </nav>
            @endif

            @if ($cover)
                @php
                    $coverImage = $cover->featuredImageUrl();
                    $coverDate = $cover->published_at?->locale(app()->getLocale())->translatedFormat('j F Y');
                @endphp
                {{-- Cover story: the featured (or newest) post, set large. --}}
                <article class="fe-cover" data-reveal data-spotlight>
                    <div class="fe-cover__media">
                        @if ($coverImage)
                            <img src="{{ $coverImage }}" alt="{{ $cover->featured_image_alt ?: '' }}">
                        @else
                            <span class="fe-post__placeholder" aria-hidden="true">
                                @include('frontend.partials.contour', ['class' => 'fe-post__contour'])
                                <x-frontend::icon name="blog" :size="64" />
                            </span>
                        @endif
                    </div>
                    <div class="fe-cover__body">
                        <p class="fe-cover__labels">
                            <span class="fe-cover__badge">
                                <span class="fe-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                                {{ __('frontend.blog.cover') }}
                            </span>
                            @if ($cover->category)
                                <a class="fe-cover__category" href="{{ route('frontend.blog.category', ['slug' => $cover->category->slug]) }}">{{ $cover->category->name }}</a>
                            @endif
                        </p>
                        <h2 class="fe-cover__title"><a href="{{ $cover->publicUrl() }}">{{ $cover->title }}</a></h2>
                        @if ($cover->excerpt)
                            <p class="fe-cover__excerpt">{{ $cover->excerpt }}</p>
                        @endif
                        <p class="fe-cover__meta">
                            @if ($cover->author)
                                <span class="fe-avatar" aria-hidden="true">{{ mb_substr($cover->author->name, 0, 1) }}</span>
                                <span>{{ $cover->author->name }}</span>
                            @endif
                            @if ($coverDate)
                                <time datetime="{{ $cover->published_at->toDateString() }}">{{ $coverDate }}</time>
                            @endif
                            <span>{{ trans_choice('frontend.blog.minutes', $cover->readingMinutes()) }}</span>
                        </p>
                        <span class="fe-btn fe-btn--primary fe-cover__cta" aria-hidden="true">
                            {{ __('frontend.blog.read') }}
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </span>
                    </div>
                </article>
            @endif

            @if ($gridPosts->isNotEmpty())
                <div class="fe-posts">
                    @foreach ($gridPosts as $post)
                        <x-frontend::post-card :post="$post" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 100 }}ms" />
                    @endforeach
                </div>
            @elseif (! $cover)
                <div class="fe-empty" data-reveal>
                    <span class="fe-empty__icon"><x-frontend::icon name="blog" :size="40" /></span>
                    <h2>{{ $search !== '' ? __('frontend.blog.results_for', ['q' => $search]) : __('frontend.blog.empty_title') }}</h2>
                    <p>{{ $search !== '' ? __('frontend.blog.no_results') : __('frontend.blog.empty_body') }}</p>
                    @if ($isFiltered)
                        <a class="fe-btn fe-btn--primary" href="{{ route('frontend.blog') }}">{{ __('frontend.blog.back') }}</a>
                    @endif
                </div>
            @endif

            {{ $posts->links('frontend.partials.pagination') }}
        </div>
    </section>
@endsection
