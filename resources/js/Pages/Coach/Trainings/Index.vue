<template>
    <AppLayout>
        <div class="coach">
            <h2 class="coach__title">{{ t('coach.trainings.title') }}</h2>

            <div class="filters">
                <button
                    class="filter-pill"
                    :class="{ 'filter-pill--active': filter === 'upcoming' }"
                    @click="setFilter('upcoming')"
                    type="button"
                >
                    {{ t('coach.trainings.upcoming') }}
                </button>
                <button
                    class="filter-pill"
                    :class="{ 'filter-pill--active': filter === 'past' }"
                    @click="setFilter('past')"
                    type="button"
                >
                    {{ t('coach.trainings.past') }}
                </button>
                <button
                    class="filter-pill"
                    :class="{ 'filter-pill--active': filter === 'archive' }"
                    @click="setFilter('archive')"
                    type="button"
                >
                    {{ t('coach.trainings.archive') }}
                </button>
            </div>

            <table class="table">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>{{ t('coach.training') }}</th>
                    <th>{{ t('home.date') }}</th>
                    <th>{{ t('home.time') }}</th>
                    <th>{{ t('home.place') }}</th>
                    <th>{{ t('coach.status') }}</th>
                    <th v-if="showActions">{{ t('coach.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="training in trainings.data" :key="training.id">
                    <td>{{ training.id }}</td>
                    <td>{{ training.sport?.name || '—' }}</td>
                    <td>{{ $formatDate(training.date) }}</td>
                    <td>{{ $formatTime(training.time) }}</td>
                    <td>{{ training.place || t('home.notSpecified') }}</td>
                    <td>
                        <span v-if="training.is_cancelled" class="badge status-cancelled">{{ t('coach.cancelled') }}</span>
                        <span v-else-if="training.is_completed" class="badge status-attended">{{ t('coach.completed') }}</span>
                        <span v-else class="badge status-pending">{{ t('coach.active') }}</span>
                    </td>
                    <td v-if="showActions" class="actions">
                        <template v-if="!training.is_completed">
                            <Link :href="`/coach/trainings/${training.id}/edit`" class="btn btn--secondary">
                                {{ t('coach.edit') }}
                            </Link>
                            <button class="btn btn--danger" @click="cancelTraining(training.id)">
                                {{ t('coach.cancelTraining') }}
                            </button>
                            <button class="btn btn--primary" @click="completeTraining(training.id)">
                                {{ t('coach.completeTraining') }}
                            </button>
                        </template>
                    </td>
                </tr>
                <tr v-if="trainings.data.length === 0">
                    <td :colspan="showActions ? 7 : 6" class="no-data">{{ t('coach.trainings.empty') }}</td>
                </tr>
                </tbody>
            </table>

            <PaginationLinks :links="trainings.links" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'
import AppLayout from '@/Layouts/AppLayout.vue'
import PaginationLinks from '@/Components/PaginationLinks.vue'
import { route } from 'ziggy-js'

const props = defineProps({
    trainings: Object,
    filters: Object,
})

const { t } = useI18n()
const filter = ref(props.filters?.filter || 'upcoming')
const showActions = computed(() => filter.value !== 'archive')

watch([filter], () => {
    router.get(route('coach.trainings.index'), { filter: filter.value }, { preserveState: true, replace: true })
})

const setFilter = (value) => {
    if (filter.value === value) return
    filter.value = value
}

const cancelTraining = (id) => {
    if (!confirm(t('coach.confirmCancelTraining'))) return
    router.post(route('coach.trainings.cancel', id))
}

const completeTraining = (id) => {
    router.post(route('coach.trainings.complete', id))
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
    flex-wrap: wrap;
}

.filter-pill {
    padding: 8px 14px;
    border-radius: 999px;
    border: 1px solid #d1d5db;
    background: #fff;
    color: #0f172a;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
}

.filter-pill--active {
    border-color: #2563eb;
    background: #2563eb;
    color: #fff;
}

.table {
    width: 100%;
    margin: auto auto 28px;
    border-collapse: collapse;
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    table-layout: fixed;

    th, td {
        padding: 12px 14px;
        text-align: left;
        word-break: break-word;
    }

    thead {
        background: #f8fafc;
    }

    tbody tr:hover {
        background: #f1f5f9;
    }
}

.actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn {
    padding: 6px 10px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
}

.btn--secondary { background: #e2e8f0; color: #0f172a; }
.btn--danger { background: #ef4444; color: #fff; }
.btn--primary { background: #2563eb; color: #fff; }

.badge {
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

.status-pending { background: #fef3c7; color: #92400e; }
.status-cancelled { background: #fee2e2; color: #b91c1c; }
.status-attended { background: #dcfce7; color: #166534; }

.no-data {
    text-align: center;
    color: #94a3b8;
    padding: 20px 0;
}
</style>
