@props(['item'])

@php
    $isVideo = $item['kind'] === 'video' && filled($item['youtube_id']);
    $date = $item['date']
        ? \Illuminate\Support\Carbon::parse($item['date'])->locale(app()->getLocale())->translatedFormat('j F Y')
        : __('frontend.ui.undated');
@endphp

<article {{ $attributes->class(['fe-media', 'fe-media--video' => $isVideo]) }} data-kind="{{ $item['kind'] }}" data-spotlight>
    <div class="fe-media__visual">
        @if ($isVideo)
            <img src="https://i.ytimg.com/vi/{{ $item['youtube_id'] }}/hqdefault.jpg" alt="" loading="lazy" width="480" height="360">
            <button class="fe-media__play" type="button" data-video="{{ $item['youtube_id'] }}" data-video-title="{{ __('frontend.ui.play') }}">
                <svg width="22" height="22" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13l11-6.5z" fill="currentColor"/></svg>
                <span class="fe-sr">{{ __('frontend.ui.play') }}</span>
            </button>
        @elseif (filled($item['image'] ?? null))
            <img src="{{ $item['image'] }}" alt="" loading="lazy">
            <span class="fe-media__badge">{{ $item['channel'] }}</span>
        @else
            <span class="fe-media__channel">{{ $item['channel'] }}</span>
        @endif
    </div>
    <div class="fe-media__body">
        <p class="fe-media__meta">
            <span class="fe-tag">{{ __('frontend.ui.'.$item['kind']) }}</span>
            <time @if ($item['date']) datetime="{{ $item['date'] }}" @endif>{{ $date }}</time>
            @if ($isVideo)
                <span>{{ $item['channel'] }}</span>
            @endif
        </p>
        <h3 class="fe-media__title" lang="{{ $item['language'] }}" dir="{{ $item['language'] === 'ar' ? 'rtl' : 'ltr' }}">{{ $item['title'] }}</h3>
        <a class="fe-textlink" href="{{ $item['url'] }}" target="_blank" rel="noopener noreferrer">{{ __('frontend.ui.external') }}</a>
    </div>
</article>
