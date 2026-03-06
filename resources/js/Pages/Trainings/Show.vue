<template>
    <AppLayout>
        <div class="training-show">
            <div class="training-hero">
                <div>
                    <span class="training-hero__label">{{ t('trainings.title') }}</span>
                    <h1 class="training-hero__title">{{ training.sport.name }}</h1>
                    <p class="training-hero__subtitle">
                        {{ $formatDate(training.date) }} · {{ $formatTime(training.time) }}
                    </p>
                </div>
                <div class="training-hero__badges">
                    <span v-if="isCancelled" class="badge badge--danger">{{ t('coach.cancelled') }}</span>
                    <span v-else-if="isCompleted" class="badge badge--success">{{ t('coach.completed') }}</span>
                    <span v-else class="badge badge--active">{{ t('coach.active') }}</span>
                </div>
            </div>

            <div class="training-grid">
                <div class="training-card">
                    <div class="training-card__row">
                        <div class="training-card__label">{{ t('home.date') }}</div>
                        <div class="training-card__value">{{ $formatDate(training.date) }}</div>
                    </div>
                    <div class="training-card__row">
                        <div class="training-card__label">{{ t('home.time') }}</div>
                        <div class="training-card__value">{{ $formatTime(training.time) }}</div>
                    </div>
                    <div class="training-card__row">
                        <div class="training-card__label">{{ t('home.place') }}</div>
                        <div class="training-card__value">{{ training.place || t('home.notSpecified') }}</div>
                    </div>
                    <div class="training-card__row">
                        <div class="training-card__label">{{ t('trainings.notes') }}</div>
                        <div class="training-card__value">{{ training.notes || t('trainings.noNotes') }}</div>
                    </div>
                    <div class="training-card__row">
                        <div class="training-card__label">{{ t('coach.regCount') }}</div>
                        <div class="training-card__value">{{ training.registrations_count ?? 0 }}</div>
                    </div>
                </div>

                <div class="training-actions">
                    <div class="training-actions__title">{{ t('trainings.register') }}</div>

                    <p v-if="isRegistered" class="training-actions__info">
                        {{ t('trainings.alreadyRegistered') }}
                    </p>

                    <button
                        v-else-if="authUser && !isPast && !isCoach && !isCancelled && !isCompleted"
                        @click="register"
                        class="btn btn--primary"
                        :disabled="loading"
                    >
                        {{ t('trainings.register') }}
                    </button>

                    <p v-else-if="isCancelled" class="training-actions__info">
                        {{ t('coach.cancelled') }}
                    </p>

                    <p v-else-if="isCompleted" class="training-actions__info">
                        {{ t('coach.completed') }}
                    </p>

                    <p v-else-if="isPast" class="training-actions__info">
                        {{ t('trainings.past') }}
                    </p>

                    <p v-else-if="isCoach" class="training-actions__info">
                        {{ t('trainings.coachView') }}
                    </p>

                    <p v-else class="training-actions__info">
                        <a href="/login">{{ t('trainings.login') }}</a>, {{ t('trainings.loginHint') }}
                    </p>

                    <div v-if="canManage" class="training-actions__coach">
                        <div class="training-actions__coach-title">{{ t('coach.actions') }}</div>
                        <div class="training-actions__coach-buttons">
                            <Link
                                :href="route('coach.trainings.edit', training.id)"
                                class="btn btn--ghost"
                            >
                                {{ t('coach.edit') }}
                            </Link>
                            <button
                                v-if="!isCancelled && !isCompleted"
                                class="btn btn--success"
                                @click="completeTraining"
                            >
                                {{ t('coach.completeTraining') }}
                            </button>
                            <button
                                v-if="!isCancelled && !isCompleted"
                                class="btn btn--danger"
                                @click="cancelTraining"
                            >
                                {{ t('coach.cancelTraining') }}
                            </button>
                        </div>
                        <p v-if="isCancelled" class="training-actions__note">
                            {{ t('coach.cancelled') }}
                        </p>
                        <p v-else-if="isCompleted" class="training-actions__note">
                            {{ t('coach.completed') }}
                        </p>
                    </div>
                </div>
            </div>

            <div v-if="canManage" class="training-registrations">
                <div class="training-registrations__header">
                    <h2>{{ t('coach.registrations') }}</h2>
                </div>
                <table class="table">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>{{ t('coach.user') }}</th>
                        <th>{{ t('coach.status') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="registration in coachRegistrations" :key="registration.id">
                        <td>{{ registration.id }}</td>
                        <td>{{ registration.user?.name || registration.user?.email || '—' }}</td>
                        <td>
                            <select
                                class="status-select"
                                :disabled="!canEditRegistrations"
                                :value="registration.status"
                                @change="updateRegistration(registration.id, $event.target.value)"
                            >
                                <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                    {{ option.label }}
                                </option>
                            </select>
                        </td>
                    </tr>
                    <tr v-if="coachRegistrations.length === 0">
                        <td colspan="3" class="no-data">{{ t('coach.noRegistrations') }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    training: Object,
    isRegistered: Boolean,
    canManage: Boolean,
    coachRegistrations: Array,
})

const page = usePage()
const authUser = computed(() => page.props.auth?.user ?? null)
const isCoach = computed(() => authUser.value?.role === 'coach')
const loading = ref(false)
const { t } = useI18n()

const isCancelled = computed(() => !!props.training.is_cancelled)
const isCompleted = computed(() => !!props.training.is_completed)
const coachRegistrations = computed(() => props.coachRegistrations || [])
const canEditRegistrations = computed(() => props.canManage && !isCompleted.value && !isCancelled.value)

const isPast = computed(() => {
    const today = new Date().toISOString().slice(0, 10)
    return props.training.date < today
})

function register() {
    loading.value = true

    router.post(
        route('trainings.register', props.training.id),
        {},
        {
            onFinish: () => (loading.value = false),
            onSuccess: () => alert(t('trainings.registerSuccess')),
            onError: () => alert(t('trainings.registerError')),
        }
    )
}

function cancelTraining() {
    if (!confirm(t('coach.confirmCancelTraining'))) return

    router.post(route('coach.trainings.cancel', props.training.id))
}

function completeTraining() {
    if (!confirm(t('coach.confirmCompleteTraining'))) return

    router.post(route('coach.trainings.complete', props.training.id))
}

const statusOptions = [
    { value: 'pending', label: t('profile.status.pending') },
    { value: 'approved', label: t('profile.status.approved') },
    { value: 'cancelled', label: t('profile.status.cancelled') },
    { value: 'rejected', label: t('profile.status.rejected') },
    { value: 'attended', label: t('profile.status.attended') },
    { value: 'no_show', label: t('profile.status.no_show') },
]

const updateRegistration = (registrationId, status) => {
    if (!canEditRegistrations.value) return
    router.put(route('coach.registrations.update', registrationId), { status })
}
</script>

<style scoped>
.training-show {
    max-width: 980px;
    margin: 2.5rem auto 0;
    padding: 0 20px 40px;
}

.training-hero {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 24px 28px;
    border-radius: 18px;
    background: linear-gradient(135deg, #e0f2fe 0%, #fef9c3 100%);
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.08);
}

.training-hero__label {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    border-radius: 999px;
    background: rgba(15, 23, 42, 0.08);
    font-size: 12px;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    color: #0f172a;
}

.training-hero__title {
    font-size: 34px;
    margin: 10px 0 6px;
    color: #0f172a;
}

.training-hero__subtitle {
    margin: 0;
    font-size: 16px;
    color: #334155;
}

.training-hero__badges {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.badge {
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
}

.badge--active {
    background: #e0f2fe;
    color: #0f172a;
}

.badge--success {
    background: #dcfce7;
    color: #14532d;
}

.badge--danger {
    background: #fee2e2;
    color: #991b1b;
}

.training-grid {
    display: grid;
    grid-template-columns: minmax(0, 1.2fr) minmax(0, 0.8fr);
    gap: 24px;
    margin-top: 24px;
}

.training-card {
    background: white;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    display: grid;
    gap: 14px;
}

.training-card__row {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    border-bottom: 1px dashed #e2e8f0;
    padding-bottom: 10px;
}

.training-card__row:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.training-card__label {
    font-size: 13px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.training-card__value {
    font-size: 16px;
    font-weight: 600;
    color: #0f172a;
    text-align: right;
    max-width: 60%;
}

.training-actions {
    background: white;
    border-radius: 16px;
    padding: 22px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.training-actions__title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.training-actions__info {
    font-weight: 600;
    color: #2563eb;
    margin: 0;
}

.training-actions__coach {
    margin-top: auto;
    border-top: 1px solid #e2e8f0;
    padding-top: 16px;
}

.training-actions__coach-title {
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 10px;
}

.training-actions__coach-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.training-actions__note {
    margin-top: 10px;
    color: #64748b;
    font-size: 13px;
}

.btn {
    padding: 10px 18px;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    font-weight: 600;
}

.btn--primary {
    background-color: #2563eb;
    color: white;
}

.btn--ghost {
    background: transparent;
    border: 1px solid #cbd5f5;
    color: #1e3a8a;
}

.btn--success {
    background: #22c55e;
    color: white;
}

.btn--danger {
    background: #ef4444;
    color: white;
}

.btn--primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.training-registrations {
    margin-top: 28px;
    background: #fff;
    border-radius: 16px;
    padding: 20px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
}

.training-registrations__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}

.training-registrations__header h2 {
    margin: 0;
    font-size: 20px;
    color: #0f172a;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th,
.table td {
    text-align: left;
    padding: 10px 12px;
    border-bottom: 1px solid #e2e8f0;
}

.table thead {
    background: #f8fafc;
}

.status-select {
    padding: 6px 10px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    background: #fff;
    color: #0f172a;
    font-weight: 600;
}

.status-select:disabled {
    opacity: 0.6;
}

.no-data {
    text-align: center;
    color: #94a3b8;
    padding: 12px 0;
}

@media (max-width: 900px) {
    .training-grid {
        grid-template-columns: 1fr;
    }

    .training-hero {
        flex-direction: column;
        align-items: flex-start;
    }

    .training-card__value {
        max-width: 100%;
        text-align: left;
    }
}
</style>
