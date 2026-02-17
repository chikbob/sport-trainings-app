<template>
    <div style="height: 100dvh" class="flex flex-col">
        <Header/>

        <main class="flex-1 container mx-auto px-4 py-8">
            <slot/>
        </main>

        <Footer/>
    </div>
</template>

<script setup>
import Header from '@/Components/Header.vue'
import Footer from '@/Components/Footer.vue'
</script>
