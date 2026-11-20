<?php

namespace App\Http\Requests\Frontend;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactMessageRequest extends FormRequest
{
    /**
     * Name of the hidden field real visitors never fill in.
     */
    public const HONEYPOT = 'company';

    /**
     * Optional subjects a visitor can tag the message with.
     */
    public const TOPICS = ['general', 'partnership', 'media', 'volunteer', 'other'];

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
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:190'],
            'topic' => ['nullable', 'string', Rule::in(self::TOPICS)],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            self::HONEYPOT => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => __('frontend.contact.form.name'),
            'email' => __('frontend.contact.form.email'),
            'topic' => __('frontend.contact.form.topic'),
            'message' => __('frontend.contact.form.message'),
        ];
    }

    public function isFromBot(): bool
    {
        return filled($this->input(self::HONEYPOT));
    }
}
