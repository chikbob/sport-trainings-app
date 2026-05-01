<template>
    <AppLayout>
        <div class="ui-page">
            <PageHeader
                eyebrow="FitClub"
                :title="t('home.title')"
                :description="t('home.subtitle')"
            >
                <template v-if="!authUser" #actions>
                    <AppButton href="/sports">{{ t('header.sections') }}</AppButton>
                    <AppButton href="/trainings" variant="secondary">{{ t('header.schedule') }}</AppButton>
                </template>
            </PageHeader>

            <div class="ui-grid ui-grid--2">
                <AppCard
                    v-if="nextRegistration"
                    :title="t('home.nextTraining')"
                    :subtitle="nextRegistration.training.sport.name"
                    soft
                >
                    <div class="ui-list">
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ $formatDate(nextRegistration.training.date) }}</div>
                                <div class="ui-list-item__meta">
                                    {{ $formatTime(nextRegistration.training.time) }} · {{ nextRegistration.training.place || t('home.notSpecified') }}
                                </div>
                            </div>
                            <StatusBadge :value="nextTrainingStatus" kind="training" />
                        </div>
                    </div>

                    <div class="ui-inline-actions" style="margin-top: 20px;">
                        <AppButton :href="route('trainings.show', nextRegistration.training.id)">
                            {{ t('home.goToTraining') }}
                        </AppButton>
                    </div>
                </AppCard>

                <AppCard
                    v-else
                    :title="t('home.latestSports')"
                    :subtitle="t('sports.title')"
                    soft
                >
                    <EmptyState
                        v-if="!latestSports?.length"
                        :title="t('home.latestSports')"
                        :description="t('profile.empty')"
                    />

                    <div v-else class="ui-grid">
                        <div
                            v-for="sport in latestSports"
                            :key="sport.id"
                            class="ui-list-item"
                        >
                            <div>
                                <div class="ui-list-item__title">{{ sport.name }}</div>
                                <div class="ui-list-item__meta">{{ sport.description || t('home.noDescription') }}</div>
                            </div>
                            <AppButton :href="route('sports.show', sport.id)" variant="secondary" size="sm">
                                {{ t('home.details') }}
                            </AppButton>
                        </div>
                    </div>
                </AppCard>

                <AppCard :title="t('home.clubActivities')" :subtitle="t('home.clubActivitiesHint')">
                    <div class="ui-list">
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('home.groupPrograms') }}</div>
                                <div class="ui-list-item__meta">{{ t('home.groupProgramsHint') }}</div>
                            </div>
                        </div>
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('home.personalSchedule') }}</div>
                                <div class="ui-list-item__meta">{{ t('home.personalScheduleHint') }}</div>
                            </div>
                        </div>
                    </div>
                </AppCard>

                <AppCard v-if="!authUser" :title="t('header.profile')" :subtitle="t('header.login')">
                    <div class="ui-inline-actions">
                        <AppButton href="/login" variant="secondary">{{ t('header.login') }}</AppButton>
                        <AppButton href="/register">{{ t('header.register') }}</AppButton>
                    </div>
                </AppCard>

                <AppCard v-else :title="t('home.memberArea')" :subtitle="t('home.memberAreaHint')" soft>
                    <div class="ui-list">
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('header.sections') }}</div>
                                <div class="ui-list-item__meta">{{ t('home.groupProgramsHint') }}</div>
                            </div>
                        </div>
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('header.schedule') }}</div>
                                <div class="ui-list-item__meta">{{ t('home.personalScheduleHint') }}</div>
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
import { usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppCard from '@/Components/AppCard.vue'
import AppButton from '@/Components/AppButton.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    nextRegistration: Object,
    latestSports: Array,
})

const { t } = useI18n()
const page = usePage()
const authUser = computed(() => page.props.auth?.user ?? null)

const nextTrainingStatus = computed(() => {
    if (!props.nextRegistration?.training) return 'planned'

    const training = props.nextRegistration.training
    if (training.is_cancelled) return 'cancelled'
    if (training.is_completed) return 'completed'

    return training.date > new Date().toISOString().slice(0, 10) ? 'planned' : 'active'
})
</script>
