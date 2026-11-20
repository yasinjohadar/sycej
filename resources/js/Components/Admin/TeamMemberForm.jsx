import MediaField from '@/Components/Admin/MediaField';
import { Link } from '@inertiajs/react';

export const TEAM_GROUPS = [
    { value: 'founder', label: 'المؤسس', icon: 'ri-vip-crown-line', hint: 'يظهر في البطاقة الكبيرة أعلى الصفحة' },
    { value: 'team', label: 'الفريق', icon: 'ri-team-line', hint: 'أعضاء فريق العمل في المركز' },
    { value: 'advisor', label: 'مستشار', icon: 'ri-lightbulb-line', hint: 'المستشارون والخبراء المتعاونون' },
];

export const groupLabel = (value) => TEAM_GROUPS.find((group) => group.value === value)?.label || value;

export function emptyTeamMember() {
    return {
        group: 'team',
        name_ar: '',
        name_en: '',
        role_ar: '',
        role_en: '',
        bio_ar: '',
        bio_en: '',
        photo_url: '',
        email: '',
        linkedin_url: '',
        is_published: true,
        sort_order: 0,
    };
}

export function teamMemberFormData(member) {
    return {
        group: member.group,
        name_ar: member.name_ar || '',
        name_en: member.name_en || '',
        role_ar: member.role_ar || '',
        role_en: member.role_en || '',
        bio_ar: member.bio_ar || '',
        bio_en: member.bio_en || '',
        photo_url: member.photo_url || '',
        email: member.email || '',
        linkedin_url: member.linkedin_url || '',
        is_published: Boolean(member.is_published),
        sort_order: member.sort_order ?? 0,
    };
}

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

export function TeamAvatar({ photo, name, size = 'md' }) {
    return (
        <span className={`team-member-avatar is-${size}`}>
            {photo ? <img src={photo} alt="" loading="lazy" /> : <span>{(name || '؟').trim().charAt(0)}</span>}
        </span>
    );
}

/**
 * Live preview mirroring the public team card, so editors see the result before saving.
 */
function Preview({ data }) {
    return (
        <div className="team-member-preview">
            <TeamAvatar photo={data.photo_url} name={data.name_ar} size="lg" />
            <span className="admin-badge admin-badge-info">{groupLabel(data.group)}</span>
            <strong>{data.name_ar || 'اسم العضو'}</strong>
            <span className="small text-muted">{data.role_ar || 'الصفة'}</span>
            {data.bio_ar && <p className="small mb-0">{data.bio_ar}</p>}
        </div>
    );
}

export default function TeamMemberForm({ data, setData, errors, processing, onSubmit, submitLabel }) {
    const input = (name, props = {}) => (
        <input
            className={`form-control${errors[name] ? ' is-invalid' : ''}`}
            value={data[name]}
            onChange={(e) => setData(name, e.target.value)}
            {...props}
        />
    );

    return (
        <form onSubmit={onSubmit}>
            <div className="row g-4">
                <div className="col-xl-8">
                    <div className="row g-3">
                        <div className="col-12">
                            <label className="form-label">الفئة</label>
                            <div className="media-appearance-kinds">
                                {TEAM_GROUPS.map((group) => (
                                    <button
                                        key={group.value}
                                        type="button"
                                        className={`media-appearance-kind${data.group === group.value ? ' is-active' : ''}`}
                                        aria-pressed={data.group === group.value}
                                        onClick={() => setData('group', group.value)}
                                    >
                                        <i className={group.icon} />
                                        <span>
                                            <strong>{group.label}</strong>
                                            <small>{group.hint}</small>
                                        </span>
                                    </button>
                                ))}
                            </div>
                            {errors.group && <div className="invalid-feedback d-block">{errors.group}</div>}
                        </div>

                        <Field label="الاسم (عربي)" error={errors.name_ar}>
                            {input('name_ar', { required: true, placeholder: 'مثال: كامل شمس الدين' })}
                        </Field>
                        <Field label="الاسم (إنجليزي)" error={errors.name_en} hint="اختياري — يُستخدم الاسم العربي إن تُرك فارغاً.">
                            {input('name_en', { dir: 'ltr', placeholder: 'e.g. Kamel Shams Alddin' })}
                        </Field>

                        <Field label="الصفة / المسمى (عربي)" error={errors.role_ar}>
                            {input('role_ar', { required: true, placeholder: 'مثال: مستشار قانوني' })}
                        </Field>
                        <Field label="الصفة / المسمى (إنجليزي)" error={errors.role_en} hint="اختياري">
                            {input('role_en', { dir: 'ltr', placeholder: 'e.g. Legal Advisor' })}
                        </Field>

                        <Field label="نبذة (عربي)" error={errors.bio_ar} hint="اختياري" className="col-md-6">
                            <textarea
                                rows={4}
                                className={`form-control${errors.bio_ar ? ' is-invalid' : ''}`}
                                value={data.bio_ar}
                                onChange={(e) => setData('bio_ar', e.target.value)}
                            />
                        </Field>
                        <Field label="نبذة (إنجليزي)" error={errors.bio_en} hint="اختياري" className="col-md-6">
                            <textarea
                                rows={4}
                                dir="ltr"
                                className={`form-control${errors.bio_en ? ' is-invalid' : ''}`}
                                value={data.bio_en}
                                onChange={(e) => setData('bio_en', e.target.value)}
                            />
                        </Field>

                        <Field label="البريد الإلكتروني" error={errors.email} hint="اختياري" className="col-md-6">
                            {input('email', { type: 'email', dir: 'ltr' })}
                        </Field>
                        <Field label="رابط لينكدإن" error={errors.linkedin_url} hint="اختياري" className="col-md-6">
                            {input('linkedin_url', { type: 'url', dir: 'ltr', placeholder: 'https://www.linkedin.com/in/…' })}
                        </Field>

                        <Field label="الترتيب" error={errors.sort_order} hint="الأصغر يظهر أولاً." className="col-md-4">
                            {input('sort_order', { type: 'number', min: 0 })}
                        </Field>

                        <div className="col-12">
                            <MediaField
                                label="الصورة الشخصية (يُفضّل صورة مربعة)"
                                previewUrl={data.photo_url || null}
                                error={errors.photo_url}
                                allowUpload={false}
                                onPick={(item) => setData('photo_url', item.url)}
                                onClear={() => setData('photo_url', '')}
                            />
                        </div>

                        <div className="col-12">
                            <label className="d-inline-flex align-items-center gap-2 mb-0">
                                <input
                                    type="checkbox"
                                    checked={data.is_published}
                                    onChange={(e) => setData('is_published', e.target.checked)}
                                />
                                ظاهر في الموقع
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
                    <Link href={route('admin.team-members.index')} className="admin-btn admin-btn-ghost">
                        إلغاء
                    </Link>
                </div>
            </div>
        </form>
    );
}
