<script setup>
import { computed } from "vue";

// Keeps the footer year dynamically updated
const currentYear = computed(() => new Date().getFullYear());
</script>

<template>
    <footer class="bg-white border-t border-slate-100 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div
                class="flex flex-col sm:flex-row items-center justify-between gap-4"
            >
                <!-- Copyright Text -->
                <p class="text-sm text-slate-400 text-center sm:text-left">
                    &copy; {{ currentYear }} DevBlog.io. All rights reserved.
                    Built with Laravel + Vue.
                </p>

                <!-- Simple Info Tag -->
                <div
                    class="flex items-center space-x-2 text-xs font-semibold text-slate-400 bg-slate-50 px-3 py-1.5 rounded-full"
                >
                    <span
                        class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"
                    ></span>
                    <span>SPA Dashboard Mode</span>
                </div>
            </div>
        </div>
    </footer>
</template>
