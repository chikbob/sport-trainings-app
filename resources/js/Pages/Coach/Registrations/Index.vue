<template>
    <AppLayout>
        <div class="coach">
            <h2 class="coach__title">{{ t('coach.registrations') }}</h2>

            <div class="filters">
                <select v-model="statusFilter" class="input">
                    <option value="">{{ t('coach.allStatuses') }}</option>
                    <option value="pending">{{ t('admin.status.pending') }}</option>
                    <option value="approved">{{ t('admin.status.approved') }}</option>
                    <option value="cancelled">{{ t('admin.status.cancelled') }}</option>
                    <option value="rejected">{{ t('admin.status.rejected') }}</option>
                    <option value="attended">{{ t('admin.status.attended') }}</option>
                    <option value="no_show">{{ t('admin.status.no_show') }}</option>
                </select>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ t('coach.user') }}</th>
                    <th>{{ t('coach.training') }}</th>
                    <th>{{ t('coach.status') }}</th>
                    <th>{{ t('coach.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="reg in registrations.data" :key="reg.id">
                    <td>{{ reg.id }}</td>
                    <td>{{ reg.user?.name || '—' }}</td>
                    <td>
                        {{ reg.training?.sport?.name || '—' }} •
                        {{ $formatDate(reg.training?.date) }} {{ $formatTime(reg.training?.time) }}
                    </td>
                    <td>
                        <span class="badge" :class="'status-' + reg.status">
                            {{ translateStatus(reg.status) }}
                        </span>
                    </td>
                    <td>
                        <select v-model="reg.status" class="status-select" @change="updateStatus(reg)">
                            <option value="pending">{{ t('admin.status.pending') }}</option>
                            <option value="approved">{{ t('admin.status.approved') }}</option>
                            <option value="cancelled">{{ t('admin.status.cancelled') }}</option>
                            <option value="rejected">{{ t('admin.status.rejected') }}</option>
                            <option value="attended">{{ t('admin.status.attended') }}</option>
                            <option value="no_show">{{ t('admin.status.no_show') }}</option>
                        </select>
                    </td>
                </tr>
                <tr v-if="registrations.data.length === 0">
                    <td colspan="5" class="no-data">{{ t('coach.noRegistrations') }}</td>
                </tr>
                </tbody>
            </table>

            <PaginationLinks :links="registrations.links" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import PaginationLinks from '@/Components/PaginationLinks.vue'
import { route } from 'ziggy-js'

const props = defineProps({
    registrations: Object,
    filters: Object,
})

const { t } = useI18n()
const statusFilter = ref(props.filters?.status || '')

watch([statusFilter], () => {
    router.get(route('coach.registrations.index'), { status: statusFilter.value }, { preserveState: true, replace: true })
})

const updateStatus = (reg) => {
    router.patch(route('coach.registrations.update', reg.id), { status: reg.status })
}

const translateStatus = (status) => {
    const map = {
        pending: t('admin.status.pending'),
        approved: t('admin.status.approved'),
        cancelled: t('admin.status.cancelled'),
        rejected: t('admin.status.rejected'),
        attended: t('admin.status.attended'),
        no_show: t('admin.status.no_show'),
    }
    return map[status] || status
}
</script>

<style scoped lang="scss">
.coach {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px;
}

.coach__title {
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 24px;
    color: #1e293b;
}

.filters {
    display: flex;
    gap: 12px;
    margin-bottom: 14px;
}

.input {
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
}

.table {
    width: 100%;
    margin: auto auto 28px;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;

    th, td {
        padding: 12px 14px;
        text-align: left;
    }

    thead {
        background: #f8fafc;
    }

    tbody tr:hover {
        background: #f1f5f9;
    }
}

.badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-approved { background: #e0f2fe; color: #0369a1; }
.status-cancelled { background: #fee2e2; color: #b91c1c; }
.status-rejected { background: #fee2e2; color: #b91c1c; }
.status-attended { background: #dcfce7; color: #166534; }
.status-no_show { background: #ede9fe; color: #6d28d9; }

.status-select {
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
}

.no-data {
    text-align: center;
    color: #94a3b8;
    padding: 20px 0;
}
</style>
