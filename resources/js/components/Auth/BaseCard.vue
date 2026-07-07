<script setup>
import { ref } from "vue";

defineProps({
    title: String,
    subTitle: String,
});
</script>

<template>
    <div
        class="min-h-[70vh] flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8"
    >
        <div
            class="w-full max-w-xl space-y-8 bg-brp-surface p-8 sm:p-10 rounded-2xl border border-bro-border shadow-2xl relative"
        >
            <header class="text-center">
                <h2 class="text-3xl font-black text-bro-light tracking-tight">
                    {{ title }}
                </h2>
                <p class="mt-2 text-sm text-bro-muted">
                    {{ subTitle }}
                </p>
            </header>

            <main class="mt-8 space-y-5">
                <slot />
            </main>

            <footer class="text-center pt-2 border-t border-bro-border">
                <slot name="footer" />
            </footer>
        </div>
    </div>
</template>
