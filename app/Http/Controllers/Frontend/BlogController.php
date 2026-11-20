<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\BlogTag;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * The public blog. Each language prefix lists only the posts written in that
 * language (the post's `language` field, set in the admin panel).
 */
class BlogController extends Controller
{
    private const PER_PAGE = 9;

    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));

        $posts = $this->postsQuery()
            ->when($search !== '', fn (Builder $query) => $query->search($search))
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // The cover story only leads the unfiltered first page.
        $cover = null;
        if ($search === '' && $posts->currentPage() === 1) {
            $cover = $this->postsQuery()->featured()->first() ?? $posts->first();
        }

        return view('frontend.pages.blog.index', [
            'posts' => $posts,
            'cover' => $cover,
            'categories' => $this->categories(),
            'search' => $search,
            'heading' => __('frontend.nav.blog'),
            'lead' => __('frontend.blog.lead'),
            'activeCategory' => null,
            'activeTag' => null,
        ]);
    }

    public function category(string $slug): View
    {
        $category = BlogCategory::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('frontend.pages.blog.index', [
            'posts' => $this->postsQuery()
                ->where('blog_category_id', $category->id)
                ->paginate(self::PER_PAGE),
            'cover' => null,
            'categories' => $this->categories(),
            'search' => '',
            'heading' => $category->name,
            'lead' => $category->description ?: __('frontend.blog.lead'),
            'activeCategory' => $category,
            'activeTag' => null,
        ]);
    }

    public function tag(string $slug): View
    {
        $tag = BlogTag::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('frontend.pages.blog.index', [
            'posts' => $this->postsQuery()
                ->whereHas('tags', fn (Builder $query) => $query->whereKey($tag->id))
                ->paginate(self::PER_PAGE),
            'cover' => null,
            'categories' => $this->categories(),
            'search' => '',
            'heading' => '#'.$tag->name,
            'lead' => $tag->description ?: __('frontend.blog.lead'),
            'activeCategory' => null,
            'activeTag' => $tag,
        ]);
    }

    public function show(string $slug): View
    {
        $post = BlogPost::query()
            ->published()
            ->where('language', app()->getLocale())
            ->where('slug', $slug)
            ->with(['author:id,name', 'category', 'tags'])
            ->firstOrFail();

        $post->incrementViews();

        $related = $this->postsQuery()
            ->whereKeyNot($post->id)
            ->when($post->blog_category_id, fn (Builder $query) => $query->where('blog_category_id', $post->blog_category_id))
            ->limit(3)
            ->get();

        $latest = $this->postsQuery()
            ->whereKeyNot($post->id)
            ->limit(3)
            ->get();

        // Neighbours in publishing order, for the previous / next links under the article.
        $previous = $post->published_at
            ? $this->postsQuery()->where('published_at', '<', $post->published_at)->first()
            : null;

        $next = $post->published_at
            ? BlogPost::query()
                ->published()
                ->where('language', app()->getLocale())
                ->where('published_at', '>', $post->published_at)
                ->oldest('published_at')
                ->first()
            : null;

        return view('frontend.pages.blog.show', [
            'post' => $post,
            'related' => $related,
            'latest' => $latest,
            'previous' => $previous,
            'next' => $next,
        ]);
    }

    /**
     * Published posts in the current language, newest first.
     *
     * @return Builder<BlogPost>
     */
    private function postsQuery(): Builder
    {
        return BlogPost::query()
            ->published()
            ->where('language', app()->getLocale())
            ->with(['author:id,name', 'category'])
            ->latest('published_at')
            ->latest('id');
    }

    /**
     * Active categories that have at least one post in the current language.
     *
     * @return Collection<int, BlogCategory>
     */
    private function categories(): Collection
    {
        return BlogCategory::query()
            ->where('is_active', true)
            ->whereHas('posts', fn (Builder $query) => $query->published()->where('language', app()->getLocale()))
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }
}
