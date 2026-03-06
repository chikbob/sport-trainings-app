<template>
    <AdminLayout>
        <div class="form-page">
            <h2>{{ t('admin.forms.update') }}</h2>

            <div v-if="hasErrors" class="form-errors">
                <div v-for="(msg, key) in errors" :key="key" class="form-error">{{ msg }}</div>
            </div>

            <form @submit.prevent="submit" class="form">
                <label>
                    {{ t('admin.sports.title') }}
                    <select v-model="form.sport_id" required>
                        <option value="">{{ t('admin.sports.title') }}</option>
                        <option v-for="sport in sports" :key="sport.id" :value="sport.id">
                            {{ sport.name }}
                        </option>
                    </select>
                </label>

                <label>
                    {{ t('admin.forms.date') }}
                    <input v-model="form.date" type="date" required />
                </label>

                <label>
                    {{ t('admin.forms.time') }}
                    <input v-model="form.time" type="time" required />
                </label>

                <label>
                    {{ t('admin.forms.place') }}
                    <input v-model="form.place" type="text" />
                </label>

                <label>
                    {{ t('admin.forms.notes') }}
                    <textarea v-model="form.notes"></textarea>
                </label>

                <button class="btn-primary">{{ t('admin.forms.update') }}</button>
            </form>
        </div>
    </AdminLayout>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    training: Object,
    sports: Array
})

const form = reactive({
    sport_id: props.training.sport_id,
    date: props.training.date,
    time: props.training.time,
    place: props.training.place ?? '',
    notes: props.training.notes ?? ''
})

const submit = () => {
    router.put(`/admin/trainings/${props.training.id}`, form)
}

const { t } = useI18n()
const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
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

.form-errors {
    background: #fee2e2;
    color: #b91c1c;
    border: 1px solid #fecaca;
    border-radius: 6px;
    padding: 10px 12px;
    margin-bottom: 12px;
    font-size: 0.9rem;
}

.form-error + .form-error {
    margin-top: 4px;
}
</style>
