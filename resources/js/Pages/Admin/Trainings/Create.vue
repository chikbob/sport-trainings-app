<template>
    <AdminLayout>
        <div class="form-page">
            <h2>Створити тренування</h2>

            <form @submit.prevent="submit" class="form">
                <label>
                    Секція
                    <select v-model="form.sport_id" required>
                        <option value="">Оберіть секцію</option>
                        <option v-for="sport in sports" :key="sport.id" :value="sport.id">
                            {{ sport.name }}
                        </option>
                    </select>
                </label>

                <label>
                    Дата
                    <input v-model="form.date" type="date" required />
                </label>

                <label>
                    Час
                    <input v-model="form.time" type="time" required />
                </label>

                <label>
                    Місце
                    <input v-model="form.place" type="text" />
                </label>

                <label>
                    Примітки
                    <textarea v-model="form.notes"></textarea>
                </label>

                <button class="btn-primary">Створити</button>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    sports: Array
})

const form = reactive({
    sport_id: '',
    date: '',
    time: '',
    place: '',
    notes: '',
})

const submit = () => {
    router.post('/admin/trainings', form)
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
