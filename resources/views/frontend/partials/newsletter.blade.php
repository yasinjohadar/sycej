{{-- Newsletter sign-up: its own section on every page, just above the footer. --}}
<section class="fe-newsletter" id="newsletter" aria-labelledby="newsletter-title">
    <div class="fe-shell">
        <div class="fe-newsletter__card" data-reveal>
            <span class="fe-glow fe-glow--sea" aria-hidden="true"></span>
            <span class="fe-glow fe-glow--mint" aria-hidden="true"></span>
            @include('frontend.partials.contour', ['class' => 'fe-newsletter__contour'])

            <div class="fe-newsletter__intro">
                <span class="fe-newsletter__icon" aria-hidden="true"><x-frontend::icon name="mail" :size="34" /></span>
                <div>
                    <h2 id="newsletter-title">{{ __('frontend.newsletter.title') }}</h2>
                    <p>{{ __('frontend.newsletter.lead') }}</p>
                    <ul class="fe-newsletter__perks">
                        @foreach (__('frontend.newsletter.perks') as $perk)
                            <li>{{ $perk }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="fe-newsletter__action">
                @if (session('newsletter_subscribed'))
                    <p class="fe-newsletter__done" role="status">
                        <x-frontend::icon name="mail" :size="22" />
                        {{ __('frontend.newsletter.subscribed') }}
                    </p>
                @else
                    <form class="fe-newsletter__form" method="POST" action="{{ route('frontend.newsletter.subscribe') }}" novalidate>
                        @csrf
                        <label class="fe-sr" for="newsletter-email">{{ __('frontend.newsletter.email') }}</label>
                        <input id="newsletter-email" name="email" type="email" dir="ltr" autocomplete="email" required maxlength="190"
                               value="{{ old('email') }}" placeholder="{{ __('frontend.newsletter.placeholder') }}"
                               @error('email', 'newsletter') aria-invalid="true" aria-describedby="newsletter-error" @enderror>
                        <div class="fe-hp" aria-hidden="true">
                            <label for="newsletter-website">Website</label>
                            <input id="newsletter-website" name="{{ \App\Http\Requests\Frontend\NewsletterSubscribeRequest::HONEYPOT }}" type="text" tabindex="-1" autocomplete="off">
                        </div>
                        <button class="fe-btn fe-btn--mint" type="submit">{{ __('frontend.newsletter.subscribe') }}</button>
                    </form>
                    @error('email', 'newsletter')
                        <p class="fe-newsletter__error" id="newsletter-error">{{ $message }}</p>
                    @enderror
                @endif
                <p class="fe-newsletter__privacy">{{ __('frontend.newsletter.privacy') }}</p>
            </div>
        </div>
    </div>
</section>
