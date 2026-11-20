{{--
    Floating WhatsApp button, switched on or off from the admin "Site settings" screen.
    Two rings pulse out from it and it gives a short wiggle every few seconds; the
    tip slides out once after the page loads and again on hover or focus.
--}}
@if ($contact['whatsapp_float'])
    <a class="fe-wafloat" href="{{ $contact['whatsapp_float_url'] }}" target="_blank" rel="noopener noreferrer"
       aria-label="{{ __('frontend.ui.whatsapp_float') }}" data-whatsapp-float>
        <span class="fe-wafloat__tip" aria-hidden="true">{{ __('frontend.ui.whatsapp_float_tip') }}</span>
        <span class="fe-wafloat__btn">
            <span class="fe-wafloat__ring" aria-hidden="true"></span>
            <span class="fe-wafloat__ring fe-wafloat__ring--late" aria-hidden="true"></span>
            <svg class="fe-wafloat__icon" width="30" height="30" viewBox="0 0 32 32" aria-hidden="true" focusable="false">
                <path fill="currentColor" d="M16.04 3C8.9 3 3.1 8.78 3.1 15.9c0 2.28.6 4.5 1.73 6.46L3 29l6.82-1.78a12.9 12.9 0 0 0 6.21 1.58h.01c7.13 0 12.94-5.78 12.94-12.9C28.98 8.78 23.17 3 16.04 3zm0 23.62h-.01a10.7 10.7 0 0 1-5.46-1.49l-.39-.23-4.05 1.06 1.08-3.94-.25-.4a10.65 10.65 0 0 1-1.64-5.71c0-5.92 4.82-10.73 10.73-10.73 2.87 0 5.56 1.11 7.58 3.14a10.64 10.64 0 0 1 3.14 7.59c0 5.92-4.82 10.71-10.73 10.71zm5.88-8.02c-.32-.16-1.9-.94-2.2-1.04-.3-.11-.51-.16-.73.16-.21.32-.83 1.04-1.02 1.26-.19.21-.38.24-.7.08-.32-.16-1.36-.5-2.59-1.6-.96-.85-1.6-1.9-1.79-2.22-.19-.32-.02-.49.14-.65.14-.14.32-.38.48-.56.16-.19.21-.32.32-.54.11-.21.05-.4-.03-.56-.08-.16-.73-1.75-1-2.4-.26-.63-.53-.54-.73-.55h-.62c-.21 0-.56.08-.86.4-.3.32-1.13 1.1-1.13 2.69 0 1.58 1.16 3.12 1.32 3.33.16.21 2.28 3.47 5.52 4.87.77.33 1.37.53 1.84.68.77.25 1.48.21 2.03.13.62-.09 1.9-.78 2.17-1.53.27-.75.27-1.4.19-1.53-.08-.13-.29-.21-.61-.37z"/>
            </svg>
        </span>
    </a>
@endif
