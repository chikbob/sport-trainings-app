<template>
    <AdminLayout>
        <div class="form-page">
            <h2>{{ t('admin.users.edit') }}</h2>

            <div v-if="hasErrors" class="form-errors">
                <div v-for="(msg, key) in errors" :key="key" class="form-error">{{ msg }}</div>
            </div>

            <form @submit.prevent="submit" class="form">
                <label>
                    {{ t('admin.forms.name') }}
                    <input v-model="form.name" type="text" required />
                </label>

                <label>
                    {{ t('admin.forms.email') }}
                    <input v-model="form.email" type="email" required />
                </label>

                <label>
                    {{ t('admin.forms.phone') }}
                    <input v-model="form.phone" type="tel" />
                </label>

                <label>
                    {{ t('admin.forms.role') }}
                    <select v-model="form.role" required>
                        <option value="user">{{ t('admin.roles.user') }}</option>
                        <option value="coach">{{ t('admin.roles.coach') }}</option>
                        <option value="admin">{{ t('admin.roles.admin') }}</option>
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
