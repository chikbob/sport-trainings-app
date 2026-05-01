<template>
    <WorkspaceLayout
        :title="t('admin.header.title')"
        :section="t('admin.header.title')"
        :brand="t('admin.common.logo')"
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
    { href: '/admin', label: t('admin.sidebar.dashboard'), active: currentRoute.value === 'admin.dashboard' },
    { href: '/admin/users', label: t('admin.sidebar.users'), active: currentRoute.value.startsWith('admin.users') },
    { href: '/admin/coaches', label: t('admin.sidebar.coaches'), active: currentRoute.value.startsWith('admin.coaches') },
    { href: '/admin/sports', label: t('admin.sidebar.sports'), active: currentRoute.value.startsWith('admin.sports') },
    { href: '/admin/trainings', label: t('admin.sidebar.trainings'), active: currentRoute.value.startsWith('admin.trainings') },
    { href: '/admin/registrations', label: t('admin.sidebar.registrations'), active: currentRoute.value.startsWith('admin.registrations') },
])
</script>
