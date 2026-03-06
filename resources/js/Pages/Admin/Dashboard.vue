<template>
    <AdminLayout>
        <div class="dashboard">
            <h2 class="dashboard__title">
                {{ t('admin.dashboard.title') }}
            </h2>

            <div class="dashboard__grid">
                <div class="card">
                    <span>{{ t('admin.stats.users') }}</span>
                    <strong>{{ safeStats.users }}</strong>
                </div>

                <div class="card">
                    <span>{{ t('admin.stats.coaches') }}</span>
                    <strong>{{ safeStats.coaches }}</strong>
                </div>

                <div class="card">
                    <span>{{ t('admin.stats.sports') }}</span>
                    <strong>{{ safeStats.sports }}</strong>
                </div>

                <div class="card">
                    <span>{{ t('admin.stats.trainings') }}</span>
                    <strong>{{ safeStats.trainings }}</strong>
                </div>

                <div class="card">
                    <span>{{ t('admin.stats.registrations') }}</span>
                    <strong>{{ safeStats.registrations }}</strong>
                </div>
            </div>

            <div class="dashboard__content">
                <section class="panel">
                    <h3>{{ t('admin.dashboard.recentUsers') }}</h3>
                    <ul class="list">
                        <li v-for="user in safeRecentUsers" :key="user.id" class="list__item">
                            <div>
                                <div class="list__title">{{ user.name }}</div>
                                <div class="list__subtitle">{{ user.email }}</div>
                            </div>
                            <div class="list__meta">
                                <span class="badge" :class="'role-' + user.role">
                                    {{ translateRole(user.role) }}
                                </span>
                                <span class="list__date">{{ $formatDate(user.created_at) }}</span>
                            </div>
                        </li>
                        <li v-if="safeRecentUsers.length === 0" class="list__empty">
                            {{ t('admin.dashboard.emptyUsers') }}
                        </li>
                    </ul>
                </section>

                <section class="panel">
                    <h3>{{ t('admin.dashboard.upcomingTrainings') }}</h3>
                    <ul class="list">
                        <li v-for="training in safeUpcomingTrainings" :key="training.id" class="list__item">
                            <div>
                                <div class="list__title">{{ training.sport?.name || t('admin.common.notSpecified') }}</div>
                                <div class="list__subtitle">
                                    {{ $formatDate(training.date) }} • {{ training.time }}
                                </div>
                            </div>
                            <div class="list__meta">{{ training.place || t('admin.common.notSpecified') }}</div>
                        </li>
                        <li v-if="safeUpcomingTrainings.length === 0" class="list__empty">
                            {{ t('admin.dashboard.emptyTrainings') }}
                        </li>
                    </ul>
                </section>

                <section class="panel">
                    <h3>{{ t('admin.dashboard.registrationsStatus') }}</h3>
                    <div class="status-grid">
                        <div
                            v-for="item in safeRegistrationsByStatus"
                            :key="item.status"
                            class="status-card"
                        >
                            <span class="status-card__label">{{ translateStatus(item.status) }}</span>
                            <strong>{{ item.total }}</strong>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed } from 'vue'
import { useI18n } from '@/i18n/useI18n'

const { t } = useI18n()

const props = defineProps({
    stats: Object,
    recentUsers: Array,
    upcomingTrainings: Array,
    registrationsByStatus: Array,
})

const safeStats = computed(() => props.stats || {
    users: 0,
    coaches: 0,
    sports: 0,
    trainings: 0,
    registrations: 0,
})

const safeRecentUsers = computed(() => props.recentUsers || [])
const safeUpcomingTrainings = computed(() => props.upcomingTrainings || [])
const safeRegistrationsByStatus = computed(() => props.registrationsByStatus || [])

function translateRole(role) {
    switch (role) {
        case 'admin':
            return t('admin.roles.admin')
        case 'coach':
            return t('admin.roles.coach')
        case 'user':
            return t('admin.roles.user')
        default:
            return role
    }
}

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
</script>

<style scoped lang="scss">
.dashboard {

    &__title {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 30px;
        color: #1e293b;
    }

    &__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 24px;
    }

    &__content {
        margin-top: 32px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
    }
}

.card {
    background: white;
    padding: 28px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    gap: 10px;

    span {
        font-size: 14px;
        color: #64748b;
    }

    strong {
        font-size: 32px;
        color: #0f172a;
    }
}

.panel {
    background: #fff;
    border-radius: 16px;
    padding: 20px 24px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);

    h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 14px;
        color: #0f172a;
    }
}

.list {
    list-style: none;
    padding: 0;
    margin: 0;

    &__item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #e2e8f0;
        gap: 12px;
    }

    &__item:last-child {
        border-bottom: none;
    }

    &__title {
        font-weight: 600;
        color: #1e293b;
    }

    &__subtitle {
        font-size: 13px;
        color: #64748b;
    }

    &__meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 6px;
        font-size: 13px;
        color: #475569;
    }

    &__date {
        font-size: 12px;
        color: #94a3b8;
    }

    &__empty {
        padding: 10px 0;
        color: #94a3b8;
        font-size: 14px;
    }
}

.badge {
    padding: 4px 10px;
    font-size: 0.7rem;
    font-weight: 700;
    border-radius: 999px;
    text-transform: uppercase;
}

.role-admin {
    background: #fee2e2;
    color: #b91c1c;
}

.role-coach {
    background: #ffedd5;
    color: #b45309;
}

.role-user {
    background: #e0f2fe;
    color: #0369a1;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 14px;
}

.status-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 12px 14px;
    display: flex;
    flex-direction: column;
    gap: 6px;

    &__label {
        font-size: 12px;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.04em;
    }

    strong {
        font-size: 22px;
        color: #0f172a;
    }
}
</style>
