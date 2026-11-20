@props(['post', 'variant' => 'grid'])

@php
    /** @var \App\Models\BlogPost $post */
    $url = $post->publicUrl();
    $image = $post->featuredImageUrl();
    $date = $post->published_at?->locale(app()->getLocale())->translatedFormat('j F Y');
@endphp

{{--
    A blog post card. `grid` stacks image over text; `row` sets a small image beside
    the text for compact lists. The title link stretches over the whole card.
--}}
<article {{ $attributes->class(['fe-post', 'fe-post--'.$variant]) }} data-spotlight>
    <div class="fe-post__media">
        @if ($image)
            <img src="{{ $image }}" alt="{{ $post->featured_image_alt ?: '' }}" loading="lazy">
        @else
            <span class="fe-post__placeholder" aria-hidden="true">
                @include('frontend.partials.contour', ['class' => 'fe-post__contour'])
                <x-frontend::icon name="blog" :size="40" />
            </span>
        @endif

        @if ($post->category && $variant === 'grid')
            <a class="fe-post__category" href="{{ route('frontend.blog.category', ['slug' => $post->category->slug]) }}">{{ $post->category->name }}</a>
        @endif
    </div>

    <div class="fe-post__body">
        <p class="fe-post__meta">
            @if ($date)
                <time datetime="{{ $post->published_at->toDateString() }}">{{ $date }}</time>
            @endif
            <span>{{ trans_choice('frontend.blog.minutes', $post->readingMinutes()) }}</span>
        </p>
        <h3 class="fe-post__title"><a href="{{ $url }}">{{ $post->title }}</a></h3>
        @if ($post->excerpt && $variant === 'grid')
            <p class="fe-post__excerpt">{{ $post->excerpt }}</p>
        @endif
        @if ($variant === 'grid')
            <span class="fe-post__more" aria-hidden="true">
                {{ __('frontend.blog.read') }}
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </span>
        @endif
    </div>
</article>
