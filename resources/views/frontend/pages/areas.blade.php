@extends('frontend.layouts.app')

@section('title', __('frontend.nav.areas'))
@section('description', __('frontend.areas.meta'))

@section('content')
    <x-frontend::hero :title="__('frontend.nav.areas')" :lead="__('frontend.areas.lead')" icon="areas" />

    <div class="fe-shell fe-areaslayout">
        {{-- Sticky index; app.js marks the area currently in view. --}}
        <nav class="fe-spy" aria-label="{{ __('frontend.nav.areas') }}" data-spy>
            <ol>
                @foreach (__('frontend.areas.items') as $area)
                    <li>
                        <a href="#{{ $area['key'] }}">
                            <x-frontend::icon :name="$area['key']" :size="22" />
                            <span>{{ $area['title'] }}</span>
                        </a>
                    </li>
                @endforeach
            </ol>
        </nav>

        <div class="fe-areaslayout__content">
            @foreach (__('frontend.areas.items') as $area)
                {{-- One card per area, each in its own brand tone. --}}
                <section class="fe-areapanel fe-areapanel--{{ $loop->iteration }}" id="{{ $area['key'] }}" data-spy-target data-reveal>
                    <header class="fe-areapanel__head">
                        @include('frontend.partials.contour', ['class' => 'fe-areapanel__dots'])
                        <span class="fe-areapanel__icon"><x-frontend::icon :name="$area['key']" :size="38" /></span>
                        <div>
                            <h2>{{ $area['title'] }}</h2>
                            <p>{{ $area['summary'] }}</p>
                        </div>
                    </header>

                    <div class="fe-areapanel__body">
                        <p class="fe-areapanel__intro">{{ $area['intro'] }}</p>
                        <h3>{{ __('frontend.areas.includes') }}</h3>
                        <ul class="fe-areapanel__points">
                            @foreach ($area['points'] as $point)
                                <li>{{ $point }}</li>
                            @endforeach
                        </ul>
                    </div>

                    @if ($area['note'])
                        <p class="fe-areapanel__note">
                            <x-frontend::icon name="about" :size="18" />
                            <span>{{ $area['note'] }}</span>
                        </p>
                    @endif
                </section>
            @endforeach
        </div>
    </div>
@endsection
