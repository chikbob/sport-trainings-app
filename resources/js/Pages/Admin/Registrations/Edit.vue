<template>
    <AdminLayout>
        <PageHeader :title="t('admin.registrations.edit')" :description="registration.user?.name || t('admin.registrations.title')" />

        <AppCard>
            <div class="ui-list">
                <div class="ui-list-item">
                    <div>
                        <div class="ui-list-item__title">{{ t('admin.registrations.user') }}</div>
                        <div class="ui-list-item__meta">{{ registration.user?.name || t('admin.common.notSpecified') }}</div>
                    </div>
                </div>
                <div class="ui-list-item">
                    <div>
                        <div class="ui-list-item__title">{{ t('admin.registrations.training') }}</div>
                        <div class="ui-list-item__meta">
                            {{ registration.training?.sport?.name || t('admin.common.notSpecified') }} ·
                            {{ $formatDate(registration.training?.date) }} {{ $formatTime(registration.training?.time) }}
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="hasErrors" class="ui-error-list" style="margin-top: 20px;">
                <div v-for="(msg, key) in errors" :key="key">{{ msg }}</div>
            </div>

            <form class="ui-form" style="margin-top: 20px;" @submit.prevent="submit">
                <AppInput v-model="form.status" :label="t('admin.registrations.status')" as="select">
                    <option value="pending">{{ t('admin.status.pending') }}</option>
                    <option value="approved">{{ t('admin.status.approved') }}</option>
                    <option value="cancelled">{{ t('admin.status.cancelled') }}</option>
                    <option value="rejected">{{ t('admin.status.rejected') }}</option>
                    <option value="attended">{{ t('admin.status.attended') }}</option>
                    <option value="no_show">{{ t('admin.status.no_show') }}</option>
                </AppInput>

                <div class="ui-inline-actions">
                    <AppButton type="submit">{{ t('admin.forms.update') }}</AppButton>
                    <AppButton href="/admin/registrations" variant="secondary">{{ t('admin.registrations.title') }}</AppButton>
                </div>
            </form>
        </AppCard>
    </AdminLayout>
</template>

<script setup>
import { computed, reactive } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import PageHeader from '@/Components/PageHeader.vue'
import { useI18n } from '@/i18n/useI18n'

const props = defineProps({
    registration: Object,
})

const form = reactive({
    status: props.registration.status,
})

const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
const { t } = useI18n()

const submit = () => {
    router.put(`/admin/registrations/${props.registration.id}`, form)
}
</script>
