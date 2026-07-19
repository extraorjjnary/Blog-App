<script setup>
import { onMounted, ref, watch } from "vue";
import api from "../../services/api";
import dayjs from "../../../utils/dayjs.js";
import BaseLoader from "../../components/ui/BaseLoader.vue";
import BaseError from "../../components/ui/BaseError.vue";
import PostFormModal from "../../components/posts/PostFormModal.vue";
import { useAuthStore } from "../../stores/AuthStore.js";
import LoadMoreBtn from "../../components/ui/LoadMoreBtn.vue";
import {
    Plus,
    SquarePen,
    ThumbsUp,
    ThumbsDown,
    MessageSquareText,
    Search,
    ChevronDown,
} from "@lucide/vue";

const auth = useAuthStore();

const posts = ref([]);
const nextPageUrl = ref(null);

// category state
const categories = ref([]);
// search and category query params
const searchQuery = ref("");
const categoryIdQuery = ref("");

// search debounce
let debounceTimer = null;
watch(searchQuery, () => {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        posts.value = [];
        nextPageUrl.value = null;
        fetchData("/posts");
    }, 500);
});

// watch changes for category
watch(categoryIdQuery, () => {
    posts.value = [];
    nextPageUrl.value = null;
    fetchData("/posts");
});

const initialLoading = ref(false);
const loadingMore = ref(false);

const errorMessage = ref(null);

const fetchData = async (url, isLoadMore = false) => {
    !isLoadMore ? (initialLoading.value = true) : (loadingMore.value = true);

    errorMessage.value = null;

    try {
        const response = await api.get(url, {
            params: {
                search: searchQuery.value || undefined,
                category_id: categoryIdQuery.value || undefined,
            },
        });
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

// Modal

const showModal = ref(false);

const onPostSaved = () => {
    showModal.value = false;

    posts.value = [];
    fetchData("/posts");
};

// Categories fetch
const fetchCategories = async () => {
    try {
        const response = await api.get("/categories");
        categories.value = response.data;
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message || "Failed to fetch categories";
    }
};

onMounted(() => {
    fetchCategories();
    posts.value = [];
    fetchData("/posts");
});
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

            <!--  Contains both Search Input and Category Dropdown -->
            <div
                class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 grow max-w-2xl sm:justify-end"
            >
                <!-- Search Input Controls Field -->
                <div class="relative grow max-w-md">
                    <input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search relatable stories..."
                        class="w-full px-4 py-2.5 pl-11 bg-bro-bg border border-bro-border rounded-xl text-bro-light text-sm focus:outline-hidden focus:border-bro-crimson focus:ring-4 focus:ring-bro-crimson/10 transition-all placeholder:text-bro-muted/40 font-medium"
                    />
                    <div
                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-bro-muted/50"
                    >
                        <Search class="w-4 h-4" />
                    </div>
                </div>

                <!-- Dropdown Category -->
                <div class="relative min-w-45">
                    <select
                        v-model="categoryIdQuery"
                        class="w-full appearance-none px-4 py-2.5 pr-10 bg-bro-bg border border-bro-border rounded-xl text-bro-light text-sm focus:outline-hidden focus:border-bro-crimson focus:ring-4 focus:ring-bro-crimson/10 transition-all font-medium cursor-pointer"
                    >
                        <option value="" selected>All Categories</option>
                        <option
                            v-for="category in categories"
                            :key="category.id"
                            :value="category.id"
                        >
                            {{ category.name }}
                        </option>
                    </select>

                    <div
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-bro-muted/60"
                    >
                        <ChevronDown class="w-4 h-4 stroke-[2.5]" />
                    </div>
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
                No experiences found.
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
                                class="flex items-center gap-2 bro-bg px-2.5 py-1 rounded-lg border border-bro-border"
                                title="Relatable Reactions"
                            >
                                <div class="flex items-center gap-1">
                                    <ThumbsUp
                                        class="w-3 h-3 text-emerald-500 stroke-[2.5]"
                                    />
                                    <span class="font-bold text-bro-light">{{
                                        post.upvotes_count
                                    }}</span>
                                </div>

                                <div class="flex items-center gap-1">
                                    <ThumbsDown
                                        class="w-3 h-3 text-rose-500 stroke-[2.5]"
                                    />
                                    <span class="font-bold text-bro-light">{{
                                        post.downvotes_count
                                    }}</span>
                                </div>
                            </div>

                            <!-- Comment Count metrics -->

                            <div
                                class="flex items-center gap-1.5 bg-bro-bg px-2.5 py-1 rounded-lg border border-bro-border"
                                title="Comments"
                            >
                                <MessageSquareText
                                    class="w-3 h-3 text-sky-400 stroke-2"
                                />
                                <span class="font-bold text-bro-light">{{
                                    post.comments_count
                                }}</span>
                            </div>
                        </div>
                    </div>
                </article>
            </RouterLink>
        </div>

        <!-- Load More Section Button  -->
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
        <div class="relative w-6 h-6">
            <Plus
                class="w-6 h-6 stroke-[2.5] transform transition-all duration-300 group-hover:rotate-90 group-hover:opacity-0 group-hover:scale-75"
            />

            <SquarePen
                class="w-6 h-6 absolute inset-0 text-white transform scale-0 opacity-0 transition-all duration-300 group-hover:scale-100 group-hover:opacity-100"
            />
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
