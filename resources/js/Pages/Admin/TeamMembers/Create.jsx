import AdminUiPage, { AdminPanel } from '@/Components/Admin/AdminUiPage';
import TeamMemberForm, { emptyTeamMember } from '@/Components/Admin/TeamMemberForm';
import { Link, useForm } from '@inertiajs/react';

export default function Create() {
    const { data, setData, post, processing, errors } = useForm(emptyTeamMember());

    return (
        <AdminUiPage
            title="إضافة عضو"
            subtitle="عضو يظهر في صفحة الفريق والخبرات"
            actions={
                <Link href={route('admin.team-members.index')} className="admin-btn admin-btn-ghost">
                    <i className="ri-arrow-right-line" />
                    العودة للقائمة
                </Link>
            }
        >
            <AdminPanel title="بيانات العضو" icon="ri-team-line">
                <TeamMemberForm
                    data={data}
                    setData={setData}
                    errors={errors}
                    processing={processing}
                    submitLabel="حفظ العضو"
                    onSubmit={(e) => {
                        e.preventDefault();
                        post(route('admin.team-members.store'));
                    }}
                />
            </AdminPanel>
        </AdminUiPage>
    );
}
