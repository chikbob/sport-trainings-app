<template>
    <CoachLayout>
        <PageHeader :title="t('coach.title')" :description="t('coach.upcoming')" />

        <div class="ui-kpi-grid">
            <StatCard :label="t('coach.stats.trainings')" :value="stats.trainings" />
            <StatCard :label="t('coach.stats.registrations')" :value="stats.registrations" />
            <StatCard :label="t('coach.stats.attended')" :value="stats.attended" />
            <StatCard :label="t('coach.registrations')" :value="registrations.data.length" />
        </div>

        <AppCard :title="t('coach.upcoming')" :subtitle="t('coach.trainings.link')">
            <EmptyState
                v-if="upcomingTrainings.length === 0"
                :title="t('coach.emptyTrainings')"
                :description="t('coach.trainings.empty')"
            />

            <div v-else class="ui-list">
                <div v-for="training in upcomingTrainings" :key="training.id" class="ui-list-item">
                    <div>
                        <div class="ui-list-item__title">{{ training.sport?.name || '—' }}</div>
                        <div class="ui-list-item__meta">
                            {{ $formatDate(training.date) }} · {{ $formatTime(training.time) }} · {{ training.place || t('home.notSpecified') }}
                        </div>
                    </div>

                    <div class="ui-inline-actions">
                        <StatusBadge :value="getTrainingStatus(training)" kind="training" />
                        <AppButton :href="`/trainings/${training.id}`" variant="secondary" size="sm">
                            {{ t('coach.viewTraining') }}
                        </AppButton>
                        <AppButton
                            type="button"
                            variant="danger"
                            size="sm"
                            :disabled="training.is_cancelled"
                            @click="cancelTraining(training.id)"
                        >
                            {{ t('coach.cancelTraining') }}
                        </AppButton>
                    </div>
                </div>
            </div>
        </AppCard>

        <AppCard :title="t('coach.registrations')" :subtitle="t('coach.registrationsPage.link')">
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
        </AppCard>
    </CoachLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import CoachLayout from '@/Layouts/CoachLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import PaginationLinks from '@/Components/PaginationLinks.vue'
import StatCard from '@/Components/StatCard.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    coach: Object,
    upcomingTrainings: Array,
    registrations: Object,
    stats: Object,
    filters: Object,
})

const { t } = useI18n()
const statusFilter = ref(props.filters?.status || '')

watch(statusFilter, () => {
    router.get(route('coach.dashboard'), { status: statusFilter.value }, { preserveState: true, replace: true })
})

const cancelTraining = (trainingId) => {
    if (!confirm(t('coach.confirmCancelTraining'))) return
    router.post(route('coach.trainings.cancel', trainingId))
}

const updateStatus = (registration, status) => {
    router.put(route('coach.registrations.update', registration.id), { status }, { preserveScroll: true })
}

const getTrainingStatus = (training) => {
    if (training.is_cancelled) return 'cancelled'
    if (training.is_completed) return 'completed'
    return training.date > new Date().toISOString().slice(0, 10) ? 'planned' : 'active'
}
</script>
