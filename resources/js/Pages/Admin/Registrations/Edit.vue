<template>
    <AdminLayout>
        <div class="form-page">
            <h2>{{ t('admin.registrations.edit') }}</h2>

            <div class="form-info">
                <div><b>{{ t('admin.registrations.user') }}:</b> {{ registration.user?.name || t('admin.common.notSpecified') }}</div>
                <div><b>{{ t('admin.registrations.sport') }}:</b> {{ registration.training?.sport?.name || t('admin.common.notSpecified') }}</div>
                <div>
                    <b>{{ t('admin.registrations.training') }}:</b>
                    {{ $formatDate(registration.training?.date) }} • {{ registration.training?.time || t('admin.common.notSpecified') }}
                </div>
            </div>

            <div v-if="hasErrors" class="form-errors">
                <div v-for="(msg, key) in errors" :key="key" class="form-error">{{ msg }}</div>
            </div>

            <form @submit.prevent="submit" class="form">
                <label>
                    {{ t('admin.registrations.status') }}
                    <select v-model="form.status" required>
                        <option value="pending">{{ t('admin.status.pending') }}</option>
                        <option value="approved">{{ t('admin.status.approved') }}</option>
                        <option value="cancelled">{{ t('admin.status.cancelled') }}</option>
                        <option value="rejected">{{ t('admin.status.rejected') }}</option>
                        <option value="attended">{{ t('admin.status.attended') }}</option>
                        <option value="no_show">{{ t('admin.status.no_show') }}</option>
                    </select>
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
    registration: Object,
})

const form = reactive({
    status: props.registration.status,
})

const submit = () => {
    router.put(`/admin/registrations/${props.registration.id}`, form)
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

.form-info {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 12px 14px;
    margin-bottom: 16px;
    font-size: 0.95rem;
}

.form label {
    display: block;
    margin-bottom: 1rem;
    font-weight: 600;
}

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
