import AdminUiPage, { AdminPanel } from '@/Components/Admin/AdminUiPage';
import MediaAppearanceForm, { mediaAppearanceFormData } from '@/Components/Admin/MediaAppearanceForm';
import { Link, useForm } from '@inertiajs/react';

export default function Edit({ appearance }) {
    const { data, setData, put, processing, errors } = useForm(mediaAppearanceFormData(appearance));

    return (
        <AdminUiPage
            title="تعديل مشاركة إعلامية"
            subtitle={appearance.title}
            actions={
                <div className="d-flex flex-wrap gap-2">
                    <a href={appearance.url} target="_blank" rel="noopener noreferrer" className="admin-btn admin-btn-ghost">
                        <i className="ri-external-link-line" />
                        فتح الرابط
                    </a>
                    <Link href={route('admin.media-appearances.index')} className="admin-btn admin-btn-ghost">
                        <i className="ri-arrow-right-line" />
                        العودة للقائمة
                    </Link>
                </div>
            }
        >
            <AdminPanel title="بيانات المشاركة" icon="ri-live-line">
                <MediaAppearanceForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    submitLabel="حفظ التعديلات"
                    onSubmit={(e) => {
                        e.preventDefault();
                        put(route('admin.media-appearances.update', appearance.id));
                    }}
                />
            </AdminPanel>
        </AdminUiPage>
    );
}
