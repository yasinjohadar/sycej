<?php

namespace App\Models;

use Database\Factories\TeamMemberFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * A person shown on the public "team & expertise" page, managed from the admin panel.
 *
 * @property int $id
 * @property string $group
 * @property string $name_ar
 * @property ?string $name_en
 * @property string $role_ar
 * @property ?string $role_en
 * @property ?string $bio_ar
 * @property ?string $bio_en
 * @property ?string $photo_url
 * @property ?string $email
 * @property ?string $linkedin_url
 * @property bool $is_published
 * @property int $sort_order
 */
class TeamMember extends Model
{
    /** @use HasFactory<TeamMemberFactory> */
    use HasFactory;

    public const GROUP_FOUNDER = 'founder';

    public const GROUP_TEAM = 'team';

    public const GROUP_ADVISOR = 'advisor';

    /** Display order of the groups on the public page. */
    public const GROUPS = [self::GROUP_FOUNDER, self::GROUP_TEAM, self::GROUP_ADVISOR];

    /** Cache key for the public list; flushed whenever a row changes. */
    public const CACHE_KEY = 'frontend.team-members';

    protected $fillable = [
        'group',
        'name_ar',
        'name_en',
        'role_ar',
        'role_en',
        'bio_ar',
        'bio_en',
        'photo_url',
        'email',
        'linkedin_url',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => self::flushCache());
        static::deleted(fn () => self::flushCache());
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * @param  Builder<self>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true);
    }

    /**
     * Shape consumed by the public team card. Kept to plain values because it is cached.
     *
     * @return array{id: int, group: string, name_ar: string, name_en: ?string, role_ar: string, role_en: ?string, bio_ar: ?string, bio_en: ?string, photo: ?string, email: ?string, linkedin: ?string}
     */
    public function toFrontendArray(): array
    {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'role_ar' => $this->role_ar,
            'role_en' => $this->role_en,
            'bio_ar' => $this->bio_ar,
            'bio_en' => $this->bio_en,
            'photo' => $this->photo_url,
            'email' => $this->email,
            'linkedin' => $this->linkedin_url,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'group' => $this->group,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'role_ar' => $this->role_ar,
            'role_en' => $this->role_en,
            'bio_ar' => $this->bio_ar,
            'bio_en' => $this->bio_en,
            'photo_url' => $this->photo_url,
            'email' => $this->email,
            'linkedin_url' => $this->linkedin_url,
            'is_published' => $this->is_published,
            'sort_order' => $this->sort_order,
            'updated_at' => $this->updated_at?->format('Y-m-d H:i'),
        ];
    }
}
