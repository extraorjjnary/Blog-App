<script setup>
import { Trash2, X } from "@lucide/vue";
import { useAuthStore } from "../../stores/AuthStore";

const auth = useAuthStore();

defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: "Delete Post",
    },
    message: {
        type: String,
        default:
            "Are you sure you want to delete this? This action cannot be undone.",
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["confirm", "close"]);
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            enter-active-class="transition duration-200"
            leave-active-class="transition duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen && auth.isLoggedIn"
                @click.self="emit('close')"
                class="fixed inset-0 z-50 flex items-center justify-center px-4 bg-black/70 backdrop-blur-sm"
            >
                <Transition
                    enter-from-class="opacity-0 scale-95"
                    enter-to-class="opacity-100 scale-100"
                    enter-active-class="transition duration-200"
                >
                    <div
                        v-if="isOpen"
                        class="relative w-full max-w-sm bg-bro-surface border border-bro-border rounded-2xl shadow-2xl p-6 space-y-5"
                    >
                        <!-- Close Button -->
                        <button
                            @click="emit('close')"
                            class="absolute top-4 right-4 text-bro-muted hover:text-bro-light transition-colors cursor-pointer"
                        >
                            <X class="w-4 h-4" />
                        </button>

                        <slot />

                        <!-- Icon + Title -->
                        <div
                            class="flex flex-col items-center text-center space-y-3 pt-2"
                        >
                            <div
                                class="w-12 h-12 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center"
                            >
                                <Trash2 class="w-5 h-5 text-rose-500" />
                            </div>

                            <div class="space-y-1">
                                <h3
                                    class="text-base font-extrabold text-bro-light tracking-tight"
                                >
                                    {{ title }}
                                </h3>
                                <p
                                    class="text-sm text-bro-muted leading-relaxed"
                                >
                                    {{ message }}
                                </p>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-bro-border"></div>

                        <!-- Actions -->
                        <div class="flex items-center gap-3">
                            <button
                                @click="emit('close')"
                                :disabled="loading"
                                type="button"
                                class="flex-1 px-4 py-2.5 bg-bro-bg border border-bro-border text-bro-muted hover:text-bro-light hover:border-bro-muted/20 rounded-xl text-sm font-semibold transition-all cursor-pointer disabled:opacity-50"
                            >
                                Cancel
                            </button>

                            <button
                                @click="emit('confirm')"
                                :disabled="loading"
                                type="button"
                                class="flex-1 px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-sm font-semibold transition-all cursor-pointer disabled:opacity-50 shadow-lg shadow-rose-950/30"
                            >
                                {{ loading ? "Deleting..." : "Yes, Delete" }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
