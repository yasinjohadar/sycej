import MediaField from '@/Components/Admin/MediaField';
import { Link } from '@inertiajs/react';

const YOUTUBE_PATTERN =
    /(?:youtube\.com\/(?:watch\?(?:.*&)?v=|embed\/|shorts\/|live\/|v\/)|youtu\.be\/)([A-Za-z0-9_-]{11})/i;

export function youtubeIdFrom(url) {
    const match = String(url || '').match(YOUTUBE_PATTERN);

    return match ? match[1] : null;
}

export function emptyMediaAppearance() {
    return {
        kind: 'article',
        title: '',
        title_language: 'ar',
        channel_ar: '',
        channel_en: '',
        url: '',
        image_url: '',
        published_on: '',
        is_published: true,
        is_featured: false,
        sort_order: 0,
    };
}

export function mediaAppearanceFormData(appearance) {
    return {
        kind: appearance.kind,
        title: appearance.title || '',
        title_language: appearance.title_language || 'ar',
        channel_ar: appearance.channel_ar || '',
        channel_en: appearance.channel_en || '',
        url: appearance.url || '',
        image_url: appearance.image_url || '',
        published_on: appearance.published_on || '',
        is_published: Boolean(appearance.is_published),
        is_featured: Boolean(appearance.is_featured),
        sort_order: appearance.sort_order ?? 0,
    };
}

const KINDS = [
    { value: 'video', label: 'فيديو', icon: 'ri-youtube-line', hint: 'مقابلة أو تقرير على يوتيوب' },
    { value: 'article', label: 'مقال / تصريح', icon: 'ri-newspaper-line', hint: 'مادة منشورة في موقع أو صحيفة' },
];

function Field({ label, error, hint, children, className = 'col-md-6' }) {
    return (
        <div className={className}>
            <label className="form-label">{label}</label>
            {children}
            {hint && !error && <div className="form-text">{hint}</div>}
            {error && <div className="invalid-feedback d-block">{error}</div>}
        </div>
    );
}

/**
 * Live preview mirroring the public media card, so editors see the result before saving.
 */
function Preview({ data }) {
    const youtubeId = data.kind === 'video' ? youtubeIdFrom(data.url) : null;
    const picture = data.image_url || (youtubeId ? `https://i.ytimg.com/vi/${youtubeId}/hqdefault.jpg` : null);
    const isRtl = data.title_language === 'ar';

    return (
        <div className="media-appearance-preview">
            <div className="media-appearance-preview__visual">
                {picture ? <img src={picture} alt="" /> : <span>{data.channel_ar || 'اسم الجهة الناشرة'}</span>}
                {data.kind === 'video' && (
                    <i className={`ri-play-fill media-appearance-preview__play${youtubeId ? '' : ' is-off'}`} />
                )}
            </div>
            <div className="media-appearance-preview__body">
                <div className="d-flex flex-wrap gap-2 align-items-center small">
                    <span className="admin-badge admin-badge-info">{data.kind === 'video' ? 'فيديو' : 'مقال / تصريح'}</span>
                    <span className="text-muted">{data.published_on || 'بدون تاريخ'}</span>
                </div>
                <strong dir={isRtl ? 'rtl' : 'ltr'}>{data.title || 'عنوان المشاركة يظهر هنا'}</strong>
                <span className="small text-muted">{data.channel_ar || '—'}</span>
            </div>
            {data.kind === 'video' && data.url && !youtubeId && (
                <div className="admin-alert admin-alert-warning mb-0 mt-2">لم يُتعرَّف على رابط يوتيوب بعد.</div>
            )}
        </div>
    );
}

export default function MediaAppearanceForm({ data, setData, errors, processing, onSubmit, submitLabel }) {
    return (
        <form onSubmit={onSubmit}>
            <div className="row g-4">
                <div className="col-xl-8">
                    <div className="row g-3">
                        <div className="col-12">
                            <label className="form-label">نوع المشاركة</label>
                            <div className="media-appearance-kinds">
                                {KINDS.map((kind) => (
                                    <button
                                        key={kind.value}
                                        type="button"
                                        className={`media-appearance-kind${data.kind === kind.value ? ' is-active' : ''}`}
                                        aria-pressed={data.kind === kind.value}
                                        onClick={() => setData('kind', kind.value)}
                                    >
                                        <i className={kind.icon} />
                                        <span>
                                            <strong>{kind.label}</strong>
                                            <small>{kind.hint}</small>
                                        </span>
                                    </button>
                                ))}
                            </div>
                            {errors.kind && <div className="invalid-feedback d-block">{errors.kind}</div>}
                        </div>

                        <Field label="العنوان" error={errors.title} className="col-md-9">
                            <textarea
                                rows={2}
                                className={`form-control${errors.title ? ' is-invalid' : ''}`}
                                value={data.title}
                                onChange={(e) => setData('title', e.target.value)}
                                required
                            />
                        </Field>
                        <Field label="لغة العنوان" error={errors.title_language} className="col-md-3">
                            <select
                                className="form-select"
                                value={data.title_language}
                                onChange={(e) => setData('title_language', e.target.value)}
                            >
                                <option value="ar">العربية</option>
                                <option value="en">English</option>
                            </select>
                        </Field>

                        <Field
                            label={data.kind === 'video' ? 'رابط الفيديو على يوتيوب' : 'رابط المادة'}
                            error={errors.url}
                            hint={data.kind === 'video' ? 'يُشغَّل الفيديو داخل الموقع تلقائياً من هذا الرابط.' : 'يفتح لدى الناشر في نافذة جديدة.'}
                            className="col-12"
                        >
                            <input
                                type="url"
                                dir="ltr"
                                className={`form-control${errors.url ? ' is-invalid' : ''}`}
                                value={data.url}
                                onChange={(e) => setData('url', e.target.value)}
                                placeholder={data.kind === 'video' ? 'https://www.youtube.com/watch?v=…' : 'https://'}
                                required
                            />
                        </Field>

                        <Field label="الجهة الناشرة (عربي)" error={errors.channel_ar}>
                            <input
                                className={`form-control${errors.channel_ar ? ' is-invalid' : ''}`}
                                value={data.channel_ar}
                                onChange={(e) => setData('channel_ar', e.target.value)}
                                placeholder="مثال: تلفزيون سوريا"
                                required
                            />
                        </Field>
                        <Field
                            label="الجهة الناشرة (إنجليزي)"
                            error={errors.channel_en}
                            hint="اختياري — يُستخدم الاسم العربي إن تُرك فارغاً."
                        >
                            <input
                                dir="ltr"
                                className={`form-control${errors.channel_en ? ' is-invalid' : ''}`}
                                value={data.channel_en}
                                onChange={(e) => setData('channel_en', e.target.value)}
                                placeholder="e.g. Syria TV"
                            />
                        </Field>

                        <Field label="تاريخ النشر" error={errors.published_on} hint="اختياري" className="col-md-4">
                            <input
                                type="date"
                                className={`form-control${errors.published_on ? ' is-invalid' : ''}`}
                                value={data.published_on}
                                onChange={(e) => setData('published_on', e.target.value)}
                            />
                        </Field>
                        <Field
                            label="الترتيب"
                            error={errors.sort_order}
                            hint="الأصغر أولاً، ثم الأحدث."
                            className="col-md-4"
                        >
                            <input
                                type="number"
                                min={0}
                                className={`form-control${errors.sort_order ? ' is-invalid' : ''}`}
                                value={data.sort_order}
                                onChange={(e) => setData('sort_order', e.target.value)}
                            />
                        </Field>

                        <div className="col-12">
                            <MediaField
                                label={data.kind === 'video' ? 'صورة مخصّصة (اختياري — تُستخدم صورة يوتيوب افتراضياً)' : 'صورة المادة (اختياري)'}
                                previewUrl={data.image_url || null}
                                error={errors.image_url}
                                allowUpload={false}
                                onPick={(item) => setData('image_url', item.url)}
                                onClear={() => setData('image_url', '')}
                            />
                        </div>

                        <div className="col-12 d-flex flex-wrap gap-4">
                            <label className="d-inline-flex align-items-center gap-2 mb-0">
                                <input
                                    type="checkbox"
                                    checked={data.is_published}
                                    onChange={(e) => setData('is_published', e.target.checked)}
                                />
                                ظاهرة في الموقع
                            </label>
                            <label className="d-inline-flex align-items-center gap-2 mb-0">
                                <input
                                    type="checkbox"
                                    checked={data.is_featured}
                                    onChange={(e) => setData('is_featured', e.target.checked)}
                                />
                                تظهر في الصفحة الرئيسية
                            </label>
                        </div>
                    </div>
                </div>

                <div className="col-xl-4">
                    <div className="media-appearance-aside">
                        <p className="form-label mb-2">معاينة البطاقة</p>
                        <Preview data={data} />
                    </div>
                </div>

                <div className="col-12 d-flex gap-2">
                    <button type="submit" className="admin-btn admin-btn-primary" disabled={processing}>
                        <i className="ri-save-line" />
                        {submitLabel}
                    </button>
                    <Link href={route('admin.media-appearances.index')} className="admin-btn admin-btn-ghost">
                        إلغاء
                    </Link>
                </div>
            </div>
        </form>
    );
}
