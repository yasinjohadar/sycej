{{--
    Decorative dot field: a fine grid of dots fading to the edges, with a few
    "sparks" glowing in the logo's three dot colours. Include with
    ['class' => '...'] to position and tint it; the grid takes its colour from
    `color` on that class.
--}}
@php
    // Spark positions vary per placement (seeded by the class) but stay stable between requests.
    mt_srand(crc32($class ?? 'fe-dots'));
    $sparks = collect(range(0, 5))->map(fn (int $i): array => [
        'x' => mt_rand(8, 92),
        'y' => mt_rand(10, 90),
        'tone' => $i % 3,
        'delay' => round($i * 0.7, 1),
    ]);
    mt_srand();
@endphp
<div class="fe-contour fe-dotfield {{ $class ?? '' }}" aria-hidden="true">
    @foreach ($sparks as $spark)
        <i class="fe-dotfield__spark fe-dotfield__spark--{{ $spark['tone'] }}" style="--x: {{ $spark['x'] }}%; --y: {{ $spark['y'] }}%; --d: {{ $spark['delay'] }}s"></i>
    @endforeach
</div>
