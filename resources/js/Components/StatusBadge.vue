<template>
    <span :class="['ui-status-badge', `ui-status-badge--${tone}`]">
        {{ resolvedLabel }}
    </span>
</template>

<script setup>
import { computed } from 'vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    value: {
        type: String,
        default: '',
    },
    kind: {
        type: String,
        default: 'registration',
    },
    label: {
        type: String,
        default: '',
    },
})

const { t } = useI18n()

const maps = computed(() => ({
    registration: {
        pending: { tone: 'warning', label: t('admin.status.pending') },
        approved: { tone: 'info', label: t('admin.status.approved') },
        cancelled: { tone: 'danger', label: t('admin.status.cancelled') },
        rejected: { tone: 'danger', label: t('admin.status.rejected') },
        attended: { tone: 'success', label: t('admin.status.attended') },
        no_show: { tone: 'neutral', label: t('admin.status.no_show') },
    },
    training: {
        planned: { tone: 'info', label: t('common.trainingStatus.planned') },
        active: { tone: 'warning', label: t('common.trainingStatus.active') },
        completed: { tone: 'success', label: t('common.trainingStatus.completed') },
        cancelled: { tone: 'danger', label: t('common.trainingStatus.cancelled') },
    },
    role: {
        admin: { tone: 'danger', label: t('admin.roles.admin') },
        coach: { tone: 'warning', label: t('admin.roles.coach') },
        user: { tone: 'info', label: t('admin.roles.user') },
    },
}))

const resolved = computed(() => maps.value[props.kind]?.[props.value] || { tone: 'neutral', label: props.value || '—' })
const tone = computed(() => resolved.value.tone)
const resolvedLabel = computed(() => props.label || resolved.value.label)
</script>
