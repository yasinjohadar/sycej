<?php

namespace Tests\Feature;

use App\Models\BlogPost;
use App\Models\MediaAppearance;
use Database\Seeders\BlogDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReseedContentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_replaces_media_and_blog_with_exactly_the_seeded_content(): void
    {
        MediaAppearance::factory()->count(5)->create();
        $this->seed(BlogDemoSeeder::class);
        BlogPost::query()->first()->delete();
        BlogPost::factory()->create();

        $this->artisan('content:reseed', ['--force' => true])->assertSuccessful();

        $this->assertSame(23, MediaAppearance::query()->count());
        $this->assertSame(9, BlogPost::withTrashed()->count());
        $this->assertSame(0, BlogPost::onlyTrashed()->count());
    }

    public function test_only_option_leaves_the_other_section_untouched(): void
    {
        BlogPost::factory()->count(2)->create();
        MediaAppearance::factory()->count(3)->create();

        $this->artisan('content:reseed', ['--only' => 'media', '--force' => true])->assertSuccessful();

        $this->assertSame(23, MediaAppearance::query()->count());
        $this->assertSame(2, BlogPost::query()->count());
    }

    public function test_it_rejects_an_unknown_section(): void
    {
        MediaAppearance::factory()->count(3)->create();

        $this->artisan('content:reseed', ['--only' => 'team', '--force' => true])->assertFailed();

        $this->assertSame(3, MediaAppearance::query()->count());
    }

    public function test_it_does_nothing_when_production_confirmation_is_declined(): void
    {
        MediaAppearance::factory()->count(3)->create();
        $this->app['env'] = 'production';

        $this->artisan('content:reseed')
            ->expectsConfirmation('Are you sure you want to run this command?', 'no')
            ->assertFailed();

        $this->assertSame(3, MediaAppearance::query()->count());
    }
}
