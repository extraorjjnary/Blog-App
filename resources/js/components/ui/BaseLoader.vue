<script setup>
import { ref } from "vue";
defineProps({
    message: {
        type: String,
        default: "Loading...",
    },
});
</script>

<template>
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="flex flex-col items-center gap-4">
            <div
                class="w-10 h-10 border-[3px] border-bro-border border-t-bro-crimson rounded-full animate-spin"
            ></div>

            <p
                class="text-sm font-bold tracking-widest text-bro-muted/40 animate-pulse"
            >
                {{ message }}
            </p>
        </div>
    </div>
</template>
