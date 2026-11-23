<?php

namespace App\Http\Requests\Admin;

use App\Models\TeamMember;
use App\Rules\MediaUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Shared by create and update: a team member has no unique fields.
 */
class TeamMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'group' => ['required', Rule::in(TeamMember::GROUPS)],
            'name_ar' => ['required', 'string', 'max:150'],
            'name_en' => ['nullable', 'string', 'max:150'],
            'role_ar' => ['required', 'string', 'max:200'],
            'role_en' => ['nullable', 'string', 'max:200'],
            'bio_ar' => ['nullable', 'string', 'max:3000'],
            'bio_en' => ['nullable', 'string', 'max:3000'],
            'photo_url' => ['nullable', 'string', 'max:1000', new MediaUrl],
            'email' => ['nullable', 'email', 'max:150'],
            'linkedin_url' => ['nullable', 'url', 'max:500'],
            'is_published' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'group' => 'الفئة',
            'name_ar' => 'الاسم (عربي)',
            'name_en' => 'الاسم (إنجليزي)',
            'role_ar' => 'الصفة (عربي)',
            'role_en' => 'الصفة (إنجليزي)',
            'bio_ar' => 'النبذة (عربي)',
            'bio_en' => 'النبذة (إنجليزي)',
            'photo_url' => 'الصورة',
            'email' => 'البريد الإلكتروني',
            'linkedin_url' => 'رابط لينكدإن',
            'sort_order' => 'الترتيب',
        ];
    }
}
