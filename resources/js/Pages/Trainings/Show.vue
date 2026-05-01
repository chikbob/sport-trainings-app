<template>
    <AppLayout>
        <div class="ui-page">
            <PageHeader
                eyebrow="Training"
                :title="training.sport.name"
                :description="`${$formatDate(training.date)} · ${$formatTime(training.time)}`"
            >
                <template #actions>
                    <StatusBadge :value="trainingStatus" kind="training" />
                </template>
            </PageHeader>

            <div class="ui-grid ui-grid--2">
                <AppCard :title="t('trainings.title')" :subtitle="t('trainings.notes')" soft>
                    <div class="ui-list">
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('home.date') }}</div>
                                <div class="ui-list-item__meta">{{ $formatDate(training.date) }}</div>
                            </div>
                        </div>
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('home.time') }}</div>
                                <div class="ui-list-item__meta">{{ $formatTime(training.time) }}</div>
                            </div>
                        </div>
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('home.place') }}</div>
                                <div class="ui-list-item__meta">{{ training.place || t('home.notSpecified') }}</div>
                            </div>
                        </div>
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('trainings.notes') }}</div>
                                <div class="ui-list-item__meta">{{ training.notes || t('trainings.noNotes') }}</div>
                            </div>
                        </div>
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('coach.regCount') }}</div>
                                <div class="ui-list-item__meta">{{ training.registrations_count ?? 0 }}</div>
                            </div>
                        </div>
                    </div>
                </AppCard>

                <AppCard :title="t('coach.actions')" :subtitle="t('trainings.register')">
                    <div class="ui-list">
                        <div class="ui-list-item">
                            <div class="ui-list-item__title">
                                {{ registrationMessage }}
                            </div>
                        </div>
                    </div>

                    <div class="ui-inline-actions" style="margin-top: 20px;">
                        <AppButton
                            v-if="authUser && !isRegistered && !isPast && !isCoach && !isCancelled && !isCompleted"
                            type="button"
                            :loading="loading"
                            @click="register"
                        >
                            {{ t('trainings.register') }}
                        </AppButton>
                        <AppButton
                            v-else-if="!authUser"
                            href="/login"
                            variant="secondary"
                        >
                            {{ t('trainings.login') }}
                        </AppButton>
                    </div>

                    <div v-if="canManage" class="ui-inline-actions" style="margin-top: 20px;">
                        <AppButton :href="route('coach.trainings.edit', training.id)" variant="secondary">
                            {{ t('coach.edit') }}
                        </AppButton>
                        <AppButton
                            v-if="!isCancelled && !isCompleted"
                            type="button"
                            variant="success"
                            @click="completeTraining"
                        >
                            {{ t('coach.completeTraining') }}
                        </AppButton>
                        <AppButton
                            v-if="!isCancelled && !isCompleted"
                            type="button"
                            variant="danger"
                            @click="cancelTraining"
                        >
                            {{ t('coach.cancelTraining') }}
                        </AppButton>
                    </div>
                </AppCard>
            </div>

            <AppCard v-if="canManage" :title="t('coach.registrations')" :subtitle="t('coach.status')">
                <div class="ui-table-card">
                    <div class="ui-table-wrap">
                        <table class="ui-table">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>{{ t('coach.user') }}</th>
                                <th>{{ t('coach.status') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="registration in coachRegistrationsList" :key="registration.id">
                                <td>{{ registration.id }}</td>
                                <td>{{ registration.user?.name || registration.user?.email || '—' }}</td>
                                <td>
                                    <AppInput
                                        :model-value="registration.status"
                                        as="select"
                                        :disabled="!canEditRegistrations"
                                        @update:model-value="updateRegistration(registration.id, $event)"
                                    >
                                        <option v-for="option in statusOptions" :key="option.value" :value="option.value">
                                            {{ option.label }}
                                        </option>
                                    </AppInput>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <EmptyState
                    v-if="coachRegistrationsList.length === 0"
                    :title="t('coach.noRegistrations')"
                    :description="t('coach.registrations')"
                />
            </AppCard>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
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
const coachRegistrationsList = computed(() => props.coachRegistrations || [])
const canEditRegistrations = computed(() => props.canManage && !isCompleted.value && !isCancelled.value)
const isPast = computed(() => props.training.date < new Date().toISOString().slice(0, 10))

const trainingStatus = computed(() => {
    if (isCancelled.value) return 'cancelled'
    if (isCompleted.value) return 'completed'
    return props.training.date > new Date().toISOString().slice(0, 10) ? 'planned' : 'active'
})

const registrationMessage = computed(() => {
    if (props.isRegistered) return t('trainings.alreadyRegistered')
    if (isCancelled.value) return t('coach.cancelled')
    if (isCompleted.value) return t('coach.completed')
    if (isPast.value) return t('trainings.past')
    if (isCoach.value) return t('trainings.coachView')
    if (!authUser.value) return `${t('trainings.login')} ${t('trainings.loginHint')}`
    return t('trainings.register')
})

function register() {
    loading.value = true
    router.post(route('trainings.register', props.training.id), {}, {
        onFinish: () => {
            loading.value = false
        },
    })
}

function cancelTraining() {
    if (!confirm(t('coach.confirmCancelTraining'))) return
    router.post(route('coach.trainings.cancel', props.training.id))
}

function completeTraining() {
    if (!confirm(t('coach.confirmCompleteTraining'))) return
    router.post(route('coach.trainings.complete', props.training.id))
}

const statusOptions = computed(() => [
    { value: 'pending', label: t('profile.status.pending') },
    { value: 'approved', label: t('profile.status.approved') },
    { value: 'cancelled', label: t('profile.status.cancelled') },
    { value: 'rejected', label: t('profile.status.rejected') },
    { value: 'attended', label: t('profile.status.attended') },
    { value: 'no_show', label: t('profile.status.no_show') },
])

const updateRegistration = (registrationId, status) => {
    if (!canEditRegistrations.value) return
    router.put(route('coach.registrations.update', registrationId), { status }, { preserveScroll: true })
}
</script>
