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
    <!-- Input Group -->
    <div class="space-y-4">
        <label
            :for="id"
            class="block text-sm font-semibold text-slate-700 mb-1.5"
            >{{ label }}</label
        >
        <!-- No emit yet -->
        <input
            :id="id"
            :type="type"
            required
            :value="modelValue"
            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400"
            :placeholder="placeholder"
            v-model="model"
        />
    </div>
</template>
