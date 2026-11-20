@props(['title', 'lead' => null, 'level' => 'h2'])

<header {{ $attributes->class('fe-sectionhead') }}>
    <{{ $level }} class="fe-sectionhead__title">{{ $title }}</{{ $level }}>
    @if ($lead)
        <p class="fe-sectionhead__lead">{{ $lead }}</p>
    @endif
    {{ $slot }}
</header>
