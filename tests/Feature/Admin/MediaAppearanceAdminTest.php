<?php

namespace Tests\Feature\Admin;

use App\Models\MediaAppearance;
use App\Models\User;
use App\Support\FrontendSite;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MediaAppearanceAdminTest extends TestCase
{
    use RefreshDatabase;

    private const PERMISSIONS = [
        'admin.media-appearances.list',
        'admin.media-appearances.create',
        'admin.media-appearances.edit',
        'admin.media-appearances.delete',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        foreach (self::PERMISSIONS as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        Role::findOrCreate('admin', 'web')->syncPermissions(Permission::all());
    }

    private function admin(): User
    {
        return User::factory()->create()->assignRole('admin');
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(array $overrides = []): array
    {
        return [
            'kind' => 'video',
            'title' => 'مقابلة عن التلوث',
            'title_language' => 'ar',
            'channel_ar' => 'تلفزيون سوريا',
            'channel_en' => 'Syria TV',
            'url' => 'https://youtu.be/UylHVtaN4pw',
            'image_url' => '',
            'published_on' => '2026-05-01',
            'is_published' => true,
            'is_featured' => false,
            'sort_order' => 0,
            ...$overrides,
        ];
    }

    public function test_the_list_page_renders_with_counts(): void
    {
        MediaAppearance::factory()->video()->create();
        MediaAppearance::factory()->count(2)->create();

        $this->actingAs($this->admin())
            ->get(route('admin.media-appearances.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/MediaAppearances/Index')
                ->has('appearances.data', 3)
                ->where('stats.videos', 1)
                ->where('stats.articles', 2));
    }

    public function test_the_list_can_be_filtered_by_kind_and_search(): void
    {
        MediaAppearance::factory()->video()->create(['title' => 'فيديو المناخ']);
        MediaAppearance::factory()->create(['title' => 'مقال النفايات']);

        $this->actingAs($this->admin())
            ->get(route('admin.media-appearances.index', ['kind' => 'video']))
            ->assertInertia(fn (Assert $page) => $page->has('appearances.data', 1)->where('appearances.data.0.title', 'فيديو المناخ'));

        $this->actingAs($this->admin())
            ->get(route('admin.media-appearances.index', ['query' => 'النفايات']))
            ->assertInertia(fn (Assert $page) => $page->has('appearances.data', 1)->where('appearances.data.0.title', 'مقال النفايات'));
    }

    public function test_an_admin_can_add_a_video_and_its_youtube_id_is_derived(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.media-appearances.store'), $this->payload())
            ->assertRedirect(route('admin.media-appearances.index'));

        $appearance = MediaAppearance::query()->sole();
        $this->assertSame('UylHVtaN4pw', $appearance->youtube_id);
        $this->assertNull($appearance->image_url);
        $this->assertSame('https://i.ytimg.com/vi/UylHVtaN4pw/hqdefault.jpg', $appearance->thumbnailUrl());
    }

    public function test_a_video_needs_a_youtube_link(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.media-appearances.store'), $this->payload(['url' => 'https://example.com/clip']))
            ->assertSessionHasErrors('url');

        $this->assertDatabaseCount('media_appearances', 0);
    }

    public function test_required_fields_are_validated(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.media-appearances.store'), $this->payload(['title' => '', 'channel_ar' => '', 'kind' => 'podcast', 'url' => 'not-a-url']))
            ->assertSessionHasErrors(['title', 'channel_ar', 'kind', 'url']);
    }

    public function test_an_admin_can_update_an_appearance(): void
    {
        $appearance = MediaAppearance::factory()->video()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.media-appearances.edit', $appearance))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/MediaAppearances/Edit')->where('appearance.id', $appearance->id));

        $this->actingAs($this->admin())
            ->put(route('admin.media-appearances.update', $appearance), $this->payload([
                'kind' => 'article',
                'title' => 'عنوان معدّل',
                'url' => 'https://sana.sy/reports/1/',
                'image_url' => 'https://cdn.example.com/cover.jpg',
                'channel_en' => '',
            ]))
            ->assertRedirect(route('admin.media-appearances.index'));

        $appearance->refresh();
        $this->assertSame('عنوان معدّل', $appearance->title);
        $this->assertNull($appearance->youtube_id, 'An article carries no video id.');
        $this->assertNull($appearance->channel_en);
        $this->assertSame('https://cdn.example.com/cover.jpg', $appearance->thumbnailUrl());
    }

    public function test_an_admin_can_toggle_visibility_and_home_placement(): void
    {
        $appearance = MediaAppearance::factory()->create();

        $this->actingAs($this->admin())->post(route('admin.media-appearances.toggle-publish', $appearance))->assertRedirect();
        $this->actingAs($this->admin())->post(route('admin.media-appearances.toggle-featured', $appearance))->assertRedirect();

        $appearance->refresh();
        $this->assertFalse($appearance->is_published);
        $this->assertTrue($appearance->is_featured);
    }

    public function test_an_admin_can_delete_an_appearance(): void
    {
        $appearance = MediaAppearance::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.media-appearances.destroy', $appearance))
            ->assertRedirect();

        $this->assertModelMissing($appearance);
    }

    public function test_a_user_without_permission_is_forbidden(): void
    {
        $appearance = MediaAppearance::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.media-appearances.index'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.media-appearances.store'), $this->payload())->assertForbidden();
        $this->actingAs($user)->delete(route('admin.media-appearances.destroy', $appearance))->assertForbidden();
    }

    public function test_the_public_list_is_cached_and_refreshed_when_a_row_changes(): void
    {
        $appearance = MediaAppearance::factory()->create(['title' => 'العنوان الأول']);

        $this->assertSame('العنوان الأول', FrontendSite::media()[0]['title']);
        $this->assertTrue(Cache::has(MediaAppearance::CACHE_KEY));

        $appearance->update(['title' => 'العنوان الجديد']);
        $this->assertFalse(Cache::has(MediaAppearance::CACHE_KEY));
        $this->assertSame('العنوان الجديد', FrontendSite::media()[0]['title']);

        $appearance->delete();
        $this->assertSame([], FrontendSite::media());
    }

    /**
     * @return array<string, array{0: string, 1: ?string}>
     */
    public static function youtubeLinks(): array
    {
        return [
            'watch' => ['https://www.youtube.com/watch?v=t04dnchKgyE', 't04dnchKgyE'],
            'watch with extra params' => ['https://www.youtube.com/watch?feature=share&v=t04dnchKgyE&t=3', 't04dnchKgyE'],
            'short link' => ['https://youtu.be/t04dnchKgyE?si=abc', 't04dnchKgyE'],
            'shorts' => ['https://youtube.com/shorts/t04dnchKgyE', 't04dnchKgyE'],
            'embed' => ['https://www.youtube.com/embed/t04dnchKgyE', 't04dnchKgyE'],
            'not youtube' => ['https://vimeo.com/123', null],
        ];
    }

    #[DataProvider('youtubeLinks')]
    public function test_the_youtube_id_is_read_from_common_link_shapes(string $url, ?string $expected): void
    {
        $this->assertSame($expected, MediaAppearance::youtubeIdFrom($url));
    }
}
