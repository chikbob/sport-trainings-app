<template>
    <footer class="footer">
        <div class="container footer__inner">
            <div class="footer__text">
                © {{ new Date().getFullYear() }} {{ appName }}. Всі права захищені.
            </div>
            <div class="footer__links">
                <a href="/about" class="footer__link">Про портал</a>
                <a href="/contacts" class="footer__link">Контакти</a>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()
const props = computed(() => page.props || {})

const appName = computed(() => props.value.appName ?? import.meta.env.VITE_APP_NAME ?? 'Sport Portal')
</script>

<style lang="scss" scoped>
.footer {
    background: #fff;
    border-top: 1px solid #ddd;
    padding: 16px 0;
    font-size: 14px;
    color: #555;

    &__inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    &__links {
        display: flex;
        gap: 16px;
    }

    &__link {
        color: #007bff;
        text-decoration: none;

        &:hover {
            text-decoration: underline;
        }
    }
}
</style>
