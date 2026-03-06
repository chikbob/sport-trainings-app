<template>
    <AdminLayout>
        <div class="page">
            <header class="page__header">
                <h2>{{ t('admin.users.title') }}</h2>
                <Link :href="route('admin.users.create')" class="btn btn-primary">
                    + {{ t('admin.users.create') }}
                </Link>
            </header>

            <div class="filters">
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('admin.users.search')"
                    class="input"
                />
                <select v-model="roleFilter" class="input">
                    <option value="">{{ t('admin.users.rolesAll') }}</option>
                    <option value="user">{{ t('admin.roles.user') }}</option>
                    <option value="coach">{{ t('admin.roles.coach') }}</option>
                    <option value="admin">{{ t('admin.roles.admin') }}</option>
                </select>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>{{ t('admin.common.id') }}</th>
                    <th>{{ t('admin.users.name') }}</th>
                    <th>{{ t('admin.forms.email') }}</th>
                    <th>{{ t('admin.users.role') }}</th>
                    <th>{{ t('admin.common.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in filteredUsers" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>
                        <span :class="['badge', 'role-' + user.role]">{{ translateRole(user.role) }}</span>
                    </td>
                    <td class="actions">
                        <Link
                            :href="route('admin.users.edit', user.id)"
                            class="btn btn-edit"
                        >
                            {{ t('admin.users.edit') }}
                        </Link>
                        <button class="btn btn-delete" @click="destroy(user.id)">
                            {{ t('admin.users.delete') }}
                        </button>
                    </td>
                </tr>
                <tr v-if="filteredUsers.length === 0">
                    <td colspan="5" class="no-data">{{ t('admin.users.notFound') }}</td>
                </tr>
                </tbody>
            </table>

            <AdminPagination :links="props.users.links" />
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {computed, ref, watch} from 'vue'
import {Link, router} from '@inertiajs/vue3'
import {route} from 'ziggy-js'
import AdminPagination from '@/Components/AdminPagination.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    users: Object,
    filters: Object,
})

const { t } = useI18n()

const search = ref(props.filters?.search || '')
const roleFilter = ref(props.filters?.role || '')

const usersArray = computed(() =>
    Array.isArray(props.users?.data) ? props.users.data : []
)

const filteredUsers = computed(() =>
    usersArray.value
        .filter((u) => {
            const matchesSearch =
                u.name.toLowerCase().includes(search.value.toLowerCase()) ||
                u.email.toLowerCase().includes(search.value.toLowerCase())

            const matchesRole = !roleFilter.value || u.role === roleFilter.value

            return matchesSearch && matchesRole
        })
        .sort((a, b) => b.id - a.id)
)

const destroy = (id) => {
    if (confirm(t('admin.users.confirmDelete'))) {
        router.delete(route('admin.users.destroy', id))
    }
}

const changePage = (newPage) => {
    router.get(
        route('admin.users.index'),
        {
            ...props.filters,
            search: search.value,
            role: roleFilter.value,
            page: newPage,
        },
        {preserveState: true, replace: true}
    )
}

watch([search, roleFilter], () => {
    changePage(1)
})

function translateRole(role) {
    switch (role) {
        case 'admin':
            return t('admin.roles.admin')
        case 'coach':
            return t('admin.roles.coach')
        case 'user':
            return t('admin.roles.user')
        default:
            return role
    }
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
        transition: border-color 0.2s ease;

        &:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 3px #2563ebaa;
        }
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

    th,
    td {
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

    .no-data {
        text-align: center;
        padding: 20px;
        color: #94a3b8;
        font-style: italic;
    }
}

.badge {
    padding: 5px 12px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
    user-select: none;
}

.role-admin {
    background-color: #fee2e2;
    color: #b91c1c;
}

.role-coach {
    background-color: #fef3c7;
    color: #92400e;
}

.role-user {
    background-color: #dbeafe;
    color: #1e40af;
}

.btn {
    border: none;
    cursor: pointer;
    padding: 8px 14px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    transition: background-color 0.25s ease;
    text-decoration: none !important;
    color: inherit;
    display: inline-flex;
    align-items: center;
    justify-content: center;

    &:hover,
    &:focus {
        text-decoration: none !important;
        outline: none;
    }

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
