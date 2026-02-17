<template>
    <AppLayout>
        <div class="training-show">
            <h1 class="training-show__title">
                {{ training.sport.name }} —
                {{ $formatDate(training.date) }} {{ $formatTime(training.time) }}
            </h1>

            <div class="training-show__details">
                <p><strong>Дата:</strong> {{ $formatDate(training.date) }}</p>
                <p><strong>Час:</strong> {{ $formatTime(training.time) }}</p>
                <p><strong>Місце:</strong> {{ training.place || 'Не вказано' }}</p>
                <p><strong>Примітки:</strong> {{ training.notes || 'Немає' }}</p>
            </div>

            <!-- ✅ Уже записан -->
            <p v-if="isRegistered" class="training-show__info">
                Ви вже записані на це тренування
            </p>

            <!-- ✅ Кнопка записи -->
            <button
                v-else-if="authUser && !isPast"
                @click="register"
                class="btn btn--primary"
                :disabled="loading"
            >
                Записатися на тренування
            </button>

            <!-- ❌ Прошедшая тренировка -->
            <p v-else-if="isPast" class="training-show__info">
                Тренування вже відбулося
            </p>

            <!-- ❌ Неавторизован -->
            <p v-else>
                <a href="/login">Увійдіть</a>, щоб записатися на тренування.
            </p>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
    training: Object,
    isRegistered: Boolean,
})

const page = usePage()
const authUser = computed(() => page.props.auth?.user ?? null)
const loading = ref(false)

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
            onSuccess: () => alert('Ви успішно записались на тренування!'),
            onError: () => alert('Сталася помилка при реєстрації'),
        }
    )
}
</script>

<style scoped>
.training-show {
    max-width: 600px;
    margin: 2rem auto 0;
    padding: 20px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 0 12px rgba(0,0,0,0.1);
}

.training-show__title {
    font-size: 28px;
    margin-bottom: 16px;
}

.training-show__details p {
    font-size: 16px;
    margin: 6px 0;
}

.training-show__info {
    margin-top: 16px;
    font-weight: 600;
    color: #2563eb;
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

.btn--primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
