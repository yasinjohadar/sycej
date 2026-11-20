import AdminUiPage, { AdminPanel } from '@/Components/Admin/AdminUiPage';
import MediaAppearanceForm, { emptyMediaAppearance } from '@/Components/Admin/MediaAppearanceForm';
import { Link, useForm } from '@inertiajs/react';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm(emptyMediaAppearance());

    return (
        <AdminUiPage
            title="إضافة مشاركة إعلامية"
            subtitle="فيديو أو مقال يظهر في أرشيف المشاركات الإعلامية"
            actions={
                <Link href={route('admin.media-appearances.index')} className="admin-btn admin-btn-ghost">
                    <i className="ri-arrow-right-line" />
                    العودة للقائمة
                </Link>
            }
        >
            <AdminPanel title="بيانات المشاركة" icon="ri-live-line">
                <MediaAppearanceForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    submitLabel="حفظ المشاركة"
                    onSubmit={(e) => {
                        e.preventDefault();
                        post(route('admin.media-appearances.store'));
                    }}
                />
            </AdminPanel>
        </AdminUiPage>
    );
}
