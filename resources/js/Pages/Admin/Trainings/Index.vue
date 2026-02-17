<template>
    <AdminLayout>
        <div class="page">
            <header class="page__header">
                <h2>Тренування</h2>
                <Link href="/admin/trainings/create" class="btn btn-primary">
                    + Додати тренування
                </Link>
            </header>

            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Секція</th>
                    <th>Дата</th>
                    <th>Час</th>
                    <th>Місце</th>
                    <th>Примітки</th>
                    <th>Дії</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="training in trainings.data" :key="training.id">
                    <td>{{ training.id }}</td>
                    <td>{{ training.sport?.name || '—' }}</td>
                    <td>{{ training.date }}</td>
                    <td>{{ training.time }}</td>
                    <td>{{ training.place || '—' }}</td>
                    <td class="wrap-text">{{ training.notes || '—' }}</td>
                    <td class="actions">
                        <Link :href="`/admin/trainings/${training.id}/edit`" class="btn btn-edit">Редагувати</Link>
                        <button class="btn btn-delete" @click="destroy(training.id)">Видалити</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router } from '@inertiajs/vue3'

defineProps({ trainings: Object })

const destroy = (id) => {
    if (confirm('Ви впевнені?')) {
        router.delete(`/admin/trainings/${id}`)
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

    .wrap-text {
        max-width: 300px;
    }

    .actions {
        display: flex;
        gap: 10px;
        justify-content: flex-start;
        white-space: nowrap;
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
