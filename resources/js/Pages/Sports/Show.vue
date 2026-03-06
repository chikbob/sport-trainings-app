<template>
    <AppLayout>
        <div class="sport-show p-8">
            <h1 class="sport-show__title">{{ sport.name }}</h1>
            <p class="sport-show__description">{{ sport.description }}</p>
            <p><strong>{{ t('sports.trainer') }}:</strong> {{ sport.coach?.user?.name || '—' }}</p>
            <p><strong>{{ t('sports.location') }}:</strong> {{ sport.location }}</p>

            <h2 class="sport-show__trainings-title mt-8 mb-4">{{ t('sport.trainings') }}</h2>

            <div v-if="sport.trainings.length === 0">
                {{ t('sport.emptyTrainings') }}
            </div>

            <ul class="sport-show__trainings-list">
                <li
                    v-for="training in sport.trainings"
                    :key="training.id"
                    class="sport-show__training"
                >
                    <div>
                        <strong>Дата:</strong> {{ $formatDate(training.date) }}
                        <strong>Час:</strong> {{ training.time }}
                        <strong>Місце:</strong> {{ training.place }}
                    </div>

                    <button
                        v-if="authUser && !training.isRegistered"
                        class="btn btn--primary"
                        @click="register(training.id)"
                    >
                        {{ t('sport.register') }}
                    </button>

                    <span
                        v-else-if="authUser && training.isRegistered"
                        class="text-green-600 font-semibold"
                    >
                        {{ t('sport.alreadyRegistered') }}
                    </span>

                    <a
                        v-else
                        href="/login"
                        class="btn btn--secondary"
                    >
                        {{ t('sport.loginToRegister') }}
                    </a>
                </li>
            </ul>
        </div>
    </AppLayout>
</template>

<script setup>
import {computed} from 'vue'
import {usePage, router} from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
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

<style scoped lang="scss">
.sport-show {
    max-width: 1240px;
    margin: 0 auto;
    padding: 24px;

    &__title {
        font-size: 2rem;
        font-weight: 700;
    }

    &__description {
        margin-top: 0.5rem;
        color: #555;
    }

    &__trainings-title {
        font-size: 1.5rem;
        font-weight: 600;
    }

    &__trainings-list {
        list-style: none;
        padding: 0;

        & > li {
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fafafa;
            padding: 10px 15px;
            border-radius: 6px;
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.1);
        }
    }
}

.btn {
    &--primary {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;

        &:hover {
            background-color: #0056b3;
        }
    }

    &--secondary {
        background-color: #6c757d;
        color: #fff;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        display: inline-block;

        &:hover {
            background-color: #5a6268;
        }
    }
}
</style>
