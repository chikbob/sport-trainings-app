<template>
    <AppLayout>
        <div class="profile-page max-w-3xl mx-auto p-6">
            <h1 class="profile-page__title">Мої записи на тренування</h1>

            <div class="profile-page__controls">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Пошук за секцією або місцем..."
                    class="profile-page__search"
                />

                <select v-model="filterStatus" class="profile-page__select">
                    <option value="">Всі статуси</option>
                    <option value="pending">Очікує підтвердження</option>
                    <option value="approved">Підтверджено</option>
                    <option value="cancelled">Скасовано</option>
                    <option value="rejected">Відхилено</option>
                    <option value="attended">Відвідано</option>
                    <option value="no_show">Не з’явився</option>
                </select>

                <select v-model="sortOrder" class="profile-page__select">
                    <option value="desc">За датою (нові спочатку)</option>
                    <option value="asc">За датою (старі спочатку)</option>
                </select>

                <label>
                    <input type="checkbox" v-model="showArchive"/>
                    Показати архів
                </label>
            </div>

            <div v-if="filteredRegistrations.length === 0" class="profile-page__empty">
                Немає записів за вашим запитом.
            </div>

            <ul v-else class="profile-page__list">
                <li
                    v-for="registration in filteredRegistrations"
                    :key="registration.id"
                    class="profile-page__item"
                    :class="{ 'profile-page__item--archived': isPastTraining(registration) }"
                >
                    <div class="profile-page__info">
                        <div><b>Секція:</b> {{ registration.training.sport.name }}</div>
                        <div><b>Дата:</b> {{ $formatDate(registration.training.date) }}</div>
                        <div><b>Час:</b> {{ registration.training.time }}</div>
                        <div><b>Місце:</b> {{ registration.training.place || 'не вказано' }}</div>
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
                            Відмінити
                        </button>

                        <button
                            v-else
                            @click="reRegister(registration.training.id)"
                            class="profile-page__btn-re-register"
                        >
                            Записатися знову
                        </button>
                    </div>
                    <div style="color: #1e293b;" v-else>
                        Поміщено до архіву
                    </div>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, computed} from 'vue'
import {router} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    registrations: Array
})

const search = ref('')
const filterStatus = ref('')
const sortOrder = ref('desc')
const showArchive = ref(false);

const statusLabels = {
    pending: 'Очікує підтвердження',
    approved: 'Підтверджено',
    cancelled: 'Скасовано',
    rejected: 'Відхилено',
    attended: 'Відвідано',
    no_show: 'Не з’явився'
}

function getStatusLabel(status) {
    return statusLabels[status] || status
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

const filteredRegistrations = computed(() => {
    return props.registrations
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
    if (!confirm('Ви впевнені, що хочете скасувати цю реєстрацію?')) {
        return
    }

    router.delete(route('registrations.cancel', registrationId), {
        onSuccess: () => {
            alert('Реєстрацію скасовано.')
            // Можно перезагрузить страницу, чтобы обновить данные
            location.reload()
        },
        onError: () => {
            alert('Сталася помилка при скасуванні.')
        }
    })
}

function reRegister(trainingId) {
    if (!confirm('Ви впевнені, що хочете записатися знову на це тренування?')) {
        return;
    }

    router.post(route('trainings.reregister', trainingId), {
        onSuccess: () => {
            alert('Ви успішно записались знову!');
            location.reload();
        },
        onError: () => {
            alert('Сталася помилка при повторній реєстрації.');
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
