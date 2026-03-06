<template>
    <AdminLayout>
        <div class="page">
            <header class="page__header">
                <h2>{{ t('admin.coaches.title') }}</h2>
                <Link href="/admin/coaches/create" class="btn btn-primary">+ {{ t('admin.coaches.create') }}</Link>
            </header>

            <div class="filters">
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('admin.users.search')"
                    class="input"
                />
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>{{ t('admin.common.id') }}</th>
                    <th>{{ t('admin.coaches.userName') }}</th>
                    <th>{{ t('admin.forms.phone') }}</th>
                    <th>{{ t('admin.forms.specialization') }}</th>
                    <th>{{ t('admin.common.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="coach in coaches.data" :key="coach.id">
                    <td>{{ coach.id }}</td>
                    <td>{{ coach.user?.name || t('admin.common.notSpecified') }}</td>
                    <td>{{ coach.phone || t('admin.common.notSpecified') }}</td>
                    <td>{{ coach.specialization || t('admin.common.notSpecified') }}</td>
                    <td class="actions">
                        <Link :href="`/admin/coaches/${coach.id}/edit`" class="btn btn-edit">{{ t('admin.common.edit') }}</Link>
                        <button class="btn btn-delete" @click="destroy(coach.id)">{{ t('admin.common.delete') }}</button>
                    </td>
                </tr>
                </tbody>
            </table>

            <AdminPagination :links="coaches.links" />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPagination from '@/Components/AdminPagination.vue'
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'
import { ref, watch } from 'vue'
import { route } from 'ziggy-js'

const props = defineProps({
    coaches: Object,
    filters: Object,
})

const { t } = useI18n()
const search = ref(props.filters?.search || '')

const destroy = (id) => {
    if (confirm(t('admin.common.confirmDelete'))) {
        router.delete(`/admin/coaches/${id}`)
    }
}

const changePage = (page) => {
    router.get(
        route('admin.coaches.index'),
        { search: search.value, page },
        { preserveState: true, replace: true }
    )
}

watch([search], () => {
    changePage(1)
})
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
    table-layout: auto;

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
        white-space: normal;
        overflow: visible;
        text-overflow: clip;
    }

    tbody tr {
        background: #fff;
        box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);
        border-radius: 8px;
        transition: background-color 0.3s ease;
    }

    tbody tr:hover {
        background: #e0f2fe;
    }

    .actions {
        display: flex;
        gap: 10px;
        justify-content: flex-start;
        white-space: nowrap;
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

    &-primary {
        background-color: #2563eb;
        color: white;
    }

    &-edit {
        background-color: #3b82f6;
        color: white;
        &:hover {
            background-color: #2563eb;
        }
    }

    &-delete {
        background-color: #ef4444;
        color: white;
        &:hover {
            background-color: #b91c1c;
        }
    }
}
</style>
