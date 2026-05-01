<template>
    <Link
        v-if="href"
        :href="href"
        :method="method"
        :class="classes"
        :preserve-scroll="preserveScroll"
        :preserve-state="preserveState"
        @click="handleLinkClick"
    >
        <slot />
    </Link>

    <button
        v-else
        :type="type"
        :class="classes"
        :disabled="disabled || loading"
    >
        <slot />
    </button>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    href: {
        type: String,
        default: '',
    },
    method: {
        type: String,
        default: 'get',
    },
    variant: {
        type: String,
        default: 'primary',
    },
    size: {
        type: String,
        default: 'md',
    },
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    preserveScroll: {
        type: Boolean,
        default: false,
    },
    preserveState: {
        type: Boolean,
        default: false,
    },
})

const classes = computed(() => [
    'ui-button',
    `ui-button--${props.variant}`,
    props.size === 'sm' ? 'ui-button--sm' : null,
    props.disabled || props.loading ? 'is-disabled' : null,
])

const handleLinkClick = (event) => {
    if (props.disabled || props.loading) {
        event.preventDefault()
    }
}
</script>
