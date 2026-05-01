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

        <div class="ui-table-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>{{ t('admin.common.id') }}</th>
                        <th>{{ t('admin.users.name') }}</th>
                        <th>{{ t('admin.forms.email') }}</th>
                        <th>{{ t('admin.forms.phone') }}</th>
                        <th>{{ t('admin.users.role') }}</th>
                        <th>{{ t('admin.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="user in filteredUsers" :key="user.id">
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
            v-if="filteredUsers.length === 0"
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
import { useI18n } from '@/i18n/useI18n'

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
    })
    .sort((a, b) => b.id - a.id))

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
</script>
