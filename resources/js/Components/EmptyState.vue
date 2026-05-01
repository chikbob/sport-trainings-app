<template>
    <div class="ui-empty">
        <h3 class="ui-empty__title">{{ title }}</h3>
        <p v-if="description" class="ui-empty__description">{{ description }}</p>
        <div v-if="$slots.actions" class="ui-inline-actions">
            <slot name="actions" />
        </div>
    </div>
</template>

<script setup>
defineProps({
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
