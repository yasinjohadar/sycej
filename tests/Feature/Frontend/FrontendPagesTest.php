<?php

namespace Tests\Feature\Frontend;

use App\Models\MediaAppearance;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\TeamMember;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class FrontendPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string, string}>
     */
    public static function pagesProvider(): array
    {
        $cases = [];

        foreach (self::pageKeys() as $page) {
            foreach (['ar', 'en'] as $locale) {
                $cases["{$page} ({$locale})"] = [$page, $locale];
            }
        }

        return $cases;
    }

    /**
     * Data providers run before the application boots, so config() is unavailable.
     *
     * @return list<string>
     */
    private static function pageKeys(): array
    {
        return ['home', 'about', 'areas', 'programs', 'knowledge', 'media', 'team', 'partners', 'governance', 'contact'];
    }

    #[DataProvider('pagesProvider')]
    public function test_every_page_renders_in_both_languages(string $page, string $locale): void
    {
        $direction = $locale === 'ar' ? 'rtl' : 'ltr';

        $this->get(route('frontend.'.$page, ['locale' => $locale]))
            ->assertOk()
            ->assertSee('<html lang="'.$locale.'" dir="'.$direction.'">', false)
            ->assertSee(trans('frontend.nav.'.$page, [], $locale))
            ->assertSee(trans('frontend.brand.name', [], $locale));
    }

    public function test_the_root_url_opens_the_arabic_home_page(): void
    {
        $this->get('/')->assertRedirect(route('frontend.home', ['locale' => 'ar']));
    }

    public function test_an_unsupported_locale_is_not_found(): void
    {
        $this->get('/fr/about')->assertNotFound();
    }

    public function test_the_language_switch_points_to_the_same_page_in_the_other_language(): void
    {
        $this->get(route('frontend.areas', ['locale' => 'ar']))
            ->assertSee('href="'.route('frontend.areas', ['locale' => 'en']).'"', false);

        $this->get(route('frontend.areas', ['locale' => 'en']))
            ->assertSee('href="'.route('frontend.areas', ['locale' => 'ar']).'"', false);
    }

    public function test_pages_declare_their_alternate_language_urls(): void
    {
        $this->get(route('frontend.about', ['locale' => 'en']))
            ->assertSee('hreflang="ar" href="'.route('frontend.about', ['locale' => 'ar']).'"', false)
            ->assertSee('hreflang="en" href="'.route('frontend.about', ['locale' => 'en']).'"', false);
    }

    public function test_internal_links_stay_in_the_current_language(): void
    {
        $this->get(route('frontend.home', ['locale' => 'en']))
            ->assertSee('href="'.route('frontend.governance', ['locale' => 'en']).'"', false)
            ->assertDontSee('href="'.route('frontend.governance', ['locale' => 'ar']).'"', false);
    }

    public function test_the_current_page_is_marked_in_the_navigation(): void
    {
        $content = $this->get(route('frontend.team', ['locale' => 'ar']))->assertOk()->getContent();
        $teamUrl = preg_quote(route('frontend.team', ['locale' => 'ar']), '#');

        $this->assertMatchesRegularExpression('#<a href="'.$teamUrl.'"\s+aria-current="page"#', $content);
        $this->assertSame(2, substr_count($content, 'aria-current="page"'), 'Only the nav link and the breadcrumb are current.');
    }

    public function test_the_team_page_shows_published_members_with_their_photos(): void
    {
        TeamMember::factory()->founder()->create(['name_ar' => 'كامل شمس الدين', 'photo_url' => 'https://cdn.example.com/kamel.jpg']);
        TeamMember::factory()->create(['name_ar' => 'ريم الحلبي', 'name_en' => 'Reem Al-Halabi']);
        TeamMember::factory()->advisor()->create(['name_ar' => 'د. سمير حداد']);
        TeamMember::factory()->hidden()->create(['name_ar' => 'عضو مخفي']);

        $this->get(route('frontend.team', ['locale' => 'ar']))
            ->assertOk()
            ->assertSee('كامل شمس الدين')
            ->assertSee('https://cdn.example.com/kamel.jpg', false)
            ->assertSee('ريم الحلبي')
            ->assertSee(trans('frontend.team.advisors', [], 'ar'))
            ->assertSee('د. سمير حداد')
            ->assertDontSee('عضو مخفي')
            ->assertDontSee(trans('frontend.ui.pending', [], 'ar'));

        $this->get(route('frontend.team', ['locale' => 'en']))
            ->assertSee('Reem Al-Halabi');
    }

    public function test_the_team_page_shows_a_placeholder_when_nobody_is_published(): void
    {
        $this->get(route('frontend.team', ['locale' => 'ar']))
            ->assertOk()
            ->assertSee(trans('frontend.ui.pending', [], 'ar'));
    }

    public function test_the_media_page_lists_published_appearances_only(): void
    {
        $video = MediaAppearance::factory()->video()->create(['title' => 'مقابلة منشورة']);
        $article = MediaAppearance::factory()->create(['title' => 'مقال منشور']);
        MediaAppearance::factory()->hidden()->create(['title' => 'مادة مخفية']);

        $this->get(route('frontend.media', ['locale' => 'ar']))
            ->assertOk()
            ->assertSee($video->title)
            ->assertSee($article->title)
            ->assertSee('data-video="t04dnchKgyE"', false)
            ->assertDontSee('مادة مخفية');
    }

    public function test_the_media_page_names_the_outlet_in_the_visitors_language(): void
    {
        MediaAppearance::factory()->create(['channel_ar' => 'وكالة سانا', 'channel_en' => 'SANA']);
        MediaAppearance::factory()->create(['channel_ar' => 'جريدة محلية', 'channel_en' => null]);

        $this->get(route('frontend.media', ['locale' => 'en']))
            ->assertSee('SANA')
            ->assertDontSee('وكالة سانا')
            ->assertSee('جريدة محلية');

        $this->get(route('frontend.media', ['locale' => 'ar']))->assertSee('وكالة سانا');
    }

    public function test_the_media_page_shows_an_empty_state_without_appearances(): void
    {
        $this->get(route('frontend.media', ['locale' => 'ar']))
            ->assertOk()
            ->assertSee(trans('frontend.media.empty', [], 'ar'))
            ->assertDontSee('data-media-grid', false);
    }

    public function test_the_home_page_leads_with_appearances_marked_for_it(): void
    {
        MediaAppearance::factory()->count(6)->create(['published_on' => '2026-10-01']);
        MediaAppearance::factory()->featured()->create(['title' => 'مادة مختارة للرئيسية', 'published_on' => '2020-01-01']);

        $content = $this->get(route('frontend.home', ['locale' => 'ar']))->assertOk()->getContent();

        $this->assertStringContainsString('مادة مختارة للرئيسية', $content);
        $this->assertSame(6, substr_count($content, 'data-kind="'), 'The home page shows six cards at most.');
    }

    public function test_the_home_page_skips_the_media_section_when_there_is_nothing_to_show(): void
    {
        $this->get(route('frontend.home', ['locale' => 'ar']))
            ->assertOk()
            ->assertDontSee(trans('frontend.home.media.title', [], 'ar'));
    }

    public function test_contact_details_from_site_settings_override_the_defaults(): void
    {
        SiteSetting::current()->update(['email' => 'hello@example.org', 'phone' => '0999 111 222']);

        $this->get(route('frontend.contact', ['locale' => 'en']))
            ->assertSee('mailto:hello@example.org', false)
            ->assertSee('tel:+999111222', false)
            ->assertDontSee(config('frontend.contact.email'));
    }

    public function test_website_and_address_come_from_site_settings_in_the_visitors_language(): void
    {
        SiteSetting::current()->update([
            'website' => 'example.org',
            'address' => 'دمشق، سوريا',
            'address_en' => 'Damascus, Syria',
        ]);

        $this->get(route('frontend.contact', ['locale' => 'ar']))
            ->assertSee('href="https://example.org"', false)
            ->assertSee('دمشق، سوريا');

        $this->get(route('frontend.contact', ['locale' => 'en']))
            ->assertSee('Damascus, Syria')
            ->assertDontSee('Aleppo, Syria');
    }

    public function test_active_social_links_are_shown_as_icons_and_inactive_ones_are_not(): void
    {
        SocialLink::query()->delete();
        SocialLink::query()->create(['label' => 'Instagram', 'url' => 'https://instagram.com/sycej', 'icon' => 'ri-instagram-line', 'is_active' => true]);
        SocialLink::query()->create(['label' => 'YouTube', 'url' => 'https://youtube.com/@hidden', 'icon' => 'ri-youtube-line', 'is_active' => false]);

        $content = $this->get(route('frontend.home', ['locale' => 'ar']))->assertOk()->getContent();

        $this->assertGreaterThanOrEqual(2, substr_count($content, 'href="https://instagram.com/sycej"'), 'Shown in the top bar and the footer.');
        $this->assertStringNotContainsString('youtube.com/@hidden', $content);
        $this->assertSame('instagram', SocialLink::query()->where('label', 'Instagram')->sole()->frontendIcon());
    }

    public function test_the_floating_whatsapp_button_opens_a_chat_with_the_prepared_message(): void
    {
        SiteSetting::current()->update(['primary_whatsapp' => '00963 944 000 111', 'whatsapp_float_message' => 'مرحباً بكم']);

        $this->get(route('frontend.home', ['locale' => 'ar']))
            ->assertSee('data-whatsapp-float', false)
            ->assertSee('href="https://wa.me/963944000111?text='.rawurlencode('مرحباً بكم').'"', false);
    }

    public function test_the_floating_whatsapp_button_can_be_switched_off(): void
    {
        SiteSetting::current()->update(['whatsapp_float_enabled' => false]);

        $this->get(route('frontend.home', ['locale' => 'ar']))
            ->assertOk()
            ->assertDontSee('data-whatsapp-float', false);
    }

    public function test_contact_details_fall_back_to_the_published_defaults(): void
    {
        $this->get(route('frontend.contact', ['locale' => 'ar']))
            ->assertSee('mailto:'.config('frontend.contact.email'), false)
            ->assertSee('tel:'.config('frontend.contact.phone_dial'), false);
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function innerPagesProvider(): array
    {
        return array_filter(self::pagesProvider(), fn (array $case): bool => $case[0] !== 'home');
    }

    #[DataProvider('innerPagesProvider')]
    public function test_inner_pages_open_with_the_shared_hero_and_its_breadcrumb(string $page, string $locale): void
    {
        $content = $this->get(route('frontend.'.$page, ['locale' => $locale]))->assertOk()->getContent();

        $this->assertStringContainsString('class="fe-hero"', $content);
        $this->assertStringContainsString('class="fe-medal"', $content);
        $this->assertMatchesRegularExpression(
            '#<h1 class="fe-hero__title" data-split>\s*'.preg_quote(e(trans('frontend.nav.'.$page, [], $locale)), '#').'\s*</h1>#u',
            $content,
        );
        $this->assertStringContainsString('class="fe-crumbs"', $content);
    }

    #[DataProvider('localesProvider')]
    public function test_the_home_page_has_its_own_hero_scene(string $locale): void
    {
        $response = $this->get(route('frontend.home', ['locale' => $locale]))
            ->assertOk()
            ->assertSee('class="fe-homehero"', false)
            ->assertDontSee('class="fe-hero"', false)
            ->assertDontSee('class="fe-crumbs"', false);

        foreach (trans('frontend.home.hero.elements', [], $locale) as $element) {
            $response->assertSee($element);
        }

        foreach (trans('frontend.areas.items', [], $locale) as $area) {
            $response->assertSee('href="'.route('frontend.areas', ['locale' => $locale]).'#'.$area['key'].'"', false);
        }
    }

    #[DataProvider('localesProvider')]
    public function test_the_home_page_shows_the_topics_ribbon(string $locale): void
    {
        $response = $this->get(route('frontend.home', ['locale' => $locale]))->assertOk();

        foreach (trans('frontend.home.themes', [], $locale) as $theme) {
            $response->assertSee($theme);
        }
    }

    #[DataProvider('localesProvider')]
    public function test_the_home_page_links_every_audience_to_a_node_in_the_hub(string $locale): void
    {
        $content = $this->get(route('frontend.home', ['locale' => $locale]))->assertOk()->getContent();
        $audiences = trans('frontend.home.audience.items', [], $locale);

        $this->assertSame(count($audiences), substr_count($content, 'class="fe-hub__node"'));
        $this->assertSame(count($audiences), substr_count($content, 'class="fe-audience__item"'));

        foreach ($audiences as $audience) {
            $this->assertStringContainsString(e($audience['who']), $content);
        }
    }

    public function test_the_areas_page_has_an_index_entry_for_every_area(): void
    {
        $response = $this->get(route('frontend.areas', ['locale' => 'en']))->assertOk();

        foreach (trans('frontend.areas.items', [], 'en') as $area) {
            $response
                ->assertSee('href="#'.$area['key'].'"', false)
                ->assertSee('id="'.$area['key'].'"', false);
        }
    }

    #[DataProvider('localesProvider')]
    public function test_the_footer_lists_every_area_and_channel(string $locale): void
    {
        $response = $this->get(route('frontend.about', ['locale' => $locale]))
            ->assertOk()
            ->assertSee(trans('frontend.footer.about_group', [], $locale))
            ->assertSee(trans('frontend.footer.knowledge_group', [], $locale))
            ->assertSee('href="'.config('frontend.contact.whatsapp_url').'"', false)
            ->assertSee('mailto:'.config('frontend.contact.email'), false)
            ->assertSee('tel:'.config('frontend.contact.phone_dial'), false)
            ->assertSee('href="'.SocialLink::query()->where('icon', 'ri-facebook-circle-line')->value('url').'"', false);

        foreach (trans('frontend.areas.items', [], $locale) as $area) {
            $response->assertSee('href="'.route('frontend.areas', ['locale' => $locale]).'#'.$area['key'].'"', false);
        }
    }

    public function test_the_footer_language_link_keeps_the_current_page(): void
    {
        $content = $this->get(route('frontend.media', ['locale' => 'ar']))->assertOk()->getContent();
        $footer = substr($content, (int) strpos($content, '<footer'));

        $this->assertStringContainsString('href="'.route('frontend.media', ['locale' => 'en']).'"', $footer);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function localesProvider(): array
    {
        return ['ar' => ['ar'], 'en' => ['en']];
    }
}
