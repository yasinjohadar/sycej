<?php

namespace App\Models;

use Database\Factories\MediaAppearanceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * One of the founder's appearances in the press or on TV, shown in the
 * public media archive and managed from the admin panel.
 *
 * @property int $id
 * @property string $kind
 * @property string $title
 * @property string $title_language
 * @property string $channel_ar
 * @property ?string $channel_en
 * @property string $url
 * @property ?string $youtube_id
 * @property ?string $image_url
 * @property ?Carbon $published_on
 * @property bool $is_published
 * @property bool $is_featured
 * @property int $sort_order
 */
class MediaAppearance extends Model
{
    /** @use HasFactory<MediaAppearanceFactory> */
    use HasFactory;

    public const KIND_VIDEO = 'video';

    public const KIND_ARTICLE = 'article';

    public const KINDS = [self::KIND_VIDEO, self::KIND_ARTICLE];

    /** Cache key for the public list; flushed whenever a row changes. */
    public const CACHE_KEY = 'frontend.media-appearances';

    protected $fillable = [
        'kind',
        'title',
        'title_language',
        'channel_ar',
        'channel_en',
        'url',
        'image_url',
        'published_on',
        'is_published',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'published_on' => 'date',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (self $appearance): void {
            $appearance->youtube_id = $appearance->kind === self::KIND_VIDEO
                ? self::youtubeIdFrom($appearance->url)
                : null;
        });

        static::saved(fn () => self::flushCache());
        static::deleted(fn () => self::flushCache());
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Pull the video id out of any common YouTube link shape.
     */
    public static function youtubeIdFrom(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        $pattern = '~(?:youtube\.com/(?:watch\?(?:.*&)?v=|embed/|shorts/|live/|v/)|youtu\.be/)([A-Za-z0-9_-]{11})~i';

        return preg_match($pattern, $url, $matches) === 1 ? $matches[1] : null;
    }

    /**
     * Archive order: manual order first, then newest, undated last.
     *
     * @param  Builder<self>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')
            ->orderByRaw('published_on is null')
            ->orderByDesc('published_on')
            ->orderByDesc('id');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * Card picture: the chosen image, else YouTube's own still.
     */
    public function thumbnailUrl(): ?string
    {
        if (filled($this->image_url)) {
            return $this->image_url;
        }

        return $this->youtube_id ? "https://i.ytimg.com/vi/{$this->youtube_id}/hqdefault.jpg" : null;
    }

    /**
     * Shape consumed by the public media card. Kept to plain values because it is cached.
     *
     * @return array{id: int, kind: string, title: string, language: string, channel_ar: string, channel_en: ?string, url: string, youtube_id: ?string, image: ?string, date: ?string, featured: bool}
     */
    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'kind' => $this->kind,
            'title' => $this->title,
            'language' => $this->title_language,
            'channel_ar' => $this->channel_ar,
            'channel_en' => $this->channel_en,
            'url' => $this->url,
            'youtube_id' => $this->youtube_id,
            'image' => $this->image_url,
            'date' => $this->published_on?->toDateString(),
            'featured' => $this->is_featured,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'kind' => $this->kind,
            'title' => $this->title,
            'title_language' => $this->title_language,
            'channel_ar' => $this->channel_ar,
            'channel_en' => $this->channel_en,
            'url' => $this->url,
            'youtube_id' => $this->youtube_id,
            'image_url' => $this->image_url,
            'thumbnail' => $this->thumbnailUrl(),
            'published_on' => $this->published_on?->toDateString(),
            'is_published' => $this->is_published,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'updated_at' => $this->updated_at?->format('Y-m-d H:i'),
        ];
    }
}
