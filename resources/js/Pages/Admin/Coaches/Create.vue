<template>
    <AdminLayout>
        <PageHeader :title="t('admin.coaches.create')" :description="t('admin.coaches.title')" />

        <AppCard>
            <div v-if="hasErrors" class="ui-error-list">
                <div v-for="(msg, key) in errors" :key="key">{{ msg }}</div>
            </div>

            <form class="ui-form" @submit.prevent="submit">
                <div class="ui-form-grid">
                    <AppInput v-model="form.user_id" :label="t('admin.coaches.userName')" as="select" required>
                        <option value="">{{ t('admin.coaches.userName') }}</option>
                        <option v-for="user in coachUsers" :key="user.id" :value="user.id">
                            {{ user.name }} ({{ user.email }})
                        </option>
                    </AppInput>
                    <AppInput v-model="form.phone" :label="t('admin.forms.phone')" />
                    <AppInput v-model="form.specialization" :label="t('admin.forms.specialization')" full />
                    <AppInput v-model="form.bio" :label="t('admin.forms.bio')" as="textarea" full />
                </div>

                <div class="ui-inline-actions">
                    <AppButton type="submit">{{ t('admin.forms.save') }}</AppButton>
                    <AppButton href="/admin/coaches" variant="secondary">{{ t('admin.coaches.title') }}</AppButton>
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

defineProps({
    coachUsers: Array,
})

const form = reactive({
    user_id: '',
    bio: '',
    phone: '',
    specialization: '',
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
const { t } = useI18n()

const submit = () => {
    router.post('/admin/coaches', form)
}
</script>
