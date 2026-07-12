<script setup>
import { ref, watch, computed, nextTick } from "vue";
import api from "../../services/api";
import { useAuthStore } from "../../stores/AuthStore";
import BaseError from "../../components/ui/BaseError.vue";
import { X } from "@lucide/vue";

const auth = useAuthStore();

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    post: Object, // null if create, update if persist post data
});

const form = ref({
    title: "",
    content: "",
});

// Watch until prop shifts to auto-populate or clear form state
watch(
    () => props.post,
    (post) => {
        if (post) {
            form.value = {
                title: post.title,
                content: post.content,
            };
        } else {
            // Reset inputs if user shifts from edit mode back to create mode
            form.value = {
                title: "",
                content: "",
            };
        }
    },
    { immediate: true },
);

const isEditMode = computed(() => !!props.post);

const emit = defineEmits(["close", "saved"]);

const loading = ref(false);
const errorMessage = ref(null);

const save = async () => {
    loading.value = true;
    errorMessage.value = null;
    try {
        let response;

        if (isEditMode.value) {
            // update existing post
            response = await api.put(`/posts/${props.post.id}`, form.value);
        } else {
            // create new post
            response = await api.post("/posts", form.value);
        }

        // Reset inputs if after creating a brandnew post

        form.value = {
            title: "",
            content: "",
        };

        emit("saved", response.data);
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            "Failed to save post. Please check your connection.";
    } finally {
        loading.value = false;
    }
};

// This will watch until rendering the input inside the DOM
const inputTitle = ref(null);

watch(
    () => props.isOpen,
    async (isOpenNow) => {
        if (isOpenNow) {
            await nextTick();
            inputTitle.value?.focus();
        }
    },
);
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-from-class="opacity-0 scale-125"
            enter-to-class="opacity-100 scale-100"
            enter-active-class="transition duration-300"
            leave-active-class="transition duration-200"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-125"
        >
            <div
                v-if="isOpen && auth.isLoggedIn"
                @keyup.esc="emit('close')"
                @click.self="emit('close')"
                class="fixed inset-0 z-100 flex items-center justify-center px-4 overflow-y-auto bg-black/70 backdrop-blur-xs"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true"
            >
                <div
                    class="relative w-full max-w-2xl bg-bro-surface p-8 rounded-2xl border border-bro-border shadow-2xl transform transition-all space-y-8 my-8 text-bro-light"
                >
                    <BaseError
                        v-if="errorMessage"
                        :error-messages="errorMessage"
                    />

                    <div class="text-center">
                        <h3
                            class="text-3xl font-extrabold text-bro-light tracking-tight"
                            id="modal-title"
                        >
                            {{
                                !isEditMode
                                    ? "Share a Relatable Story"
                                    : "Edit Your Experience"
                            }}
                        </h3>
                        <p class="mt-2 text-sm text-bro-muted max-w-md mx-auto">
                            BroCore thrives on honesty. Share an experience,
                            struggle, or lesson learned that other men can
                            connect with.
                        </p>
                    </div>

                    <form class="space-y-6" @submit.prevent="save">
                        <div class="space-y-5">
                            <div>
                                <label
                                    for="title"
                                    class="block text-sm font-semibold text-bro-muted mb-1.5"
                                    >Post Title (Catchy & Relatable)</label
                                >
                                <input
                                    ref="inputTitle"
                                    v-model="form.title"
                                    id="title"
                                    type="text"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-bro-bg border border-bro-border rounded-xl text-bro-light text-sm focus:outline-hidden focus:border-bro-crimson focus:ring-4 focus:ring-bro-crimson/10 transition-all placeholder:text-bro-muted/30"
                                    placeholder="e.g., Finally started training BJJ at 30..."
                                />
                            </div>

                            <div>
                                <label
                                    for="content"
                                    class="block text-sm font-semibold text-bro-muted mb-1.5"
                                    >Your Experience (The Story)</label
                                >
                                <textarea
                                    v-model="form.content"
                                    id="content"
                                    required
                                    rows="10"
                                    class="w-full px-3.5 py-2.5 bg-bro-bg border border-bro-border rounded-xl text-bro-light text-sm focus:outline-hidden focus:border-bro-crimson focus:ring-4 focus:ring-bro-crimson/10 transition-all placeholder:text-bro-muted/30 resize-none"
                                    placeholder="Tell us what happened, what you learned, how it felt..."
                                ></textarea>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end gap-3 pt-6 border-t border-bro-border"
                        >
                            <button
                                @click="emit('close')"
                                type="button"
                                class="px-5 py-2.5 border border-bro-border rounded-xl text-sm font-semibold text-bro-muted hover:bg-bro-border hover:text-bro-light transition-colors cursor-pointer"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                :disabled="loading"
                                class="px-5 py-2.5 rounded-xl text-sm font-semibold text-white bg-bro-crimson hover:bg-bro-crimson-hover focus:outline-hidden disabled:opacity-50 disabled:cursor-not-allowed transition-all cursor-pointer shadow-md shadow-bro-crimson/10 flex items-center gap-2"
                            >
                                <div
                                    v-if="loading"
                                    class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                                ></div>
                                {{
                                    loading
                                        ? "Saving..."
                                        : !isEditMode
                                          ? "Publish Post"
                                          : "Update Post"
                                }}
                            </button>
                        </div>
                    </form>

                    <button
                        @click="emit('close')"
                        type="button"
                        class="absolute top-6 right-6 text-bro-muted hover:text-bro-light transition-colors cursor-pointer"
                        aria-label="Close modal"
                    >
                        <X class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
