<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\NewsletterSubscribeRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function store(NewsletterSubscribeRequest $request): RedirectResponse
    {
        /*
         * Bots and addresses already on the list get the same answer as a new
         * subscriber, so the form reveals nothing about who is subscribed.
         */
        if (! $request->isFromBot()) {
            NewsletterSubscriber::query()->firstOrCreate(
                ['email' => Str::lower($request->validated('email'))],
                [
                    'locale' => app()->getLocale(),
                    'ip_address' => $request->ip(),
                    'subscribed_at' => now(),
                ],
            );
        }

        return redirect()
            ->to(strtok(url()->previous(), '#').'#newsletter')
            ->with('newsletter_subscribed', true);
    }
}
