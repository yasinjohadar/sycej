@props(['member', 'featured' => false])

{{--
    One person on the team page. Regular cards sit on a toned brand band with the
    portrait overlapping it; the founder variant is a wide dark card with a large portrait.
--}}
<article {{ $attributes->class(['fe-member', 'fe-member--featured' => $featured]) }} data-spotlight>
    @if ($featured)
        @include('frontend.partials.contour', ['class' => 'fe-member__dots'])
    @else
        <div class="fe-member__scene" aria-hidden="true">
            <span class="fe-member__chip">{{ __('frontend.team.chip.'.$member['group']) }}</span>
        </div>
    @endif

    <div class="fe-member__portrait">
        <span class="fe-member__ring" aria-hidden="true"></span>
        <span class="fe-member__photo">
            @if ($member['photo'])
                <img src="{{ $member['photo'] }}" alt="{{ $member['name'] }}" loading="lazy" decoding="async">
            @else
                <span aria-hidden="true">{{ mb_substr($member['name'], 0, 1) }}</span>
            @endif
        </span>
    </div>

    <div class="fe-member__body">
        @if ($featured)
            <p class="fe-member__label">{{ __('frontend.team.founder.label') }}</p>
        @endif
        <h3 class="fe-member__name">{{ $member['name'] }}</h3>
        <p class="fe-member__role">{{ $member['role'] }}</p>
        @if ($member['bio'])
            <p class="fe-member__bio">{{ $member['bio'] }}</p>
        @endif
        @if ($member['email'] || $member['linkedin'])
            <div class="fe-member__links">
                @if ($member['email'])
                    <a href="mailto:{{ $member['email'] }}" aria-label="{{ __('frontend.team.email', ['name' => $member['name']]) }}">
                        <x-frontend::icon name="mail" :size="20" />
                    </a>
                @endif
                @if ($member['linkedin'])
                    <a href="{{ $member['linkedin'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('frontend.team.linkedin', ['name' => $member['name']]) }}">
                        <x-frontend::icon name="link" :size="20" />
                    </a>
                @endif
            </div>
        @endif
    </div>
</article>
