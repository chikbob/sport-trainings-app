<template>
    <header class="header">
        <div class="app-container header__inner">
            <Link href="/" class="header__logo">
                <span class="header__logo-mark">FC</span>
                <div class="header__brand">
                    <span class="header__title">{{ appName }}</span>
                    <span class="header__subtitle">{{ t('header.subtitle') }}</span>
                </div>
            </Link>

            <button type="button" class="header__menu" @click="mobileOpen = !mobileOpen">
                Menu
            </button>

            <div :class="['header__panel', { 'is-open': mobileOpen }]">
                <nav class="header__nav">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        :class="['header__nav-link', { 'header__nav-link--active': item.active }]"
                        @click="mobileOpen = false"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                <div class="header__right">
                    <select class="header__lang-select" v-model="currentLang">
                        <option value="uk">UA</option>
                        <option value="en">EN</option>
                    </select>

                    <template v-if="authUser">
                        <Link href="/profile" class="header__profile-link" @click="mobileOpen = false">
                            {{ t('header.profile') }}
                        </Link>
                        <span class="header__username">{{ authUser.name }}</span>
                        <Link href="/logout" method="post" class="header__logout-btn" @click="mobileOpen = false">
                            {{ t('header.logout') }}
                        </Link>
                    </template>

                    <template v-else>
                        <Link href="/login" class="header__auth-link" @click="mobileOpen = false">
                            {{ t('header.login') }}
                        </Link>
                        <Link href="/register" class="header__auth-link header__auth-link--accent" @click="mobileOpen = false">
                            {{ t('header.register') }}
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </header>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'

const page = usePage()
const props = computed(() => page.props || {})

const authUser = computed(() => props.value.auth?.user ?? null)
const appName = computed(() => props.value.appName ?? import.meta.env.VITE_APP_NAME ?? 'Sport Portal')
const isAdmin = computed(() => authUser.value?.role === 'admin')
const isCoach = computed(() => authUser.value?.role === 'coach')
const currentRoute = computed(() => props.value.currentRoute ?? '')
const { t, currentLang } = useI18n()
const mobileOpen = ref(false)

const navItems = computed(() => [
    { href: '/sports', label: t('header.sections'), active: currentRoute.value.startsWith('sports.') },
    { href: '/trainings', label: t('header.schedule'), active: currentRoute.value.startsWith('trainings.') },
    ...(isCoach.value ? [{ href: '/coach', label: t('header.coach'), active: currentRoute.value.startsWith('coach.') }] : []),
    ...(isAdmin.value ? [{ href: '/admin', label: t('header.participants'), active: currentRoute.value.startsWith('admin.') }] : []),
])
</script>

<style lang="scss" scoped>
.header {
    position: sticky;
    top: 0;
    z-index: 30;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(14px);
    border-bottom: 1px solid rgba(191, 204, 220, 0.7);

    &__inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        min-height: 78px;
    }

    &__logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: #0f172a;
        flex-shrink: 0;

        &-mark {
            height: 44px;
            width: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f766e 0%, #2563eb 100%);
            color: #fff;
            font-size: 15px;
            font-weight: 800;
            box-shadow: 0 14px 24px rgba(37, 99, 235, 0.18);
        }
    }

    &__brand {
        display: flex;
        flex-direction: column;
        line-height: 1.1;
    }

    &__title {
        font-size: 18px;
        font-weight: 800;
    }

    &__subtitle {
        font-size: 12px;
        color: #64748b;
    }

    &__menu {
        display: none;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 999px;
        border: 1px solid #d9e2ec;
        background: #fff;
        font-weight: 700;
    }

    &__panel {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex: 1;
    }

    &__nav {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    &__nav-link {
        display: inline-flex;
        align-items: center;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 999px;
        text-decoration: none;
        color: #42576d;
        font-weight: 700;
        transition: background-color 0.15s ease, color 0.15s ease;
    }

    &__nav-link:hover,
    &__nav-link--active {
        background: #eff6ff;
        color: #1d4ed8;
    }

    &__right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    &__lang-select {
        min-height: 40px;
        padding: 0 12px;
        border: 1px solid #d9e2ec;
        border-radius: 999px;
        font-size: 13px;
        background: #fff;
        color: #0f172a;
    }

    &__auth-link {
        display: inline-flex;
        align-items: center;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 999px;
        text-decoration: none;
        color: #1d4ed8;
        font-weight: 700;

        &--accent {
            background: #2563eb;
            color: #fff;
        }
    }

    &__logout-btn {
        display: inline-flex;
        align-items: center;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 999px;
        text-decoration: none;
        background: #dc2626;
        color: #fff;
        font-weight: 700;

        &:hover {
            background: #b91c1c;
        }
    }

    &__username {
        font-size: 14px;
        color: #475569;
        font-weight: 700;
    }

    &__profile-link {
        display: inline-flex;
        align-items: center;
        min-height: 40px;
        padding: 0 14px;
        border-radius: 999px;
        text-decoration: none;
        color: #0f172a;
        background: #f8fafc;
        border: 1px solid #d9e2ec;
        font-weight: 700;
    }
}

@media (max-width: 960px) {
    .header {
        &__menu {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        &__panel {
            position: absolute;
            left: 12px;
            right: 12px;
            top: calc(100% + 8px);
            display: none;
            flex-direction: column;
            align-items: stretch;
            padding: 16px;
            background: #fff;
            border: 1px solid rgba(191, 204, 220, 0.8);
            border-radius: 18px;
            box-shadow: 0 24px 48px rgba(15, 23, 42, 0.12);
        }

        &__panel.is-open {
            display: flex;
        }

        &__nav,
        &__right {
            width: 100%;
            justify-content: flex-start;
        }

        &__nav-link,
        &__profile-link,
        &__auth-link,
        &__logout-btn,
        &__lang-select {
            width: 100%;
            justify-content: center;
        }
    }
}
</style>
