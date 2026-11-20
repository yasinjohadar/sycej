@extends('frontend.layouts.app')

@section('title', __('frontend.nav.team'))
@section('description', __('frontend.team.meta'))

@section('content')
    <x-frontend::hero :title="__('frontend.nav.team')" :lead="__('frontend.team.lead')" icon="team" />

    @if (empty($team))
        <section class="fe-section">
            <div class="fe-shell">
                <x-frontend::pending data-reveal />
            </div>
        </section>
    @endif

    @foreach ($team['founder'] ?? [] as $member)
        <section class="fe-section">
            <div class="fe-shell">
                <x-frontend::team-card :member="$member" featured data-reveal />
            </div>
        </section>
    @endforeach

    @foreach (['team' => 'staff', 'advisor' => 'advisors'] as $group => $heading)
        @continue(empty($team[$group]))
        <section @class(['fe-section', 'fe-band' => $group === 'advisor'])>
            <div class="fe-shell">
                <x-frontend::section-head :title="__('frontend.team.'.$heading)" :lead="__('frontend.team.'.$heading.'_lead')" data-reveal />
                <div class="fe-members">
                    @foreach ($team[$group] as $member)
                        <x-frontend::team-card :member="$member" data-reveal style="--reveal-delay: {{ ($loop->index % 3) * 120 }}ms" />
                    @endforeach
                </div>
            </div>
        </section>
    @endforeach

    @if (count($videos))
    <section class="fe-section {{ empty($team['advisor']) ? 'fe-band' : '' }}">
        <div class="fe-shell">
            <x-frontend::section-head :title="__('frontend.team.appearances')" data-reveal>
                <a class="fe-textlink" href="{{ route('frontend.media') }}">{{ __('frontend.ui.media_archive') }}</a>
            </x-frontend::section-head>
            <x-frontend::notice data-reveal>{{ __('frontend.ui.founder_media') }}</x-frontend::notice>
            <div class="fe-mediagrid fe-mediagrid--videos">
                @foreach ($videos as $item)
                    <x-frontend::media-card :item="$item" data-reveal style="--reveal-delay: {{ $loop->index * 120 }}ms" />
                @endforeach
            </div>
        </div>
    </section>
    @endif
@endsection
