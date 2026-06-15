<template>
    <AdminLayout>
        <PageHeader :title="t('admin.trainings.title')" :description="t('admin.users.search')">
            <template #actions>
                <AppButton href="/admin/trainings/create">{{ t('admin.trainings.create') }}</AppButton>
            </template>
        </PageHeader>

        <AppCard>
            <div class="filters">
                <AppInput v-model="search" :label="t('admin.users.search')" :placeholder="t('admin.users.search')" />
            </div>
        </AppCard>

        <div class="ui-table-toolbar">
            <div class="ui-table-toolbar__meta">
                {{ t('admin.common.reportSummary') }}: {{ props.trainings.total ?? sortedTrainings.length }}
            </div>
            <AppButton type="button" variant="secondary" @click="downloadReport">
                {{ t('admin.common.report') }}
            </AppButton>
        </div>

        <div class="ui-table-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th><button class="ui-table__sort" type="button" @click="setSort('id')">{{ t('admin.common.id') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('id') }">{{ sortIndicator('id') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('sportName')">{{ t('admin.sports.title') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('sportName') }">{{ sortIndicator('sportName') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('date')">{{ t('admin.forms.date') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('date') }">{{ sortIndicator('date') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('time')">{{ t('admin.forms.time') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('time') }">{{ sortIndicator('time') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('place')">{{ t('admin.forms.place') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('place') }">{{ sortIndicator('place') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('notes')">{{ t('admin.trainings.notes') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('notes') }">{{ sortIndicator('notes') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('status')">{{ t('coach.status') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('status') }">{{ sortIndicator('status') }}</span></button></th>
                        <th>{{ t('admin.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="training in sortedTrainings" :key="training.id">
                        <td>{{ training.id }}</td>
                        <td>{{ training.sport?.name || t('admin.common.notSpecified') }}</td>
                        <td>{{ $formatDate(training.date) }}</td>
                        <td>{{ $formatTime(training.time) }}</td>
                        <td>{{ training.place || t('admin.common.notSpecified') }}</td>
                        <td>{{ training.notes || t('admin.common.notSpecified') }}</td>
                        <td><StatusBadge :value="trainingStatus(training)" kind="training" /></td>
                        <td>
                            <div class="ui-inline-actions">
                                <AppButton :href="`/admin/trainings/${training.id}/edit`" variant="secondary" size="sm">
                                    {{ t('admin.common.edit') }}
                                </AppButton>
                                <AppButton type="button" variant="danger" size="sm" @click="destroy(training.id)">
                                    {{ t('admin.common.delete') }}
                                </AppButton>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <EmptyState
            v-if="sortedTrainings.length === 0"
            :title="t('admin.trainings.title')"
            :description="t('admin.users.search')"
        />

        <AdminPagination :links="trainings.links" />
    </AdminLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPagination from '@/Components/AdminPagination.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { useSortableTable } from '@/composables/useSortableTable'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({ trainings: Object, filters: Object })

const { t } = useI18n()
const search = ref(props.filters?.search || '')
const trainingsArray = computed(() => Array.isArray(props.trainings?.data) ? props.trainings.data : [])

watch(search, () => {
    router.get(route('admin.trainings.index'), { search: search.value, page: 1 }, { preserveState: true, replace: true })
})

const destroy = (id) => {
    if (!confirm(t('admin.common.confirmDelete'))) return
    router.delete(`/admin/trainings/${id}`)
}

const trainingStatus = (training) => {
    if (training.is_cancelled) return 'cancelled'
    if (training.is_completed) return 'completed'
    return training.date > new Date().toISOString().slice(0, 10) ? 'planned' : 'active'
}

const {
    sortDirection,
    sortedRows: sortedTrainings,
    setSort,
    isSortedBy,
} = useSortableTable(trainingsArray, {
    initialKey: 'id',
    initialDirection: 'desc',
    accessors: {
        sportName: (training) => training.sport?.name || '',
        status: (training) => trainingStatus(training),
    },
})

const sortIndicator = (key) => {
    if (!isSortedBy(key)) return ''
    return sortDirection.value === 'asc' ? t('admin.common.sortAsc') : t('admin.common.sortDesc')
}

const downloadReport = () => {
    const sort = ['id', 'sportName', 'date', 'time', 'place', 'notes', 'status']
        .find((key) => isSortedBy(key)) || 'id'

    window.open(route('admin.reports.trainings', {
        search: search.value,
        sort,
        direction: sortDirection.value,
    }), '_blank', 'noopener')
}
</script>
