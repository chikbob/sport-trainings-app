<template>
    <footer class="footer">
        <div class="app-container footer__inner">
            <div class="footer__brand">
                <strong>{{ appName }}</strong>
                <span>© {{ new Date().getFullYear() }} {{ t('footer.rights') }}</span>
            </div>
            <div class="footer__links">
                <Link href="/sports" class="footer__link">{{ t('header.sections') }}</Link>
                <Link href="/trainings" class="footer__link">{{ t('header.schedule') }}</Link>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'

const page = usePage()
const props = computed(() => page.props || {})

const appName = computed(() => props.value.appName ?? import.meta.env.VITE_APP_NAME ?? 'Sport Portal')
const { t } = useI18n()
</script>

<style lang="scss" scoped>
.footer {
    background: #ffffff;
    border-top: 1px solid rgba(191, 204, 220, 0.8);
    padding: 22px 0 28px;
    font-size: 14px;
    color: #556b82;

    &__inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
    }

    &__brand {
        display: flex;
        flex-direction: column;
        gap: 4px;

        strong {
            color: #0f172a;
        }
    }

    &__links {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
    }

    &__link {
        color: #1d4ed8;
        text-decoration: none;
        font-weight: 700;

        &:hover {
            color: #1e40af;
        }
    }
}
</style>
