<template>
    <AdminLayout>
        <PageHeader :title="t('admin.trainings.create')" :description="t('admin.trainings.title')" />

        <AppCard>
            <div v-if="hasErrors" class="ui-error-list">
                <div v-for="(msg, key) in errors" :key="key">{{ msg }}</div>
            </div>

            <form class="ui-form" @submit.prevent="submit">
                <div class="ui-form-grid">
                    <AppInput v-model="form.sport_id" :label="t('admin.sports.title')" as="select" required>
                        <option value="">{{ t('admin.sports.title') }}</option>
                        <option v-for="sport in sports" :key="sport.id" :value="sport.id">
                            {{ sport.name }}
                        </option>
                    </AppInput>
                    <AppInput v-model="form.date" :label="t('admin.forms.date')" type="date" required />
                    <AppInput v-model="form.time" :label="t('admin.forms.time')" type="time" required />
                    <AppInput v-model="form.place" :label="t('admin.forms.place')" />
                    <AppInput v-model="form.notes" :label="t('admin.forms.notes')" as="textarea" full />
                </div>

                <div class="ui-inline-actions">
                    <AppButton type="submit">{{ t('admin.forms.create') }}</AppButton>
                    <AppButton href="/admin/trainings" variant="secondary">{{ t('admin.trainings.title') }}</AppButton>
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
    sports: Array,
})

const form = reactive({
    sport_id: '',
    date: '',
    time: '',
    place: '',
    notes: '',
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
const { t } = useI18n()

const submit = () => {
    router.post('/admin/trainings', form)
}
</script>
