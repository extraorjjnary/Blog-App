<script setup>
import { ref } from "vue";
import { errorMessages } from "vue/compiler-sfc";

const props = defineProps({
    errorMessages: String,
});
</script>

<template>
    <div
        class="p-4 rounded-xl border border-red-200 bg-red-50 text-red-700 text-sm"
    >
        {{ errorMessages }}
    </div>
</template>
