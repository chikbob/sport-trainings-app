<template>
    <AdminLayout>
        <PageHeader :title="t('admin.forms.update')" :description="coach.user?.name || t('admin.coaches.title')" />

        <AppCard>
            <div v-if="hasErrors" class="ui-error-list">
                <div v-for="(msg, key) in errors" :key="key">{{ msg }}</div>
            </div>

            <form class="ui-form" @submit.prevent="submit">
                <div class="ui-form-grid">
                    <AppInput :model-value="coach.user?.name || ''" :label="t('admin.coaches.userName')" disabled />
                    <AppInput v-model="form.phone" :label="t('admin.forms.phone')" />
                    <AppInput v-model="form.specialization" :label="t('admin.forms.specialization')" full />
                    <AppInput v-model="form.bio" :label="t('admin.forms.bio')" as="textarea" full />
                </div>

                <div class="ui-inline-actions">
                    <AppButton type="submit">{{ t('admin.forms.update') }}</AppButton>
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

const props = defineProps({
    coach: Object,
})

const form = reactive({
    bio: props.coach.bio ?? '',
    phone: props.coach.phone ?? '',
    specialization: props.coach.specialization ?? '',
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
const { t } = useI18n()

const submit = () => {
    router.put(`/admin/coaches/${props.coach.id}`, form)
}
</script>
