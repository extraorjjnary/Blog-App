<script setup>
import { ref, watch, computed, nextTick } from "vue";
import api from "../../services/api";
import { useAuthStore } from "../../stores/AuthStore";
import BaseError from "../../components/ui/BaseError.vue";

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

watch(
    () => props.post,
    (post) => {
        if (post) {
            form.value = {
                title: post.title,
                content: post.content,
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
                class="fixed inset-0 z-100 flex items-center justify-center px-4 overflow-y-auto bg-slate-900/60 backdrop-blur-xs"
                aria-labelledby="modal-title"
                role="dialog"
                aria-modal="true"
            >
                <div
                    class="relative w-full max-w-2xl bg-white p-8 rounded-2xl border border-slate-100 shadow-2xl shadow-slate-900/10 transform transition-all space-y-8 my-8"
                >
                    <!-- Error -->
                    <BaseError
                        v-if="errorMessage"
                        :error-messages="errorMessage"
                    />
                    <div class="text-center">
                        <h3
                            class="text-3xl font-extrabold text-slate-900 tracking-tight"
                            id="modal-title"
                        >
                            Share a Relatable Story
                        </h3>
                        <p class="mt-2 text-sm text-slate-500 max-w-md mx-auto">
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
                                    class="block text-sm font-semibold text-slate-700 mb-1.5"
                                    >Post Title (Catchy & Relatable)</label
                                >
                                <input
                                    ref="inputTitle"
                                    v-model="form.title"
                                    id="title"
                                    type="text"
                                    required
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400"
                                    placeholder="e.g., Finally started training BJJ at 30..."
                                />
                            </div>

                            <div>
                                <label
                                    for="content"
                                    class="block text-sm font-semibold text-slate-700 mb-1.5"
                                    >Your Experience (The Story)</label
                                >
                                <textarea
                                    v-model="form.content"
                                    id="content"
                                    required
                                    rows="12"
                                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400 resize-none"
                                    placeholder="Tell us what happened, what you learned, how it felt..."
                                ></textarea>
                            </div>
                        </div>

                        <div
                            class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100"
                        >
                            <button
                                @click="emit('close')"
                                type="button"
                                class="px-5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-slate-800 transition-colors cursor-pointer"
                            >
                                Cancel
                            </button>

                            <button
                                type="submit"
                                class="px-5 py-2.5 border border-transparent rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-hidden focus:ring-4 focus:ring-indigo-500/20 active:bg-indigo-800 transition-all cursor-pointer shadow-xs shadow-indigo-600/10"
                            >
                                {{
                                    !isEditMode ? "Publish Post" : "Update Post"
                                }}
                            </button>
                        </div>
                    </form>

                    <button
                        @click="emit('close')"
                        type="button"
                        class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 transition-colors cursor-pointer"
                        aria-label="Close modal"
                    >
                        <svg
                            class="w-5 h-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
