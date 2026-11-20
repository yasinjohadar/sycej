import AdminUiPage, { AdminPanel } from '@/Components/Admin/AdminUiPage';
import DeleteConfirmModal from '@/Components/Admin/DeleteConfirmModal';
import Pagination from '@/Components/Admin/Pagination';
import TableEmptyRow from '@/Components/Admin/TableEmptyRow';
import useCan from '@/hooks/useCan';
import { Link, router } from '@inertiajs/react';
import { useEffect, useState } from 'react';

const KIND_TABS = [
    { value: '', label: 'الكل' },
    { value: 'video', label: 'فيديو' },
    { value: 'article', label: 'مقالات' },
];

export default function Index({ appearances, stats, filters, flash }) {
    const { can } = useCan();
    const [query, setQuery] = useState(filters.query || '');
    const [itemToDelete, setItemToDelete] = useState(null);
    const [deleting, setDeleting] = useState(false);
    const canEdit = can('admin.media-appearances.edit');

    const applyFilters = (changes) => {
        const next = { ...filters, query, ...changes };
        router.get(
            route('admin.media-appearances.index'),
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

    const toggle = (name, item) =>
        router.post(route(`admin.media-appearances.${name}`, item.id), {}, { preserveScroll: true });

    const rows = appearances.data || [];

    return (
        <AdminUiPage
            title="المشاركات الإعلامية"
            subtitle="الفيديوهات والمقالات المعروضة في أرشيف المشاركات الإعلامية على الموقع"
            flash={flash}
            chips={[
                { label: 'الإجمالي', value: stats.total, icon: 'ri-stack-line' },
                { label: 'فيديو', value: stats.videos, icon: 'ri-youtube-line' },
                { label: 'مقالات', value: stats.articles, icon: 'ri-newspaper-line' },
                { label: 'في الرئيسية', value: stats.featured, icon: 'ri-home-4-line' },
            ]}
            actions={
                can('admin.media-appearances.create') && (
                    <Link href={route('admin.media-appearances.create')} className="admin-btn admin-btn-primary">
                        <i className="ri-add-line" />
                        مشاركة جديدة
                    </Link>
                )
            }
        >
            <DeleteConfirmModal
                show={Boolean(itemToDelete)}
                message={itemToDelete ? `هل أنت متأكد من حذف «${itemToDelete.title}»؟` : ''}
                processing={deleting}
                onConfirm={() => {
                    if (!itemToDelete || deleting) {
                        return;
                    }
                    setDeleting(true);
                    router.delete(route('admin.media-appearances.destroy', itemToDelete.id), {
                        preserveScroll: true,
                        onFinish: () => {
                            setDeleting(false);
                            setItemToDelete(null);
                        },
                    });
                }}
                onCancel={() => !deleting && setItemToDelete(null)}
            />

            <AdminPanel title="قائمة المشاركات" icon="ri-live-line">
                <div className="card-toolbar mb-3">
                    <div className="admin-filters w-100 d-flex flex-wrap gap-2 align-items-center">
                        <div className="search-input-wrap flex-grow-1">
                            <i className="ri-search-line" />
                            <input
                                type="search"
                                className="form-control"
                                placeholder="بحث بالعنوان أو الجهة الناشرة..."
                                value={query}
                                onChange={(e) => setQuery(e.target.value)}
                            />
                        </div>
                        <div className="d-flex gap-1">
                            {KIND_TABS.map((tab) => (
                                <button
                                    key={tab.value || 'all'}
                                    type="button"
                                    className={`admin-btn ${(filters.kind || '') === tab.value ? 'admin-btn-primary' : 'admin-btn-ghost'}`}
                                    onClick={() => applyFilters({ kind: tab.value })}
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
                            <option value="published">ظاهرة</option>
                            <option value="hidden">مخفية</option>
                            <option value="featured">في الرئيسية</option>
                        </select>
                    </div>
                </div>

                <div className="admin-table-wrap">
                    <table className="admin-data-table">
                        <thead>
                            <tr>
                                <th>المشاركة</th>
                                <th>الجهة الناشرة</th>
                                <th>التاريخ</th>
                                <th>الترتيب</th>
                                <th>الظهور</th>
                                <th>الرئيسية</th>
                                <th>العمليات</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows.map((item) => (
                                <tr key={item.id}>
                                    <td>
                                        <div className="media-appearance-row">
                                            <span className={`media-appearance-row__thumb is-${item.kind}`}>
                                                {item.thumbnail ? (
                                                    <img src={item.thumbnail} alt="" loading="lazy" />
                                                ) : (
                                                    <i className="ri-newspaper-line" />
                                                )}
                                                {item.kind === 'video' && <i className="ri-play-fill media-appearance-row__play" />}
                                            </span>
                                            <span>
                                                <a
                                                    href={item.url}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="media-appearance-row__title"
                                                    dir={item.title_language === 'ar' ? 'rtl' : 'ltr'}
                                                >
                                                    {item.title}
                                                </a>
                                                <span className="admin-badge admin-badge-info">
                                                    {item.kind === 'video' ? 'فيديو' : 'مقال / تصريح'}
                                                </span>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{item.channel_ar}</div>
                                        {item.channel_en && (
                                            <div className="small text-muted" dir="ltr">
                                                {item.channel_en}
                                            </div>
                                        )}
                                    </td>
                                    <td className="text-nowrap">{item.published_on || '—'}</td>
                                    <td>{item.sort_order}</td>
                                    <td>
                                        <button
                                            type="button"
                                            className={`admin-badge ${item.is_published ? 'admin-badge-success' : 'admin-badge-warning'} border-0`}
                                            disabled={!canEdit}
                                            title={canEdit ? 'تبديل الظهور' : undefined}
                                            onClick={() => toggle('toggle-publish', item)}
                                        >
                                            {item.is_published ? 'ظاهرة' : 'مخفية'}
                                        </button>
                                    </td>
                                    <td>
                                        <button
                                            type="button"
                                            className={`admin-action-btn${item.is_featured ? ' text-warning' : ''}`}
                                            disabled={!canEdit}
                                            title={item.is_featured ? 'إزالة من الرئيسية' : 'إظهار في الرئيسية'}
                                            onClick={() => toggle('toggle-featured', item)}
                                        >
                                            <i className={item.is_featured ? 'ri-star-fill' : 'ri-star-line'} />
                                        </button>
                                    </td>
                                    <td>
                                        <div className="admin-action-group">
                                            {canEdit && (
                                                <Link
                                                    href={route('admin.media-appearances.edit', item.id)}
                                                    className="admin-action-btn admin-action-edit"
                                                    title="تعديل"
                                                >
                                                    <i className="ri-edit-line" />
                                                </Link>
                                            )}
                                            {can('admin.media-appearances.delete') && (
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
                            {rows.length === 0 && (
                                <TableEmptyRow colSpan={7}>لا توجد مشاركات إعلامية مطابقة.</TableEmptyRow>
                            )}
                        </tbody>
                    </table>
                </div>

                <Pagination paginator={appearances} />
            </AdminPanel>
        </AdminUiPage>
    );
}
