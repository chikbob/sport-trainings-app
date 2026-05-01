<template>
    <CoachLayout>
        <PageHeader :title="t('coach.edit')" :description="training.sport?.name || t('coach.training')" />

        <AppCard>
            <form class="ui-form" @submit.prevent="submit">
                <div class="ui-form-grid">
                    <AppInput v-model="form.date" :label="t('home.date')" type="date" required />
                    <AppInput v-model="form.time" :label="t('home.time')" type="time" required />
                    <AppInput v-model="form.place" :label="t('home.place')" />
                    <AppInput v-model="form.notes" :label="t('trainings.notes')" as="textarea" full />
                </div>

                <div class="ui-inline-actions">
                    <AppButton type="submit">{{ t('admin.forms.save') }}</AppButton>
                    <AppButton href="/coach/trainings" variant="secondary">{{ t('coach.trainings.title') }}</AppButton>
                </div>
            </form>
        </AppCard>
    </CoachLayout>
</template>

<script setup>
import { reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import CoachLayout from '@/Layouts/CoachLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import PageHeader from '@/Components/PageHeader.vue'
import { useI18n } from '@/i18n/useI18n'

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
