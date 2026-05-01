<template>
    <WorkspaceLayout
        :title="t('coach.title')"
        :section="t('coach.title')"
        brand="Sport Coach"
        :navigation="navigation"
        :back-label="t('admin.header.back')"
        :logout-label="t('admin.header.logout')"
    >
        <slot />
    </WorkspaceLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'
import WorkspaceLayout from '@/Layouts/WorkspaceLayout.vue'

const page = usePage()
const { t } = useI18n()

const currentRoute = computed(() => page.props.currentRoute ?? '')

const navigation = computed(() => [
    { href: '/coach', label: t('coach.title'), active: currentRoute.value === 'coach.dashboard' },
    { href: '/coach/trainings', label: t('coach.trainings.title'), active: currentRoute.value.startsWith('coach.trainings') },
    { href: '/coach/registrations', label: t('coach.registrations'), active: currentRoute.value.startsWith('coach.registrations') },
])
</script>
