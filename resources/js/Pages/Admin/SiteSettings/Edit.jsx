import AdminUiPage, { AdminPanel } from '@/Components/Admin/AdminUiPage';
import MediaField from '@/Components/Admin/MediaField';
import SocialLinksEditor from '@/Components/Admin/SocialLinksEditor';
import useCan from '@/hooks/useCan';
import { useForm } from '@inertiajs/react';

export default function Edit({ setting, socialLinks, iconOptions, flash }) {
    const { can } = useCan();
    const { data, setData, post, processing, errors } = useForm({
        site_name: setting.site_name || '',
        site_name_en: setting.site_name_en || '',
        tagline: setting.tagline || '',
        primary_whatsapp: setting.primary_whatsapp || '',
        whatsapp_float_enabled: setting.whatsapp_float_enabled ?? true,
        whatsapp_float_message: setting.whatsapp_float_message || '',
        website: setting.website || '',
        address_en: setting.address_en || '',
        email: setting.email || '',
        phone: setting.phone || '',
        address: setting.address || '',
        footer_text: setting.footer_text || '',
        logo: null,
        favicon: null,
        logo_url: '',
        favicon_url: '',
        logo_preview: setting.logo_url || '',
        favicon_preview: setting.favicon_url || '',
        remove_logo: false,
        remove_favicon: false,
        social_links: socialLinks || [],
        _method: 'put',
    });

    const submit = (e) => {
        e.preventDefault();
        post(route('admin.site-settings.update'), {
            forceFormData: true,
            preserveScroll: true,
        });
    };

    return (
        <AdminUiPage
            title="إعدادات الموقع العامة"
            subtitle="الاسم، الشعار، بيانات التواصل، زر الواتساب العائم، ووسائل التواصل الاجتماعي — كلها تظهر مباشرة في الموقع"
            flash={flash}
        >
            <form onSubmit={submit}>
                <AdminPanel title="الهوية" icon="ri-global-line" className="mb-3">
                    <div className="row g-3">
                        <div className="col-md-6">
                            <label className="form-label">اسم الموقع</label>
                            <input
                                className={`form-control${errors.site_name ? ' is-invalid' : ''}`}
                                value={data.site_name}
                                onChange={(e) => setData('site_name', e.target.value)}
                                required
                            />
                            {errors.site_name && (
                                <div className="invalid-feedback">{errors.site_name}</div>
                            )}
                        </div>
                        <div className="col-md-6">
                            <label className="form-label">الاسم بالإنكليزية</label>
                            <input
                                className="form-control"
                                value={data.site_name_en}
                                onChange={(e) => setData('site_name_en', e.target.value)}
                            />
                        </div>
                        <div className="col-12">
                            <label className="form-label">شعار مختصر</label>
                            <input
                                className="form-control"
                                value={data.tagline}
                                onChange={(e) => setData('tagline', e.target.value)}
                            />
                        </div>
                        <div className="col-md-6">
                            <MediaField
                                label="اللوغو"
                                previewUrl={
                                    data.remove_logo
                                        ? null
                                        : data.logo_preview ||
                                          (data.logo instanceof File
                                              ? URL.createObjectURL(data.logo)
                                              : null)
                                }
                                error={errors.logo || errors.logo_url}
                                onPick={(item) => {
                                    setData({
                                        ...data,
                                        logo: null,
                                        logo_url: item.url,
                                        logo_preview: item.url,
                                        remove_logo: false,
                                    });
                                }}
                                onFile={(file) => {
                                    setData({
                                        ...data,
                                        logo: file,
                                        logo_url: '',
                                        logo_preview: file ? URL.createObjectURL(file) : '',
                                        remove_logo: false,
                                    });
                                }}
                                onClear={() => {
                                    setData({
                                        ...data,
                                        logo: null,
                                        logo_url: '',
                                        logo_preview: '',
                                        remove_logo: true,
                                    });
                                }}
                            />
                        </div>
                        <div className="col-md-6">
                            <MediaField
                                label="أيقونة المتصفح (Favicon)"
                                previewUrl={
                                    data.remove_favicon
                                        ? null
                                        : data.favicon_preview ||
                                          (data.favicon instanceof File
                                              ? URL.createObjectURL(data.favicon)
                                              : null)
                                }
                                error={errors.favicon || errors.favicon_url}
                                onPick={(item) => {
                                    setData({
                                        ...data,
                                        favicon: null,
                                        favicon_url: item.url,
                                        favicon_preview: item.url,
                                        remove_favicon: false,
                                    });
                                }}
                                onFile={(file) => {
                                    setData({
                                        ...data,
                                        favicon: file,
                                        favicon_url: '',
                                        favicon_preview: file ? URL.createObjectURL(file) : '',
                                        remove_favicon: false,
                                    });
                                }}
                                onClear={() => {
                                    setData({
                                        ...data,
                                        favicon: null,
                                        favicon_url: '',
                                        favicon_preview: '',
                                        remove_favicon: true,
                                    });
                                }}
                            />
                        </div>
                    </div>
                </AdminPanel>

                <AdminPanel title="التواصل" icon="ri-phone-line" className="mb-3">
                    <div className="row g-3">
                        <div className="col-md-4">
                            <label className="form-label">رقم واتساب الأساسي</label>
                            <input
                                className="form-control"
                                value={data.primary_whatsapp}
                                onChange={(e) => setData('primary_whatsapp', e.target.value)}
                                placeholder="9665xxxxxxxx"
                                dir="ltr"
                            />
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">الهاتف</label>
                            <input
                                className="form-control"
                                value={data.phone}
                                onChange={(e) => setData('phone', e.target.value)}
                                dir="ltr"
                            />
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">البريد</label>
                            <input
                                type="email"
                                className={`form-control${errors.email ? ' is-invalid' : ''}`}
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                dir="ltr"
                            />
                            {errors.email && (
                                <div className="invalid-feedback">{errors.email}</div>
                            )}
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">الموقع الإلكتروني</label>
                            <input
                                className={`form-control${errors.website ? ' is-invalid' : ''}`}
                                value={data.website}
                                onChange={(e) => setData('website', e.target.value)}
                                placeholder="sycej.org"
                                dir="ltr"
                            />
                            {errors.website && <div className="invalid-feedback">{errors.website}</div>}
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">العنوان (عربي)</label>
                            <input
                                className="form-control"
                                value={data.address}
                                onChange={(e) => setData('address', e.target.value)}
                                placeholder="حلب، سوريا"
                            />
                        </div>
                        <div className="col-md-4">
                            <label className="form-label">العنوان (إنكليزي)</label>
                            <input
                                className="form-control"
                                value={data.address_en}
                                onChange={(e) => setData('address_en', e.target.value)}
                                placeholder="Aleppo, Syria"
                                dir="ltr"
                            />
                        </div>
                        <div className="col-12">
                            <div className="form-text">
                                الحقول الفارغة تعرض القيم الافتراضية في الموقع. رقم الواتساب الأساسي يُستخدم في الزر العائم وكل روابط واتساب في الموقع.
                            </div>
                        </div>
                        <div className="col-12">
                            <label className="form-label">نص التذييل</label>
                            <textarea
                                className="form-control"
                                rows={2}
                                value={data.footer_text}
                                onChange={(e) => setData('footer_text', e.target.value)}
                            />
                        </div>
                    </div>
                </AdminPanel>

                <AdminPanel title="زر الواتساب العائم" icon="ri-whatsapp-line" className="mb-3">
                    <div className="row g-3 align-items-start">
                        <div className="col-lg-7">
                            <label className="d-flex align-items-center gap-2 mb-3">
                                <span className="form-check form-switch mb-0">
                                    <input
                                        type="checkbox"
                                        role="switch"
                                        className="form-check-input"
                                        checked={Boolean(data.whatsapp_float_enabled)}
                                        onChange={(e) => setData('whatsapp_float_enabled', e.target.checked)}
                                    />
                                </span>
                                <strong>{data.whatsapp_float_enabled ? 'الزر ظاهر في الموقع' : 'الزر مخفي من الموقع'}</strong>
                            </label>
                            <label className="form-label">رسالة جاهزة (اختياري)</label>
                            <textarea
                                className={`form-control${errors.whatsapp_float_message ? ' is-invalid' : ''}`}
                                rows={2}
                                maxLength={300}
                                value={data.whatsapp_float_message}
                                onChange={(e) => setData('whatsapp_float_message', e.target.value)}
                                placeholder="مرحباً، أود الاستفسار عن..."
                            />
                            {errors.whatsapp_float_message && (
                                <div className="invalid-feedback">{errors.whatsapp_float_message}</div>
                            )}
                            <div className="form-text">تُكتب تلقائياً في محادثة واتساب عندما يضغط الزائر على الزر.</div>
                        </div>
                        <div className="col-lg-5">
                            <div className={`wa-float-preview${data.whatsapp_float_enabled ? '' : ' is-off'}`}>
                                <span className="wa-float-preview__tip">هل لديك سؤال؟ راسلنا الآن</span>
                                <span className="wa-float-preview__btn">
                                    <i className="ri-whatsapp-line" />
                                </span>
                            </div>
                            {!data.primary_whatsapp && (
                                <div className="admin-alert admin-alert-warning mt-2 mb-0">
                                    لم يُحدَّد رقم واتساب أساسي، سيستخدم الزر الرقم الافتراضي.
                                </div>
                            )}
                        </div>
                    </div>
                </AdminPanel>

                <AdminPanel title="وسائل التواصل الاجتماعي" icon="ri-share-line" className="mb-3">
                    <p className="text-muted small mb-3">
                        تظهر الروابط المفعّلة كأيقونات في الشريط العلوي والتذييل وصفحة التواصل، بالترتيب المحدد هنا.
                    </p>
                    <SocialLinksEditor
                        links={data.social_links}
                        iconOptions={iconOptions}
                        errors={errors}
                        onChange={(links) => setData('social_links', links)}
                    />
                </AdminPanel>

                {can('admin.site-settings.edit') && (
                    <button type="submit" className="btn btn-primary" disabled={processing}>
                        حفظ الإعدادات
                    </button>
                )}
            </form>
        </AdminUiPage>
    );
}
