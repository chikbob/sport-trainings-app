<template>
    <nav class="pagination" v-if="paginationLinks.length > 1">
        <template v-for="(link, index) in paginationLinks" :key="`${index}-${link.label}`">
            <span
                v-if="!link.url"
                class="pagination__link pagination__link--disabled"
                v-html="link.displayLabel"
            />

            <Link
                v-else
                class="pagination__link"
                :class="{ 'pagination__link--active': link.active }"
                :href="link.url"
                preserve-scroll
                preserve-state
                v-html="link.displayLabel"
            />
        </template>
    </nav>
</template>

<script setup>
import { computed } from 'vue'
import { Link } from '@inertiajs/vue3'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    links: {
        type: Array,
        default: () => [],
    },
})

const { t } = useI18n()

const normalizeUrl = (url) => {
    if (!url) return null

    try {
        const base = typeof window !== 'undefined' ? window.location.origin : 'http://localhost'
        const normalized = new URL(url, base)

        return `${normalized.pathname}${normalized.search}${normalized.hash}`
    } catch {
        return url
    }
}

const paginationLinks = computed(() => props.links.map((link) => ({
    ...link,
    url: normalizeUrl(link.url),
    displayLabel: labelFor(link.label),
})))

const labelFor = (label) => {
    if (!label) return ''
    const lower = label.toLowerCase()
    if (lower.includes('previous') || lower.includes('prev') || label.includes('«')) {
        return t('pagination.previous')
    }
    if (lower.includes('next') || label.includes('»')) {
        return t('pagination.next')
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
}

.pagination__link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 40px;
    padding: 8px 12px;
    border-radius: 8px;
    border: 1px solid #d1d5db;
    text-decoration: none;
    color: #1e293b;
    background: #fff;
    font-weight: 600;
    transition: 0.2s ease;
}

.pagination__link:hover {
    background: #f1f5f9;
}

.pagination__link--active {
    background: #2563eb;
    color: #fff;
    border-color: #2563eb;
}

.pagination__link--disabled {
    pointer-events: none;
    opacity: 0.5;
}
</style>
