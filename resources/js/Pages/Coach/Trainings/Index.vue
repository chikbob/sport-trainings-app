<template>
    <CoachLayout>
        <PageHeader :title="t('coach.trainings.title')" :description="t('coach.upcoming')" />

        <AppCard>
            <div class="ui-inline-actions">
                <AppButton
                    type="button"
                    :variant="filter === 'upcoming' ? 'primary' : 'secondary'"
                    @click="setFilter('upcoming')"
                >
                    {{ t('coach.trainings.upcoming') }}
                </AppButton>
                <AppButton
                    type="button"
                    :variant="filter === 'past' ? 'primary' : 'secondary'"
                    @click="setFilter('past')"
                >
                    {{ t('coach.trainings.past') }}
                </AppButton>
                <AppButton
                    type="button"
                    :variant="filter === 'archive' ? 'primary' : 'secondary'"
                    @click="setFilter('archive')"
                >
                    {{ t('coach.trainings.archive') }}
                </AppButton>
            </div>
        </AppCard>

        <div class="ui-table-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ t('coach.training') }}</th>
                        <th>{{ t('home.date') }}</th>
                        <th>{{ t('home.time') }}</th>
                        <th>{{ t('home.place') }}</th>
                        <th>{{ t('coach.status') }}</th>
                        <th v-if="showActions">{{ t('coach.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="training in trainings.data" :key="training.id">
                        <td>{{ training.id }}</td>
                        <td>{{ training.sport?.name || '—' }}</td>
                        <td>{{ $formatDate(training.date) }}</td>
                        <td>{{ $formatTime(training.time) }}</td>
                        <td>{{ training.place || t('home.notSpecified') }}</td>
                        <td>
                            <StatusBadge :value="getTrainingStatus(training)" kind="training" />
                        </td>
                        <td v-if="showActions">
                            <div class="ui-inline-actions">
                                <AppButton :href="`/coach/trainings/${training.id}/edit`" variant="secondary" size="sm">
                                    {{ t('coach.edit') }}
                                </AppButton>
                                <AppButton type="button" variant="danger" size="sm" @click="cancelTraining(training.id)">
                                    {{ t('coach.cancelTraining') }}
                                </AppButton>
                                <AppButton type="button" variant="success" size="sm" @click="completeTraining(training.id)">
                                    {{ t('coach.completeTraining') }}
                                </AppButton>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <EmptyState
            v-if="trainings.data.length === 0"
            :title="t('coach.trainings.empty')"
            :description="t('coach.trainings.title')"
        />

        <PaginationLinks :links="trainings.links" />
    </CoachLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import CoachLayout from '@/Layouts/CoachLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import PaginationLinks from '@/Components/PaginationLinks.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    trainings: Object,
    filters: Object,
})

const { t } = useI18n()
const filter = ref(props.filters?.filter || 'upcoming')
const showActions = computed(() => filter.value !== 'archive')

watch(filter, () => {
    router.get(route('coach.trainings.index'), { filter: filter.value }, { preserveState: true, replace: true })
})

const setFilter = (value) => {
    if (filter.value !== value) filter.value = value
}

const cancelTraining = (id) => {
    if (!confirm(t('coach.confirmCancelTraining'))) return
    router.post(route('coach.trainings.cancel', id))
}

const completeTraining = (id) => {
    if (!confirm(t('coach.confirmCompleteTraining'))) return
    router.post(route('coach.trainings.complete', id))
}

const getTrainingStatus = (training) => {
    if (training.is_cancelled) return 'cancelled'
    if (training.is_completed) return 'completed'
    return training.date > new Date().toISOString().slice(0, 10) ? 'planned' : 'active'
}
</script>
