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

        <div class="ui-table-card">
            <div class="ui-table-wrap">
                <table class="ui-table">
                    <thead>
                    <tr>
                        <th>{{ t('admin.common.id') }}</th>
                        <th>{{ t('admin.registrations.user') }}</th>
                        <th>{{ t('admin.registrations.sport') }}</th>
                        <th>{{ t('admin.registrations.training') }}</th>
                        <th>{{ t('admin.registrations.status') }}</th>
                        <th>{{ t('admin.registrations.createdAt') }}</th>
                        <th>{{ t('admin.registrations.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="reg in registrations.data" :key="reg.id">
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
            v-if="registrations.data.length === 0"
            :title="t('admin.registrations.empty')"
            :description="t('admin.registrations.title')"
        />

        <AdminPagination :links="registrations.links" />
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

const props = defineProps({
    registrations: Object,
    filters: Object,
})

const { t } = useI18n()
const search = ref(props.filters?.search || '')
const statusFilter = ref(props.filters?.status || '')

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
</script>
