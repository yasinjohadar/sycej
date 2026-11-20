@extends('frontend.layouts.app')

@section('title', __('frontend.nav.about'))
@section('description', __('frontend.about.meta'))

@section('content')
    <x-frontend::hero :title="__('frontend.nav.about')" :lead="__('frontend.about.lead')" icon="about" />

    @php [$origin, $daily, $cooperation] = __('frontend.about.profile.paragraphs'); @endphp

    {{-- Same layout as governance: the three strands, justice in daily life, cooperation. --}}
    <section class="fe-section fe-gov">
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.about.profile.title')" :lead="$origin" data-reveal />

            <ul class="fe-gov__principles fe-gov__principles--three">
                @foreach (__('frontend.about.pillars') as $pillar)
                    <li class="fe-gov__principle" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 90 }}ms">
                        <span class="fe-gov__principleicon"><x-frontend::icon :name="$pillar['icon']" :size="26" /></span>
                        <span>{{ $pillar['title'] }}</span>
                    </li>
                @endforeach
            </ul>

            <div class="fe-gov__grid">
                <article class="fe-gov__card" data-reveal data-spotlight>
                    <span class="fe-gov__badge"><x-frontend::icon name="justice" :size="28" /></span>
                    <h3>{{ __('frontend.about.daily_title') }}</h3>
                    <p>{{ $daily }}</p>
                    <ul class="fe-gov__tags">
                        @foreach (__('frontend.about.rights') as $right)
                            <li>{{ $right }}</li>
                        @endforeach
                    </ul>
                </article>

                <article class="fe-gov__card fe-gov__card--ink" data-reveal style="--reveal-delay: 140ms">
                    @include('frontend.partials.contour', ['class' => 'fe-gov__dots'])
                    <span class="fe-gov__badge"><x-frontend::icon name="partners" :size="28" /></span>
                    <h3>{{ __('frontend.about.independence_title') }}</h3>
                    <p>{{ $cooperation }}</p>
                    <ul class="fe-together__actors">
                        @foreach (__('frontend.about.collaborators') as $collaborator)
                            <li><span class="fe-together__name">{{ $collaborator }}</span></li>
                        @endforeach
                    </ul>
                    <p class="fe-gov__legal">
                        <x-frontend::icon name="about" :size="18" />
                        {{ __('frontend.ui.legal_notice') }} {{ __('frontend.ui.no_stats') }}
                    </p>
                </article>
            </div>
        </div>
    </section>

    <section class="fe-section fe-section--ink">
        @include('frontend.partials.contour', ['class' => 'fe-section__contour'])
        <div class="fe-shell fe-statements">
            @foreach (['vision', 'mission'] as $statement)
                <article class="fe-statement" data-reveal style="--reveal-delay: {{ $loop->index * 160 }}ms">
                    <h2><span class="fe-dots" aria-hidden="true"><i></i><i></i><i></i></span>{{ __('frontend.about.'.$statement.'.title') }}</h2>
                    <p>{{ __('frontend.about.'.$statement.'.body') }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="fe-section">
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.about.values.title')" data-reveal />
            <dl class="fe-values">
                @foreach (__('frontend.about.values.items') as $value)
                    <div class="fe-values__item" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 90 }}ms">
                        <dt>{{ $value['name'] }}</dt>
                        <dd>{{ $value['body'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <section class="fe-section fe-section--flush">
        <div class="fe-shell fe-grid2">
            <x-frontend::link-card :href="route('frontend.team')" :title="__('frontend.nav.team')" :body="__('frontend.team.lead')" icon="team" data-reveal />
            <x-frontend::link-card :href="route('frontend.governance')" :title="__('frontend.nav.governance')" :body="__('frontend.governance.tagline')" icon="governance" data-reveal style="--reveal-delay: 120ms" />
        </div>
    </section>
@endsection
