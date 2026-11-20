@extends('frontend.layouts.app')

@section('title', __('frontend.nav.contact'))
@section('description', __('frontend.contact.meta'))

@php
    $quickLinks = [
        ['key' => 'whatsapp', 'icon' => 'whatsapp', 'href' => $contact['whatsapp_url'], 'value' => $contact['whatsapp'], 'ltr' => true, 'external' => true],
        ['key' => 'email', 'icon' => 'mail', 'href' => 'mailto:'.$contact['email'], 'value' => $contact['email'], 'ltr' => true, 'external' => false],
        ['key' => 'phone', 'icon' => 'phone', 'href' => 'tel:'.$contact['phone_dial'], 'value' => $contact['phone'], 'ltr' => true, 'external' => false],
        ['key' => 'location', 'icon' => 'location', 'href' => null, 'value' => $contact['location'], 'ltr' => false, 'external' => false],
    ];
    $selectedTopic = old('topic', 'general');
@endphp

@section('content')
    <x-frontend::hero :title="__('frontend.nav.contact')" :lead="__('frontend.contact.lead')" icon="contact" />

    <section class="fe-section fe-contactpage">
        <div class="fe-shell">
            {{-- Quick ways to reach the Center, one tone per channel. --}}
            <ul class="fe-reach" role="list">
                @foreach ($quickLinks as $link)
                    <li class="fe-reach__item fe-reach__item--{{ $link['key'] }}" data-reveal data-spotlight style="--reveal-delay: {{ $loop->index * 90 }}ms">
                        @if ($link['href'])
                            <a class="fe-reach__link" href="{{ $link['href'] }}" @if ($link['external']) target="_blank" rel="noopener noreferrer" @endif>
                        @else
                            <div class="fe-reach__link">
                        @endif
                            <span class="fe-reach__icon"><x-frontend::icon :name="$link['icon']" :size="26" /></span>
                            <span class="fe-reach__text">
                                <span class="fe-reach__label">{{ __('frontend.contact.quick.'.$link['key']) }}</span>
                                <span class="fe-reach__value" @if ($link['ltr']) dir="ltr" @endif>{{ $link['value'] }}</span>
                            </span>
                        @if ($link['href'])
                            </a>
                        @else
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            <div class="fe-contact">
                <aside class="fe-contact__aside" data-reveal>
                    @include('frontend.partials.contour', ['class' => 'fe-contact__dots'])
                    <span class="fe-contact__orb fe-contact__orb--a" aria-hidden="true"></span>
                    <span class="fe-contact__orb fe-contact__orb--b" aria-hidden="true"></span>

                    <p class="fe-contact__eyebrow"><span class="fe-contact__pulse" aria-hidden="true"></span>{{ __('frontend.contact.aside.eyebrow') }}</p>
                    <h2 class="fe-contact__title">{{ __('frontend.contact.aside.title') }}</h2>
                    <p class="fe-contact__body">{{ __('frontend.contact.aside.body') }}</p>

                    <h3 class="fe-contact__subhead">{{ __('frontend.contact.details') }}</h3>
                    <dl class="fe-contactlist">
                        <div>
                            <dt><span class="fe-contactlist__icon"><x-frontend::icon name="location" :size="20" /></span>{{ __('frontend.contact.labels.location') }}</dt>
                            <dd>{{ $contact['location'] }}</dd>
                        </div>
                        <div>
                            <dt><span class="fe-contactlist__icon"><x-frontend::icon name="mail" :size="20" /></span>{{ __('frontend.contact.labels.email') }}</dt>
                            <dd><a href="mailto:{{ $contact['email'] }}" dir="ltr">{{ $contact['email'] }}</a></dd>
                        </div>
                        <div>
                            <dt><span class="fe-contactlist__icon"><x-frontend::icon name="phone" :size="20" /></span>{{ __('frontend.contact.labels.phone') }}</dt>
                            <dd><a href="tel:{{ $contact['phone_dial'] }}" dir="ltr">{{ $contact['phone'] }}</a></dd>
                        </div>
                        <div>
                            <dt><span class="fe-contactlist__icon"><x-frontend::icon name="whatsapp" :size="20" /></span>{{ __('frontend.contact.labels.whatsapp') }}</dt>
                            <dd><a href="{{ $contact['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" dir="ltr">{{ $contact['whatsapp'] }}</a></dd>
                        </div>
                        <div>
                            <dt><span class="fe-contactlist__icon"><x-frontend::icon name="globe" :size="20" /></span>{{ __('frontend.contact.labels.website') }}</dt>
                            <dd><a href="{{ $contact['website_url'] }}" dir="ltr">{{ $contact['website'] }}</a></dd>
                        </div>
                    </dl>

                    <div class="fe-contact__follow">
                        <span>{{ __('frontend.contact.aside.follow') }}</span>
                        @foreach ($socialLinks as $social)
                            <a href="{{ $social->url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social->label }}" title="{{ $social->label }}"><x-frontend::icon :name="$social->frontendIcon()" :size="20" /></a>
                        @endforeach
                        <a href="{{ $contact['whatsapp_url'] }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp"><x-frontend::icon name="whatsapp" :size="20" /></a>
                        <a href="mailto:{{ $contact['email'] }}" aria-label="{{ __('frontend.contact.labels.email') }}"><x-frontend::icon name="mail" :size="20" /></a>
                    </div>
                </aside>

                <div class="fe-contact__form" data-reveal style="--reveal-delay: 140ms">
                    <header class="fe-contact__formhead">
                        <span class="fe-contact__formicon"><x-frontend::icon name="message" :size="28" /></span>
                        <div>
                            <h2 class="fe-contact__formtitle">{{ __('frontend.contact.form.title') }}</h2>
                            <p class="fe-contact__formsub">{{ __('frontend.contact.form.subtitle') }}</p>
                        </div>
                    </header>

                    @if (session('contact_sent'))
                        <p class="fe-alert fe-alert--success" role="status">
                            <x-frontend::icon name="check" :size="24" />
                            <span>{{ __('frontend.contact.form.sent') }}</span>
                        </p>
                    @endif

                    <form method="POST" action="{{ route('frontend.contact.send') }}" novalidate>
                        @csrf

                        <fieldset class="fe-topics">
                            <legend>{{ __('frontend.contact.form.topic') }}</legend>
                            <div class="fe-topics__list">
                                @foreach (\App\Http\Requests\Frontend\ContactMessageRequest::TOPICS as $topic)
                                    <label class="fe-topic">
                                        <input type="radio" name="topic" value="{{ $topic }}" @checked($selectedTopic === $topic)>
                                        <span>{{ __('frontend.contact.form.topics.'.$topic) }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('topic')<p class="fe-field__error">{{ $message }}</p>@enderror
                        </fieldset>

                        <div class="fe-contact__row">
                            <div class="fe-field">
                                <label for="contact-name">{{ __('frontend.contact.form.name') }}</label>
                                <div class="fe-field__control">
                                    <x-frontend::icon name="user" :size="20" class="fe-field__icon" />
                                    <input id="contact-name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required maxlength="120"
                                           placeholder="{{ __('frontend.contact.form.name_placeholder') }}"
                                           @error('name') aria-invalid="true" aria-describedby="contact-name-error" @enderror>
                                </div>
                                @error('name')<p class="fe-field__error" id="contact-name-error">{{ $message }}</p>@enderror
                            </div>

                            <div class="fe-field">
                                <label for="contact-email">{{ __('frontend.contact.form.email') }}</label>
                                <div class="fe-field__control">
                                    <x-frontend::icon name="mail" :size="20" class="fe-field__icon" />
                                    <input id="contact-email" name="email" type="email" dir="ltr" value="{{ old('email') }}" autocomplete="email" required maxlength="190"
                                           placeholder="{{ __('frontend.contact.form.email_placeholder') }}"
                                           @error('email') aria-invalid="true" aria-describedby="contact-email-error" @enderror>
                                </div>
                                @error('email')<p class="fe-field__error" id="contact-email-error">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="fe-field">
                            <label for="contact-message">{{ __('frontend.contact.form.message') }}</label>
                            <div class="fe-field__control fe-field__control--area">
                                <x-frontend::icon name="message" :size="20" class="fe-field__icon" />
                                <textarea id="contact-message" name="message" rows="6" required minlength="10" maxlength="5000"
                                          placeholder="{{ __('frontend.contact.form.message_placeholder') }}"
                                          @error('message') aria-invalid="true" aria-describedby="contact-message-error" @enderror>{{ old('message') }}</textarea>
                            </div>
                            @error('message')<p class="fe-field__error" id="contact-message-error">{{ $message }}</p>@enderror
                        </div>

                        {{-- Honeypot: hidden from people and assistive tech, filled in by bots. --}}
                        <div class="fe-hp" aria-hidden="true">
                            <label for="contact-company">Company</label>
                            <input id="contact-company" name="{{ \App\Http\Requests\Frontend\ContactMessageRequest::HONEYPOT }}" type="text" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="fe-contact__submit">
                            <p class="fe-form__privacy"><x-frontend::icon name="lock" :size="18" />{{ __('frontend.contact.form.privacy') }}</p>
                            <button class="fe-btn fe-btn--primary fe-contact__send" type="submit">
                                <span>{{ __('frontend.contact.form.send') }}</span>
                                <x-frontend::icon name="send" :size="20" />
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection
