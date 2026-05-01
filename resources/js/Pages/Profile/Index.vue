<template>
    <AppLayout>
        <div class="ui-page">
            <PageHeader :title="t('profile.title')" :description="t('profile.searchPlaceholder')" />

            <AppCard>
                <div class="filters">
                    <AppInput
                        v-model="search"
                        :label="t('admin.users.search')"
                        :placeholder="t('profile.searchPlaceholder')"
                    />
                    <AppInput v-model="filterStatus" :label="t('profile.allStatuses')" as="select">
                        <option value="">{{ t('profile.allStatuses') }}</option>
                        <option value="pending">{{ t('profile.status.pending') }}</option>
                        <option value="approved">{{ t('profile.status.approved') }}</option>
                        <option value="cancelled">{{ t('profile.status.cancelled') }}</option>
                        <option value="rejected">{{ t('profile.status.rejected') }}</option>
                        <option value="attended">{{ t('profile.status.attended') }}</option>
                        <option value="no_show">{{ t('profile.status.no_show') }}</option>
                    </AppInput>
                    <AppInput v-model="sortOrder" :label="t('trainings.title')" as="select">
                        <option value="desc">{{ t('profile.sortDesc') }}</option>
                        <option value="asc">{{ t('profile.sortAsc') }}</option>
                    </AppInput>
                    <label class="ui-switch">
                        <span class="ui-switch__copy">
                            <span class="ui-field__label">{{ t('profile.showArchive') }}</span>
                            <span class="ui-field__hint">{{ t('profile.showArchiveHint') }}</span>
                        </span>
                        <span :class="['ui-switch__track', { 'is-active': showArchive }]">
                            <input v-model="showArchive" class="ui-switch__input" type="checkbox" />
                            <span class="ui-switch__thumb" />
                        </span>
                    </label>
                </div>
            </AppCard>

            <EmptyState
                v-if="filteredRegistrations.length === 0"
                :title="t('profile.title')"
                :description="t('profile.empty')"
            />

            <div v-else class="ui-grid">
                <AppCard
                    v-for="registration in filteredRegistrations"
                    :key="registration.id"
                    :title="registration.training.sport.name"
                    :subtitle="registration.training.place || t('home.notSpecified')"
                    :soft="!isPastTraining(registration)"
                >
                    <div class="ui-list">
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ $formatDate(registration.training.date) }}</div>
                                <div class="ui-list-item__meta">{{ $formatTime(registration.training.time) }}</div>
                            </div>
                            <StatusBadge :value="registration.status" />
                        </div>
                    </div>

                    <div class="ui-inline-actions" style="margin-top: 20px;">
                        <AppButton
                            v-if="!isPastTraining(registration) && registration.status !== 'cancelled'"
                            type="button"
                            variant="danger"
                            @click="cancelRegistration(registration.id)"
                        >
                            {{ t('profile.cancel') }}
                        </AppButton>
                        <AppButton
                            v-else-if="!isPastTraining(registration)"
                            type="button"
                            @click="reRegister(registration.training.id)"
                        >
                            {{ t('profile.reRegister') }}
                        </AppButton>
                        <StatusBadge
                            v-else
                            value="completed"
                            kind="training"
                            :label="t('profile.archived')"
                        />
                    </div>
                </AppCard>
            </div>

            <PaginationLinks
                v-if="props.registrations?.links?.length > 1"
                :links="props.registrations.links"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import PaginationLinks from '@/Components/PaginationLinks.vue'
import StatusBadge from '@/Components/StatusBadge.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    registrations: Object,
})

const search = ref('')
const filterStatus = ref('')
const sortOrder = ref('desc')
const showArchive = ref(false)
const { t } = useI18n()

const registrationsList = computed(() => props.registrations?.data || [])

const filteredRegistrations = computed(() => registrationsList.value
    .filter((registration) => {
        const searchText = search.value.toLowerCase()
        const sportName = registration.training.sport.name.toLowerCase()
        const place = (registration.training.place || '').toLowerCase()
        const statusMatch = filterStatus.value ? registration.status === filterStatus.value : true
        const searchMatch = sportName.includes(searchText) || place.includes(searchText)
        const archiveMatch = showArchive.value ? isPastTraining(registration) : !isPastTraining(registration)

        return statusMatch && searchMatch && archiveMatch
    })
    .sort((a, b) => {
        const first = new Date(`${a.training.date}T${a.training.time}`)
        const second = new Date(`${b.training.date}T${b.training.time}`)

        return sortOrder.value === 'asc' ? first - second : second - first
    }))

function isPastTraining(registration) {
    return new Date(`${registration.training.date}T${registration.training.time}`) < new Date()
}

function cancelRegistration(registrationId) {
    if (!confirm(t('profile.confirmCancel'))) return

    router.delete(route('registrations.cancel', registrationId), {
        preserveScroll: true,
    })
}

function reRegister(trainingId) {
    if (!confirm(t('profile.confirmReRegister'))) return

    router.post(route('trainings.reregister', trainingId), {}, {
        preserveScroll: true,
    })
}
</script>
