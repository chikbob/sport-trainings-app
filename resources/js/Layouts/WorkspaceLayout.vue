<template>
    <div class="workspace">
        <aside :class="['workspace__sidebar', { 'is-open': mobileOpen }]">
            <div class="workspace__brand">
                <span class="workspace__brand-name">{{ brand }}</span>
                <span class="workspace__brand-meta">{{ section }}</span>
            </div>

            <nav class="workspace__nav">
                <Link
                    v-for="item in navigation"
                    :key="item.href"
                    :href="item.href"
                    :class="['workspace__nav-link', { 'workspace__nav-link--active': item.active }]"
                    @click="mobileOpen = false"
                >
                    {{ item.label }}
                </Link>
            </nav>

            <div class="workspace__footer">
                <AppButton href="/" variant="secondary" size="sm">
                    {{ backLabel }}
                </AppButton>
                <AppButton href="/logout" method="post" variant="danger" size="sm">
                    {{ logoutLabel }}
                </AppButton>
            </div>
        </aside>

        <div :class="['workspace__content', { 'is-sidebar-open': mobileOpen }]">
            <header class="workspace__topbar">
                <div class="workspace__topbar-meta">
                    <button
                        type="button"
                        class="ui-button ui-button--secondary ui-button--sm workspace__menu-toggle"
                        @click="mobileOpen = !mobileOpen"
                    >
                        Menu
                    </button>
                    <span class="workspace__topbar-label">{{ section }}</span>
                    <span class="workspace__topbar-title">{{ title }}</span>
                </div>

                <div class="workspace__topbar-actions">
                    <slot name="actions" />

                    <div class="workspace__user" v-if="user">
                        <div class="workspace__user-meta">
                            <span class="workspace__user-name">{{ user.name }}</span>
                            <span class="workspace__user-role">{{ user.role }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <main class="workspace__body">
                <div class="ui-page">
                    <slot />
                </div>
            </main>
        </div>
    </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import AppButton from '@/Components/AppButton.vue'

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    section: {
        type: String,
        required: true,
    },
    brand: {
        type: String,
        required: true,
    },
    navigation: {
        type: Array,
        default: () => [],
    },
    backLabel: {
        type: String,
        default: 'Back to site',
    },
    logoutLabel: {
        type: String,
        default: 'Log out',
    },
})

const page = usePage()
const mobileOpen = ref(false)

const user = computed(() => page.props.auth?.user ?? null)
</script>
