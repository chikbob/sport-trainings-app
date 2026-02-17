<template>
    <AppLayout>
        <div class="sports-create">
            <h2 class="sports-create__title">Додати нову секцію</h2>

            <form @submit.prevent="submit" class="sports-create__form">
                <input
                    v-model="form.name"
                    type="text"
                    placeholder="Назва секції"
                    class="sports-create__input"
                />
                <textarea
                    v-model="form.description"
                    placeholder="Опис"
                    class="sports-create__textarea"
                ></textarea>
                <input
                    v-model="form.coach_name"
                    type="text"
                    placeholder="Ім'я тренера"
                    class="sports-create__input"
                />
                <input
                    v-model="form.location"
                    type="text"
                    placeholder="Локація"
                    class="sports-create__input"
                />

                <button type="submit" class="sports-create__button">Зберегти</button>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = ref({
    name: '',
    description: '',
    coach_name: '',
    location: ''
})

const submit = () => {
    router.post('/sports', form.value)
}
</script>

<style lang="scss" scoped>
.sports-create {
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

    &__input,
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
    }
}
</style>
