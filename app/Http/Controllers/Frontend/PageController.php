<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Support\FrontendSite;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('frontend.pages.home', [
            'mediaItems' => FrontendSite::homeMedia(),
            'latestPosts' => BlogPost::query()
                ->published()
                ->where('language', app()->getLocale())
                ->with('category')
                ->latest('published_at')
                ->latest('id')
                ->limit(4)
                ->get(),
        ]);
    }

    public function about(): View
    {
        return view('frontend.pages.about');
    }

    public function areas(): View
    {
        return view('frontend.pages.areas');
    }

    public function programs(): View
    {
        return view('frontend.pages.programs');
    }

    public function knowledge(): View
    {
        return view('frontend.pages.knowledge');
    }

    public function media(): View
    {
        return view('frontend.pages.media', [
            'items' => FrontendSite::media(),
        ]);
    }

    public function team(): View
    {
        return view('frontend.pages.team', [
            'team' => FrontendSite::team(),
            'videos' => FrontendSite::media('video'),
        ]);
    }

    public function partners(): View
    {
        return view('frontend.pages.partners');
    }

    public function governance(): View
    {
        return view('frontend.pages.governance');
    }
}
