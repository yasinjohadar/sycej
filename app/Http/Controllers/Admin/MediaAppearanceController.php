<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesPanelResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaAppearanceRequest;
use App\Models\MediaAppearance;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The founder's media appearances shown on the public "media archive" page.
 */
class MediaAppearanceController extends Controller implements HasMiddleware
{
    use AuthorizesPanelResource;

    public static function middleware(): array
    {
        return self::panelResourceMiddleware(
            'admin',
            'media-appearances',
            false,
            [
                'admin.media-appearances.edit' => ['togglePublish', 'toggleFeatured'],
            ],
        );
    }

    public function index(Request $request): Response
    {
        $query = trim((string) $request->string('query'));
        $kind = $request->string('kind')->toString();
        $status = $request->string('status')->toString();

        $appearances = MediaAppearance::query()
            ->when($query !== '', function (Builder $builder) use ($query) {
                $builder->where(function (Builder $inner) use ($query) {
                    $inner->where('title', 'like', "%{$query}%")
                        ->orWhere('channel_ar', 'like', "%{$query}%")
                        ->orWhere('channel_en', 'like', "%{$query}%");
                });
            })
            ->when(in_array($kind, MediaAppearance::KINDS, true), fn (Builder $q) => $q->where('kind', $kind))
            ->when($status === 'published', fn (Builder $q) => $q->where('is_published', true))
            ->when($status === 'hidden', fn (Builder $q) => $q->where('is_published', false))
            ->when($status === 'featured', fn (Builder $q) => $q->where('is_featured', true))
            ->ordered()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (MediaAppearance $appearance) => $appearance->toAdminArray());

        return Inertia::render('Admin/MediaAppearances/Index', [
            'appearances' => $appearances,
            'stats' => [
                'total' => MediaAppearance::query()->count(),
                'videos' => MediaAppearance::query()->where('kind', MediaAppearance::KIND_VIDEO)->count(),
                'articles' => MediaAppearance::query()->where('kind', MediaAppearance::KIND_ARTICLE)->count(),
                'featured' => MediaAppearance::query()->where('is_featured', true)->count(),
            ],
            'filters' => [
                'query' => $query,
                'kind' => $kind,
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
        return Inertia::render('Admin/MediaAppearances/Create');
    }

    public function store(MediaAppearanceRequest $request): RedirectResponse
    {
        MediaAppearance::query()->create($this->attributes($request));

        return redirect()
            ->route('admin.media-appearances.index')
            ->with('success', 'تمت إضافة المشاركة الإعلامية بنجاح.');
    }

    public function edit(MediaAppearance $media_appearance): Response
    {
        return Inertia::render('Admin/MediaAppearances/Edit', [
            'appearance' => $media_appearance->toAdminArray(),
        ]);
    }

    public function update(MediaAppearanceRequest $request, MediaAppearance $media_appearance): RedirectResponse
    {
        $media_appearance->update($this->attributes($request));

        return redirect()
            ->route('admin.media-appearances.index')
            ->with('success', 'تم تحديث المشاركة الإعلامية بنجاح.');
    }

    public function destroy(MediaAppearance $media_appearance): RedirectResponse
    {
        $media_appearance->delete();

        return back()->with('success', 'تم حذف المشاركة الإعلامية.');
    }

    public function togglePublish(MediaAppearance $media_appearance): RedirectResponse
    {
        $media_appearance->update(['is_published' => ! $media_appearance->is_published]);

        return back()->with('success', $media_appearance->is_published ? 'أصبحت المشاركة ظاهرة في الموقع.' : 'تم إخفاء المشاركة من الموقع.');
    }

    public function toggleFeatured(MediaAppearance $media_appearance): RedirectResponse
    {
        $media_appearance->update(['is_featured' => ! $media_appearance->is_featured]);

        return back()->with('success', $media_appearance->is_featured ? 'ستظهر المشاركة في الصفحة الرئيسية.' : 'أُزيلت المشاركة من الصفحة الرئيسية.');
    }

    /**
     * @return array<string, mixed>
     */
    private function attributes(MediaAppearanceRequest $request): array
    {
        $data = $request->validated();

        return [
            ...$data,
            'channel_en' => filled($data['channel_en'] ?? null) ? $data['channel_en'] : null,
            'image_url' => filled($data['image_url'] ?? null) ? $data['image_url'] : null,
            'is_published' => $request->boolean('is_published'),
            'is_featured' => $request->boolean('is_featured'),
            'sort_order' => (int) ($data['sort_order'] ?? 0),
        ];
    }
}
