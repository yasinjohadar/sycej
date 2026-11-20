<?php

use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\NewsletterController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\PrivateFileController;
use App\Http\Middleware\SetFrontendLocale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
| The public website. Every page lives under a language prefix (/ar, /en) so each
| translation has its own indexable URL; the bare domain opens the Arabic home.
| Staff reach the panel through /login and /admin as before.
*/
Route::get('/', fn () => redirect()->route('frontend.home', ['locale' => config('frontend.default_locale')]));

Route::prefix('{locale}')
    ->whereIn('locale', config('frontend.locales'))
    ->middleware(SetFrontendLocale::class)
    ->name('frontend.')
    ->group(function () {
        Route::get('/', [PageController::class, 'home'])->name('home');
        Route::get('about', [PageController::class, 'about'])->name('about');
        Route::get('areas', [PageController::class, 'areas'])->name('areas');
        Route::get('programs', [PageController::class, 'programs'])->name('programs');
        Route::get('knowledge', [PageController::class, 'knowledge'])->name('knowledge');
        Route::get('media', [PageController::class, 'media'])->name('media');
        Route::get('team', [PageController::class, 'team'])->name('team');
        Route::get('partners', [PageController::class, 'partners'])->name('partners');
        Route::get('governance', [PageController::class, 'governance'])->name('governance');
        Route::get('blog', [BlogController::class, 'index'])->name('blog');
        Route::get('blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
        Route::get('blog/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
        Route::get('blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
        Route::get('contact', [ContactController::class, 'show'])->name('contact');
        Route::post('contact', [ContactController::class, 'send'])
            ->middleware('throttle:5,1')
            ->name('contact.send');
        Route::post('newsletter', [NewsletterController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('newsletter.subscribe');
    });

/*
| The blog moved into the public site. Old /blog links (shared or indexed before)
| land on the Arabic blog, where posts were published until now.
*/
Route::prefix('blog')->name('blog.')->group(function () {
    $blog = fn (string $name, array $parameters = []) => redirect()->route(
        'frontend.'.$name,
        ['locale' => config('frontend.default_locale'), ...$parameters],
        301,
    );

    Route::get('/', fn () => $blog('blog'))->name('index');
    Route::get('/search', fn (Request $request) => $blog('blog', array_filter(['q' => $request->query('q')])))->name('search');
    Route::get('/category/{slug}', fn (string $slug) => $blog('blog.category', ['slug' => $slug]))->name('category');
    Route::get('/tag/{slug}', fn (string $slug) => $blog('blog.tag', ['slug' => $slug]))->name('tag');
    Route::get('/{slug}', fn (string $slug) => $blog('blog.show', ['slug' => $slug]))->name('show');
});

require __DIR__.'/admin.php';
require __DIR__.'/auth.php';

/*
| A private file behind a signed, expiring link. Only used by drivers that cannot
| sign their own object URLs; S3 hands the client a signed address directly.
*/
Route::get('files/{disk}/{path}', PrivateFileController::class)
    ->where('path', '.*')
    ->middleware('signed')
    ->name('files.private');
