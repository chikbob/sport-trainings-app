<template>
    <div class="p-8 max-w-md mx-auto">
        <h2 class="text-2xl font-bold mb-4">Реєстрація учасника</h2>
        <form @submit.prevent="submit" class="space-y-3">
            <select v-model="form.sport_id" class="w-full border p-2 rounded">
                <option value="">Оберіть секцію</option>
                <option v-for="sport in props.sports" :value="sport.id">{{ sport.name }}</option>
            </select>
            <input v-model="form.full_name" type="text" placeholder="ПІБ" class="w-full border p-2 rounded"/>
            <input v-model="form.email" type="email" placeholder="Email" class="w-full border p-2 rounded"/>
            <input v-model="form.phone" type="text" placeholder="Телефон" class="w-full border p-2 rounded"/>
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Зареєструватись</button>
        </form>
    </div>
</template>

<script setup>
import {ref} from 'vue'
import {router} from '@inertiajs/vue3'

const props = defineProps({sports: Array})

const form = ref({
    sport_id: '',
    full_name: '',
    email: '',
    phone: ''
})

const submit = () => {
    router.post('/participants', form.value)
}
</script>
