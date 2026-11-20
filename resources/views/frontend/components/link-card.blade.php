@props(['href', 'title', 'body' => null, 'icon' => null])

<a {{ $attributes->class('fe-linkcard') }} href="{{ $href }}" data-spotlight>
    @if ($icon)
        <span class="fe-linkcard__icon"><x-frontend::icon :name="$icon" :size="28" /></span>
    @endif
    <span class="fe-linkcard__text">
        <span class="fe-linkcard__title">{{ $title }}</span>
        @if ($body)
            <span class="fe-linkcard__body">{{ $body }}</span>
        @endif
    </span>
    <svg class="fe-linkcard__arrow" width="22" height="22" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
</a>
