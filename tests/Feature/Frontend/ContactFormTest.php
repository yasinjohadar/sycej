<?php

namespace Tests\Feature\Frontend;

use App\Mail\Frontend\ContactMessageMail;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Rana',
            'email' => 'rana@example.org',
            'message' => 'We would like to discuss a joint workshop.',
        ], $overrides);
    }

    public function test_a_valid_message_is_mailed_to_the_center(): void
    {
        Mail::fake();

        $this->post(route('frontend.contact.send', ['locale' => 'en']), $this->validPayload())
            ->assertRedirect(route('frontend.contact', ['locale' => 'en']))
            ->assertSessionHas('contact_sent', true);

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail): bool {
            return $mail->hasTo(config('frontend.contact.email'))
                && $mail->hasReplyTo('rana@example.org')
                && $mail->senderName === 'Rana'
                && $mail->visitorLocale === 'en';
        });
    }

    public function test_the_chosen_topic_travels_with_the_message(): void
    {
        Mail::fake();

        $this->post(route('frontend.contact.send', ['locale' => 'ar']), $this->validPayload(['topic' => 'partnership']))
            ->assertSessionHasNoErrors();

        Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail): bool {
            return $mail->topic === 'partnership'
                && str_contains($mail->render(), trans('frontend.contact.form.topics.partnership', [], 'ar'));
        });
    }

    public function test_an_unknown_topic_is_rejected(): void
    {
        Mail::fake();

        $this->post(route('frontend.contact.send', ['locale' => 'ar']), $this->validPayload(['topic' => 'spam']))
            ->assertSessionHasErrors('topic');

        Mail::assertNothingSent();
    }

    public function test_the_message_goes_to_the_email_set_in_site_settings(): void
    {
        Mail::fake();
        SiteSetting::current()->update(['email' => 'inbox@example.org']);

        $this->post(route('frontend.contact.send', ['locale' => 'ar']), $this->validPayload());

        Mail::assertSent(ContactMessageMail::class, fn (ContactMessageMail $mail): bool => $mail->hasTo('inbox@example.org'));
    }

    public function test_the_success_message_is_shown_after_sending(): void
    {
        Mail::fake();

        $this->followingRedirects()
            ->post(route('frontend.contact.send', ['locale' => 'ar']), $this->validPayload())
            ->assertOk()
            ->assertSee(trans('frontend.contact.form.sent', [], 'ar'));
    }

    public function test_required_fields_are_validated(): void
    {
        Mail::fake();

        $this->from(route('frontend.contact', ['locale' => 'en']))
            ->post(route('frontend.contact.send', ['locale' => 'en']), [])
            ->assertRedirect(route('frontend.contact', ['locale' => 'en']))
            ->assertSessionHasErrors(['name', 'email', 'message']);

        Mail::assertNothingSent();
    }

    public function test_an_invalid_email_and_a_too_short_message_are_rejected(): void
    {
        Mail::fake();

        $this->post(route('frontend.contact.send', ['locale' => 'en']), $this->validPayload([
            'email' => 'not-an-email',
            'message' => 'short',
        ]))->assertSessionHasErrors(['email', 'message']);

        Mail::assertNothingSent();
    }

    public function test_a_filled_honeypot_is_silently_dropped(): void
    {
        Mail::fake();

        $this->post(route('frontend.contact.send', ['locale' => 'en']), $this->validPayload(['company' => 'Spam Inc']))
            ->assertRedirect(route('frontend.contact', ['locale' => 'en']))
            ->assertSessionHas('contact_sent', true);

        Mail::assertNothingSent();
    }

    public function test_sending_is_rate_limited(): void
    {
        Mail::fake();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('frontend.contact.send', ['locale' => 'en']), $this->validPayload())->assertRedirect();
        }

        $this->post(route('frontend.contact.send', ['locale' => 'en']), $this->validPayload())
            ->assertTooManyRequests();
    }
}
