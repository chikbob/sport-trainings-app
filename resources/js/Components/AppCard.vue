<template>
    <section :class="classes">
        <header v-if="title || subtitle || $slots.header" class="ui-card__header">
            <div>
                <h3 v-if="title" class="ui-card__title">{{ title }}</h3>
                <p v-if="subtitle" class="ui-card__description">{{ subtitle }}</p>
            </div>

            <slot name="header" />
        </header>

        <div class="ui-card__body">
            <slot />
        </div>
    </section>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    subtitle: {
        type: String,
        default: '',
    },
    soft: {
        type: Boolean,
        default: false,
    },
})

const classes = computed(() => [
    'ui-card',
    props.soft ? 'ui-card--soft' : null,
])
</script>
