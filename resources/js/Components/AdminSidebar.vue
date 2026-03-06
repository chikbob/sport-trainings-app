<template>
    <aside class="sidebar">
        <div class="sidebar__logo">
            {{ t('admin.common.logo') }}
        </div>

        <nav class="sidebar__nav">
            <Link href="/admin" class="sidebar__link">{{ t('admin.sidebar.dashboard') }}</Link>
            <Link href="/admin/sports" class="sidebar__link">{{ t('admin.sidebar.sports') }}</Link>
            <Link href="/admin/trainings" class="sidebar__link">{{ t('admin.sidebar.trainings') }}</Link>
            <Link href="/admin/users" class="sidebar__link">{{ t('admin.sidebar.users') }}</Link>
            <Link href="/admin/registrations" class="sidebar__link">{{ t('admin.sidebar.registrations') }}</Link>
            <Link href="/admin/coaches" class="sidebar__link">{{ t('admin.sidebar.coaches') }}</Link>
        </nav>

        <div class="sidebar__footer">
            <button
                class="sidebar__back"
                @click="logout"
            >
                ← {{ t('admin.header.back') }}
            </button>
        </div>
    </aside>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'

const { t } = useI18n()

const logout = () => {
    router.post('/logout')
}
</script>

<style scoped lang="scss">
.sidebar {
    width: 260px;
    background: #0f172a;
    color: white;
    display: flex;
    flex-direction: column;

    &__logo {
        padding: 24px;
        font-size: 20px;
        font-weight: 700;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    &__nav {
        flex: 1;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    &__link {
        padding: 10px 14px;
        border-radius: 8px;
        text-decoration: none;
        color: #cbd5e1;
        transition: 0.2s ease;

        &:hover {
            background: #1e293b;
            color: white;
        }
    }

    &__footer {
        padding: 20px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    &__back {
        font-size: 14px;
        color: #94a3b8;
        background: transparent;
        cursor: pointer;
        border: 0;
        text-decoration: none;

        &:hover {
            color: white;
        }
    }
}
</style>
