<template>
    <AdminLayout>
        <div class="dashboard">
            <h2 class="dashboard__title">
                Статистика системи
            </h2>

            <div class="dashboard__grid">
                <div class="card">
                    <span>Користувачі</span>
                    <strong>{{ stats.users }}</strong>
                </div>

                <div class="card">
                    <span>Секції</span>
                    <strong>{{ stats.sports }}</strong>
                </div>

                <div class="card">
                    <span>Тренування</span>
                    <strong>{{ stats.trainings }}</strong>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'

defineProps({
    stats: Object
})
</script>

<style scoped lang="scss">
.dashboard {

    &__title {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 30px;
        color: #1e293b;
    }

    &__grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 24px;
    }
}

.card {
    background: white;
    padding: 28px;
    border-radius: 16px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    display: flex;
    flex-direction: column;
    gap: 10px;

    span {
        font-size: 14px;
        color: #64748b;
    }

    strong {
        font-size: 32px;
        color: #0f172a;
    }
}
</style>
