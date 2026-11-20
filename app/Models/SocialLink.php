<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialLink extends Model
{
    /**
     * Admin icon (Remix Icon class) => public site line icon (frontend icon component).
     */
    private const FRONTEND_ICONS = [
        'ri-whatsapp-line' => 'whatsapp',
        'ri-telegram-line' => 'telegram',
        'ri-instagram-line' => 'instagram',
        'ri-facebook-circle-line' => 'facebook',
        'ri-twitter-x-line' => 'x',
        'ri-youtube-line' => 'youtube',
        'ri-linkedin-box-line' => 'linkedin',
        'ri-tiktok-line' => 'tiktok',
        'ri-snapchat-line' => 'snapchat',
        'ri-github-line' => 'github',
        'ri-discord-line' => 'discord',
        'ri-mail-line' => 'mail',
        'ri-global-line' => 'globe',
        'ri-phone-line' => 'phone',
    ];

    protected $fillable = [
        'label',
        'url',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * Name of the matching icon on the public site; a generic link icon otherwise.
     */
    public function frontendIcon(): string
    {
        return self::FRONTEND_ICONS[$this->icon] ?? 'link';
    }

    /**
     * @return array<string, mixed>
     */
    public function toAdminArray(): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'url' => $this->url,
            'icon' => $this->icon,
            'sort_order' => $this->sort_order,
            'is_active' => $this->is_active,
        ];
    }
}
