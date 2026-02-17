<template>
    <AdminLayout>
        <div class="form-page">
            <h2>Редагувати секцію</h2>

            <form @submit.prevent="submit" class="form">
                <label>
                    Назва
                    <input v-model="form.name" type="text" required />
                </label>

                <label>
                    Локація
                    <input v-model="form.location" type="text" />
                </label>

                <label>
                    Опис
                    <textarea v-model="form.description"></textarea>
                </label>

                <label>
                    Тренер
                    <select v-model="form.coach_id" class="input">
                        <option value="">Оберіть тренера</option>
                        <option v-for="coach in coaches" :key="coach.id" :value="coach.id">
                            {{ coach.user.name }}
                        </option>
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
    sport: Object,
    coaches: Array
})

const form = reactive({
    name: props.sport.name,
    location: props.sport.location,
    description: props.sport.description,
    coach_id: props.sport.coach_id ?? ''
})

const submit = () => {
    router.put(`/admin/sports/${props.sport.id}`, form)
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
.form textarea,
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
