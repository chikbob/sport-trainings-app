<template>
    <label :class="['ui-field', full ? 'ui-field--full' : null]">
        <span v-if="label" class="ui-field__label">{{ label }}</span>
        <span v-if="hint" class="ui-field__hint">{{ hint }}</span>

        <component
            :is="tag"
            v-bind="$attrs"
            :value="modelValue"
            :class="inputClass"
            @input="updateValue"
            @change="updateValue"
        >
            <slot />
        </component>

        <span v-if="error" class="ui-field__error">{{ error }}</span>
    </label>
</template>

<script setup>
import { computed } from 'vue'

defineOptions({
    inheritAttrs: false,
})

const props = defineProps({
    as: {
        type: String,
        default: 'input',
    },
    modelValue: {
        type: [String, Number],
        default: '',
    },
    label: {
        type: String,
        default: '',
    },
    hint: {
        type: String,
        default: '',
    },
    error: {
        type: String,
        default: '',
    },
    full: {
        type: Boolean,
        default: false,
    },
})

const emit = defineEmits(['update:modelValue'])

const tag = computed(() => props.as)
const inputClass = computed(() => {
    if (props.as === 'textarea') return 'ui-textarea'
    if (props.as === 'select') return 'ui-select'
    return 'ui-input'
})

const updateValue = (event) => {
    emit('update:modelValue', event.target.value)
}
</script>
