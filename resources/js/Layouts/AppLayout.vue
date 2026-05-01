<template>
    <div class="app-shell">
        <Header />

        <main class="app-main">
            <div class="app-container">
                <slot />
            </div>
        </main>

        <Footer />
    </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
</script>
