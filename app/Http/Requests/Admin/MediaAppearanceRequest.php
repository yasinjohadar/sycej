<?php

namespace App\Http\Requests\Admin;

use App\Models\MediaAppearance;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Shared by create and update: a media appearance has no unique fields.
 */
class MediaAppearanceRequest extends FormRequest
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
            'kind' => ['required', Rule::in(MediaAppearance::KINDS)],
            'title' => ['required', 'string', 'max:500'],
            'title_language' => ['required', Rule::in(['ar', 'en'])],
            'channel_ar' => ['required', 'string', 'max:150'],
            'channel_en' => ['nullable', 'string', 'max:150'],
            'url' => ['required', 'url', 'max:1000'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'published_on' => ['nullable', 'date'],
            'is_published' => ['boolean'],
            'is_featured' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ];
    }

    /**
     * A video must point at YouTube, since the site plays it in place.
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($this->input('kind') === MediaAppearance::KIND_VIDEO
                    && ! $validator->errors()->has('url')
                    && MediaAppearance::youtubeIdFrom($this->string('url')->toString()) === null) {
                    $validator->errors()->add('url', 'رابط الفيديو يجب أن يكون رابط يوتيوب صالحاً.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'kind' => 'النوع',
            'title' => 'العنوان',
            'title_language' => 'لغة العنوان',
            'channel_ar' => 'اسم الجهة الناشرة (عربي)',
            'channel_en' => 'اسم الجهة الناشرة (إنجليزي)',
            'url' => 'الرابط',
            'image_url' => 'الصورة',
            'published_on' => 'تاريخ النشر',
            'sort_order' => 'الترتيب',
        ];
    }
}
