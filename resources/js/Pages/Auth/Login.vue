<template>
    <AppLayout>
        <div class="ui-page" style="max-width: 520px; margin: 0 auto;">
            <PageHeader :title="t('auth.loginTitle')" :description="t('home.subtitle')" />

            <AppCard soft>
                <div v-if="hasErrors" class="ui-error-list">
                    <div v-for="(msg, key) in errors" :key="key">{{ msg }}</div>
                </div>

                <form class="ui-form" @submit.prevent="submit">
                    <AppInput
                        v-model="form.email"
                        :label="t('auth.email')"
                        type="email"
                        autocomplete="email"
                        required
                    />

                    <AppInput
                        v-model="form.password"
                        :label="t('auth.password')"
                        type="password"
                        autocomplete="current-password"
                        required
                    />

                    <div class="ui-inline-actions">
                        <AppButton type="submit" :loading="loading">{{ t('auth.login') }}</AppButton>
                        <AppButton href="/register" variant="secondary">{{ t('auth.register') }}</AppButton>
                    </div>
                </form>
            </AppCard>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed, ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'
import AppButton from '@/Components/AppButton.vue'
import AppCard from '@/Components/AppCard.vue'
import AppInput from '@/Components/AppInput.vue'
import PageHeader from '@/Components/PageHeader.vue'
import { useI18n } from '@/i18n/useI18n'

const form = ref({
    email: '',
    password: '',
})

const loading = ref(false)
const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
const { t } = useI18n()

function submit() {
    loading.value = true
    router.post('/login', form.value, {
        onFinish: () => {
            loading.value = false
        },
    })
}
</script>
