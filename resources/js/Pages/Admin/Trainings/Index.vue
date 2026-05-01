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

        <div class="ui-table-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>{{ t('admin.common.id') }}</th>
                        <th>{{ t('admin.sports.title') }}</th>
                        <th>{{ t('admin.forms.date') }}</th>
                        <th>{{ t('admin.forms.time') }}</th>
                        <th>{{ t('admin.forms.place') }}</th>
                        <th>{{ t('admin.trainings.notes') }}</th>
                        <th>{{ t('coach.status') }}</th>
                        <th>{{ t('admin.common.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="training in trainings.data" :key="training.id">
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
            v-if="trainings.data.length === 0"
            :title="t('admin.trainings.title')"
            :description="t('admin.users.search')"
        />

        <AdminPagination :links="trainings.links" />
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
import StatusBadge from '@/Components/StatusBadge.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({ trainings: Object, filters: Object })

const { t } = useI18n()
const search = ref(props.filters?.search || '')

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
</script>
