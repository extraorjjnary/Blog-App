<script setup>
import { onMounted, ref } from "vue";
import api from "../../services/api";
import dayjs from "dayjs";
import BaseLoader from "../../components/ui/BaseLoader.vue";
import BaseError from "../../components/ui/BaseError.vue";
import PostFormModal from "./PostFormModal.vue";
import { useAuthStore } from "../../stores/AuthStore.js";

const auth = useAuthStore();

const posts = ref([]);
const nextPageUrl = ref(null);

const initialLoading = ref(false);
const loadingMore = ref(false);

const errorMessage = ref(null);

const fetchData = async (url, isLoadMore = false) => {
    !isLoadMore ? (initialLoading.value = true) : (loadingMore.value = true);

    errorMessage.value = null;

    try {
        const response = await api.get(url);
        posts.value = [...posts.value, ...response.data.data];
        nextPageUrl.value = response.data.next_page_url;
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            "Failed to fetch posts. Please check your connection.";
    } finally {
        initialLoading.value = false;
        loadingMore.value = false;
    }
};

const loadMore = () => {
    fetchData(nextPageUrl.value, true);
};

onMounted(() => {
    posts.value = [];
    fetchData("/posts");
});

// Modal

const showModal = ref(false);

const onPostSaved = (newPost) => {
    showModal.value = false;
    posts.value.unshift(newPost);
};
</script>

<template>
    <!-- FULL PAGE LOADER -->
    <BaseLoader v-if="initialLoading" />

    <!-- Main Content -->
    <div v-else class="space-y-10">
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-5 bg-white border border-slate-100 rounded-2xl shadow-sm"
        >
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">
                Recent Experiences
            </h1>

            <div class="relative grow max-w-md">
                <input
                    type="search"
                    placeholder="Search relatable stories..."
                    class="w-full px-4 py-2.5 pl-11 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400"
                />
                <div
                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"
                >
                    <svg
                        class="h-5 w-5 text-slate-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Error -->
            <BaseError v-if="errorMessage" :error-messages="errorMessage" />

            <div
                v-if="!initialLoading && posts.length === 0 && !errorMessage"
                class="py-12 text-center text-slate-500"
            >
                No experiences found yet.
            </div>

            <!-- Posts -->
            <RouterLink
                v-for="post in posts"
                :to="{ name: 'posts.show', params: { id: post.id } }"
                :key="post.id"
            >
                <article
                    class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm hover:shadow-lg hover:shadow-indigo-500/5 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full group"
                >
                    <div class="grow">
                        <div
                            class="flex items-center gap-3 mb-4 text-xs text-slate-500"
                        >
                            <span
                                class="font-medium text-slate-700 bg-slate-100 px-2.5 py-1 rounded-full group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors"
                                >By {{ post.user.name }}</span
                            >
                            <span class="text-slate-300">|</span>
                            <span>{{
                                dayjs(post.created_at).format("YYYY-MM-DD")
                            }}</span>
                        </div>

                        <h3
                            class="text-lg font-semibold text-slate-900 mb-3 leading-snug group-hover:text-indigo-600 transition-colors"
                        >
                            {{ post.title }}
                        </h3>

                        <p class="text-sm text-slate-600 mb-5 line-clamp-3">
                            {{ post.content }}
                        </p>
                    </div>

                    <div
                        class="mt-auto border-t border-slate-100 pt-4 flex items-center justify-between gap-4 text-xs"
                    >
                        <div class="flex items-center gap-4 text-slate-500">
                            <div
                                class="flex items-center gap-1.5"
                                title="Relatable Reactions"
                            >
                                <svg
                                    class="w-4 h-4 text-emerald-500"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M14 10h4.757a4.5 4.5 0 00-4.5-4.5H13M10 14H5.243a4.5 4.5 0 004.5 4.5H11m4 4h4.757a4.5 4.5 0 01-4.5-4.5H15M10 14V10"
                                    />
                                </svg>
                                <span class="font-medium text-slate-800">{{
                                    post.reactions_count
                                }}</span>
                            </div>
                            <div
                                class="flex items-center gap-1.5"
                                title="Comments"
                            >
                                <svg
                                    class="w-4 h-4 text-slate-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"
                                    />
                                </svg>
                                <span class="font-medium text-slate-800">{{
                                    post.comments_count
                                }}</span>
                            </div>
                        </div>
                    </div>
                </article>
            </RouterLink>
        </div>

        <!-- Load More -->
        <div v-if="nextPageUrl" class="flex justify-center pt-4">
            <button
                @click="loadMore"
                :disabled="loadingMore"
                class="px-6 py-3 rounded-xl font-medium text-white bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-sm cursor-pointer"
            >
                <div class="flex justify-center items-center gap-3">
                    <div
                        v-if="loadingMore"
                        class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"
                    ></div>

                    {{ loadingMore ? "Loading..." : "Load More Stories" }}
                </div>
            </button>
        </div>
    </div>

    <button
        v-if="auth.isLoggedIn"
        @click="showModal = true"
        class="fixed bottom-6 right-6 lg:bottom-10 lg:right-8 z-50 flex items-center justify-center gap-2.5 p-4 rounded-full shadow-xl bg-indigo-600 text-white hover:bg-indigo-700 hover:scale-105 active:scale-95 transition-all duration-300 group cursor-pointer"
        title="Create New Post"
    >
        <div class="relative">
            <svg
                class="w-6 h-6 transform transition-transform duration-300 group-hover:rotate-90 group-hover:opacity-0"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2.5"
                    d="M12 4v16m8-8H4"
                />
            </svg>

            <svg
                class="w-6 h-6 absolute inset-0 text-emerald-300 transform scale-0 opacity-0 transition-all duration-300 group-hover:scale-100 group-hover:opacity-100"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2.5"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                />
            </svg>
        </div>

        <span class="hidden lg:block font-semibold text-sm">New Story</span>
    </button>

    <PostFormModal
        :is-open="showModal"
        :post="null"
        @close="showModal = false"
        @saved="onPostSaved"
    />
</template>
