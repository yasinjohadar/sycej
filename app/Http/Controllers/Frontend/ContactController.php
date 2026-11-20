<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ContactMessageRequest;
use App\Mail\Frontend\ContactMessageMail;
use App\Support\FrontendSite;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show(): View
    {
        return view('frontend.pages.contact');
    }

    public function send(ContactMessageRequest $request): RedirectResponse
    {
        /*
         * A bot that filled the honeypot gets the same success message as a person,
         * so it learns nothing about why its message went nowhere.
         */
        if (! $request->isFromBot()) {
            Mail::to(FrontendSite::contact()['email'])->send(new ContactMessageMail(
                senderName: $request->validated('name'),
                senderEmail: $request->validated('email'),
                body: $request->validated('message'),
                visitorLocale: app()->getLocale(),
                topic: $request->validated('topic'),
            ));
        }

        return to_route('frontend.contact')->with('contact_sent', true);
    }
}
