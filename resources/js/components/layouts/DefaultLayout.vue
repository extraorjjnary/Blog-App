<script setup>
import { ref } from "vue";
import TheNavbar from "../TheNavbar.vue";
import TheFooter from "../TheFooter.vue";
</script>

<template>
    <div
        class="min-h-screen bg-slate-50 flex flex-col font-sans antialiased text-slate-800"
    >
        <TheNavbar />
        <main class="grow max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8">
            <!-- Your RouterView loads IndexPage, CreatePage, etc. here -->
            <RouterView />
        </main>
        <TheFooter />
    </div>
</template>
