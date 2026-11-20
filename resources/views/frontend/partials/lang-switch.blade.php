@php $otherLocale = app()->getLocale() === 'ar' ? 'en' : 'ar'; @endphp
<a class="fe-lang" href="{{ \App\Support\FrontendSite::alternateUrl($otherLocale) }}"
   hreflang="{{ $otherLocale }}" lang="{{ $otherLocale }}"
   aria-label="{{ __('frontend.ui.switch_language_label') }}" data-lang-switch>
    {{ __('frontend.ui.switch_language') }}
</a>
