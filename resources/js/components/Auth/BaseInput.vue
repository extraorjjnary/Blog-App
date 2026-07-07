<script setup>
import { ref } from "vue";

defineProps({
    label: String,
    id: String,
    type: {
        type: String,
        default: "text",
    },
    placeholder: String,
});

const model = defineModel();
</script>

<template>
    <div class="space-y-2">
        <label
            :for="id"
            class="block text-sm font-semibold text-bro-muted mb-1.5"
            >{{ label }}</label
        >

        <input
            :id="id"
            :type="type"
            required
            class="w-full px-4 py-3 bg-bro-bg border border-bro-border rounded-xl text-bro-light text-sm focus:outline-hidden focus:border-bro-crimson focus:ring-4 focus:ring-bro-crimson/10 transition-all placeholder:text-bro-muted/40 font-medium"
            :placeholder="placeholder"
            v-model="model"
        />
    </div>
</template>
