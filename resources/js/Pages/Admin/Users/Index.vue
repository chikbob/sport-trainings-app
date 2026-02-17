<template>
    <AdminLayout>
        <div class="page">
            <header class="page__header">
                <h2>Користувачі</h2>
                <Link :href="route('admin.users.create')" class="btn btn-primary">
                    + Створити користувача
                </Link>
            </header>

            <div class="filters">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Пошук..."
                    class="input"
                />
                <select v-model="roleFilter" class="input">
                    <option value="">Всі ролі</option>
                    <option value="user">User</option>
                    <option value="coach">Coach</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Ім’я</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Дії</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user in filteredUsers" :key="user.id">
                    <td>{{ user.id }}</td>
                    <td>{{ user.name }}</td>
                    <td>{{ user.email }}</td>
                    <td>
                        <span :class="['badge', 'role-' + user.role]">{{ user.role }}</span>
                    </td>
                    <td class="actions">
                        <Link
                            :href="route('admin.users.edit', user.id)"
                            class="btn btn-edit"
                        >
                            Редагувати
                        </Link>
                        <button class="btn btn-delete" @click="destroy(user.id)">
                            Видалити
                        </button>
                    </td>
                </tr>
                <tr v-if="filteredUsers.length === 0">
                    <td colspan="5" class="no-data">Користувачі не знайдені</td>
                </tr>
                </tbody>
            </table>

            <!-- Пагінація -->
            <nav class="pagination" v-if="props.users && props.users.links.length > 1">
                <button
                    class="page-btn"
                    :disabled="props.users.current_page === 1"
                    @click="changePage(props.users.current_page - 1)"
                >
                    ← Попередня
                </button>

                <button
                    v-for="link in props.users.links"
                    :key="link.label"
                    class="page-btn"
                    :class="{ active: link.active }"
                    v-html="link.label"
                    :disabled="!link.url"
                    @click="link.url && changePage(extractPage(link.url))"
                ></button>

                <button
                    class="page-btn"
                    :disabled="props.users.current_page === props.users.last_page"
                    @click="changePage(props.users.current_page + 1)"
                >
                    Наступна →
                </button>
            </nav>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import {computed, ref, watch} from 'vue'
import {Link, router} from '@inertiajs/vue3'
import {route} from 'ziggy-js'

const props = defineProps({
    users: Object,
    filters: Object,
})

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
    if (confirm('Видалити користувача?')) {
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

function extractPage(url) {
    const params = new URLSearchParams(url.split('?')[1])
    return parseInt(params.get('page'))
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
