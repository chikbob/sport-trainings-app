<template>
    <AdminLayout>
        <PageHeader :title="t('admin.dashboard.title')" :description="t('admin.header.title')" />

        <div class="ui-kpi-grid">
            <StatCard :label="t('admin.stats.users')" :value="safeStats.users" />
            <StatCard :label="t('admin.stats.coaches')" :value="safeStats.coaches" />
            <StatCard :label="t('admin.stats.sports')" :value="safeStats.sports" />
            <StatCard :label="t('admin.stats.trainings')" :value="safeStats.trainings" />
        </div>

        <div class="ui-grid ui-grid--3">
            <AppCard :title="t('admin.dashboard.recentUsers')">
                <EmptyState
                    v-if="safeRecentUsers.length === 0"
                    :title="t('admin.dashboard.emptyUsers')"
                    :description="t('admin.users.title')"
                />
                <div v-else class="ui-list">
                    <div v-for="user in safeRecentUsers" :key="user.id" class="ui-list-item">
                        <div>
                            <div class="ui-list-item__title">{{ user.name }}</div>
                            <div class="ui-list-item__meta">{{ user.email }}</div>
                        </div>
                        <div class="ui-inline-actions">
                            <StatusBadge :value="user.role" kind="role" />
                        </div>
                    </div>
                </div>
            </AppCard>

            <AppCard :title="t('admin.dashboard.upcomingTrainings')">
                <EmptyState
                    v-if="safeUpcomingTrainings.length === 0"
                    :title="t('admin.dashboard.emptyTrainings')"
                    :description="t('admin.trainings.title')"
                />
                <div v-else class="ui-list">
                    <div v-for="training in safeUpcomingTrainings" :key="training.id" class="ui-list-item">
                        <div>
                            <div class="ui-list-item__title">{{ training.sport?.name || t('admin.common.notSpecified') }}</div>
                            <div class="ui-list-item__meta">
                                {{ $formatDate(training.date) }} · {{ $formatTime(training.time) }}
                            </div>
                        </div>
                        <StatusBadge :value="trainingStatus(training)" kind="training" />
                    </div>
                </div>
            </AppCard>

            <AppCard :title="t('admin.dashboard.registrationsStatus')">
                <EmptyState
                    v-if="safeRegistrationsByStatus.length === 0"
                    :title="t('admin.registrations.empty')"
                    :description="t('admin.registrations.title')"
                />
                <div v-else class="ui-list">
                    <div v-for="item in safeRegistrationsByStatus" :key="item.status" class="ui-list-item">
                        <div class="ui-list-item__title">{{ translateStatus(item.status) }}</div>
                        <StatusBadge :value="item.status" />
                        <strong>{{ item.total }}</strong>
                    </div>
                </div>
            </AppCard>
        </div>
    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AppCard from '@/Components/AppCard.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import StatCard from '@/Components/StatCard.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    stats: Object,
    recentUsers: Array,
    upcomingTrainings: Array,
    registrationsByStatus: Array,
})

const { t } = useI18n()

const safeStats = computed(() => props.stats || { users: 0, coaches: 0, sports: 0, trainings: 0, registrations: 0 })
const safeRecentUsers = computed(() => props.recentUsers || [])
const safeUpcomingTrainings = computed(() => props.upcomingTrainings || [])
const safeRegistrationsByStatus = computed(() => props.registrationsByStatus || [])

function translateStatus(status) {
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

function trainingStatus(training) {
    if (training.is_cancelled) return 'cancelled'
    if (training.is_completed) return 'completed'
    return training.date > new Date().toISOString().slice(0, 10) ? 'planned' : 'active'
}
</script>
