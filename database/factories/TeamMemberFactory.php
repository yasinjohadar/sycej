<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMember>
 */
class TeamMemberFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'group' => TeamMember::GROUP_TEAM,
            'name_ar' => 'سامر '.fake()->randomNumber(3),
            'name_en' => fake()->name(),
            'role_ar' => 'باحث بيئي',
            'role_en' => 'Environmental Researcher',
            'bio_ar' => 'نبذة قصيرة عن العضو.',
            'bio_en' => 'A short member bio.',
            'photo_url' => null,
            'email' => null,
            'linkedin_url' => null,
            'is_published' => true,
            'sort_order' => 0,
        ];
    }

    public function founder(): static
    {
        return $this->state(fn () => ['group' => TeamMember::GROUP_FOUNDER]);
    }

    public function advisor(): static
    {
        return $this->state(fn () => ['group' => TeamMember::GROUP_ADVISOR]);
    }

    public function hidden(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
