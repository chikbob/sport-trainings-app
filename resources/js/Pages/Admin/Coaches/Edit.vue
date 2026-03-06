<template>
    <AdminLayout>
        <div class="form-page">
            <h2>{{ t('admin.forms.update') }}</h2>

            <div v-if="hasErrors" class="form-errors">
                <div v-for="(msg, key) in errors" :key="key" class="form-error">{{ msg }}</div>
            </div>

            <form @submit.prevent="submit" class="form">
                <label>
                    {{ t('admin.forms.bio') }}:
                    <textarea v-model="form.bio" :placeholder="t('admin.forms.bio')"></textarea>
                </label>

                <label>
                    {{ t('admin.forms.phone') }}:
                    <input v-model="form.phone" type="tel" placeholder="+380..." />
                </label>

                <label>
                    {{ t('admin.forms.specialization') }}:
                    <input v-model="form.specialization" type="text" :placeholder="t('admin.forms.specialization')" />
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
    coach: Object
})

const form = reactive({
    bio: props.coach.bio ?? '',
    phone: props.coach.phone ?? '',
    specialization: props.coach.specialization ?? '',
})

const submit = () => {
    router.put(`/admin/coaches/${props.coach.id}`, form)
}

const { t } = useI18n()
const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
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
