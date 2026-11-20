<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the `{locale}` route segment of the public site to the application.
 *
 * Setting it as a URL default lets every `route('frontend.*')` call inside a
 * page omit the locale and still stay in the visitor's language.
 */
class SetFrontendLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = (string) $request->route('locale');

        if (! in_array($locale, config('frontend.locales'), true)) {
            abort(404);
        }

        app()->setLocale($locale);
        URL::defaults(['locale' => $locale]);

        // The segment is consumed here so controller actions do not receive it.
        $request->route()->forgetParameter('locale');

        return $next($request);
    }
}
