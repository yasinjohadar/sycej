@php
    $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar';
    $linkGroups = [
        __('frontend.footer.about_group') => ['about', 'areas', 'programs', 'team', 'governance'],
        __('frontend.footer.knowledge_group') => ['knowledge', 'blog', 'media', 'partners'],
    ];
    $contactRows = [
        ['icon' => 'location', 'value' => $contact['location'], 'href' => null, 'ltr' => false],
        ['icon' => 'mail', 'value' => $contact['email'], 'href' => 'mailto:'.$contact['email'], 'ltr' => true],
        ['icon' => 'phone', 'value' => $contact['phone'], 'href' => 'tel:'.$contact['phone_dial'], 'ltr' => true],
        ['icon' => 'whatsapp', 'value' => __('frontend.contact.labels.whatsapp'), 'href' => $contact['whatsapp_url'], 'ltr' => false],
    ];
@endphp

{{--
    Footer in three tidy tiers: identity and social links, a four-column
    directory, then the legal line. The newsletter lives in its own section above.
--}}
<footer class="fe-footer">
    <div class="fe-footer__bg" aria-hidden="true">
        @include('frontend.partials.contour', ['class' => 'fe-footer__contour'])
    </div>

    <div class="fe-shell">
        <div class="fe-footer__top">
            <a class="fe-footer__logo" href="{{ route('frontend.home') }}">
                <span class="fe-footer__mark"><img src="{{ $logoUrl }}" alt="" width="56" height="55" loading="lazy"></span>
                <span>
                    <strong>{{ __('frontend.brand.name') }}</strong>
                    <span>{{ __('frontend.brand.description') }}</span>
                </span>
            </a>

            <div class="fe-footer__follow">
                <span>{{ __('frontend.footer.follow') }}</span>
                <div class="fe-socials">
                    @foreach ($socialLinks as $social)
                        <a class="fe-social" href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social->label }}" title="{{ $social->label }}"><x-frontend::icon :name="$social->frontendIcon()" :size="19" /></a>
                    @endforeach
                    <a class="fe-social" href="{{ $contact['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" title="WhatsApp"><x-frontend::icon name="whatsapp" :size="19" /></a>
                    <a class="fe-social" href="mailto:{{ $contact['email'] }}" aria-label="{{ __('frontend.contact.labels.email') }}" title="{{ __('frontend.contact.labels.email') }}"><x-frontend::icon name="mail" :size="19" /></a>
                </div>
            </div>
        </div>

        <div class="fe-footer__grid">
            @foreach ($linkGroups as $heading => $pages)
                <nav class="fe-footer__col" aria-label="{{ $heading }}">
                    <h2>{{ $heading }}</h2>
                    <ul class="fe-footer__links">
                        @foreach ($pages as $pageKey)
                            <li><a href="{{ route('frontend.'.$pageKey) }}">{{ __('frontend.nav.'.$pageKey) }}</a></li>
                        @endforeach
                    </ul>
                </nav>
            @endforeach

            <nav class="fe-footer__col" aria-label="{{ __('frontend.footer.areas') }}">
                <h2>{{ __('frontend.footer.areas') }}</h2>
                <ul class="fe-footer__areas">
                    @foreach (__('frontend.areas.items') as $area)
                        <li>
                            <a href="{{ route('frontend.areas') }}#{{ $area['key'] }}">
                                <span class="fe-footer__areaicon"><x-frontend::icon :name="$area['key']" :size="18" /></span>
                                <span>{{ $area['title'] }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </nav>

            <div class="fe-footer__col">
                <h2>{{ __('frontend.footer.reach') }}</h2>
                <ul class="fe-footer__contact">
                    @foreach ($contactRows as $row)
                        <li>
                            <span class="fe-footer__contacticon"><x-frontend::icon :name="$row['icon']" :size="17" /></span>
                            @if ($row['href'])
                                <a href="{{ $row['href'] }}" @if ($row['ltr']) dir="ltr" @endif @if (str_starts_with($row['href'], 'http')) target="_blank" rel="noopener noreferrer" @endif>{{ $row['value'] }}</a>
                            @else
                                <span>{{ $row['value'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
                <a class="fe-btn fe-btn--mint fe-footer__cta" href="{{ route('frontend.contact') }}">{{ __('frontend.contact.form.title') }}</a>
            </div>
        </div>
    </div>

    <div class="fe-footer__base">
        <div class="fe-shell fe-footer__baseinner">
            <p>
                © {{ now()->year }} {{ __('frontend.brand.name') }}. {{ __('frontend.footer.rights') }}
                <span class="fe-footer__legal">{{ __('frontend.ui.legal_notice') }}</span>
            </p>
            <div class="fe-footer__baselinks">
                <a href="{{ \App\Support\FrontendSite::alternateUrl($otherLocale) }}" hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}">
                    <x-frontend::icon name="globe" :size="16" />
                    {{ __('frontend.ui.switch_language') }}
                </a>
                <a href="{{ $contact['website_url'] }}" dir="ltr">{{ $contact['website'] }}</a>
                <a href="#content" class="fe-footer__top-link">
                    {{ __('frontend.footer.top') }}
                    <x-frontend::icon name="arrow-up" :size="15" />
                </a>
            </div>
        </div>
    </div>
</footer>
