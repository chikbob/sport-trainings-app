<template>
    <AppLayout>
        <div class="auth">
            <h2 class="auth__title">Реєстрація</h2>
            <form @submit.prevent="submit" class="auth__form">
                <input v-model="form.name" type="text" placeholder="Ім'я" class="auth__input"/>
                <input v-model="form.email" type="email" placeholder="Email" class="auth__input"/>
                <input v-model="form.password" type="password" placeholder="Пароль" class="auth__input"/>
                <input v-model="form.password_confirmation" type="password" placeholder="Підтвердження паролю"
                       class="auth__input"/>
                <button type="submit" class="auth__button">Зареєструватись</button>
            </form>
            <p class="auth__link">
                Вже маєте акаунт?
                <Link href="/login">Увійти</Link>
            </p>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref} from 'vue'
import {router, Link} from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue";

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})

function submit() {
    router.post('/register', form.value)
}
</script>

<style lang="scss" scoped>
.auth {
    max-width: 400px;
    margin: 60px auto;
    display: flex;
    flex-direction: column;
    align-items: center;

    &__title {
        font-size: 1.8rem;
        font-weight: bold;
        margin-bottom: 1rem;
    }

    &__form {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    &__input {
        margin-bottom: 0.8rem;
        padding: 0.6rem;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    &__button {
        background: #007bff;
        color: white;
        padding: 0.6rem;
        border-radius: 5px;
        border: none;
        cursor: pointer;
    }

    &__link {
        margin-top: 1rem;
    }
}
</style>
