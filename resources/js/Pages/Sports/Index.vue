<template>
    <AppLayout>
        <div class="sports-page max-w-4xl mx-auto p-6">
            <h2 class="sports-page__title">{{ t('sports.title') }}</h2>

            <div class="sports-page__controls">
                <input
                    v-model="search"
                    type="text"
                    :placeholder="t('sports.searchPlaceholder')"
                    class="sports-page__search"
                />

                <select v-model="sortOrder" class="sports-page__select">
                    <option value="asc">{{ t('sports.sortAsc') }}</option>
                    <option value="desc">{{ t('sports.sortDesc') }}</option>
                </select>

                <a
                    v-if="isAdmin"
                    href="/sports/create"
                    class="sports-page__btn-add"
                >
                    {{ t('sports.add') }}
                </a>
            </div>

            <div class="sports-page__grid">
                <div
                    v-for="sport in sportsList"
                    :key="sport.id"
                    class="sports-page__card"
                >
                    <h3 class="sports-page__name">{{ sport.name }}</h3>
                    <p class="sports-page__description">{{ sport.description }}</p>
                    <p><strong>{{ t('sports.trainer') }}:</strong> {{ sport.coach?.user?.name || '—' }}</p>
                    <p><strong>{{ t('sports.location') }}:</strong> {{ sport.location }}</p>
                    <div class="sports-page__actions">
                        <a :href="`/sports/${sport.id}`" class="sports-page__link-details">
                            {{ t('sports.details') }}
                        </a>
                    </div>
                </div>
            </div>

            <PaginationLinks
                v-if="sports?.links?.length > 1"
                class="sports-page__pagination"
                :links="sports.links"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import {computed, ref, watch} from 'vue'
import {usePage, router} from '@inertiajs/vue3'
import {route} from 'ziggy-js'
import AppLayout from "@/Layouts/AppLayout.vue"
import PaginationLinks from "@/Components/PaginationLinks.vue"
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    sports: Object,
    filters: Object,
})

const page = usePage()
const authUser = computed(() => page.props.auth?.user ?? null)
const isAdmin = computed(() => authUser.value?.role === 'admin')
const { t } = useI18n()

const search = ref(props.filters?.search || '')
const sortOrder = ref(props.filters?.sort || 'asc')

const sportsList = computed(() => props.sports?.data || [])

const changePage = (pageNumber) => {
    router.get(
        route('sports.index'),
        {
            search: search.value,
            sort: sortOrder.value,
            page: pageNumber,
        },
        {preserveState: true, replace: true}
    )
}

watch([search, sortOrder], () => {
    changePage(1)
})

</script>

<style lang="scss" scoped>
.sports-page {
    max-width: 1240px;
    margin: 0 auto;
    padding: 24px;

    &__title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 24px;
        color: #1e293b;
    }

    &__controls {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 24px;
        align-items: center;
    }

    &__search,
    &__select {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        font-size: 15px;
        min-width: 160px;
        flex-grow: 1;
    }

    &__btn-add {
        display: inline-block;
        background-color: #2563eb;
        color: #fff;
        padding: 10px 18px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.25s ease;

        &:hover {
            background-color: #1d4ed8;
        }
    }

    &__grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    &__pagination {
        margin-top: 28px;
    }

    &__card {
        background: #fff;
        border-radius: 10px;
        padding: 20px 24px;
        box-shadow: 0 3px 8px rgb(0 0 0 / 0.1);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        transition: box-shadow 0.3s ease;

        &:hover {
            box-shadow: 0 5px 14px rgb(0 0 0 / 0.15);
        }
    }

    &__name {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #334155;
    }

    &__description {
        font-size: 15px;
        color: #64748b;
        margin-bottom: 12px;
        line-height: 1.4;
    }

    &__actions {
        margin-top: auto;
    }

    &__link-details {
        font-weight: 600;
        color: #2563eb;
        text-decoration: none;

        &:hover {
            text-decoration: underline;
        }
    }
}
</style>
