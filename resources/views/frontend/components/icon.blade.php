@props(['name', 'size' => 44])

{{-- Line icons for the public site. One stroke style so every page icon reads as one family. --}}
<svg {{ $attributes->class('fe-icon') }} width="{{ $size }}" height="{{ $size }}" viewBox="0 0 48 48" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    @switch($name)
        @case('justice')
            <path d="M24 7v33M14 40h20M10 14h28"/>
            <path d="M10 14l-6 13a6 6 0 0 0 12 0zM38 14l-6 13a6 6 0 0 0 12 0z"/>
            <circle cx="24" cy="10" r="2.2"/>
            @break
        @case('research')
            <circle cx="21" cy="21" r="12"/>
            <path d="M30 30l10 10"/>
            <path d="M16 25c0-6 4-10 10-10 0 6-4 10-10 10zM16 25l5-5"/>
            @break
        @case('awareness')
            <path d="M24 30V18"/>
            <path d="M24 22c-5 0-8-3-8-8 5 0 8 3 8 8zM24 19c0-4 3-7 7-7 0 4-3 7-7 7z"/>
            <path d="M6 32c5 0 8 2 11 5h10a3 3 0 0 0 0-6h-6M42 32c-5 0-8 2-11 5"/>
            @break
        @case('about')
            <circle cx="24" cy="24" r="17"/>
            <path d="M24 22v11M24 15.5v.5"/>
            @break
        @case('areas')
            <circle cx="24" cy="24" r="17"/>
            <path d="M30.5 17.5l-4 9-9 4 4-9z"/>
            <path d="M24 4v4M24 40v4M4 24h4M40 24h4"/>
            @break
        @case('programs')
            <path d="M24 42V24"/>
            <path d="M24 28c-8 0-13-5-13-13 8 0 13 5 13 13zM24 24c0-7 4.5-11.5 11.5-11.5 0 7-4.5 11.5-11.5 11.5z"/>
            <path d="M14 42h20"/>
            @break
        @case('knowledge')
            <path d="M24 13c-4-3-10-4-17-3v26c7-1 13 0 17 3 4-3 10-4 17-3V10c-7-1-13 0-17 3z"/>
            <path d="M24 13v26"/>
            @break
        @case('media')
            <rect x="5" y="10" width="38" height="26" rx="5"/>
            <path d="M21 18v10l8-5z"/>
            <path d="M16 42h16"/>
            @break
        @case('team')
            <circle cx="18" cy="17" r="6"/>
            <circle cx="33" cy="19" r="4.5"/>
            <path d="M6 39c0-7 5.5-12 12-12s12 5 12 12M30 28.5c1-.3 2-.5 3-.5 5 0 9 4 9 10"/>
            @break
        @case('partners')
            <path d="M4 22l8-8 7 4 5-3 5 3 7-4 8 8"/>
            <path d="M12 26l9 9a3 3 0 0 0 4.2 0L36 24"/>
            <path d="M19 23l5-4 7 6"/>
            @break
        @case('governance')
            <path d="M6 18L24 8l18 10z"/>
            <path d="M10 18v16M19 18v16M29 18v16M38 18v16M6 40h36"/>
            @break
        @case('contact')
            <rect x="5" y="11" width="38" height="27" rx="5"/>
            <path d="M6 14l18 13 18-13"/>
            @break
        @case('water')
            <path d="M24 6c6 9 12 15 12 22a12 12 0 0 1-24 0c0-7 6-13 12-22z"/>
            <path d="M18 30a6 6 0 0 0 6 6"/>
            @break
        @case('soil')
            <path d="M6 32h36M10 38h28M16 44h16"/>
            <path d="M24 32V18"/>
            <path d="M24 22c-5 0-8-3-8-8 5 0 8 3 8 8zM24 19c0-4 3-7 7-7 0 4-3 7-7 7z"/>
            @break
        @case('air')
            <path d="M6 18h22a5 5 0 1 0-5-5"/>
            <path d="M6 26h30a5 5 0 1 1-5 5"/>
            <path d="M6 34h14"/>
            @break
        @case('climate')
            <circle cx="18" cy="18" r="7"/>
            <path d="M18 5v3M5 18h3M8.8 8.8l2.1 2.1M27.2 8.8l-2.1 2.1"/>
            <path d="M16 40h20a8 8 0 0 0 0-16 11 11 0 0 0-19.5 4A6 6 0 0 0 16 40z"/>
            @break
        @case('location')
            <path d="M24 43s14-12.4 14-23a14 14 0 0 0-28 0c0 10.6 14 23 14 23z"/>
            <circle cx="24" cy="20" r="5"/>
            @break
        @case('phone')
            <path d="M14 6h-4a4 4 0 0 0-4 4c0 17.7 14.3 32 32 32a4 4 0 0 0 4-4v-4l-9-4-4 4c-5-2-9-6-11-11l4-4-4-9z"/>
            @break
        @case('whatsapp')
            <path d="M8 40l2.6-8A17 17 0 1 1 17 38.3z"/>
            <path d="M18 17c0 7 6 13 13 13l2.5-3.5-4-2-2 2c-2.5-1-4.5-3-5.5-5.5l2-2-2-4z"/>
            @break
        @case('facebook')
            <rect x="6" y="6" width="36" height="36" rx="9"/>
            <path d="M31 16h-3.5a4.5 4.5 0 0 0-4.5 4.5V42M18 26h12"/>
            @break
        @case('globe')
            <circle cx="24" cy="24" r="18"/>
            <path d="M6 24h36M24 6c5 5 7 11 7 18s-2 13-7 18c-5-5-7-11-7-18s2-13 7-18z"/>
            @break
        @case('mail')
            <rect x="5" y="11" width="38" height="27" rx="5"/>
            <path d="M6 14l18 13 18-13"/>
            @break
        @case('blog')
            <path d="M30 8l10 10-20 20H10V28z"/>
            <path d="M26 12l10 10M8 42h32"/>
            @break
        @case('search')
            <circle cx="21" cy="21" r="13"/>
            <path d="M31 31l10 10"/>
            @break
        @case('clock')
            <circle cx="24" cy="24" r="18"/>
            <path d="M24 14v11l7 4"/>
            @break
        @case('eye')
            <path d="M4 24s7-13 20-13 20 13 20 13-7 13-20 13S4 24 4 24z"/>
            <circle cx="24" cy="24" r="6"/>
            @break
        @case('calendar')
            <rect x="6" y="10" width="36" height="32" rx="5"/>
            <path d="M6 20h36M16 6v8M32 6v8"/>
            @break
        @case('link')
            <path d="M20 28l8-8"/>
            <path d="M22 14l3-3a9 9 0 0 1 12.7 12.7l-3 3M26 34l-3 3A9 9 0 0 1 10.3 24.3l3-3"/>
            @break
        @case('x')
            <path d="M8 8l32 32M40 8L8 40"/>
            @break
        @case('telegram')
            <path d="M42 8L5 22l12 5 3 13 7-8 10 8z"/>
            <path d="M17 27l20-15"/>
            @break
        @case('instagram')
            <rect x="6" y="6" width="36" height="36" rx="10"/>
            <circle cx="24" cy="24" r="8"/>
            <path d="M34 13.5v.5"/>
            @break
        @case('youtube')
            <rect x="4" y="10" width="40" height="28" rx="8"/>
            <path d="M20 18v12l10-6z"/>
            @break
        @case('linkedin')
            <rect x="6" y="6" width="36" height="36" rx="8"/>
            <path d="M15 21v13M15 14.5v.5M22 34V21M22 27c0-3.5 2.5-6 6-6s5 2.5 5 6v7"/>
            @break
        @case('tiktok')
            <path d="M26 6v25a7 7 0 1 1-7-7"/>
            <path d="M26 6c1 5.5 5 9 10 9.5"/>
            @break
        @case('snapchat')
            <path d="M24 7c-6 0-10 4.5-10 10v5l-4 2 4 3c-1 3-4 5-7 6 2 1 4 1 5 2s3 2 5 2 4 3 7 3 5-3 7-3 4-1 5-2 3-1 5-2c-3-1-6-3-7-6l4-3-4-2v-5c0-5.5-4-10-10-10z"/>
            @break
        @case('github')
            <path d="M19 41v-6c-6 1.5-8-2-9-4M29 41v-6c0-2-1-3-1-3 6-.7 10-3 10-10 0-2-.7-4-2-5.5.3-2 .2-4-.5-5.5 0 0-2 0-5 2a17 17 0 0 0-9 0c-3-2-5-2-5-2-.7 1.5-.8 3.5-.5 5.5C12.7 17 12 19 12 21c0 7 4 9.3 10 10 0 0-1 1-1 3"/>
            @break
        @case('discord')
            <path d="M15 13c3-1.5 6-2 9-2s6 .5 9 2c3.5 5 5 11 5 18-3 3-6 4.5-9 5l-2-3.5M15 13c-3.5 5-5 11-5 18 3 3 6 4.5 9 5l2-3.5M16 31c5 2.5 11 2.5 16 0"/>
            <circle cx="19" cy="24" r="2.5"/>
            <circle cx="29" cy="24" r="2.5"/>
            @break
        @case('send')
            <path d="M42 6L20 28M42 6L28 42l-8-14-14-8z"/>
            @break
        @case('user')
            <circle cx="24" cy="16" r="8"/>
            <path d="M8 42c0-8.8 7.2-15 16-15s16 6.2 16 15"/>
            @break
        @case('message')
            <path d="M8 10h32a3 3 0 0 1 3 3v20a3 3 0 0 1-3 3H20l-9 7v-7H8a3 3 0 0 1-3-3V13a3 3 0 0 1 3-3z"/>
            <path d="M14 20h20M14 27h12"/>
            @break
        @case('check')
            <circle cx="24" cy="24" r="18"/>
            <path d="M16 24.5l5.5 5.5L33 18.5"/>
            @break
        @case('lock')
            <rect x="9" y="21" width="30" height="21" rx="5"/>
            <path d="M15 21v-6a9 9 0 0 1 18 0v6M24 30v5"/>
            @break
        @case('arrow-up')
            <path d="M24 40V8M12 20l12-12 12 12"/>
            @break
        @default
            <circle cx="24" cy="24" r="17"/>
    @endswitch
</svg>
