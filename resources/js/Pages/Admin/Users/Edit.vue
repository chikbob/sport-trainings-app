<template>
    <AdminLayout>
        <div class="form-page">
            <h2>Редагувати користувача</h2>

            <form @submit.prevent="submit" class="form">
                <label>
                    Ім'я
                    <input v-model="form.name" type="text" required />
                </label>

                <label>
                    Email
                    <input v-model="form.email" type="email" required />
                </label>

                <label>
                    Телефон
                    <input v-model="form.phone" type="tel" />
                </label>

                <label>
                    Роль
                    <select v-model="form.role" required>
                        <option value="user">User</option>
                        <option value="coach">Coach</option>
                        <option value="admin">Admin</option>
                    </select>
                </label>

                <button class="btn-primary">Оновити</button>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    user: Object
})

const form = reactive({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone ?? '',
    role: props.user.role,
})

const submit = () => {
    router.put(`/admin/users/${props.user.id}`, form)
}
</script>

<style scoped>
.form-page {
    max-width: 600px;
    margin: 2rem auto;
}

.form label {
    display: block;
    margin-bottom: 1rem;
    font-weight: 600;
}

.form input,
.form select {
    width: 100%;
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid #ccc;
    margin-top: 0.25rem;
    font-size: 1rem;
}

.btn-primary {
    background-color: #2563eb;
    color: white;
    padding: 10px 20px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 700;
    transition: background-color 0.2s;
}

.btn-primary:hover {
    background-color: #1d4ed8;
}
</style>
