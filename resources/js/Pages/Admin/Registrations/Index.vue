<template>
    <AdminLayout>
        <PageHeader :title="t('admin.registrations.title')" :description="t('admin.registrations.search')" />

        <AppCard>
            <div class="filters">
                <AppInput v-model="search" :label="t('admin.registrations.search')" :placeholder="t('admin.registrations.search')" />
                <AppInput v-model="statusFilter" :label="t('admin.registrations.status')" as="select">
                    <option value="">{{ t('admin.registrations.allStatuses') }}</option>
                    <option value="pending">{{ t('admin.status.pending') }}</option>
                    <option value="approved">{{ t('admin.status.approved') }}</option>
                    <option value="cancelled">{{ t('admin.status.cancelled') }}</option>
                    <option value="rejected">{{ t('admin.status.rejected') }}</option>
                    <option value="attended">{{ t('admin.status.attended') }}</option>
                    <option value="no_show">{{ t('admin.status.no_show') }}</option>
                </AppInput>
            </div>
        </AppCard>

        <div class="ui-table-toolbar">
            <div class="ui-table-toolbar__meta">
                {{ t('admin.common.reportSummary') }}: {{ props.registrations.total ?? sortedRegistrations.length }}
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
                        <th><button class="ui-table__sort" type="button" @click="setSort('userName')">{{ t('admin.registrations.user') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('userName') }">{{ sortIndicator('userName') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('sportName')">{{ t('admin.registrations.sport') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('sportName') }">{{ sortIndicator('sportName') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('trainingDate')">{{ t('admin.registrations.training') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('trainingDate') }">{{ sortIndicator('trainingDate') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('status')">{{ t('admin.registrations.status') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('status') }">{{ sortIndicator('status') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('created_at')">{{ t('admin.registrations.createdAt') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('created_at') }">{{ sortIndicator('created_at') }}</span></button></th>
                        <th>{{ t('admin.registrations.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="reg in sortedRegistrations" :key="reg.id">
                        <td>{{ reg.id }}</td>
                        <td>{{ reg.user?.name || t('admin.common.notSpecified') }}</td>
                        <td>{{ reg.training?.sport?.name || t('admin.common.notSpecified') }}</td>
                        <td>{{ $formatDate(reg.training?.date) }} · {{ $formatTime(reg.training?.time) }}</td>
                        <td><StatusBadge :value="reg.status" /></td>
                        <td>{{ $formatDate(reg.created_at) }}</td>
                        <td>
                            <AppButton :href="`/admin/registrations/${reg.id}/edit`" variant="secondary" size="sm">
                                {{ t('admin.registrations.edit') }}
                            </AppButton>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <EmptyState
            v-if="sortedRegistrations.length === 0"
            :title="t('admin.registrations.empty')"
            :description="t('admin.registrations.title')"
        />

        <AdminPagination :links="registrations.links" />
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

const props = defineProps({
    registrations: Object,
    filters: Object,
})

const { t } = useI18n()
const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')
const registrationsArray = computed(() => Array.isArray(props.registrations?.data) ? props.registrations.data : [])

const {
    sortDirection,
    sortedRows: sortedRegistrations,
    setSort,
    isSortedBy,
} = useSortableTable(registrationsArray, {
    initialKey: 'created_at',
    initialDirection: 'desc',
    accessors: {
        userName: (registration) => registration.user?.name || '',
        sportName: (registration) => registration.training?.sport?.name || '',
        trainingDate: (registration) => registration.training?.date || '',
    },
})

watch([search, statusFilter], () => {
    router.get(route('admin.registrations.index'), {
        search: search.value,
        status: statusFilter.value,
        page: 1,
    }, {
        preserveState: true,
        replace: true,
    })
})

const sortIndicator = (key) => {
    if (!isSortedBy(key)) return ''
    return sortDirection.value === 'asc' ? t('admin.common.sortAsc') : t('admin.common.sortDesc')
}

const downloadReport = () => {
    const sort = ['id', 'userName', 'sportName', 'trainingDate', 'status', 'created_at']
        .find((key) => isSortedBy(key)) || 'created_at'

    window.open(route('admin.reports.registrations', {
        search: search.value,
        status: statusFilter.value,
        sort,
        direction: sortDirection.value,
    }), '_blank', 'noopener')
}
</script>
