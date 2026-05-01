<template>
    <AdminLayout>
        <PageHeader :title="t('admin.sports.create')" :description="t('admin.sports.title')" />

        <AppCard>
            <div v-if="hasErrors" class="ui-error-list">
                <div v-for="(msg, key) in errors" :key="key">{{ msg }}</div>
            </div>

            <form class="ui-form" @submit.prevent="submit">
                <div class="ui-form-grid">
                    <AppInput v-model="form.name" :label="t('admin.forms.name')" required />
                    <AppInput v-model="form.location" :label="t('admin.forms.location')" />
                    <AppInput v-model="form.coach_id" :label="t('admin.forms.coach')" as="select">
                        <option value="">{{ t('admin.forms.coach') }}</option>
                        <option v-for="coach in coaches" :key="coach.id" :value="coach.id">
                            {{ coach.user.name }}
                        </option>
                    </AppInput>
                    <AppInput v-model="form.description" :label="t('admin.forms.description')" as="textarea" full />
                </div>

                <div class="ui-inline-actions">
                    <AppButton type="submit">{{ t('admin.forms.save') }}</AppButton>
                    <AppButton href="/admin/sports" variant="secondary">{{ t('admin.sports.title') }}</AppButton>
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
    coaches: Array,
})

const form = reactive({
    name: '',
    location: '',
    description: '',
    coach_id: '',
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
const { t } = useI18n()

const submit = () => {
    router.post('/admin/sports', form)
}
</script>
