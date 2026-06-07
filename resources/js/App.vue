<script setup>
import TheFooter from "./components/TheFooter.vue";
import TheNavbar from "./components/TheNavbar.vue";
</script>

<template>
    <div
        class="min-h-screen bg-slate-50 flex flex-col font-sans antialiased text-slate-800"
    >
        <TheNavbar />
        <!-- Main Content Area where your pages dynamically load -->
        <main class="grow max-w-7xl w-full mx-auto p-4 sm:p-6 lg:p-8">
            <!-- Your RouterView loads IndexPage, CreatePage, etc. here -->
            <RouterView />
        </main>
        <TheFooter />
    </div>
</template>
