<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesPanelResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TeamMemberRequest;
use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The people shown on the public "team & expertise" page.
 */
class TeamMemberController extends Controller implements HasMiddleware
{
    use AuthorizesPanelResource;

    public static function middleware(): array
    {
        return self::panelResourceMiddleware(
            'admin',
            'team-members',
            false,
            [
                'admin.team-members.edit' => ['togglePublish'],
            ],
        );
    }

    public function index(Request $request): Response
    {
        $query = trim((string) $request->string('query'));
        $group = $request->string('group')->toString();
        $status = $request->string('status')->toString();

        $members = TeamMember::query()
            ->when($query !== '', function (Builder $builder) use ($query) {
                $builder->where(function (Builder $inner) use ($query) {
                    $inner->where('name_ar', 'like', "%{$query}%")
                        ->orWhere('name_en', 'like', "%{$query}%")
                        ->orWhere('role_ar', 'like', "%{$query}%")
                        ->orWhere('role_en', 'like', "%{$query}%");
                });
            })
            ->when(in_array($group, TeamMember::GROUPS, true), fn (Builder $q) => $q->where('group', $group))
            ->when($status === 'published', fn (Builder $q) => $q->where('is_published', true))
            ->when($status === 'hidden', fn (Builder $q) => $q->where('is_published', false))
            ->ordered()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (TeamMember $member) => $member->toAdminArray());

        return Inertia::render('Admin/TeamMembers/Index', [
            'members' => $members,
            'stats' => [
                'total' => TeamMember::query()->count(),
                'team' => TeamMember::query()->where('group', TeamMember::GROUP_TEAM)->count(),
                'advisors' => TeamMember::query()->where('group', TeamMember::GROUP_ADVISOR)->count(),
                'hidden' => TeamMember::query()->where('is_published', false)->count(),
            ],
            'filters' => [
                'query' => $query,
                'group' => $group,
                'status' => $status,
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/TeamMembers/Create');
    }

    public function store(TeamMemberRequest $request): RedirectResponse
    {
        TeamMember::query()->create($this->attributes($request));

        return redirect()
            ->route('admin.team-members.index')
            ->with('success', 'تمت إضافة العضو بنجاح.');
    }

    public function edit(TeamMember $team_member): Response
    {
        return Inertia::render('Admin/TeamMembers/Edit', [
            'member' => $team_member->toAdminArray(),
        ]);
    }

    public function update(TeamMemberRequest $request, TeamMember $team_member): RedirectResponse
    {
        $team_member->update($this->attributes($request));

        return redirect()
            ->route('admin.team-members.index')
            ->with('success', 'تم تحديث بيانات العضو بنجاح.');
    }

    public function destroy(TeamMember $team_member): RedirectResponse
    {
        $team_member->delete();

        return back()->with('success', 'تم حذف العضو.');
    }

    public function togglePublish(TeamMember $team_member): RedirectResponse
    {
        $team_member->update(['is_published' => ! $team_member->is_published]);

        return back()->with('success', $team_member->is_published ? 'أصبح العضو ظاهراً في الموقع.' : 'تم إخفاء العضو من الموقع.');
    }

    /**
     * Blank optional fields are stored as null rather than empty strings.
     *
     * @return array<string, mixed>
     */
    private function attributes(TeamMemberRequest $request): array
    {
        $data = $request->validated();

        foreach (['name_en', 'role_en', 'bio_ar', 'bio_en', 'photo_url', 'email', 'linkedin_url'] as $optional) {
            $data[$optional] = filled($data[$optional] ?? null) ? $data[$optional] : null;
        }

        return [
            ...$data,
            'is_published' => $request->boolean('is_published'),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
