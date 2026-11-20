@extends('frontend.layouts.app')

@section('title', __('frontend.nav.knowledge'))
@section('description', __('frontend.knowledge.meta'))

@section('content')
    <x-frontend::hero :title="__('frontend.nav.knowledge')" :lead="__('frontend.knowledge.tagline')" icon="knowledge" />

    @php [$aim, $audience, $standards] = __('frontend.knowledge.paragraphs'); @endphp

    {{-- Same layout as governance: what the space holds, who it serves, how it publishes. --}}
    <section class="fe-section fe-gov">
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.knowledge.lead')" :lead="$aim" data-reveal />

            <ul class="fe-gov__principles">
                @foreach (__('frontend.knowledge.types') as $type)
                    <li class="fe-gov__principle" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 90 }}ms">
                        <span class="fe-gov__principleicon"><x-frontend::icon :name="$type['icon']" :size="26" /></span>
                        <span>{{ $type['title'] }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="fe-gov__grid">
                <article class="fe-gov__card" data-reveal data-spotlight>
                    <span class="fe-gov__badge"><x-frontend::icon name="team" :size="28" /></span>
                    <h3>{{ __('frontend.knowledge.audience_title') }}</h3>
                    <p>{{ $audience }}</p>
                    <ul class="fe-gov__tags">
                        @foreach (__('frontend.knowledge.audience') as $reader)
                            <li>{{ $reader }}</li>
                        @endforeach
                    </ul>
                </article>

                <article class="fe-gov__card fe-gov__card--ink" data-reveal style="--reveal-delay: 140ms">
                    @include('frontend.partials.contour', ['class' => 'fe-gov__dots'])
                    <span class="fe-gov__badge"><x-frontend::icon name="justice" :size="28" /></span>
                    <h3>{{ __('frontend.knowledge.standards_title') }}</h3>
                    <p>{{ $standards }}</p>
                    <ul class="fe-gov__checks">
                        @foreach (__('frontend.knowledge.standards') as $standard)
                            <li>{{ $standard }}</li>
                        @endforeach
                    </ul>
                </article>
            </div>
        </div>
    </section>

    <section class="fe-section fe-band">
        <div class="fe-shell">
            <div class="fe-grid2">
                @foreach (__('frontend.knowledge.collections') as $collection)
                    <article class="fe-panel fe-panel--shelf" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 140 }}ms">
                        <span class="fe-panel__badge"><x-frontend::icon :name="$loop->first ? 'research' : 'justice'" :size="30" /></span>
                        <h2 class="fe-panel__title">{{ $collection['title'] }}</h2>
                        <p>{{ $collection['body'] }}</p>
                        <x-frontend::pending />
                    </article>
                @endforeach
            </div>
            <x-frontend::notice data-reveal>{{ __('frontend.ui.no_stats') }}</x-frontend::notice>
        </div>
    </section>
@endsection
