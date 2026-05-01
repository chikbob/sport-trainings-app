<template>
    <AppLayout>
        <div class="ui-page" style="max-width: 620px; margin: 0 auto;">
            <PageHeader :title="t('auth.registerTitle')" :description="t('home.subtitle')" />

            <AppCard soft>
                <div v-if="hasErrors" class="ui-error-list">
                    <div v-for="(msg, key) in errors" :key="key">{{ msg }}</div>
                </div>

                <form class="ui-form" @submit.prevent="submit">
                    <div class="ui-form-grid">
                        <AppInput
                            v-model="form.name"
                            :label="t('auth.name')"
                            autocomplete="name"
                            required
                        />
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
                            autocomplete="new-password"
                            required
                        />
                        <AppInput
                            v-model="form.password_confirmation"
                            :label="t('auth.passwordConfirm')"
                            type="password"
                            autocomplete="new-password"
                            required
                        />
                    </div>

                    <div class="ui-inline-actions">
                        <AppButton type="submit" :loading="loading">{{ t('auth.register') }}</AppButton>
                        <AppButton href="/login" variant="secondary">{{ t('auth.login') }}</AppButton>
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
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
})

const loading = ref(false)
const page = usePage()
const errors = computed(() => page.props.errors || {})
const hasErrors = computed(() => Object.keys(errors.value).length > 0)
const { t } = useI18n()

function submit() {
    loading.value = true
    router.post('/register', form.value, {
        onFinish: () => {
            loading.value = false
        },
    })
}
</script>
