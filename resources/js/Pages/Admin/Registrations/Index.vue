<template>
    <AdminLayout>
        <div class="page">
            <header class="page__header">
                <h2>{{ t('admin.registrations.title') }}</h2>
            </header>

            <div class="filters">
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('admin.registrations.search')"
                    class="input"
                />
                <select v-model="statusFilter" class="input">
                    <option value="">{{ t('admin.registrations.allStatuses') }}</option>
                    <option value="pending">{{ t('admin.status.pending') }}</option>
                    <option value="approved">{{ t('admin.status.approved') }}</option>
                    <option value="cancelled">{{ t('admin.status.cancelled') }}</option>
                    <option value="rejected">{{ t('admin.status.rejected') }}</option>
                    <option value="attended">{{ t('admin.status.attended') }}</option>
                    <option value="no_show">{{ t('admin.status.no_show') }}</option>
                </select>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>{{ t('admin.common.id') }}</th>
                    <th>{{ t('admin.registrations.user') }}</th>
                    <th>{{ t('admin.registrations.sport') }}</th>
                    <th>{{ t('admin.registrations.training') }}</th>
                    <th>{{ t('admin.registrations.status') }}</th>
                    <th>{{ t('admin.registrations.createdAt') }}</th>
                    <th>{{ t('admin.registrations.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="reg in registrations.data" :key="reg.id">
                    <td>{{ reg.id }}</td>
                    <td>{{ reg.user?.name || t('admin.common.notSpecified') }}</td>
                    <td>{{ reg.training?.sport?.name || t('admin.common.notSpecified') }}</td>
                    <td>
                        {{ $formatDate(reg.training?.date) }} • {{ reg.training?.time || t('admin.common.notSpecified') }}
                    </td>
                    <td>
                        <span class="badge" :class="'status-' + reg.status">
                            {{ translateStatus(reg.status) }}
                        </span>
                    </td>
                    <td>{{ $formatDate(reg.created_at) }}</td>
                    <td class="actions">
                        <Link :href="`/admin/registrations/${reg.id}/edit`" class="btn btn-edit">
                            {{ t('admin.registrations.edit') }}
                        </Link>
                    </td>
                </tr>
                <tr v-if="registrations.data.length === 0">
                    <td colspan="7" class="no-data">{{ t('admin.registrations.empty') }}</td>
                </tr>
                </tbody>
            </table>

            <AdminPagination :links="registrations.links" />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPagination from '@/Components/AdminPagination.vue'
import { useI18n } from '@/i18n/useI18n'
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { route } from 'ziggy-js'

const props = defineProps({
    registrations: Object,
    filters: Object,
})

const { t } = useI18n()
const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')

const changePage = (page) => {
    router.get(
        route('admin.registrations.index'),
        {
            search: search.value,
            status: statusFilter.value,
            page,
        },
        { preserveState: true, replace: true }
    )
}

watch([search, statusFilter], () => {
    changePage(1)
})

function translateStatus(status) {
    const map = {
        pending: t('admin.status.pending'),
        approved: t('admin.status.approved'),
        cancelled: t('admin.status.cancelled'),
        rejected: t('admin.status.rejected'),
        attended: t('admin.status.attended'),
        no_show: t('admin.status.no_show'),
    }
    return map[status] || status
}
</script>

<style scoped lang="scss">
.page {
    max-width: 1200px;
    margin: 30px auto;
    padding: 0 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: #1e293b;
}

.page__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;

    h2 {
        font-weight: 700;
        font-size: 1.8rem;
    }
}

.table {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    box-shadow: 0 4px 10px rgb(0 0 0 / 0.05);
    border-radius: 12px;
    overflow: hidden;

    thead {
        background: #f5f7fa;
        font-weight: 600;
        color: #475569;
    }

    th, td {
        padding: 14px 16px;
        text-align: left;
        vertical-align: middle;
        font-size: 0.95rem;
    }

    tbody tr {
        background: #fff;
        box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
        transition: background-color 0.3s ease;
    }

    tbody tr:hover {
        background: #e0f2fe;
    }
}

.filters {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;

    .input {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #d1d5db;
        font-size: 1rem;
        color: #334155;
    }
}

.actions {
    display: flex;
    gap: 10px;
    white-space: nowrap;
}

.btn, .btn:link, .btn:visited {
    text-decoration: none !important;
}

.btn {
    border: none;
    cursor: pointer;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: background-color 0.25s ease;

    &-edit {
        background-color: #3b82f6;
        color: white;
        &:hover {
            background-color: #2563eb;
        }
    }
}

.badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-approved { background: #e0f2fe; color: #0369a1; }
.status-cancelled { background: #fee2e2; color: #b91c1c; }
.status-rejected { background: #fee2e2; color: #b91c1c; }
.status-attended { background: #dcfce7; color: #166534; }
.status-no_show { background: #ede9fe; color: #6d28d9; }

.no-data {
    text-align: center;
    color: #94a3b8;
    padding: 20px 0;
}
</style>
