<template>
    <AdminLayout>
        <div class="form-page">
            <h2>Створити тренера</h2>

            <form @submit.prevent="submit" class="form">
                <label>
                    Біо:
                    <textarea v-model="form.bio" placeholder="Біографія, інформація про тренера"></textarea>
                </label>

                <label>
                    Телефон:
                    <input v-model="form.phone" type="tel" placeholder="+380..." />
                </label>

                <label>
                    Спеціалізація:
                    <input v-model="form.specialization" type="text" placeholder="Наприклад: футбол, теніс" />
                </label>

                <button class="btn-primary">Зберегти</button>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const form = reactive({
    bio: '',
    phone: '',
    specialization: '',
})

const submit = () => {
    router.post('/admin/coaches', form)
}
</script>

<style scoped>
.form-page {
    max-width: 500px;
    margin: 2rem auto;
}

.form label {
    display: block;
    margin-bottom: 1rem;
    font-weight: 600;
}

.form input,
.form textarea {
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
