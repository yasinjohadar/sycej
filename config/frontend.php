<?php

/*
|--------------------------------------------------------------------------
| Public website (frontend)
|--------------------------------------------------------------------------
|
| Structural data for the bilingual public site. Copy lives in
| lang/{ar,en}/frontend.php; contact details below are fallbacks used while
| the matching fields in the admin "Site settings" screen are empty.
|
*/

return [

    'locales' => ['ar', 'en'],

    'default_locale' => 'ar',

    /*
     * Order of the main navigation. Each key is both the route name suffix
     * (frontend.{key}) and the translation key (frontend.nav.{key}).
     */
    'pages' => [
        'home',
        'about',
        'areas',
        'programs',
        'knowledge',
        'media',
        'blog',
        'team',
        'partners',
        'governance',
        'contact',
    ],

    'contact' => [
        'email' => 'info@sycej.org',
        'phone' => '00963950582125',
        'phone_dial' => '+963950582125',
        'whatsapp_url' => 'https://wa.me/963950582125',
        'website' => 'sycej.org',
    ],

];
