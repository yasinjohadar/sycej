<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NewsletterSubscribeRequest extends FormRequest
{
    /**
     * Hidden field real visitors never fill in.
     */
    public const HONEYPOT = 'website';

    /**
     * Its own error bag, so a failed subscription never shows up inside the
     * contact form on the same page (and vice versa).
     *
     * @var string
     */
    protected $errorBag = 'newsletter';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:190'],
            self::HONEYPOT => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => __('frontend.newsletter.email'),
        ];
    }

    public function isFromBot(): bool
    {
        return filled($this->input(self::HONEYPOT));
    }

    /**
     * Back to the newsletter section, not the top of the page.
     */
    protected function getRedirectUrl(): string
    {
        return strtok(url()->previous(), '#').'#newsletter';
    }
}
