<script setup>
import { ref } from "vue";
import { CircleAlert } from "@lucide/vue";

const props = defineProps({
    errorMessages: {
        type: [String, Object],
        default: null,
    },
});
</script>

<template>
    <div
        v-if="errorMessages"
        class="text-rose-400 text-sm p-3 bg-rose-500/10 border border-rose-500/20 rounded-xl"
    >
        <div class="flex items-start gap-2">
            <CircleAlert class="w-4 h-4 shrink-0 mt-0.5" />

            <div class="flex-1">
                <p v-if="typeof errorMessages === 'string'">
                    {{ errorMessages }}
                </p>

                <ul v-else class="space-y-1">
                    <li v-for="(errors, field) in errorMessages" :key="field">
                        {{ Array.isArray(errors) ? errors[0] : errors }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
