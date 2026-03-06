<template>
    <AppLayout>
        <div class="form-page">
            <h2>{{ t('coach.edit') }}</h2>

            <form @submit.prevent="submit" class="form">
                <label>
                    {{ t('home.date') }}
                    <input v-model="form.date" type="date" required />
                </label>

                <label>
                    {{ t('home.time') }}
                    <input v-model="form.time" type="time" required />
                </label>

                <label>
                    {{ t('home.place') }}
                    <input v-model="form.place" type="text" />
                </label>

                <label>
                    {{ t('trainings.notes') }}
                    <textarea v-model="form.notes"></textarea>
                </label>

                <button class="btn-primary">{{ t('admin.forms.save') }}</button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    training: Object,
})

const form = reactive({
    date: props.training.date,
    time: props.training.time,
    place: props.training.place ?? '',
    notes: props.training.notes ?? '',
})

const { t } = useI18n()

const submit = () => {
    router.put(`/coach/trainings/${props.training.id}`, form)
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
