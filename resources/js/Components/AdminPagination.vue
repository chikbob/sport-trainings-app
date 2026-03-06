<template>
    <nav class="pagination" v-if="links && links.length > 1">
        <Link
            v-for="(link, i) in links"
            :key="i"
            class="pagination__link"
            :class="{ 'pagination__link--active': link.active, 'pagination__link--disabled': !link.url }"
            :href="link.url || ''"
            preserve-scroll
            preserve-state
            v-html="labelFor(link.label)"
        />
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'

defineProps({
    links: Array,
})

const { t } = useI18n()

const labelFor = (label) => {
    if (!label) return ''
    const lower = label.toLowerCase()
    if (lower.includes('previous') || lower.includes('prev') || label.includes('«')) {
        return t('admin.pagination.previous')
    }
    if (lower.includes('next') || label.includes('»')) {
        return t('admin.pagination.next')
    }
    return label
}
</script>

<style scoped lang="scss">
.pagination {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
    margin-top: 20px;
}

.pagination__link {
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    text-decoration: none;
    color: #1e293b;
    background: #fff;
    font-weight: 600;
    transition: 0.2s ease;
}

.pagination__link:hover {
    background: #e2e8f0;
}

.pagination__link--active {
    background: #0f172a;
    color: #fff;
    border-color: #0f172a;
}

.pagination__link--disabled {
    pointer-events: none;
    opacity: 0.5;
}
</style>
