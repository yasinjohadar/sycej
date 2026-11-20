@extends('frontend.layouts.app')

@section('title', __('frontend.nav.media'))
@section('description', __('frontend.media.meta'))

@section('content')
    <x-frontend::hero :title="__('frontend.nav.media')" :lead="__('frontend.media.lead')" icon="media" />

    <section class="fe-section fe-section--tight">
        <div class="fe-shell">
            <x-frontend::notice data-reveal>{{ __('frontend.ui.founder_media') }} {{ __('frontend.ui.publisher_note') }}</x-frontend::notice>

            @if (count($items))
                <div class="fe-filter" role="group" aria-label="{{ __('frontend.ui.filter') }}" data-media-filter data-reveal>
                    @foreach (['all', 'video', 'article'] as $kind)
                        <button type="button" class="fe-filter__btn" data-filter="{{ $kind }}" aria-pressed="{{ $kind === 'all' ? 'true' : 'false' }}">
                            {{ __('frontend.ui.'.$kind) }}
                            <span class="fe-filter__count">{{ $kind === 'all' ? count($items) : collect($items)->where('kind', $kind)->count() }}</span>
                        </button>
                    @endforeach
                </div>

                <div class="fe-mediagrid" data-media-grid>
                    @foreach ($items as $item)
                        <x-frontend::media-card :item="$item" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 100 }}ms" />
                    @endforeach
                </div>
            @else
                <p class="fe-mediaempty">{{ __('frontend.media.empty') }}</p>
            @endif
        </div>
    </section>
@endsection
