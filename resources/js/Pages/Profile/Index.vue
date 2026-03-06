<template>
    <AppLayout>
        <div class="profile-page max-w-3xl mx-auto p-6">
            <h1 class="profile-page__title">{{ t('profile.title') }}</h1>

            <div class="profile-page__controls">
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('profile.searchPlaceholder')"
                    class="profile-page__search"
                />

                <select v-model="filterStatus" class="profile-page__select">
                    <option value="">{{ t('profile.allStatuses') }}</option>
                    <option value="pending">{{ t('profile.status.pending') }}</option>
                    <option value="approved">{{ t('profile.status.approved') }}</option>
                    <option value="cancelled">{{ t('profile.status.cancelled') }}</option>
                    <option value="rejected">{{ t('profile.status.rejected') }}</option>
                    <option value="attended">{{ t('profile.status.attended') }}</option>
                    <option value="no_show">{{ t('profile.status.no_show') }}</option>
                </select>

                <select v-model="sortOrder" class="profile-page__select">
                    <option value="desc">{{ t('profile.sortDesc') }}</option>
                    <option value="asc">{{ t('profile.sortAsc') }}</option>
                </select>

                <label>
                    <input type="checkbox" v-model="showArchive"/>
                    {{ t('profile.showArchive') }}
                </label>
            </div>

            <div v-if="filteredRegistrations.length === 0" class="profile-page__empty">
                {{ t('profile.empty') }}
            </div>

            <ul v-else class="profile-page__list">
                <li
                    v-for="registration in filteredRegistrations"
                    :key="registration.id"
                    class="profile-page__item"
                    :class="{ 'profile-page__item--archived': isPastTraining(registration) }"
                >
                    <div class="profile-page__info">
                        <div><b>{{ t('home.section') }}:</b> {{ registration.training.sport.name }}</div>
                        <div><b>{{ t('home.date') }}:</b> {{ $formatDate(registration.training.date) }}</div>
                        <div><b>{{ t('home.time') }}:</b> {{ registration.training.time }}</div>
                        <div><b>{{ t('home.place') }}:</b> {{ registration.training.place || t('home.notSpecified') }}</div>
                        <div><b>Статус:</b>
                            <span :class="getStatusClass(registration.status)">
                             {{" " + getStatusLabel(registration.status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Если тренировка НЕ прошла -->
                    <div v-if="!isPastTraining(registration)">
                        <button
                            v-if="registration.status !== 'cancelled'"
                            @click="cancelRegistration(registration.id)"
                            class="profile-page__btn-cancel"
                        >
                            {{ t('profile.cancel') }}
                        </button>

                        <button
                            v-else
                            @click="reRegister(registration.training.id)"
                            class="profile-page__btn-re-register"
                        >
                            {{ t('profile.reRegister') }}
                        </button>
                    </div>
                    <div style="color: #1e293b;" v-else>
                        {{ t('profile.archived') }}
                    </div>
                </li>
            </ul>

            <PaginationLinks
                v-if="props.registrations?.links?.length > 1"
                class="profile-page__pagination"
                :links="props.registrations.links"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, computed} from 'vue'
import {router} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import PaginationLinks from '@/Components/PaginationLinks.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    registrations: Object
})

const search = ref('')
const filterStatus = ref('')
const sortOrder = ref('desc')
const showArchive = ref(false);

const { t } = useI18n()

const statusLabels = computed(() => ({
    pending: t('profile.status.pending'),
    approved: t('profile.status.approved'),
    cancelled: t('profile.status.cancelled'),
    rejected: t('profile.status.rejected'),
    attended: t('profile.status.attended'),
    no_show: t('profile.status.no_show')
}))

function getStatusLabel(status) {
    return statusLabels.value[status] || status
}

function getStatusClass(status) {
    return {
        'profile-page__status-pending': status === 'pending',
        'profile-page__status-approved': status === 'approved',
        'profile-page__status-cancelled': status === 'cancelled',
        'profile-page__status-rejected': status === 'rejected',
        'profile-page__status-attended': status === 'attended',
        'profile-page__status-no-show': status === 'no_show'
    }
}

const registrationsList = computed(() => props.registrations?.data || [])

const filteredRegistrations = computed(() => {
    return registrationsList.value
        .filter(r => {
            const searchText = search.value.toLowerCase();
            const sportName = r.training.sport.name.toLowerCase();
            const place = (r.training.place || '').toLowerCase();

            const statusMatch = filterStatus.value ? r.status === filterStatus.value : true;
            const searchMatch = sportName.includes(searchText) || place.includes(searchText);

            // Фильтрация по архиву — показываем записи с прошлой датой если showArchive, иначе будущие и текущие
            const isPast = new Date(r.training.date) < new Date();
            const archiveMatch = showArchive.value ? isPast : !isPast;

            return statusMatch && searchMatch && archiveMatch;
        })
        .sort((a, b) => {
            if (sortOrder.value === 'asc') {
                return new Date(a.training.date) - new Date(b.training.date);
            } else {
                return new Date(b.training.date) - new Date(a.training.date);
            }
        });
});

function isPastTraining(registration) {
    const trainingDateTime = new Date(
        registration.training.date + 'T' + registration.training.time
    )
    return trainingDateTime < new Date()
}

function cancelRegistration(registrationId) {
    if (!confirm(t('profile.confirmCancel'))) {
        return
    }

    router.delete(route('registrations.cancel', registrationId), {
        onSuccess: () => {
            alert(t('profile.cancelSuccess'))
            // Можно перезагрузить страницу, чтобы обновить данные
            location.reload()
        },
        onError: () => {
            alert(t('profile.cancelError'))
        }
    })
}

function reRegister(trainingId) {
    if (!confirm(t('profile.confirmReRegister'))) {
        return;
    }

    router.post(route('trainings.reregister', trainingId), {
        onSuccess: () => {
            alert(t('profile.reRegisterSuccess'));
            location.reload();
        },
        onError: () => {
            alert(t('profile.reRegisterError'));
        }
    });
}
</script>

<style lang="scss" scoped>
.profile-page {
    max-width: 1240px;
    margin: 0 auto;
    padding: 24px;

    &__title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 24px;
        color: #1e293b;
    }

    &__controls {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }

    &__search,
    &__select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 15px;
        min-width: 160px;
        flex-grow: 1;
    }

    &__list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    &__pagination {
        margin-top: 24px;
    }

    &__item {
        background: #fff;
        border-radius: 10px;
        padding: 20px 24px;
        box-shadow: 0 3px 8px rgb(0 0 0 / 0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: box-shadow 0.3s ease;
    }

    &__item--archived {
        opacity: 0.80;
        background: #f1f5f9;
        box-shadow: none;
        filter: grayscale(40%);
    }

    &__item:hover {
        box-shadow: 0 5px 14px rgb(0 0 0 / 0.15);
    }

    &__info {
        font-size: 15px;
        color: #334155;
        line-height: 1.5;
        max-width: 75%;
    }

    &__status-cancelled {
        color: #ef4444;
        font-weight: 600;
    }

    &__btn-cancel {
        background-color: #dc2626;
        color: #fff;
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.25s ease;
        flex-shrink: 0;
        user-select: none;
    }

    &__btn-cancel:hover {
        background-color: #b91c1c;
    }

    &__empty {
        text-align: center;
        font-size: 16px;
        color: #64748b;
        padding: 40px 0;
    }

    &__btn-re-register {
        background-color: #2563eb;
        color: #fff;
        padding: 10px 18px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.25s ease;
        flex-shrink: 0;
        user-select: none;

        &:hover {
            background-color: #1d4ed8;
        }
    }

    &__status-pending {
        color: #f59e0b;
        font-weight: 600;
    }

    &__status-approved {
        color: #16a34a;
        font-weight: 600;
    }

    &__status-cancelled {
        color: #ef4444;
        font-weight: 600;
    }

    &__status-rejected {
        color: #dc2626;
        font-weight: 600;
    }

    &__status-attended {
        color: #2563eb;
        font-weight: 600;
    }

    &__status-no-show {
        color: #7c3aed;
        font-weight: 600;
    }
}
</style>
