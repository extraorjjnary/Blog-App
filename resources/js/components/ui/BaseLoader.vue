<script setup>
import { ref } from "vue";
import { Loader2 } from "@lucide/vue";

defineProps({
    message: {
        type: String,
        default: "Loading...",
    },
});
</script>

<template>
    <div class="flex items-center justify-center min-h-[60vh] select-none">
        <div class="flex flex-col items-center gap-3">
            <Loader2 class="w-10 h-10 text-bro-crimson animate-spin stroke-2" />

            <p
                class="text-xs font-mono font-bold uppercase tracking-widest text-bro-muted/50 animate-pulse mt-1"
            >
                {{ message || "LOADING..." }}
            </p>
        </div>
    </div>
</template>
