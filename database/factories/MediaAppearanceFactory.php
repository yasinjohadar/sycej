<?php

namespace Database\Factories;

use App\Models\MediaAppearance;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MediaAppearance>
 */
class MediaAppearanceFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'kind' => MediaAppearance::KIND_ARTICLE,
            'title' => fake()->sentence(6),
            'title_language' => 'ar',
            'channel_ar' => 'صحيفة الثورة السورية',
            'channel_en' => 'Al-Thawra newspaper',
            'url' => fake()->url(),
            'published_on' => fake()->dateTimeBetween('-1 year')->format('Y-m-d'),
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    public function video(string $youtubeId = 't04dnchKgyE'): static
    {
        return $this->state(fn () => [
            'kind' => MediaAppearance::KIND_VIDEO,
            'url' => 'https://www.youtube.com/watch?v='.$youtubeId,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }
}
