<template>
    <AdminLayout>
        <PageHeader :title="t('admin.users.title')" :description="t('admin.users.search')">
            <template #actions>
                <AppButton :href="route('admin.users.create')">{{ t('admin.users.create') }}</AppButton>
            </template>
        </PageHeader>

        <AppCard>
            <div class="filters">
                <AppInput v-model="search" :label="t('admin.users.search')" :placeholder="t('admin.users.search')" />
                <AppInput v-model="roleFilter" :label="t('admin.users.role')" as="select">
                    <option value="">{{ t('admin.users.rolesAll') }}</option>
                    <option value="user">{{ t('admin.roles.user') }}</option>
                    <option value="coach">{{ t('admin.roles.coach') }}</option>
                    <option value="admin">{{ t('admin.roles.admin') }}</option>
                </AppInput>
            </div>
        </AppCard>

        <div class="ui-table-toolbar">
            <div class="ui-table-toolbar__meta">
                {{ t('admin.common.reportSummary') }}: {{ sortedUsers.length }}
            </div>
            <AppButton type="button" variant="secondary" @click="printReport">
                {{ t('admin.common.report') }}
            </AppButton>
        </div>

        <div class="ui-table-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th><button class="ui-table__sort" type="button" @click="setSort('id')">{{ t('admin.common.id') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('id') }">{{ sortIndicator('id') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('name')">{{ t('admin.users.name') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('name') }">{{ sortIndicator('name') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('email')">{{ t('admin.forms.email') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('email') }">{{ sortIndicator('email') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('phone')">{{ t('admin.forms.phone') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('phone') }">{{ sortIndicator('phone') }}</span></button></th>
                        <th><button class="ui-table__sort" type="button" @click="setSort('role')">{{ t('admin.users.role') }} <span class="ui-table__sort-indicator" :class="{ 'is-active': isSortedBy('role') }">{{ sortIndicator('role') }}</span></button></th>
                        <th>{{ t('admin.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in sortedUsers" :key="user.id">
                        <td>{{ user.id }}</td>
                        <td>{{ user.name }}</td>
                        <td>{{ user.email }}</td>
                        <td>{{ user.phone || t('admin.common.notSpecified') }}</td>
                        <td><StatusBadge :value="user.role" kind="role" /></td>
                        <td>
                            <div class="ui-inline-actions">
                                <AppButton :href="route('admin.users.edit', user.id)" variant="secondary" size="sm">
                                    {{ t('admin.users.edit') }}
                                </AppButton>
                                <AppButton type="button" variant="danger" size="sm" @click="destroy(user.id)">
                                    {{ t('admin.users.delete') }}
                                </AppButton>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <EmptyState
            v-if="sortedUsers.length === 0"
            :title="t('admin.users.notFound')"
            :description="t('admin.users.title')"
        />

        <AdminPagination :links="props.users.links" />
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
import { printTableReport } from '@/utils/printTableReport'

const props = defineProps({
    users: Object,
    filters: Object,
})

const { t } = useI18n()
const search = ref(props.filters?.search || '')
const roleFilter = ref(props.filters?.role || '')

const usersArray = computed(() => Array.isArray(props.users?.data) ? props.users.data : [])
const filteredUsers = computed(() => usersArray.value
    .filter((user) => {
        const matchesSearch = user.name.toLowerCase().includes(search.value.toLowerCase())
            || user.email.toLowerCase().includes(search.value.toLowerCase())
        const matchesRole = !roleFilter.value || user.role === roleFilter.value

        return matchesSearch && matchesRole
    }))

const {
    sortDirection,
    sortedRows: sortedUsers,
    setSort,
    isSortedBy,
} = useSortableTable(filteredUsers, {
    initialKey: 'id',
    initialDirection: 'desc',
})

const changePage = (page) => {
    router.get(route('admin.users.index'), {
        search: search.value,
        role: roleFilter.value,
        page,
    }, {
        preserveState: true,
        replace: true,
    })
}

watch([search, roleFilter], () => changePage(1))

const destroy = (id) => {
    if (!confirm(t('admin.users.confirmDelete'))) return
    router.delete(route('admin.users.destroy', id))
}

const sortIndicator = (key) => {
    if (!isSortedBy(key)) return ''
    return sortDirection.value === 'asc' ? t('admin.common.sortAsc') : t('admin.common.sortDesc')
}

const printReport = () => {
    printTableReport({
        title: t('admin.reports.users'),
        columns: [
            t('admin.common.id'),
            t('admin.users.name'),
            t('admin.forms.email'),
            t('admin.forms.phone'),
            t('admin.users.role'),
        ],
        rows: sortedUsers.value.map((user) => [
            user.id,
            user.name,
            user.email,
            user.phone || t('admin.common.notSpecified'),
            t(`admin.roles.${user.role}`),
        ]),
        summary: `${t('admin.common.reportSummary')}: ${sortedUsers.value.length}`,
        printedAt: `${t('admin.common.printedAt')}: ${new Date().toLocaleString()}`,
        emptyText: t('admin.users.notFound'),
    })
}
</script>
