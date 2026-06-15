<template>
    <AdminLayout>
        <PageHeader :title="t('admin.coaches.title')" :description="t('admin.users.search')">
            <template #actions>
                <AppButton href="/admin/coaches/create">{{ t('admin.coaches.create') }}</AppButton>
            </template>
        </PageHeader>

        <AppCard>
            <div class="filters">
                <AppInput v-model="search" :label="t('admin.users.search')" :placeholder="t('admin.users.search')" />
            </div>
        </AppCard>

        <div class="ui-table-toolbar">
            <div class="ui-table-toolbar__meta">
                {{ t('admin.common.reportSummary') }}: {{ props.coaches.total ?? sortedCoaches.length }}
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
                        <th><button class="ui-table__sort" type="button" @click="setSort('userName')">{{ t('admin.coaches.userName') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('userName') }">{{ sortIndicator('userName') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('phone')">{{ t('admin.forms.phone') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('phone') }">{{ sortIndicator('phone') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('specialization')">{{ t('admin.forms.specialization') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('specialization') }">{{ sortIndicator('specialization') }}</span></button></th>
                        <th>{{ t('admin.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="coach in sortedCoaches" :key="coach.id">
                        <td>{{ coach.id }}</td>
                        <td>{{ coach.user?.name || t('admin.common.notSpecified') }}</td>
                        <td>{{ coach.phone || t('admin.common.notSpecified') }}</td>
                        <td>{{ coach.specialization || t('admin.common.notSpecified') }}</td>
                        <td>
                            <div class="ui-inline-actions">
                                <AppButton :href="`/admin/coaches/${coach.id}/edit`" variant="secondary" size="sm">
                                    {{ t('admin.common.edit') }}
                                </AppButton>
                                <AppButton type="button" variant="danger" size="sm" @click="destroy(coach.id)">
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
            v-if="sortedCoaches.length === 0"
            :title="t('admin.coaches.title')"
            :description="t('admin.users.search')"
        />

        <AdminPagination :links="coaches.links" />
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

const props = defineProps({
    coaches: Object,
    filters: Object,
})

const { t } = useI18n()
const search = ref(props.filters?.search || '')
const coachesArray = computed(() => Array.isArray(props.coaches?.data) ? props.coaches.data : [])

const {
    sortDirection,
    sortedRows: sortedCoaches,
    setSort,
    isSortedBy,
} = useSortableTable(coachesArray, {
    initialKey: 'id',
    initialDirection: 'desc',
    accessors: {
        userName: (coach) => coach.user?.name || '',
    },
})

watch(search, () => {
    router.get(route('admin.coaches.index'), { search: search.value, page: 1 }, { preserveState: true, replace: true })
})

const destroy = (id) => {
    if (!confirm(t('admin.common.confirmDelete'))) return
    router.delete(`/admin/coaches/${id}`)
}

const sortIndicator = (key) => {
    if (!isSortedBy(key)) return ''
    return sortDirection.value === 'asc' ? t('admin.common.sortAsc') : t('admin.common.sortDesc')
}

const downloadReport = () => {
    const sort = ['id', 'userName', 'phone', 'specialization']
        .find((key) => isSortedBy(key)) || 'id'

    window.open(route('admin.reports.coaches', {
        search: search.value,
        sort,
        direction: sortDirection.value,
    }), '_blank', 'noopener')
}
</script>
