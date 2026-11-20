<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Database\Seeders\BlogDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_publishes_demo_posts_in_both_languages(): void
    {
        $this->seed(BlogDemoSeeder::class);

        $this->assertSame(6, BlogPost::query()->published()->where('language', 'ar')->count());
        $this->assertSame(3, BlogPost::query()->published()->where('language', 'en')->count());
        $this->assertSame(1, BlogPost::query()->featured()->where('language', 'ar')->count());
        $this->assertSame(1, BlogPost::query()->featured()->where('language', 'en')->count());
    }

    public function test_running_it_twice_does_not_duplicate_anything(): void
    {
        $this->seed(BlogDemoSeeder::class);
        $this->seed(BlogDemoSeeder::class);

        $this->assertSame(9, BlogPost::query()->count());
        $this->assertSame(5, BlogCategory::query()->count());
        $this->assertSame(5, BlogTag::query()->count());
    }

    public function test_the_seeded_posts_fill_the_home_blog_section(): void
    {
        $this->seed(BlogDemoSeeder::class);

        $this->get(route('frontend.home', ['locale' => 'ar']))
            ->assertOk()
            ->assertSee('النفايات في الأرياف السورية: أزمة صامتة تهدد الصحة والغذاء')
            ->assertDontSee('class="fe-soon"', false);

        $this->get(route('frontend.blog.show', ['locale' => 'en', 'slug' => 'adapting-to-drought']))
            ->assertOk()
            ->assertSee('Adapting to drought');
    }
}
