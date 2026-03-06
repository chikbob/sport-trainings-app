<template>
    <AppLayout>
        <div class="auth">
            <h2 class="auth__title">{{ t('auth.registerTitle') }}</h2>
            <div v-if="hasErrors" class="auth__errors">
                <div v-for="(msg, key) in errors" :key="key" class="auth__error">
                    {{ msg }}
                </div>
            </div>
            <form @submit.prevent="submit" class="auth__form">
                <input v-model="form.name" type="text" :placeholder="t('auth.name')" class="auth__input"/>
                <input v-model="form.email" type="email" :placeholder="t('auth.email')" class="auth__input"/>
                <input v-model="form.password" type="password" :placeholder="t('auth.password')" class="auth__input"/>
                <input v-model="form.password_confirmation" type="password" :placeholder="t('auth.passwordConfirm')"
                       class="auth__input"/>
                <button type="submit" class="auth__button">{{ t('auth.register') }}</button>
            </form>
            <p class="auth__link">
                {{ t('auth.haveAccount') }}
                <Link href="/login">{{ t('auth.login') }}</Link>
            </p>
        </div>
    </AppLayout>
</template>

<script setup>
import {ref, computed} from 'vue'
import {router, Link, usePage} from '@inertiajs/vue3'
import AppLayout from "@/Layouts/AppLayout.vue";
import { useI18n } from '@/i18n/useI18n'

const form = ref({
    name: '',
    email: '',
    password: '',
    password_confirmation: ''
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
const { t } = useI18n()

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

    &__errors {
        width: 100%;
        background: #fee2e2;
        color: #b91c1c;
        border: 1px solid #fecaca;
        border-radius: 6px;
        padding: 10px 12px;
        margin-bottom: 12px;
        font-size: 0.9rem;
    }

    &__error + &__error {
        margin-top: 4px;
    }

    &__link {
        margin-top: 1rem;
    }
}
</style>
