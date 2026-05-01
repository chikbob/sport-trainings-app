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

        <div class="ui-table-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>{{ t('admin.common.id') }}</th>
                        <th>{{ t('admin.coaches.userName') }}</th>
                        <th>{{ t('admin.forms.phone') }}</th>
                        <th>{{ t('admin.forms.specialization') }}</th>
                        <th>{{ t('admin.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="coach in coaches.data" :key="coach.id">
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
            v-if="coaches.data.length === 0"
            :title="t('admin.coaches.title')"
            :description="t('admin.users.search')"
        />

        <AdminPagination :links="coaches.links" />
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

const props = defineProps({
    coaches: Object,
    filters: Object,
})

const { t } = useI18n()
const search = ref(props.filters?.search || '')

watch(search, () => {
    router.get(route('admin.coaches.index'), { search: search.value, page: 1 }, { preserveState: true, replace: true })
})

const destroy = (id) => {
    if (!confirm(t('admin.common.confirmDelete'))) return
    router.delete(`/admin/coaches/${id}`)
}
</script>
