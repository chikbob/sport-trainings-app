<template>
    <header class="topbar">
        <div class="topbar__title">
            {{ t('admin.header.title') }}
        </div>

        <div class="topbar__right">
            <div class="topbar__lang">
                <label class="topbar__lang-label">{{ t('admin.language.label') }}</label>
                <select
                    class="topbar__lang-select"
                    v-model="currentLang"
                >
                    <option value="ru">{{ t('admin.language.ru') }}</option>
                    <option value="uk">{{ t('admin.language.uk') }}</option>
                    <option value="en">{{ t('admin.language.en') }}</option>
                </select>
            </div>
            <span class="topbar__user">
                {{ user.name }}
            </span>

            <button
                class="topbar__logout"
                @click="logout"
            >
                {{ t('admin.header.logout') }}
            </button>
        </div>
    </header>
</template>

<script setup>
import {usePage, router} from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'

const page = usePage()
const user = page.props.auth.user
const { t, currentLang } = useI18n()

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

    &__lang {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    &__lang-label {
        font-size: 12px;
        color: #64748b;
    }

    &__lang-select {
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 12px;
        background: #fff;
        color: #0f172a;
        -webkit-text-fill-color: #0f172a;
        appearance: auto;
    }

    &__lang-select option {
        color: #0f172a;
        background: #fff;
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
