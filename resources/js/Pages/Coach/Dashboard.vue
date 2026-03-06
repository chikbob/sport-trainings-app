<template>
    <AppLayout>
        <div class="coach">
            <h2 class="coach__title">{{ t('coach.title') }}</h2>

            <div class="coach__stats">
                <div class="card">
                    <span>{{ t('coach.stats.trainings') }}</span>
                    <strong>{{ stats.trainings }}</strong>
                </div>
                <div class="card">
                    <span>{{ t('coach.stats.registrations') }}</span>
                    <strong>{{ stats.registrations }}</strong>
                </div>
                <div class="card">
                    <span>{{ t('coach.stats.attended') }}</span>
                    <strong>{{ stats.attended }}</strong>
                </div>
            </div>

            <section class="panel">
                <h3>{{ t('coach.upcoming') }}</h3>
                <div class="coach__links">
                    <a href="/coach/trainings" class="link">{{ t('coach.trainings.link') }}</a>
                    <a href="/coach/registrations" class="link">{{ t('coach.registrationsPage.link') }}</a>
                </div>
                <div v-if="upcomingTrainings.length === 0" class="empty">
                    {{ t('coach.emptyTrainings') }}
                </div>

                <div class="trainings" v-else>
                    <div v-for="training in upcomingTrainings" :key="training.id" class="training">
                        <div class="training__info">
                            <div class="training__title">
                                {{ training.sport?.name || '—' }}
                            </div>
                            <div class="training__meta">
                                {{ $formatDate(training.date) }} • {{ $formatTime(training.time) }} • {{ training.place || t('home.notSpecified') }}
                            </div>
                            <div class="training__count">
                                {{ t('coach.regCount') }}: {{ training.registrations?.length || 0 }}
                            </div>
                        </div>
                        <div class="training__actions">
                            <button
                                class="btn btn--danger"
                                @click="cancelTraining(training.id)"
                                :disabled="training.is_cancelled"
                            >
                                {{ training.is_cancelled ? t('coach.cancelled') : t('coach.cancelTraining') }}
                            </button>
                            <a :href="`/trainings/${training.id}`" class="btn btn--secondary">
                                {{ t('coach.viewTraining') }}
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="panel">
                <h3>{{ t('coach.registrations') }}</h3>

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
            </section>
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
    coach: Object,
    upcomingTrainings: Array,
    registrations: Object,
    stats: Object,
    filters: Object,
})

const { t } = useI18n()
const statusFilter = ref(props.filters?.status || '')

const cancelTraining = (trainingId) => {
    if (!confirm(t('coach.confirmCancelTraining'))) return
    router.post(route('coach.trainings.cancel', trainingId))
}

const updateStatus = (reg) => {
    router.patch(route('coach.registrations.updateStatus', reg.id), {
        status: reg.status,
    })
}

watch([statusFilter], () => {
    router.get(route('coach.dashboard'), { status: statusFilter.value }, { preserveState: true, replace: true })
})

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

    &__title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 24px;
        color: #1e293b;
    }

    &__stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
}

.card {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    display: flex;
    flex-direction: column;
    gap: 8px;

    span {
        font-size: 13px;
        color: #64748b;
    }

    strong {
        font-size: 26px;
        color: #0f172a;
    }
}

.panel {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 24px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);

    h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 14px;
    }
}

.coach__links {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
}

.link {
    color: #2563eb;
    font-weight: 600;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}

.trainings {
    display: grid;
    gap: 12px;
}

.training {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px;
}

.training__title {
    font-weight: 700;
    margin-bottom: 4px;
}

.training__meta {
    color: #64748b;
    font-size: 14px;
}

.training__count {
    margin-top: 6px;
    font-size: 13px;
}

.training__actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 8px 12px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
}

.btn--danger {
    background: #ef4444;
    color: #fff;
}

.btn--secondary {
    background: #e2e8f0;
    color: #0f172a;
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

.empty,
.no-data {
    color: #94a3b8;
    text-align: center;
}
</style>
