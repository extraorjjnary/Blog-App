<script setup>
import { ref } from "vue";

defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
    loadMore: {
        type: Function,
        required: true,
    },
});
</script>

<template>
    <div class="flex justify-center pt-4">
        <button
            @click="loadMore"
            :disabled="loading"
            class="px-6 py-3 rounded-xl font-bold text-white bg-bro-crimson hover:bg-bro-crimson-hover disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-md shadow-bro-crimson/10 cursor-pointer active:scale-95"
        >
            <div class="flex justify-center items-center gap-3">
                <div
                    v-if="loading"
                    class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                ></div>

                {{ loading ? "Loading..." : "Load More Stories" }}
            </div>
        </button>
    </div>
</template>
