<?php

namespace Tests\Feature;

use App\Models\MediaAppearance;
use Database\Seeders\MediaAppearanceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaAppearanceSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_every_article_and_video_from_the_archive(): void
    {
        $this->seed(MediaAppearanceSeeder::class);

        $this->assertSame(9, MediaAppearance::query()->where('kind', MediaAppearance::KIND_ARTICLE)->count());
        $this->assertSame(14, MediaAppearance::query()->where('kind', MediaAppearance::KIND_VIDEO)->count());
        $this->assertSame(23, MediaAppearance::query()->published()->count());
        $this->assertSame(3, MediaAppearance::query()->where('is_featured', true)->count());
    }

    public function test_running_it_twice_does_not_duplicate_anything(): void
    {
        $this->seed(MediaAppearanceSeeder::class);
        $this->seed(MediaAppearanceSeeder::class);

        $this->assertSame(23, MediaAppearance::query()->count());
    }

    public function test_youtube_videos_get_their_id_and_other_platforms_stay_without_one(): void
    {
        $this->seed(MediaAppearanceSeeder::class);

        $this->assertSame('e5Am_8T9fy0', MediaAppearance::query()->where('url', 'https://youtube.com/shorts/e5Am_8T9fy0')->value('youtube_id'));
        $this->assertSame('1GE7L-8J1Mg', MediaAppearance::query()->where('url', 'https://youtu.be/1GE7L-8J1Mg')->value('youtube_id'));
        $this->assertNull(MediaAppearance::query()->where('url', 'https://www.facebook.com/watch/?v=781674231637074')->value('youtube_id'));
        $this->assertSame(8, MediaAppearance::query()->whereNotNull('youtube_id')->count());
    }

    public function test_it_updates_rows_that_were_seeded_earlier(): void
    {
        MediaAppearance::factory()->create([
            'kind' => MediaAppearance::KIND_ARTICLE,
            'url' => 'https://thawra.sy/local/الغطاء-النباتي-في-سوريا/',
            'published_on' => null,
        ]);

        $this->seed(MediaAppearanceSeeder::class);

        $this->assertSame(23, MediaAppearance::query()->count());
        $this->assertSame('2026-06-11', MediaAppearance::query()
            ->where('url', 'https://thawra.sy/local/الغطاء-النباتي-في-سوريا/')
            ->first()
            ->published_on
            ->toDateString());
    }
}
