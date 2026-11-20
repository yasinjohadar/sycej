import AdminUiPage, { AdminPanel } from '@/Components/Admin/AdminUiPage';
import DeleteConfirmModal from '@/Components/Admin/DeleteConfirmModal';
import Pagination from '@/Components/Admin/Pagination';
import TableEmptyRow from '@/Components/Admin/TableEmptyRow';
import { TEAM_GROUPS, TeamAvatar, groupLabel } from '@/Components/Admin/TeamMemberForm';
import useCan from '@/hooks/useCan';
import { Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

const GROUP_TABS = [{ value: '', label: 'الكل' }, ...TEAM_GROUPS];

export default function Index({ members, stats, filters, flash }) {
    const { can } = useCan();
    const [query, setQuery] = useState(filters.query || '');
    const [itemToDelete, setItemToDelete] = useState(null);
    const [deleting, setDeleting] = useState(false);
    const canEdit = can('admin.team-members.edit');

    const applyFilters = (changes) => {
        const next = { ...filters, query, ...changes };
        router.get(
            route('admin.team-members.index'),
            Object.fromEntries(Object.entries(next).filter(([, value]) => value)),
            { preserveState: true, replace: true, preserveScroll: true },
        );
    };

    useEffect(() => {
        if (query === (filters.query || '')) {
            return undefined;
        }

        const timer = setTimeout(() => applyFilters({ query }), 350);

        return () => clearTimeout(timer);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [query]);

    const rows = members.data || [];

    return (
        <AdminUiPage
            title="الفريق والخبرات"
            subtitle="الأعضاء والمستشارون المعروضون في صفحة الفريق على الموقع"
            flash={flash}
            chips={[
                { label: 'الإجمالي', value: stats.total, icon: 'ri-group-line' },
                { label: 'الفريق', value: stats.team, icon: 'ri-team-line' },
                { label: 'المستشارون', value: stats.advisors, icon: 'ri-lightbulb-line' },
                { label: 'مخفيون', value: stats.hidden, icon: 'ri-eye-off-line' },
            ]}
            actions={
                can('admin.team-members.create') && (
                    <Link href={route('admin.team-members.create')} className="admin-btn admin-btn-primary">
                        <i className="ri-add-line" />
                        عضو جديد
                    </Link>
                )
            }
        >
            <DeleteConfirmModal
                show={Boolean(itemToDelete)}
                message={itemToDelete ? `هل أنت متأكد من حذف «${itemToDelete.name_ar}»؟` : ''}
                processing={deleting}
                onConfirm={() => {
                    if (!itemToDelete || deleting) {
                        return;
                    }
                    setDeleting(true);
                    router.delete(route('admin.team-members.destroy', itemToDelete.id), {
                        preserveScroll: true,
                        onFinish: () => {
                            setDeleting(false);
                            setItemToDelete(null);
                        },
                    });
                }}
                onCancel={() => !deleting && setItemToDelete(null)}
            />

            <AdminPanel title="قائمة الأعضاء" icon="ri-team-line">
                <div className="card-toolbar mb-3">
                    <div className="admin-filters w-100 d-flex flex-wrap gap-2 align-items-center">
                        <div className="search-input-wrap flex-grow-1">
                            <i className="ri-search-line" />
                            <input
                                type="search"
                                className="form-control"
                                placeholder="بحث بالاسم أو الصفة..."
                                value={query}
                                onChange={(e) => setQuery(e.target.value)}
                            />
                        </div>
                        <div className="d-flex gap-1">
                            {GROUP_TABS.map((tab) => (
                                <button
                                    key={tab.value || 'all'}
                                    type="button"
                                    className={`admin-btn ${(filters.group || '') === tab.value ? 'admin-btn-primary' : 'admin-btn-ghost'}`}
                                    onClick={() => applyFilters({ group: tab.value })}
                                >
                                    {tab.label}
                                </button>
                            ))}
                        </div>
                        <select
                            className="form-select w-auto"
                            value={filters.status || ''}
                            onChange={(e) => applyFilters({ status: e.target.value })}
                        >
                            <option value="">كل الحالات</option>
                            <option value="published">ظاهر</option>
                            <option value="hidden">مخفي</option>
                        </select>
                    </div>
                </div>

                <div className="admin-table-wrap">
                    <table className="admin-data-table">
                        <thead>
                            <tr>
                                <th>العضو</th>
                                <th>الصفة</th>
                                <th>الفئة</th>
                                <th>الترتيب</th>
                                <th>الظهور</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows.map((item) => (
                                <tr key={item.id}>
                                    <td>
                                        <div className="team-member-row">
                                            <TeamAvatar photo={item.photo_url} name={item.name_ar} />
                                            <span>
                                                <strong>{item.name_ar}</strong>
                                                {item.name_en && (
                                                    <span className="small text-muted" dir="ltr">
                                                        {item.name_en}
                                                    </span>
                                                )}
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{item.role_ar}</div>
                                        {item.role_en && (
                                            <div className="small text-muted" dir="ltr">
                                                {item.role_en}
                                            </div>
                                        )}
                                    </td>
                                    <td>
                                        <span className="admin-badge admin-badge-info">{groupLabel(item.group)}</span>
                                    </td>
                                    <td>{item.sort_order}</td>
                                    <td>
                                        <button
                                            type="button"
                                            className={`admin-badge ${item.is_published ? 'admin-badge-success' : 'admin-badge-warning'} border-0`}
                                            disabled={!canEdit}
                                            title={canEdit ? 'تبديل الظهور' : undefined}
                                            onClick={() =>
                                                router.post(route('admin.team-members.toggle-publish', item.id), {}, { preserveScroll: true })
                                            }
                                        >
                                            {item.is_published ? 'ظاهر' : 'مخفي'}
                                        </button>
                                    </td>
                                    <td>
                                        <div className="admin-action-group">
                                            {canEdit && (
                                                <Link
                                                    href={route('admin.team-members.edit', item.id)}
                                                    className="admin-action-btn admin-action-edit"
                                                    title="تعديل"
                                                >
                                                    <i className="ri-edit-line" />
                                                </Link>
                                            )}
                                            {can('admin.team-members.delete') && (
                                                <button
                                                    type="button"
                                                    className="admin-action-btn admin-action-delete"
                                                    title="حذف"
                                                    onClick={() => setItemToDelete(item)}
                                                >
                                                    <i className="ri-delete-bin-line" />
                                                </button>
                                            )}
                                        </div>
                                    </td>
                                </tr>
                            ))}
                            {rows.length === 0 && <TableEmptyRow colSpan={6}>لا يوجد أعضاء مطابقون.</TableEmptyRow>}
                        </tbody>
                    </table>
                </div>

                <Pagination paginator={members} />
            </AdminPanel>
        </AdminUiPage>
    );
}
