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

        <div class="ui-table-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>{{ t('admin.common.id') }}</th>
                        <th>{{ t('admin.sports.name') }}</th>
                        <th>{{ t('admin.forms.location') }}</th>
                        <th>{{ t('admin.sports.trainer') }}</th>
                        <th>{{ t('admin.forms.description') }}</th>
                        <th>{{ t('admin.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="sport in sports.data" :key="sport.id">
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
            v-if="sports.data.length === 0"
            :title="t('admin.sports.title')"
            :description="t('admin.users.search')"
        />

        <AdminPagination :links="sports.links" />
    </AdminLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AdminPagination from '@/Components/AdminPagination.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({ sports: Object, filters: Object })

const { t } = useI18n()
const search = ref(props.filters?.search || '')

watch(search, () => {
    router.get(route('admin.sports.index'), { search: search.value, page: 1 }, { preserveState: true, replace: true })
})

const destroy = (id) => {
    if (!confirm(t('admin.common.confirmDelete'))) return
    router.delete(`/admin/sports/${id}`)
}
</script>
