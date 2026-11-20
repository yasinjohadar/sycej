<?php

namespace Tests\Feature\Frontend;

use App\Models\NewsletterSubscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_page_has_the_newsletter_section_above_the_footer(): void
    {
        $content = $this->get(route('frontend.about', ['locale' => 'ar']))->assertOk()->getContent();

        $section = strpos($content, 'id="newsletter"');
        $footer = strpos($content, '<footer');

        $this->assertNotFalse($section);
        $this->assertLessThan($footer, $section, 'The newsletter is its own section, before the footer.');
        $this->assertStringContainsString('action="'.route('frontend.newsletter.subscribe', ['locale' => 'ar']).'"', $content);
    }

    public function test_a_visitor_can_subscribe(): void
    {
        $this->from(route('frontend.blog', ['locale' => 'en']))
            ->post(route('frontend.newsletter.subscribe', ['locale' => 'en']), ['email' => 'Reader@Example.org'])
            ->assertRedirect(route('frontend.blog', ['locale' => 'en']).'#newsletter')
            ->assertSessionHas('newsletter_subscribed', true);

        $subscriber = NewsletterSubscriber::query()->sole();
        $this->assertSame('reader@example.org', $subscriber->email);
        $this->assertSame('en', $subscriber->locale);
        $this->assertNotNull($subscriber->subscribed_at);
    }

    public function test_the_confirmation_replaces_the_form_after_subscribing(): void
    {
        $this->from(route('frontend.home', ['locale' => 'ar']))
            ->followingRedirects()
            ->post(route('frontend.newsletter.subscribe', ['locale' => 'ar']), ['email' => 'a@example.org'])
            ->assertOk()
            ->assertSee(trans('frontend.newsletter.subscribed', [], 'ar'));
    }

    public function test_subscribing_twice_keeps_a_single_record(): void
    {
        NewsletterSubscriber::factory()->create(['email' => 'repeat@example.org', 'locale' => 'ar']);

        $this->post(route('frontend.newsletter.subscribe', ['locale' => 'en']), ['email' => 'REPEAT@example.org'])
            ->assertSessionHas('newsletter_subscribed', true);

        $this->assertSame(1, NewsletterSubscriber::query()->count());
        $this->assertSame('ar', NewsletterSubscriber::query()->sole()->locale);
    }

    public function test_an_invalid_email_is_rejected_in_its_own_error_bag(): void
    {
        $this->from(route('frontend.contact', ['locale' => 'ar']))
            ->post(route('frontend.newsletter.subscribe', ['locale' => 'ar']), ['email' => 'not-an-email'])
            ->assertRedirect(route('frontend.contact', ['locale' => 'ar']).'#newsletter')
            ->assertSessionHasErrorsIn('newsletter', ['email']);

        $this->assertSame(0, NewsletterSubscriber::query()->count());
    }

    public function test_a_filled_honeypot_is_silently_dropped(): void
    {
        $this->post(route('frontend.newsletter.subscribe', ['locale' => 'ar']), [
            'email' => 'bot@example.org',
            'website' => 'https://spam.example',
        ])->assertSessionHas('newsletter_subscribed', true);

        $this->assertSame(0, NewsletterSubscriber::query()->count());
    }

    public function test_subscribing_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post(route('frontend.newsletter.subscribe', ['locale' => 'ar']), ['email' => "user{$attempt}@example.org"])->assertRedirect();
        }

        $this->post(route('frontend.newsletter.subscribe', ['locale' => 'ar']), ['email' => 'late@example.org'])
            ->assertTooManyRequests();
    }
}
