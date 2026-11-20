import AdminUiPage, { AdminPanel } from '@/Components/Admin/AdminUiPage';
import TeamMemberForm, { teamMemberFormData } from '@/Components/Admin/TeamMemberForm';
import { Link, useForm } from '@inertiajs/react';

export default function Edit({ member }) {
    const { data, setData, put, processing, errors } = useForm(teamMemberFormData(member));

    return (
        <AdminUiPage
            title="تعديل عضو"
            subtitle={member.name_ar}
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
                    submitLabel="حفظ التعديلات"
                    onSubmit={(e) => {
                        e.preventDefault();
                        put(route('admin.team-members.update', member.id));
                    }}
                />
            </AdminPanel>
        </AdminUiPage>
    );
}
