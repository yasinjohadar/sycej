@extends('frontend.layouts.app')

@section('title', __('frontend.nav.programs'))
@section('description', __('frontend.programs.meta'))

@section('content')
    <x-frontend::hero :title="__('frontend.nav.programs')" :lead="__('frontend.programs.tagline')" icon="programs" />

    @php [$purpose, $fields, $journey] = __('frontend.programs.paragraphs'); @endphp

    {{-- Same layout as governance: the fields, what each initiative shows, and its stages. --}}
    <section class="fe-section fe-gov">
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.programs.lead')" :lead="$fields" data-reveal />

            <ul class="fe-gov__principles fe-gov__principles--three">
                @foreach (__('frontend.programs.fields') as $field)
                    <li class="fe-gov__principle" data-reveal data-spotlight style="--reveal-delay: {{ ($loop->index % 3) * 90 }}ms">
                        <span class="fe-gov__principleicon"><x-frontend::icon :name="$field['icon']" :size="26" /></span>
                        <span>{{ $field['title'] }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="fe-gov__grid">
                <article class="fe-gov__card" data-reveal data-spotlight>
                    <span class="fe-gov__badge"><x-frontend::icon name="programs" :size="28" /></span>
                    <h3>{{ __('frontend.programs.profile_title') }}</h3>
                    <p>{{ $purpose }}</p>
                    <ul class="fe-gov__tags">
                        @foreach (__('frontend.programs.profile') as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </article>

                <article class="fe-gov__card fe-gov__card--ink" data-reveal style="--reveal-delay: 140ms">
                    @include('frontend.partials.contour', ['class' => 'fe-gov__dots'])
                    <span class="fe-gov__badge"><x-frontend::icon name="areas" :size="28" /></span>
                    <h3>{{ __('frontend.programs.journey_title') }}</h3>
                    <p>{{ $journey }}</p>
                    {{-- A real sequence: proposed, in preparation, completed. --}}
                    <ol class="fe-gov__flow">
                        @foreach (__('frontend.programs.statuses.items') as $stage)
                            <li style="--stage: {{ $loop->index }}">{{ $stage }}</li>
                        @endforeach
                    </ol>
                </article>
            </div>
        </div>
    </section>

    <section class="fe-section fe-section--ink">
        @include('frontend.partials.contour', ['class' => 'fe-section__contour'])
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.programs.method.title')" :lead="__('frontend.programs.method.subtitle')" data-reveal />
            <p class="fe-measure" data-reveal>{{ __('frontend.programs.method.intro') }}</p>
            {{-- A real sequence, so the steps are numbered; the connecting line draws in. --}}
            <ol class="fe-steps" data-reveal>
                @foreach (__('frontend.programs.method.steps') as $step)
                    <li class="fe-steps__item" style="--step: {{ $loop->index }}">
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['body'] }}</p>
                    </li>
                @endforeach
            </ol>
            <blockquote class="fe-quote" data-reveal>{{ __('frontend.programs.method.closing') }}</blockquote>
        </div>
    </section>

    <section class="fe-section">
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.programs.statuses.title')" data-reveal />
            <x-frontend::notice data-reveal>{{ __('frontend.ui.no_stats') }}</x-frontend::notice>
            <div class="fe-grid3">
                @foreach (__('frontend.programs.statuses.items') as $status)
                    <article class="fe-panel fe-panel--status" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 120 }}ms; --tone: var(--fe-tone-{{ $loop->iteration }})">
                        <h3 class="fe-panel__title">{{ $status }}</h3>
                        <x-frontend::pending />
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endsection
