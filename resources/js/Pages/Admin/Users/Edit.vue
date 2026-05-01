<template>
    <AdminLayout>
        <PageHeader :title="t('admin.users.edit')" :description="user.email" />

        <AppCard>
            <div v-if="hasErrors" class="ui-error-list">
                <div v-for="(msg, key) in errors" :key="key">{{ msg }}</div>
            </div>

            <form class="ui-form" @submit.prevent="submit">
                <div class="ui-form-grid">
                    <AppInput v-model="form.name" :label="t('admin.forms.name')" required />
                    <AppInput v-model="form.email" :label="t('admin.forms.email')" type="email" required />
                    <AppInput v-model="form.phone" :label="t('admin.forms.phone')" />
                    <AppInput v-model="form.role" :label="t('admin.forms.role')" as="select">
                        <option value="user">{{ t('admin.roles.user') }}</option>
                        <option value="coach">{{ t('admin.roles.coach') }}</option>
                        <option value="admin">{{ t('admin.roles.admin') }}</option>
                    </AppInput>
                </div>

                <div class="ui-inline-actions">
                    <AppButton type="submit">{{ t('admin.forms.update') }}</AppButton>
                    <AppButton href="/admin/users" variant="secondary">{{ t('admin.users.title') }}</AppButton>
                </div>
            </form>
        </AppCard>
    </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import PageHeader from '@/Components/PageHeader.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    user: Object,
})

const form = reactive({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone ?? '',
    role: props.user.role,
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
const { t } = useI18n()

const submit = () => {
    router.put(`/admin/users/${props.user.id}`, form)
}
</script>
