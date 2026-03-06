<template>
    <AppLayout>
        <div class="home max-w-4xl mx-auto p-8">
            <h1 class="home__title">{{ t('home.title') }}</h1>
            <p class="home__subtitle">
                {{ t('home.subtitle') }}
            </p>

            <!-- Ближайшая тренировка -->
            <div v-if="nextRegistration" class="home__card fade">
                <h2 class="home__card-title">{{ t('home.nextTraining') }}</h2>

                <div class="home__info">
                    <div><b>{{ t('home.section') }}:</b> {{ nextRegistration.training.sport.name }}</div>
                    <div><b>{{ t('home.date') }}:</b> {{ $formatDate(nextRegistration.training.date) }}</div>
                    <div><b>{{ t('home.time') }}:</b> {{ nextRegistration.training.time }}</div>
                    <div><b>{{ t('home.place') }}:</b> {{ nextRegistration.training.place || t('home.notSpecified') }}</div>
                </div>

                <a
                    :href="route('trainings.show', nextRegistration.training.id)"
                    class="home__btn"
                >
                    {{ t('home.goToTraining') }}
                </a>
            </div>

            <!-- Последние секции -->
            <div v-else-if="latestSports?.length" class="home__sections fade">
                <h2 class="home__card-title">{{ t('home.latestSports') }}</h2>

                <div class="home__sections-list">
                    <div
                        v-for="sport in latestSports"
                        :key="sport.id"
                        class="home__section-card"
                    >
                        <h3 class="home__section-name">{{ sport.name }}</h3>
                        <p class="home__section-desc">{{ sport.description || t('home.noDescription') }}</p>

                        <a :href="route('sports.show', sport.id)" class="home__btn-small">
                            {{ t('home.details') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { useI18n } from '@/i18n/useI18n'

defineProps({
    nextRegistration: Object,
    latestSports: Array,
});

const { t } = useI18n()
</script>

<style lang="scss" scoped>
.home {
    text-align: center;
    max-width: 1240px;
    margin: 0 auto;
    padding: 24px;

    &__title {
        font-size: 32px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 10px;
    }

    &__subtitle {
        font-size: 16px;
        color: #64748b;
        margin-bottom: 40px;
    }

    /* Карточки */
    &__card,
    &__section-card {
        background: #ffffff;
        border-radius: 14px;
        padding: 24px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
        transition: box-shadow 0.3s ease, transform 0.2s ease;
        text-align: left;

        &:hover {
            box-shadow: 0 7px 18px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }
    }

    &__card-title {
        font-size: 22px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        text-align: left;
    }

    &__info {
        font-size: 15px;
        color: #334155;
        line-height: 1.6;
        margin-bottom: 20px;

        div {
            margin-bottom: 6px;
        }
    }

    /* Большая кнопка */
    &__btn {
        display: inline-block;
        background: #2563eb;
        color: white;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.25s ease;

        &:hover {
            background: #1d4ed8;
        }
    }

    /* Маленькая кнопка */
    &__btn-small {
        background: #0ea5e9;
        color: white;
        padding: 8px 14px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
        margin-top: 12px;
        text-decoration: none;
        transition: background 0.25s ease;

        &:hover {
            background: #0284c7;
        }
    }

    /* Секции */
    &__sections-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }

    &__section-name {
        font-size: 18px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
    }

    &__section-desc {
        color: #475569;
        font-size: 14px;
        margin-bottom: 10px;
        min-height: 40px;
    }
}

/* Плавное появление */
.fade {
    animation: fadeIn 0.4s ease;
}
@keyframes fadeIn {
    from {
        opacity: 0;
        translate: 0 5px;
    }
    to {
        opacity: 1;
        translate: 0 0;
    }
}
</style>
