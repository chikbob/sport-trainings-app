<template>
    <AdminLayout>
        <PageHeader :title="t('admin.sports.title')" :description="t('admin.users.search')">
            <template #actions>
                <AppButton href="/admin/sports/create">{{ t('admin.sports.create') }}</AppButton>
            </template>
        </PageHeader>

        <AppCard>
            <div class="filters">
                <AppInput v-model="search" :label="t('admin.users.search')" :placeholder="t('admin.users.search')" />
            </div>
        </AppCard>

        <div class="ui-table-toolbar">
            <div class="ui-table-toolbar__meta">
                {{ t('admin.common.reportSummary') }}: {{ props.sports.total ?? sortedSports.length }}
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
                        <th><button class="ui-table__sort" type="button" @click="setSort('name')">{{ t('admin.sports.name') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('name') }">{{ sortIndicator('name') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('location')">{{ t('admin.forms.location') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('location') }">{{ sortIndicator('location') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('coachName')">{{ t('admin.sports.trainer') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('coachName') }">{{ sortIndicator('coachName') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('description')">{{ t('admin.forms.description') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('description') }">{{ sortIndicator('description') }}</span></button></th>
                        <th>{{ t('admin.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="sport in sortedSports" :key="sport.id">
                        <td>{{ sport.id }}</td>
                        <td>{{ sport.name }}</td>
                        <td>{{ sport.location || t('admin.common.notSpecified') }}</td>
                        <td>{{ sport.coach?.user?.name || t('admin.common.notSpecified') }}</td>
                        <td>{{ sport.description || t('admin.common.notSpecified') }}</td>
                        <td>
                            <div class="ui-inline-actions">
                                <AppButton :href="`/admin/sports/${sport.id}/edit`" variant="secondary" size="sm">
                                    {{ t('admin.common.edit') }}
                                </AppButton>
                                <AppButton type="button" variant="danger" size="sm" @click="destroy(sport.id)">
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
            v-if="sortedSports.length === 0"
            :title="t('admin.sports.title')"
            :description="t('admin.users.search')"
        />

        <AdminPagination :links="sports.links" />
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
import { useSortableTable } from '@/composables/useSortableTable'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({ sports: Object, filters: Object })

const { t } = useI18n()
const search = ref(props.filters?.search || '')
const sportsArray = computed(() => Array.isArray(props.sports?.data) ? props.sports.data : [])

const {
    sortDirection,
    sortedRows: sortedSports,
    setSort,
    isSortedBy,
} = useSortableTable(sportsArray, {
    initialKey: 'id',
    initialDirection: 'desc',
    accessors: {
        coachName: (sport) => sport.coach?.user?.name || '',
    },
})

watch(search, () => {
    router.get(route('admin.sports.index'), { search: search.value, page: 1 }, { preserveState: true, replace: true })
})

const destroy = (id) => {
    if (!confirm(t('admin.common.confirmDelete'))) return
    router.delete(`/admin/sports/${id}`)
}

const sortIndicator = (key) => {
    if (!isSortedBy(key)) return ''
    return sortDirection.value === 'asc' ? t('admin.common.sortAsc') : t('admin.common.sortDesc')
}

const downloadReport = () => {
    const sort = ['id', 'name', 'location', 'coachName', 'description']
        .find((key) => isSortedBy(key)) || 'id'

    window.open(route('admin.reports.sports', {
        search: search.value,
        sort,
        direction: sortDirection.value,
    }), '_blank', 'noopener')
}
</script>
