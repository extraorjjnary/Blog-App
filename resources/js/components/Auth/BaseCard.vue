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
            class="w-full max-w-xl space-y-8 bg-white p-8 sm:p-10 rounded-2xl border border-slate-100 shadow-xl shadow-slate-200/50"
        >
            <header class="text-center">
                <h2
                    class="text-3xl font-extrabold text-slate-900 tracking-tight"
                >
                    {{ title }}
                </h2>
                <p class="mt-2 text-sm text-slate-500">
                    {{ subTitle }}
                </p>
            </header>

            <main class="mt-8 space-y-5">
                <slot />
            </main>

            <footer class="text-center pt-2 border-t border-slate-100">
                <slot name="footer" />
            </footer>
        </div>
    </div>
</template>
