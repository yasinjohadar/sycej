<?php

namespace Tests\Feature\Admin;

use App\Models\TeamMember;
use App\Models\User;
use App\Support\FrontendSite;
use Database\Seeders\TeamMemberSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TeamMemberAdminTest extends TestCase
{
    use RefreshDatabase;

    private const PERMISSIONS = [
        'admin.team-members.list',
        'admin.team-members.create',
        'admin.team-members.edit',
        'admin.team-members.delete',
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
            'group' => 'team',
            'name_ar' => 'ريم الحلبي',
            'name_en' => 'Reem Al-Halabi',
            'role_ar' => 'منسقة البرامج',
            'role_en' => '',
            'bio_ar' => 'نبذة',
            'bio_en' => '',
            'photo_url' => 'https://cdn.example.com/reem.jpg',
            'email' => '',
            'linkedin_url' => '',
            'is_published' => true,
            'sort_order' => 2,
            ...$overrides,
        ];
    }

    public function test_the_list_page_renders_with_counts(): void
    {
        TeamMember::factory()->founder()->create();
        TeamMember::factory()->count(2)->create();
        TeamMember::factory()->advisor()->hidden()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.team-members.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/TeamMembers/Index')
                ->has('members.data', 4)
                ->where('stats.total', 4)
                ->where('stats.team', 2)
                ->where('stats.advisors', 1)
                ->where('stats.hidden', 1));
    }

    public function test_the_list_can_be_filtered_by_group_and_search(): void
    {
        TeamMember::factory()->advisor()->create(['name_ar' => 'د. سمير حداد', 'role_ar' => 'مستشار قانوني', 'role_en' => 'Legal Advisor']);
        TeamMember::factory()->create(['name_ar' => 'عمر الخطيب', 'role_ar' => 'باحث']);

        $this->actingAs($this->admin())
            ->get(route('admin.team-members.index', ['group' => 'advisor']))
            ->assertInertia(fn (Assert $page) => $page->has('members.data', 1)->where('members.data.0.name_ar', 'د. سمير حداد'));

        $this->actingAs($this->admin())
            ->get(route('admin.team-members.index', ['query' => 'باحث']))
            ->assertInertia(fn (Assert $page) => $page->has('members.data', 1)->where('members.data.0.name_ar', 'عمر الخطيب'));
    }

    public function test_an_admin_can_add_a_member_and_blank_optionals_are_stored_as_null(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.team-members.store'), $this->payload())
            ->assertRedirect(route('admin.team-members.index'));

        $member = TeamMember::query()->sole();
        $this->assertSame('ريم الحلبي', $member->name_ar);
        $this->assertSame('https://cdn.example.com/reem.jpg', $member->photo_url);
        $this->assertSame(2, $member->sort_order);
        $this->assertNull($member->role_en);
        $this->assertNull($member->email);
    }

    public function test_fields_are_validated(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.team-members.store'), $this->payload([
                'name_ar' => '',
                'role_ar' => '',
                'group' => 'intern',
                'photo_url' => 'not-a-url',
                'email' => 'not-an-email',
            ]))
            ->assertSessionHasErrors(['name_ar', 'role_ar', 'group', 'photo_url', 'email']);

        $this->assertDatabaseCount('team_members', 0);
    }

    public function test_an_admin_can_update_a_member(): void
    {
        $member = TeamMember::factory()->create();

        $this->actingAs($this->admin())
            ->get(route('admin.team-members.edit', $member))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/TeamMembers/Edit')->where('member.id', $member->id));

        $this->actingAs($this->admin())
            ->put(route('admin.team-members.update', $member), $this->payload([
                'group' => 'advisor',
                'name_ar' => 'اسم معدّل',
                'photo_url' => '',
            ]))
            ->assertRedirect(route('admin.team-members.index'));

        $member->refresh();
        $this->assertSame('اسم معدّل', $member->name_ar);
        $this->assertSame('advisor', $member->group);
        $this->assertNull($member->photo_url);
    }

    public function test_an_admin_can_toggle_visibility(): void
    {
        $member = TeamMember::factory()->create();

        $this->actingAs($this->admin())->post(route('admin.team-members.toggle-publish', $member))->assertRedirect();

        $this->assertFalse($member->refresh()->is_published);
    }

    public function test_an_admin_can_delete_a_member(): void
    {
        $member = TeamMember::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.team-members.destroy', $member))
            ->assertRedirect();

        $this->assertModelMissing($member);
    }

    public function test_a_user_without_permission_is_forbidden(): void
    {
        $member = TeamMember::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('admin.team-members.index'))->assertForbidden();
        $this->actingAs($user)->post(route('admin.team-members.store'), $this->payload())->assertForbidden();
        $this->actingAs($user)->put(route('admin.team-members.update', $member), $this->payload())->assertForbidden();
        $this->actingAs($user)->delete(route('admin.team-members.destroy', $member))->assertForbidden();
    }

    public function test_the_public_list_is_grouped_localized_and_refreshed_when_a_row_changes(): void
    {
        TeamMember::factory()->advisor()->create(['name_ar' => 'مستشار', 'name_en' => 'Advisor']);
        $member = TeamMember::factory()->create(['name_ar' => 'الاسم الأول', 'name_en' => null]);
        TeamMember::factory()->hidden()->create();

        $team = FrontendSite::team();
        $this->assertSame(['team', 'advisor'], array_keys($team));
        $this->assertCount(1, $team['team']);
        $this->assertTrue(Cache::has(TeamMember::CACHE_KEY));

        app()->setLocale('en');
        $this->assertSame('Advisor', FrontendSite::team()['advisor'][0]['name']);
        $this->assertSame('الاسم الأول', FrontendSite::team()['team'][0]['name'], 'Falls back to the Arabic name.');

        $member->update(['name_ar' => 'الاسم الجديد']);
        $this->assertFalse(Cache::has(TeamMember::CACHE_KEY));
        app()->setLocale('ar');
        $this->assertSame('الاسم الجديد', FrontendSite::team()['team'][0]['name']);
    }

    public function test_the_seeder_is_safe_to_run_twice(): void
    {
        $this->seed(TeamMemberSeeder::class);
        $count = TeamMember::query()->count();
        $this->seed(TeamMemberSeeder::class);

        $this->assertGreaterThan(1, $count);
        $this->assertSame($count, TeamMember::query()->count());
        $this->assertSame(1, TeamMember::query()->where('group', TeamMember::GROUP_FOUNDER)->count());
    }
}
