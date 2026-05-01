<template>
    <AppLayout>
        <div class="ui-page">
            <PageHeader :title="t('sports.title')" :description="t('sports.searchPlaceholder')">
                <template #actions>
                    <AppButton v-if="isAdmin" href="/admin/sports" variant="secondary">
                        {{ t('sports.add') }}
                    </AppButton>
                </template>
            </PageHeader>

            <AppCard>
                <div class="filters filters--sports">
                    <AppInput
                        v-model="search"
                        :label="t('admin.users.search')"
                        :placeholder="t('sports.searchPlaceholder')"
                    />
                    <AppInput v-model="sortOrder" :label="t('sports.title')" as="select">
                        <option value="asc">{{ t('sports.sortAsc') }}</option>
                        <option value="desc">{{ t('sports.sortDesc') }}</option>
                    </AppInput>
                </div>
            </AppCard>

            <EmptyState
                v-if="sportsList.length === 0"
                :title="t('sports.title')"
                :description="t('profile.empty')"
            />

            <div v-else class="ui-grid ui-grid--3">
                <AppCard
                    v-for="sport in sportsList"
                    :key="sport.id"
                    :title="sport.name"
                    :subtitle="sport.description || t('home.noDescription')"
                    soft
                >
                    <div class="ui-list">
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('sports.trainer') }}</div>
                                <div class="ui-list-item__meta">{{ sport.coach?.user?.name || '—' }}</div>
                            </div>
                        </div>
                        <div class="ui-list-item">
                            <div>
                                <div class="ui-list-item__title">{{ t('sports.location') }}</div>
                                <div class="ui-list-item__meta">{{ sport.location || t('home.notSpecified') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="ui-inline-actions" style="margin-top: 20px;">
                        <AppButton :href="route('sports.show', sport.id)" variant="secondary">
                            {{ t('sports.details') }}
                        </AppButton>
                    </div>
                </AppCard>
            </div>

            <PaginationLinks
                v-if="sports?.links?.length > 1"
                :links="sports.links"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import EmptyState from '@/Components/EmptyState.vue'
import PageHeader from '@/Components/PageHeader.vue'
import PaginationLinks from '@/Components/PaginationLinks.vue'
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
    router.get(route('sports.index'), {
        search: search.value,
        sort: sortOrder.value,
        page: pageNumber,
    }, {
        preserveState: true,
        replace: true,
    })
}

watch([search, sortOrder], () => {
    changePage(1)
})
</script>

<style scoped>
.filters--sports {
    display: grid;
    grid-template-columns: minmax(320px, 2.4fr) minmax(240px, 1fr);
    width: 100%;
}

@media (max-width: 720px) {
    .filters--sports {
        grid-template-columns: 1fr;
    }
}
</style>
