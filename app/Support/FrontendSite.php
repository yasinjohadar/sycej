<?php

namespace App\Support;

use App\Models\MediaAppearance;
use App\Models\SiteSetting;
use App\Models\SocialLink;
use App\Models\TeamMember;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Shared data for the public website's layout (header, footer, contact page).
 *
 * Values entered in the admin "Site settings" screen win; config/frontend.php
 * supplies the published defaults while those fields are still empty.
 */
class FrontendSite
{
    /**
     * Contact details for the visitor's language, ready for links.
     *
     * @return array{email: string, phone: string, phone_dial: string, whatsapp: string, whatsapp_url: string, website: string, website_url: string, location: string, whatsapp_float: bool, whatsapp_float_url: string}
     */
    public static function contact(): array
    {
        $defaults = config('frontend.contact');
        $settings = self::settings();
        $english = app()->getLocale() === 'en';

        $contact = [
            'email' => $defaults['email'],
            'phone' => $defaults['phone'],
            'phone_dial' => $defaults['phone_dial'],
            'whatsapp' => $defaults['phone'],
            'whatsapp_url' => $defaults['whatsapp_url'],
        ];

        if (filled($settings->email)) {
            $contact['email'] = $settings->email;
        }

        if (filled($settings->phone)) {
            $contact['phone'] = $settings->phone;
            $contact['whatsapp'] = $settings->phone;
            $contact['phone_dial'] = '+'.self::internationalDigits($settings->phone);
        }

        if (filled($settings->primary_whatsapp)) {
            $contact['whatsapp'] = $settings->primary_whatsapp;
            $contact['whatsapp_url'] = 'https://wa.me/'.self::internationalDigits($settings->primary_whatsapp);
        }

        $website = filled($settings->website) ? trim($settings->website) : $defaults['website'];
        $contact['website'] = rtrim(preg_replace('~^https?://~i', '', $website), '/');
        $contact['website_url'] = preg_match('~^https?://~i', $website) ? $website : 'https://'.$website;

        $address = $english ? ($settings->address_en ?: $settings->address) : $settings->address;
        $contact['location'] = filled($address) ? $address : __('frontend.brand.location');

        $message = trim((string) $settings->whatsapp_float_message);
        $contact['whatsapp_float'] = $settings->whatsapp_float_enabled ?? true;
        $contact['whatsapp_float_url'] = $contact['whatsapp_url'].($message !== '' ? '?text='.rawurlencode($message) : '');

        return $contact;
    }

    /**
     * A phone number as wa.me / tel: want it: digits only, without the leading 00.
     */
    private static function internationalDigits(string $number): string
    {
        return ltrim(preg_replace('/\D+/', '', $number), '0');
    }

    /**
     * The current page in another language.
     *
     * A blog post exists in one language only, so its counterpart is the other
     * language's blog; the POST-only contact route falls back to the contact page.
     */
    public static function alternateUrl(string $locale): string
    {
        $route = request()->route();
        $name = $route?->getName() ?? 'frontend.home';
        $parameters = $route?->parameters() ?? [];

        if ($name === 'frontend.blog.show') {
            [$name, $parameters] = ['frontend.blog', []];
        }

        if ($name === 'frontend.contact.send') {
            $name = 'frontend.contact';
        }

        if ($name === 'frontend.newsletter.subscribe') {
            [$name, $parameters] = ['frontend.home', []];
        }

        if (! str_starts_with($name, 'frontend.')) {
            [$name, $parameters] = ['frontend.home', []];
        }

        return route($name, [...$parameters, 'locale' => $locale]);
    }

    public static function logoUrl(): string
    {
        return self::settings()->logoUrl() ?? asset('assets/images/brand/sycej-logo.png');
    }

    /**
     * The settings row, read once per request however many partials ask for it.
     */
    private static function settings(): SiteSetting
    {
        return once(fn (): SiteSetting => SiteSetting::current());
    }

    /**
     * @return Collection<int, SocialLink>
     */
    public static function socialLinks(): Collection
    {
        return once(fn (): Collection => SocialLink::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get());
    }

    /**
     * The founder's published media appearances, optionally narrowed to one kind.
     * Read once from the database and cached until a row changes in the panel.
     *
     * @return list<array{id: int, kind: string, title: string, language: string, channel: string, url: string, youtube_id: ?string, image: ?string, date: ?string, featured: bool}>
     */
    public static function media(?string $kind = null): array
    {
        $items = Cache::rememberForever(MediaAppearance::CACHE_KEY, fn (): array => MediaAppearance::query()
            ->published()
            ->ordered()
            ->get()
            ->map(fn (MediaAppearance $appearance): array => $appearance->toFrontendArray())
            ->all());

        $english = app()->getLocale() === 'en';

        $items = array_map(function (array $item) use ($english): array {
            $item['channel'] = $english && filled($item['channel_en']) ? $item['channel_en'] : $item['channel_ar'];
            unset($item['channel_ar'], $item['channel_en']);

            return $item;
        }, $items);

        if ($kind === null) {
            return $items;
        }

        return array_values(array_filter($items, fn (array $item): bool => $item['kind'] === $kind));
    }

    /**
     * Published team members in the visitor's language, keyed by group in display order
     * (founder, team, advisor). Empty groups are left out. Cached until a row changes.
     *
     * @return array<string, list<array{id: int, group: string, name: string, role: string, bio: ?string, photo: ?string, email: ?string, linkedin: ?string}>>
     */
    public static function team(): array
    {
        $members = Cache::rememberForever(TeamMember::CACHE_KEY, fn (): array => TeamMember::query()
            ->published()
            ->ordered()
            ->get()
            ->map(fn (TeamMember $member): array => $member->toFrontendArray())
            ->all());

        $english = app()->getLocale() === 'en';
        $groups = array_fill_keys(TeamMember::GROUPS, []);

        foreach ($members as $member) {
            $groups[$member['group']][] = [
                'id' => $member['id'],
                'group' => $member['group'],
                'name' => $english && filled($member['name_en']) ? $member['name_en'] : $member['name_ar'],
                'role' => $english && filled($member['role_en']) ? $member['role_en'] : $member['role_ar'],
                'bio' => $english ? ($member['bio_en'] ?: $member['bio_ar']) : ($member['bio_ar'] ?: $member['bio_en']),
                'photo' => $member['photo'],
                'email' => $member['email'],
                'linkedin' => $member['linkedin'],
            ];
        }

        return array_filter($groups);
    }

    /**
     * Items for the home page: those marked for it first, topped up with the newest.
     *
     * @return list<array<string, mixed>>
     */
    public static function homeMedia(int $limit = 6): array
    {
        $items = self::media();

        usort($items, fn (array $a, array $b): int => (int) $b['featured'] <=> (int) $a['featured']);

        return array_slice($items, 0, $limit);
    }
}
