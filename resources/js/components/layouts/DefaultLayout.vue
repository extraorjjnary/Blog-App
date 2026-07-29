<script setup>
import { ref } from "vue";
import TheNavbar from "../TheNavbar.vue";
</script>

<template>
    <div
        class="min-h-screen bg-bro-bg flex flex-col font-sans antialiased text-bro-light"
    >
        <TheNavbar />
        <main class="grow max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8">
            <!-- Nested children component of Default -->
            <RouterView />
        </main>
    </div>
</template>
