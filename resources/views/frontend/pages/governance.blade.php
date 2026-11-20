@extends('frontend.layouts.app')

@section('title', __('frontend.nav.governance'))
@section('description', __('frontend.governance.meta'))

@section('content')
    <x-frontend::hero :title="__('frontend.nav.governance')" :lead="__('frontend.governance.tagline')" icon="governance" />

    @php [$commitment, $policies, $disclosure] = __('frontend.governance.paragraphs'); @endphp

    {{-- The three paragraphs as principles, policies and disclosure. --}}
    <section class="fe-section fe-gov">
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.governance.lead')" :lead="$commitment" data-reveal />

            <ul class="fe-gov__principles">
                @foreach (__('frontend.governance.principles') as $principle)
                    <li class="fe-gov__principle" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 90 }}ms">
                        <span class="fe-gov__principleicon"><x-frontend::icon :name="$principle['icon']" :size="26" /></span>
                        <span>{{ $principle['title'] }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="fe-gov__grid">
                <article class="fe-gov__card" data-reveal data-spotlight>
                    <span class="fe-gov__badge"><x-frontend::icon name="knowledge" :size="28" /></span>
                    <h3>{{ __('frontend.governance.policies_title') }}</h3>
                    <p>{{ $policies }}</p>
                    <ul class="fe-gov__tags">
                        @foreach (__('frontend.governance.policies') as $area)
                            <li>{{ $area }}</li>
                        @endforeach
                    </ul>
                </article>

                <article class="fe-gov__card fe-gov__card--ink" data-reveal style="--reveal-delay: 140ms">
                    @include('frontend.partials.contour', ['class' => 'fe-gov__dots'])
                    <span class="fe-gov__badge"><x-frontend::icon name="globe" :size="28" /></span>
                    <h3>{{ __('frontend.governance.disclosure_title') }}</h3>
                    <p>{{ $disclosure }}</p>
                    <p class="fe-gov__status">{{ __('frontend.ui.in_preparation') }}</p>
                    <p class="fe-gov__legal">
                        <x-frontend::icon name="about" :size="18" />
                        {{ __('frontend.ui.legal_notice') }}
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="fe-section fe-section--ink">
        @include('frontend.partials.contour', ['class' => 'fe-section__contour'])
        <div class="fe-shell fe-grid3">
            @foreach (__('frontend.governance.documents') as $document)
                <article class="fe-doc" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 120 }}ms">
                    <svg class="fe-doc__icon" width="36" height="36" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"><path d="M6 3h8l4 4v14H6z"/><path d="M14 3v4h4M9 12h6M9 16h6"/></svg>
                    <h2>{{ $document }}</h2>
                    <x-frontend::pending />
                </article>
            @endforeach
        </div>
    </section>
@endsection
