<template>
    <header class="header">
        <div class="container header__inner">
            <div class="header__left">
                <nav class="header__nav">
                    <Link href="/" class="header__logo">
                        <img :src="logo" alt="logo" class="header__logo-img" />
                        <span class="header__title">{{ appName }}</span>
                    </Link>
                    <Link href="/sports" :class="isActive('/sports')">Секції</Link>
                    <Link href="/trainings" :class="isActive('/trainings')">Розклад</Link>
                    <Link v-if="isAdmin" href="/admin/users" :class="isActive('/participants')">
                        Учасники
                    </Link>
                </nav>
            </div>

            <div class="header__right">
                <template v-if="authUser">
                    <Link href="/profile" class="header__profile-link">Профіль</Link>
                    <span class="header__username">{{ authUser.name }}</span>
                    <Link href="/logout" method="post" class="header__logout-btn">Вийти</Link>
                </template>
                <template v-else>
                    <Link href="/login" class="header__auth-link">Увійти</Link>
                    <Link href="/register" class="header__auth-link header__auth-link--accent">Реєстрація</Link>
                </template>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import logo from '@/images/logo.png'

const page = usePage()
const props = computed(() => page.props || {})

const authUser = computed(() => props.value.auth?.user ?? null)
const appName = computed(() => props.value.appName ?? import.meta.env.VITE_APP_NAME ?? 'Sport Portal')
const isAdmin = computed(() => authUser.value?.role === 'admin')

function isActive(path) {
    return window.location.pathname === path
        ? 'header__nav-link header__nav-link--active'
        : 'header__nav-link'
}
</script>

<style lang="scss" scoped>
.header {
    background: #fff;
    margin: 0 auto;
    padding: 0 24px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);

    &__inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
    }

    &__logo {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: #000;

        &-img {
            height: 32px;
            margin-right: 8px;
        }

        & .header__title {
            font-size: 20px;
            font-weight: 700;
        }
    }

    &__nav {
        margin-left: 24px;

        &-link {
            margin-right: 16px;
            text-decoration: none;
            color: #555;
            font-weight: 500;

            &:hover {
                color: #007bff;
            }

            &--active {
                color: #007bff;
                font-weight: 600;
            }
        }
    }

    &__right {
        display: flex;
        align-items: center;
    }

    &__auth-link {
        text-decoration: none;
        color: #007bff;
        margin-left: 10px;

        &--accent {
            font-weight: 600;
        }
    }

    &__logout-btn {
        background: #e53935;
        color: #fff;
        padding: 6px 12px;
        border-radius: 4px;
        text-decoration: none;
        font-size: 14px;

        &:hover {
            background: #c62828;
        }
    }

    &__username {
        font-size: 14px;
        color: #333;
        margin-right: 10px;
    }

    /* Новый стиль для ссылки Профіль */
    &__profile-link {
        margin-right: 10px;
        text-decoration: none;
        color: #007bff;
        font-weight: 600;
        cursor: pointer;

        &:hover {
            color: #0056b3;
        }
    }
}
</style>
