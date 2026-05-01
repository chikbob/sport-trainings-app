<template>
    <AppLayout>
        <div class="ui-page">
            <PageHeader
                eyebrow="Sport"
                :title="sport.name"
                :description="sport.description || t('home.noDescription')"
            />

            <div class="ui-grid ui-grid--2">
                <AppCard :title="t('sports.title')" :subtitle="sport.coach?.user?.name || '—'" soft>
                    <div class="ui-list">
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('sports.trainer') }}</div>
                                <div class="ui-list-item__meta">{{ sport.coach?.user?.name || '—' }}</div>
                            </div>
                        </div>
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('sports.location') }}</div>
                                <div class="ui-list-item__meta">{{ sport.location || t('home.notSpecified') }}</div>
                            </div>
                        </div>
                    </div>
                </AppCard>

                <AppCard :title="t('sport.trainings')" :subtitle="t('trainings.title')">
                    <EmptyState
                        v-if="sport.trainings.length === 0"
                        :title="t('sport.emptyTrainings')"
                        :description="t('trainings.title')"
                    />

                    <div v-else class="ui-list">
                        <div v-for="training in sport.trainings" :key="training.id" class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ $formatDate(training.date) }} · {{ $formatTime(training.time) }}</div>
                                <div class="ui-list-item__meta">{{ training.place || t('home.notSpecified') }}</div>
                            </div>

                            <div class="ui-inline-actions">
                                <AppButton
                                    v-if="authUser && !training.isRegistered"
                                    type="button"
                                    @click="register(training.id)"
                                >
                                    {{ t('sport.register') }}
                                </AppButton>
                                <StatusBadge
                                    v-else-if="authUser && training.isRegistered"
                                    value="approved"
                                    :label="t('sport.alreadyRegistered')"
                                />
                                <AppButton v-else href="/login" variant="secondary">
                                    {{ t('sport.loginToRegister') }}
                                </AppButton>
                            </div>
                        </div>
                    </div>
                </AppCard>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { useI18n } from '@/i18n/useI18n'

defineProps({
    sport: Object,
})

const page = usePage()
const authUser = computed(() => page.props.auth?.user ?? null)
const { t } = useI18n()

function register(trainingId) {
    router.post(`/trainings/${trainingId}/register`)
}
</script>
