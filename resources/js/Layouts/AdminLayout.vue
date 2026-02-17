<template>
    <div class="admin-layout">
        <AdminSidebar />

        <div class="admin-content">
            <AdminTopbar />
            <main class="admin-main">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import AdminSidebar from '@/Components/AdminSidebar.vue'
import AdminTopbar from '@/Components/AdminTopbar.vue'
</script>

<style scoped lang="scss">
.admin-layout {
    display: flex;
    min-height: 100vh;
    background: #f1f5f9;
}

.admin-content {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.admin-main {
    flex: 1;
    padding: 40px;
}
</style>
