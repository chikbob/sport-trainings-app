<template>
    <header class="topbar">
        <div class="topbar__title">
            Панель адміністратора
        </div>

        <div class="topbar__right">
            <span class="topbar__user">
                {{ user.name }}
            </span>

            <button
                class="topbar__logout"
                @click="logout"
            >
                Вийти
            </button>
        </div>
    </header>
</template>

<script setup>
import {usePage, router} from '@inertiajs/vue3'

const page = usePage()
const user = page.props.auth.user

const logout = () => {
    router.post('/logout')
}
</script>

<style scoped lang="scss">
.topbar {
    height: 70px;
    background: white;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 40px;

    &__title {
        font-size: 18px;
        font-weight: 600;
        color: #1e293b;
    }

    &__right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    &__user {
        font-size: 14px;
        color: #475569;
    }

    &__logout {
        background: #ef4444;
        border: none;
        padding: 6px 14px;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        transition: 0.2s;

        &:hover {
            background: #dc2626;
        }
    }
}
</style>
