<?php

namespace Tests\Feature\Frontend;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_blog_lists_published_posts_in_the_current_language_only(): void
    {
        $arabic = BlogPost::factory()->published()->create(['title' => 'مقال عربي منشور', 'language' => 'ar']);
        $english = BlogPost::factory()->published()->create(['title' => 'An English post', 'language' => 'en']);
        $draft = BlogPost::factory()->create(['title' => 'مسودة غير منشورة', 'language' => 'ar']);

        $this->get(route('frontend.blog', ['locale' => 'ar']))
            ->assertOk()
            ->assertSee($arabic->title)
            ->assertDontSee($english->title)
            ->assertDontSee($draft->title);

        $this->get(route('frontend.blog', ['locale' => 'en']))
            ->assertOk()
            ->assertSee($english->title)
            ->assertDontSee($arabic->title);
    }

    public function test_an_empty_blog_shows_the_empty_state(): void
    {
        $this->get(route('frontend.blog', ['locale' => 'en']))
            ->assertOk()
            ->assertSee(trans('frontend.blog.empty_title', [], 'en'));
    }

    public function test_the_featured_post_leads_as_the_cover_story(): void
    {
        BlogPost::factory()->published()->create(['language' => 'ar']);
        $featured = BlogPost::factory()->published()->featured()->create(['title' => 'القصة المميزة', 'language' => 'ar']);

        $content = $this->get(route('frontend.blog', ['locale' => 'ar']))->assertOk()->getContent();

        $this->assertStringContainsString('class="fe-cover"', $content);
        $this->assertSame(1, substr_count($content, e($featured->title)), 'The cover story is not repeated in the grid.');
    }

    public function test_search_filters_posts(): void
    {
        BlogPost::factory()->published()->create(['title' => 'إدارة النفايات في حلب', 'language' => 'ar']);
        BlogPost::factory()->published()->create(['title' => 'التكيف المناخي', 'language' => 'ar']);

        $this->get(route('frontend.blog', ['locale' => 'ar', 'q' => 'النفايات']))
            ->assertOk()
            ->assertSee('إدارة النفايات في حلب')
            ->assertDontSee('التكيف المناخي');

        $this->get(route('frontend.blog', ['locale' => 'ar', 'q' => 'لا شيء يطابق']))
            ->assertOk()
            ->assertSee(trans('frontend.blog.no_results', [], 'ar'));
    }

    public function test_the_category_page_lists_only_its_posts(): void
    {
        $category = BlogCategory::factory()->create(['name' => 'المياه']);
        BlogPost::factory()->published()->for($category, 'category')->create(['title' => 'مقال عن المياه', 'language' => 'ar']);
        BlogPost::factory()->published()->create(['title' => 'مقال آخر', 'language' => 'ar']);

        $this->get(route('frontend.blog.category', ['locale' => 'ar', 'slug' => $category->slug]))
            ->assertOk()
            ->assertSee('مقال عن المياه')
            ->assertDontSee('مقال آخر');
    }

    public function test_the_tag_page_lists_only_its_posts(): void
    {
        $tag = BlogTag::query()->create(['name' => 'تدوير', 'slug' => 'recycling', 'is_active' => true]);
        $tagged = BlogPost::factory()->published()->create(['title' => 'مقال موسوم', 'language' => 'ar']);
        $tagged->tags()->attach($tag);
        BlogPost::factory()->published()->create(['title' => 'مقال غير موسوم', 'language' => 'ar']);

        $this->get(route('frontend.blog.tag', ['locale' => 'ar', 'slug' => 'recycling']))
            ->assertOk()
            ->assertSee('مقال موسوم')
            ->assertDontSee('مقال غير موسوم');
    }

    public function test_an_unknown_category_or_tag_is_not_found(): void
    {
        $this->get(route('frontend.blog.category', ['locale' => 'ar', 'slug' => 'missing']))->assertNotFound();
        $this->get(route('frontend.blog.tag', ['locale' => 'ar', 'slug' => 'missing']))->assertNotFound();
    }

    public function test_the_article_page_renders_the_post_and_counts_the_view(): void
    {
        $post = BlogPost::factory()->published()->create([
            'title' => 'عنوان المقال',
            'content' => '<h2>القسم الأول</h2><p>نص المقال الكامل.</p>',
            'language' => 'ar',
            'views_count' => 4,
        ]);

        $this->get(route('frontend.blog.show', ['locale' => 'ar', 'slug' => $post->slug]))
            ->assertOk()
            ->assertSee('عنوان المقال')
            ->assertSee('<h2>القسم الأول</h2>', false)
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('application/ld+json', false)
            ->assertSee('rel="canonical" href="'.$post->publicUrl().'"', false);

        $this->assertSame(5, $post->fresh()->views_count);
    }

    public function test_the_article_page_lists_related_posts_from_the_same_category(): void
    {
        $category = BlogCategory::factory()->create();
        $post = BlogPost::factory()->published()->for($category, 'category')->create(['language' => 'ar']);
        $related = BlogPost::factory()->published()->for($category, 'category')->create(['title' => 'مقال ذو صلة', 'language' => 'ar']);

        $this->get(route('frontend.blog.show', ['locale' => 'ar', 'slug' => $post->slug]))
            ->assertOk()
            ->assertSee(trans('frontend.blog.related', [], 'ar'))
            ->assertSee($related->title);
    }

    public function test_the_article_page_links_its_neighbours_and_the_latest_posts(): void
    {
        $older = BlogPost::factory()->published()->create(['title' => 'مقال أقدم', 'language' => 'ar', 'published_at' => now()->subDays(5)]);
        $post = BlogPost::factory()->published()->create(['title' => 'المقال الحالي', 'language' => 'ar', 'published_at' => now()->subDays(3)]);
        $newer = BlogPost::factory()->published()->create(['title' => 'مقال أحدث', 'language' => 'ar', 'published_at' => now()->subDay()]);

        $this->get(route('frontend.blog.show', ['locale' => 'ar', 'slug' => $post->slug]))
            ->assertOk()
            ->assertSee('rel="prev"', false)
            ->assertSee('href="'.$older->publicUrl().'" rel="prev"', false)
            ->assertSee('href="'.$newer->publicUrl().'" rel="next"', false)
            ->assertSee(trans('frontend.blog.latest', [], 'ar'))
            ->assertSee('data-read-ring', false);
    }

    public function test_drafts_and_other_language_posts_have_no_article_page(): void
    {
        $draft = BlogPost::factory()->create(['language' => 'ar']);
        $english = BlogPost::factory()->published()->create(['language' => 'en']);

        $this->get(route('frontend.blog.show', ['locale' => 'ar', 'slug' => $draft->slug]))->assertNotFound();
        $this->get(route('frontend.blog.show', ['locale' => 'ar', 'slug' => $english->slug]))->assertNotFound();
    }

    public function test_the_language_switch_on_an_article_leads_to_the_other_blog(): void
    {
        $post = BlogPost::factory()->published()->create(['language' => 'ar']);

        $this->get(route('frontend.blog.show', ['locale' => 'ar', 'slug' => $post->slug]))
            ->assertOk()
            ->assertSee('href="'.route('frontend.blog', ['locale' => 'en']).'"', false)
            ->assertDontSee('hreflang="en" href', false);
    }

    public function test_the_home_page_shows_the_latest_posts(): void
    {
        $post = BlogPost::factory()->published()->create(['title' => 'أحدث مقال', 'language' => 'ar']);

        $this->get(route('frontend.home', ['locale' => 'ar']))
            ->assertOk()
            ->assertSee(trans('frontend.blog.home_title', [], 'ar'))
            ->assertSee($post->title);
    }

    public function test_the_home_page_keeps_the_blog_section_with_a_coming_soon_card_before_any_post(): void
    {
        $this->get(route('frontend.home', ['locale' => 'ar']))
            ->assertOk()
            ->assertSee('id="home-blog"', false)
            ->assertSee('class="fe-soon"', false)
            ->assertSee(trans('frontend.blog.empty_title', [], 'ar'));
    }

    public function test_old_blog_links_redirect_to_the_arabic_blog(): void
    {
        $this->get('/blog')->assertRedirect(route('frontend.blog', ['locale' => 'ar']))->assertStatus(301);
        $this->get('/blog/some-post')->assertRedirect(route('frontend.blog.show', ['locale' => 'ar', 'slug' => 'some-post']));
        $this->get('/blog/category/water')->assertRedirect(route('frontend.blog.category', ['locale' => 'ar', 'slug' => 'water']));
        $this->get('/blog/search?q=climate')->assertRedirect(route('frontend.blog', ['locale' => 'ar', 'q' => 'climate']));
    }
}
