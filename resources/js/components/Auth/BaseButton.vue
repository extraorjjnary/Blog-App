<script setup>
import { ref } from "vue";

defineProps({
    isLoading: Boolean,
});

const emit = defineEmits(["click"]);
</script>

<template>
    <!-- Submit Button -->
    <div>
        <button
            type="submit"
            :disabled="isLoading"
            @click="emit('click')"
            class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-hidden focus:ring-4 focus:ring-indigo-500/20 active:bg-indigo-800 transition-all cursor-pointer shadow-xs shadow-indigo-600/10"
        >
            <slot />
        </button>
    </div>
</template>
