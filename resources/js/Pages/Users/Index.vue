<template>
    <AppLayout>
        <div class="participants">
            <h2 class="participants__title">Список користувачів</h2>

            <!-- Панель управления -->
            <div class="filters">
                <input
                    v-model="search"
                    type="text"
                    class="filters__input"
                    placeholder="Пошук за ім'ям або email..."
                >

                <select v-model="roleFilter" class="filters__select">
                    <option value="">Всі ролі</option>
                    <option value="user">Користувач</option>
                    <option value="coach">Тренер</option>
                    <option value="admin">Адмін</option>
                </select>

                <select v-model="sortBy" class="filters__select">
                    <option value="id_desc">ID ↓</option>
                    <option value="id_asc">ID ↑</option>
                    <option value="name_asc">Ім'я A→Z</option>
                    <option value="name_desc">Ім'я Z→A</option>
                </select>
            </div>

            <div class="participants__list">
                <div
                    v-for="user in filteredUsers"
                    :key="user.id"
                    class="participants__item"
                >
                    <!-- Header -->
                    <div class="participants__header">
                        <div class="avatar">
                            {{ user.name[0].toUpperCase() }}
                        </div>

                        <div class="user-info-row">
                            <span class="user-name">{{ user.name }}</span>
                            <span class="user-email">{{ user.email }}</span>

                            <span
                                class="role-badge"
                                :class="'role-' + user.role"
                            >
                                {{ translateRole(user.role) }}
                            </span>
                        </div>
                    </div>

                    <!-- Регистрации -->
                    <div class="participants__registrations" v-if="user.registrations.length">
                        <h4 class="participants__subtitle">Реєстрації</h4>

                        <ul class="registrations-list">
                            <li
                                v-for="reg in user.registrations"
                                :key="reg.id"
                                class="registrations-item"
                            >
                                <span class="registrations-sport">{{ reg.training.sport.name }}</span>
                                <span class="registrations-date">{{ $formatDate(reg.training.date) }}</span>

                                <span
                                    class="registrations-status"
                                    :class="'status-' + reg.status"
                                >
                                    {{ reg.status }}
                                </span>
                            </li>
                        </ul>
                    </div>

                    <div v-else class="empty">
                        Користувач ще не записувався на тренування
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
import {computed, ref} from 'vue'

const props = defineProps({
    users: Array
})

const search = ref('')
const roleFilter = ref('')
const sortBy = ref('id_desc')

const filteredUsers = computed(() => {
    let result = [...props.users]

    if (search.value.trim()) {
        result = result.filter(u =>
            u.name.toLowerCase().includes(search.value.toLowerCase()) ||
            u.email.toLowerCase().includes(search.value.toLowerCase())
        )
    }

    if (roleFilter.value) {
        result = result.filter(u => u.role === roleFilter.value)
    }

    switch (sortBy.value) {
        case 'id_asc':
            result.sort((a, b) => a.id - b.id);
            break
        case 'id_desc':
            result.sort((a, b) => b.id - a.id);
            break
        case 'name_asc':
            result.sort((a, b) => a.name.localeCompare(b.name));
            break
        case 'name_desc':
            result.sort((a, b) => b.name.localeCompare(a.name));
            break
    }

    return result
})

function translateRole(role) {
    switch (role) {
        case 'admin':
            return 'Адмін'
        case 'coach':
            return 'Тренер'
        case 'user':
            return 'Користувач'
        default:
            return role
    }
}
</script>

<style scoped lang="scss">
.participants {
    padding: 20px;

    &__title {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
}

/* Фильтры */
.filters {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    flex-wrap: wrap;

    &__input,
    &__select {
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid #d6d6d6;
        font-size: 1rem;
    }

    &__input {
        flex: 1 1 250px;
    }
}

/* Сетка карточек */
.participants__list {
    display: grid;
    gap: 20px;
    grid-template-columns: repeat(2, 1fr);

    @media (max-width: 800px) {
        grid-template-columns: 1fr;
    }
}

.participants__item {
    background: white;
    padding: 20px;
    border-radius: 12px;
    border: 1px solid #e6e6e6;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
    transition: 0.2s;

    &:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
    }
}

/* Новый горизонтальный вывод */
.participants__header {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 15px;
}

.user-info-row {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.user-name {
    font-size: 1.1rem;
    font-weight: 600;
}

.user-email {
    font-size: 0.9rem;
    color: #666;
}

/* Аватар */
.avatar {
    width: 55px;
    height: 55px;
    background: #4d7cff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    border-radius: 50%;
    font-weight: 600;
}

/* Роль (бейдж) */
.role-badge {
    padding: 4px 10px;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 6px;
    text-transform: uppercase;
    white-space: nowrap; /* не переносится */
}

/* Цвета */
.role-admin {
    background: #ffe0e0;
    color: #b00000;
}

.role-coach {
    background: #ffe9cc;
    color: #b55a00;
}

.role-user {
    background: #e2eaff;
    color: #0033aa;
}

/* Регистрации */
.participants__registrations {
    margin-top: 15px;
}

.participants__subtitle {
    font-size: 1.1rem;
    margin-bottom: 10px;
    font-weight: 600;
}

.registrations-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.registrations-item {
    background: #f8f9ff;
    padding: 10px 12px;
    border-radius: 8px;
    margin-bottom: 8px;
    display: grid;
    grid-template-columns: 0.75fr auto 110px;
    align-items: center;
    gap: 8px;
    font-size: 0.95rem;
}

.registrations-sport {
    font-weight: 600;
    color: #333;
}

.registrations-date {
    color: #555;
}

.registrations-status {
    text-align: center;
    padding: 6px 4px;
    border-radius: 6px;
    font-size: 0.8rem;
    text-transform: lowercase;
    font-weight: 600;
    width: 110px;
}

.status-зареєстровано {
    background: #d4f8d4;
    color: #097a09;
}

.status-відмінено {
    background: #ffe1e1;
    color: #b40000;
}

.status-підтверджено {
    background: #e1ebff;
    color: #09317a;
}

.empty {
    margin-top: 10px;
    font-size: 0.9rem;
    color: #999;
}

</style>
