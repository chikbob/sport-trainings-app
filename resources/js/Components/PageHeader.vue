<template>
    <header class="ui-page-header">
        <div class="ui-page-header__content">
            <span v-if="eyebrow" class="ui-page-header__eyebrow">{{ eyebrow }}</span>
            <h1 class="ui-page-header__title">{{ title }}</h1>
            <p v-if="description" class="ui-page-header__description">{{ description }}</p>
        </div>

        <div v-if="$slots.actions" class="ui-inline-actions">
            <slot name="actions" />
        </div>
    </header>
</template>

<script setup>
defineProps({
    eyebrow: {
        type: String,
        default: '',
    },
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
})
</script>
