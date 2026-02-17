<template>
    <AppLayout>
        <div class="trainings-create">
            <h2 class="trainings-create__title">Додати тренування</h2>

            <form @submit.prevent="submit" class="trainings-create__form">
                <div class="trainings-create__field">
                    <label for="sport_id" class="trainings-create__label">Секція</label>
                    <select
                        id="sport_id"
                        v-model="form.sport_id"
                        class="trainings-create__select"
                    >
                        <option value="" disabled>Оберіть секцію</option>
                        <option v-for="sport in sports" :key="sport.id" :value="sport.id">
                            {{ sport.name }}
                        </option>
                    </select>
                    <p v-if="errors.sport_id" class="trainings-create__error">
                        {{ errors.sport_id }}
                    </p>
                </div>

                <div class="trainings-create__field">
                    <label for="date" class="trainings-create__label">Дата</label>
                    <input
                        id="date"
                        type="date"
                        v-model="form.date"
                        class="trainings-create__input"
                    />
                    <p v-if="errors.date" class="trainings-create__error">
                        {{ errors.date }}
                    </p>
                </div>

                <div class="trainings-create__field">
                    <label for="time" class="trainings-create__label">Час</label>
                    <input
                        id="time"
                        type="time"
                        v-model="form.time"
                        class="trainings-create__input"
                    />
                    <p v-if="errors.time" class="trainings-create__error">
                        {{ errors.time }}
                    </p>
                </div>

                <div class="trainings-create__field">
                    <label for="place" class="trainings-create__label">Місце (необов’язково)</label>
                    <input
                        id="place"
                        type="text"
                        v-model="form.place"
                        class="trainings-create__input"
                    />
                </div>

                <div class="trainings-create__field">
                    <label for="notes" class="trainings-create__label">Примітки (необов’язково)</label>
                    <textarea
                        id="notes"
                        v-model="form.notes"
                        class="trainings-create__textarea"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    class="trainings-create__button"
                    :disabled="processing"
                >
                    Додати
                </button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { reactive, computed } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const page = usePage()
const sports = page.props.sports || []

const form = useForm({
    sport_id: '',
    date: '',
    time: '',
    place: '',
    notes: '',
})

const errors = computed(() => form.errors)
const processing = computed(() => form.processing)

function submit() {
    form.post('/trainings', {
        onSuccess: () => form.reset(),
    })
}
</script>

<style lang="scss" scoped>
.trainings-create {
    max-width: 480px;
    margin: 20px auto 0;
    padding: 2rem;
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(15, 23, 42, 0.08);

    &__title {
        font-size: 1.8rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    &__form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    &__field {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    &__label {
        font-size: 0.95rem;
        font-weight: 600;
    }

    &__input,
    &__select,
    &__textarea {
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.95rem;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;

        &:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 1px rgba(37, 99, 235, 0.35);
        }
    }

    &__textarea {
        min-height: 80px;
        resize: vertical;
    }

    &__error {
        font-size: 0.8rem;
        color: #dc2626;
    }

    &__button {
        align-self: flex-start;
        margin-top: 0.5rem;
        padding: 10px 20px;
        border-radius: 6px;
        border: none;
        background: #2563eb;
        color: #ffffff;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.1s ease, box-shadow 0.1s ease;

        &:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
            transform: translateY(-1px);
        }

        &:disabled {
            opacity: 0.6;
            cursor: default;
            box-shadow: none;
            transform: none;
        }
    }
}
</style>
