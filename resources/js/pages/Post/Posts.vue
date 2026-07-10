<script setup>
import { onMounted, ref } from "vue";
import api from "../../services/api";
import dayjs from "../../../utils/dayjs.js";
import BaseLoader from "../../components/ui/BaseLoader.vue";
import BaseError from "../../components/ui/BaseError.vue";
import PostFormModal from "../../components/posts/PostFormModal.vue";
import { useAuthStore } from "../../stores/AuthStore.js";
import LoadMoreBtn from "../../components/ui/LoadMoreBtn.vue";

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

    posts.value = [];
    fetchData("/posts");
};
</script>

<template>
    <!-- FULL PAGE LOADER -->
    <BaseLoader v-if="initialLoading" />

    <!-- Main Content -->
    <div v-else class="space-y-10">
        <div
            class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 p-5 bg-bro-surface border border-bro-border rounded-2xl shadow-md"
        >
            <h1 class="text-2xl font-black text-bro-light tracking-tight">
                Recent Experiences
            </h1>

            <div class="relative grow max-w-md">
                <input
                    type="search"
                    placeholder="Search relatable stories..."
                    class="w-full px-4 py-2.5 pl-11 bro-bg border border-bro-border rounded-xl text-bro-light text-sm focus:outline-hidden focus:border-bro-crimson focus:ring-4 focus:ring-bro-crimson/10 transition-all placeholder:text-bro-muted/40 font-medium"
                />
                <div
                    class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"
                >
                    <svg
                        class="h-5 w-5 text-bro-muted/60"
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

        <!-- Feed Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <BaseError v-if="errorMessage" :error-messages="errorMessage" />

            <div
                v-if="!initialLoading && posts.length === 0 && !errorMessage"
                class="py-12 text-center text-bro-muted col-span-full font-medium"
            >
                No experiences found yet.
            </div>

            <RouterLink
                v-for="post in posts"
                :to="{ name: 'posts.show', params: { id: post.id } }"
                :key="post.id"
                class="block h-full group"
            >
                <article
                    class="bg-bro-surface border border-bro-border p-6 rounded-2xl shadow-sm hover:border-bro-crimson/40 hover:-translate-y-1 transition-all duration-300 flex flex-col h-full"
                >
                    <div class="grow">
                        <div
                            class="flex items-center gap-3 mb-4 text-xs text-bro-muted"
                        >
                            <span
                                class="font-bold text-bro-light bro-bg border border-bro-border px-2.5 py-1 rounded-full group-hover:border-bro-crimson/30 group-hover:text-bro-crimson transition-colors"
                            >
                                By {{ post.user.name }}
                            </span>
                            <span class="text-bro-border">|</span>
                            <span class="font-medium">{{
                                dayjs(post.created_at).format("YYYY-MM-DD")
                            }}</span>
                        </div>

                        <h3
                            class="text-lg font-black text-bro-light mb-3 leading-snug group-hover:text-bro-crimson transition-colors"
                        >
                            {{ post.title }}
                        </h3>

                        <p
                            class="text-sm text-bro-muted mb-5 line-clamp-3 wrap-break-word font-medium leading-relaxed"
                        >
                            {{ post.content }}
                        </p>
                    </div>

                    <div
                        class="mt-auto border-t border-bro-border pt-4 flex items-center justify-between gap-4 text-xs"
                    >
                        <div class="flex items-center gap-4 text-bro-muted">
                            <!-- Upvote Metrics -->
                            <div
                                class="flex items-center gap-1.5 bro-bg px-2 py-1 rounded-lg border border-bro-border"
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
                                <span class="font-bold text-bro-light">{{
                                    post.upvotes_count
                                }}</span>

                                <svg
                                    class="w-4 h-4 text-rose-500 ml-1"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 14H5.243a4.5 4.5 0 004.5 4.5H11m4-4h4.757a4.5 4.5 0 014.5 4.5H15M10 10V14"
                                    />
                                </svg>
                                <span class="font-bold text-bro-light">{{
                                    post.downvotes_count
                                }}</span>
                            </div>

                            <!-- Comment Count metrics -->
                            <div
                                class="flex items-center gap-1.5 bro-bg px-2 py-1 rounded-lg border border-bro-border"
                                title="Comments"
                            >
                                <svg
                                    class="w-4 h-4 text-bro-muted/70"
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
                                <span class="font-bold text-bro-light">{{
                                    post.comments_count
                                }}</span>
                            </div>
                        </div>
                    </div>
                </article>
            </RouterLink>
        </div>

        <!-- Load More Section Button Trigger -->
        <LoadMoreBtn
            v-if="nextPageUrl"
            :loading="loadingMore"
            :load-more="loadMore"
        />
    </div>

    <button
        v-if="auth.isLoggedIn"
        @click="showModal = true"
        class="fixed bottom-6 right-6 lg:bottom-10 lg:right-8 z-50 flex items-center justify-center gap-2.5 p-4 rounded-full shadow-2xl bg-bro-crimson text-white hover:bg-bro-crimson-hover hover:scale-105 active:scale-95 transition-all duration-300 group cursor-pointer"
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
                class="w-6 h-6 absolute inset-0 text-white transform scale-0 opacity-0 transition-all duration-300 group-hover:scale-100 group-hover:opacity-100"
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

        <span class="hidden lg:block font-bold text-sm pr-1">New Story</span>
    </button>

    <PostFormModal
        :is-open="showModal"
        :post="null"
        @close="showModal = false"
        @saved="onPostSaved"
    />
</template>
