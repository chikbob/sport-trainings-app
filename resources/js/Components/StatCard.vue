<template>
    <div class="ui-card ui-stat-card">
        <span class="ui-stat-card__label">{{ label }}</span>
        <strong class="ui-stat-card__value">{{ value }}</strong>
        <span v-if="hint" class="ui-stat-card__hint">{{ hint }}</span>
    </div>
</template>

<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: [String, Number],
        required: true,
    },
    hint: {
        type: String,
        default: '',
    },
})
</script>
