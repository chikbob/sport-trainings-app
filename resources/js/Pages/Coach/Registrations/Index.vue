<template>
    <CoachLayout>
        <PageHeader :title="t('coach.registrations')" :description="t('coach.allStatuses')" />

        <AppCard>
            <div class="filters ui-table-toolbar">
                <AppInput v-model="statusFilter" :label="t('coach.allStatuses')" as="select">
                    <option value="">{{ t('coach.allStatuses') }}</option>
                    <option value="pending">{{ t('admin.status.pending') }}</option>
                    <option value="approved">{{ t('admin.status.approved') }}</option>
                    <option value="cancelled">{{ t('admin.status.cancelled') }}</option>
                    <option value="rejected">{{ t('admin.status.rejected') }}</option>
                    <option value="attended">{{ t('admin.status.attended') }}</option>
                    <option value="no_show">{{ t('admin.status.no_show') }}</option>
                </AppInput>
            </div>
        </AppCard>

        <div class="ui-table-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ t('coach.user') }}</th>
                        <th>{{ t('coach.training') }}</th>
                        <th>{{ t('coach.status') }}</th>
                        <th>{{ t('coach.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="reg in registrations.data" :key="reg.id">
                        <td>{{ reg.id }}</td>
                        <td>{{ reg.user?.name || '—' }}</td>
                        <td>
                            {{ reg.training?.sport?.name || '—' }} ·
                            {{ $formatDate(reg.training?.date) }} {{ $formatTime(reg.training?.time) }}
                        </td>
                        <td>
                            <StatusBadge :value="reg.status" />
                        </td>
                        <td>
                            <AppInput
                                v-model="reg.status"
                                as="select"
                                @update:model-value="updateStatus(reg, $event)"
                            >
                                <option value="pending">{{ t('admin.status.pending') }}</option>
                                <option value="approved">{{ t('admin.status.approved') }}</option>
                                <option value="cancelled">{{ t('admin.status.cancelled') }}</option>
                                <option value="rejected">{{ t('admin.status.rejected') }}</option>
                                <option value="attended">{{ t('admin.status.attended') }}</option>
                                <option value="no_show">{{ t('admin.status.no_show') }}</option>
                            </AppInput>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <EmptyState
            v-if="registrations.data.length === 0"
            :title="t('coach.noRegistrations')"
            :description="t('coach.allStatuses')"
        />

        <div class="ui-table-pagination">
            <PaginationLinks :links="registrations.links" />
        </div>
    </CoachLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import CoachLayout from '@/Layouts/CoachLayout.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import PaginationLinks from '@/Components/PaginationLinks.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    registrations: Object,
    filters: Object,
})

const { t } = useI18n()
const statusFilter = ref(props.filters?.status || '')

watch(statusFilter, () => {
    router.get(route('coach.registrations.index'), { status: statusFilter.value }, { preserveState: true, replace: true })
})

const updateStatus = (registration, status) => {
    router.put(route('coach.registrations.update', registration.id), { status }, { preserveScroll: true })
}
</script>
