<script setup>
import { TriangleAlert, ArrowLeft } from "@lucide/vue";
</script>
<template>
    <div
        class="min-h-screen w-full bg-bro-bg flex items-center justify-center p-6"
    >
        <div class="max-w-md w-full flex flex-col items-center text-center">
            <div class="relative flex items-center justify-center mb-8">
                <div
                    class="absolute inset-0 rounded-full bg-bro-crimson/20 blur-3xl scale-150 animate-pulse"
                ></div>

                <div
                    class="relative px-8 py-4 bg-bro-surface border border-bro-border rounded-3xl shadow-2xl flex flex-col items-center gap-2 group transition-all duration-300 hover:border-bro-crimson/40"
                >
                    <span
                        class="text-7xl font-black text-transparent bg-clip-text bg-linear-to-r from-bro-light via-bro-light to-bro-crimson tracking-tighter"
                    >
                        404
                    </span>
                    <div
                        class="flex items-center gap-2 text-bro-crimson text-xs font-extrabold uppercase tracking-widest px-3 py-1 bg-bro-crimson/10 rounded-full border border-bro-crimson/20"
                    >
                        <TriangleAlert
                            class="w-3 h-3 stroke-[1.5] animate-spin"
                        />
                        Page Not Found
                    </div>
                </div>
            </div>

            <h1 class="text-3xl font-black text-bro-light tracking-tight mb-3">
                Lost in the Code, Browskie?
            </h1>

            <p
                class="text-sm font-medium text-bro-muted/70 leading-relaxed mb-8"
            >
                The route or URL you entered doesn't exist or has been moved.
                Check the address bar for typos or head back to the main feed.
            </p>

            <div
                class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full"
            >
                <RouterLink
                    :to="{ name: 'posts.index' }"
                    type="button"
                    class="w-full sm:w-auto px-6 py-3 bg-bro-crimson hover:bg-bro-crimson/90 active:scale-95 text-white font-bold text-sm rounded-xl transition-all shadow-lg shadow-bro-crimson/25 cursor-pointer flex items-center justify-center gap-2"
                >
                    <ArrowLeft class="w-4 h-4 stroke-3" />
                    Back to Recent Experiences
                </RouterLink>
            </div>
        </div>
    </div>
</template>
