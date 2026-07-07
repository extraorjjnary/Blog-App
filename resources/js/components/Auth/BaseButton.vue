<script setup>
import { ref } from "vue";

defineProps({
    isLoading: Boolean,
});

const emit = defineEmits(["click"]);
</script>

<template>
    <div>
        <button
            type="submit"
            :disabled="isLoading"
            @click="emit('click')"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl text-sm font-bold text-white bg-bro-crimson hover:bg-bro-crimson-hover focus:outline-hidden focus:ring-4 focus:ring-bro-crimson/20 disabled:opacity-50 disabled:cursor-not-allowed transition-all cursor-pointer shadow-md shadow-bro-crimson/10 active:scale-95 select-none"
        >
            <slot />
        </button>
    </div>
</template>
