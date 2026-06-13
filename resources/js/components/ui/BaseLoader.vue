<script setup>
import { ref } from "vue";
</script>

<template>
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="flex flex-col items-center gap-3">
            <div
                class="w-10 h-10 border-4 border-indigo-200 border-t-indigo-600 rounded-full animate-spin"
            ></div>

            <p class="text-sm text-slate-500">Loading experiences...</p>
        </div>
    </div>
</template>
