@extends('frontend.layouts.app')

@section('title', __('frontend.nav.partners'))
@section('description', __('frontend.partners.meta'))

@section('content')
    <x-frontend::hero :title="__('frontend.nav.partners')" :lead="__('frontend.partners.tagline')" icon="partners" />

    @php [$shared, $cooperation] = __('frontend.partners.paragraphs'); @endphp

    {{-- The statement (who must work together) beside the areas it covers. --}}
    <section class="fe-section">
        <div class="fe-shell fe-together">
            <article class="fe-together__card" data-reveal>
                <span class="fe-glow fe-glow--sea" aria-hidden="true"></span>
                @include('frontend.partials.contour', ['class' => 'fe-together__dots'])
                <span class="fe-together__quote" aria-hidden="true">&#8221;</span>
                <h2>{{ __('frontend.partners.lead') }}</h2>
                <p>{{ $shared }}</p>
                <ul class="fe-together__actors">
                    @foreach (__('frontend.partners.actors') as $actor)
                        <li style="--i: {{ $loop->index }}">
                            <span class="fe-together__avatar"><x-frontend::icon :name="$actor['icon']" :size="22" /></span>
                            <span class="fe-together__name">{{ $actor['title'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </article>

            <div class="fe-together__side">
                <h2 class="fe-together__title" data-reveal>{{ __('frontend.partners.domains_title') }}</h2>
                <p class="fe-together__lead" data-reveal>{{ $cooperation }}</p>
                <ul class="fe-together__domains">
                    @foreach (__('frontend.partners.domains') as $domain)
                        <li data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 90 }}ms">
                            <span class="fe-together__domainicon"><x-frontend::icon :name="$domain['icon']" :size="26" /></span>
                            <span>{{ $domain['title'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <section class="fe-section fe-band">
        <div class="fe-shell">
            <div class="fe-partners">
                @foreach (__('frontend.partners.groups') as $group)
                    <article class="fe-partner" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 90 }}ms; --tone: var(--fe-tone-{{ $loop->index % 3 + 1 }})">
                        <h3>{{ $group['who'] }}</h3>
                        <p>{{ $group['body'] }}</p>
                    </article>
                @endforeach
            </div>
            <x-frontend::notice data-reveal>{{ __('frontend.ui.no_stats') }}</x-frontend::notice>
        </div>
    </section>

    <section class="fe-section fe-section--flush">
        <div class="fe-shell">
            <x-frontend::link-card :href="route('frontend.contact')" :title="__('frontend.partners.cta')" :body="__('frontend.contact.lead')" icon="partners" data-reveal />
        </div>
    </section>
@endsection
