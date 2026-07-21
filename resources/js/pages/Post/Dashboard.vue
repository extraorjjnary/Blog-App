<script setup>
import { onMounted, ref } from "vue";
import BaseError from "../../components/ui/BaseError.vue";
import api from "../../services/api";
import dayjs from "../../../utils/dayjs.js";
import BaseLoader from "../../components/ui/BaseLoader.vue";
import PostFormModal from "../../components/posts/PostFormModal.vue";
import LoadMoreBtn from "../../components/ui/LoadMoreBtn.vue";
import { useRouter } from "vue-router";
import { Plus, SquarePen, Trash2, FileText, Tag } from "@lucide/vue";
import { useErrorHandler } from "../../composables/useErrorHandler.js";

const { getErrorMessage } = useErrorHandler();

const router = useRouter();

const posts = ref([]);
const nextPageUrl = ref(null);

const showCreateModal = ref(false);
const showEditingModal = ref(false);
const editingPost = ref(null);

const initialLoading = ref(false);
const loadingMore = ref(false);

const errorMessage = ref(null);

const fetchMyPosts = async (url, isLoadMore = false) => {
    !isLoadMore ? (initialLoading.value = true) : (loadingMore.value = true);
    errorMessage.value = null;

    try {
        const response = await api.get(url);
        posts.value = [...posts.value, ...response.data.data];
        nextPageUrl.value = response.data.next_page_url;
    } catch (error) {
        errorMessage.value = getErrorMessage(
            error,
            "Failed to fetch your Posts. Please try again.",
        );
    } finally {
        initialLoading.value = false;
        loadingMore.value = false;
    }
};

const loadMore = () => {
    fetchMyPosts(nextPageUrl.value, true);
};

onMounted(() => {
    posts.value = [];

    fetchMyPosts("/my-posts");
});

const onPostSaved = () => {
    showCreateModal.value = false;

    posts.value = [];
    fetchMyPosts("/my-posts");
};

const onPostUpdated = async (updatedPost) => {
    showEditingModal.value = false;
    const index = posts.value.findIndex((post) => post.id === updatedPost.id);

    posts.value.splice(index, 1, updatedPost);
};

const deleteLoading = ref(false);
const destroy = async (post) => {
    if (!confirm("Are you sure you want to delete this experience, bro?"))
        return;
    deleteLoading.value = true;
    errorMessage.value = null;
    try {
        await api.delete(`/posts/${post.id}`);

        posts.value = [];
        fetchMyPosts("/my-posts");
    } catch (error) {
        errorMessage.value = getErrorMessage(
            error,
            "Failed to delete Post. Please try again.",
        );
    } finally {
        deleteLoading.value = false;
    }
};
</script>

<template>
    <BaseLoader v-if="initialLoading" />
    <div
        v-else
        class="w-full max-w-6xl mx-auto space-y-6 text-bro-light antialiased"
    >
        <!-- Dashboard Header -->
        <div
            class="flex items-center justify-between pb-5 border-b border-bro-border"
        >
            <div>
                <h1
                    class="text-2xl font-extrabold tracking-tighter text-bro-light"
                >
                    BroCore Control Center
                </h1>
                <p class="text-xs text-bro-muted/60 font-medium mt-1">
                    Manage and track your published brotherhood experiences
                </p>
            </div>

            <button
                @click="showCreateModal = true"
                type="button"
                class="inline-flex items-center gap-2 px-4 py-2 bg-bro-crimson hover:bg-bro-crimson-hover text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-colors cursor-pointer shadow-md shadow-red-950/20"
            >
                <Plus class="w-5 h-5" />
                <span>New Post</span>
            </button>
        </div>

        <BaseError v-if="errorMessage" :error-messages="errorMessage" />

        <div
            v-if="posts.length > 0"
            class="bg-bro-surface border border-bro-border rounded-2xl overflow-hidden shadow-xl"
        >
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="border-b border-bro-border bg-bro-bg/50 text-[11px] font-bold uppercase tracking-widest text-bro-muted/40 select-none"
                        >
                            <th class="py-4 px-6 font-bold">
                                Experience Title
                            </th>

                            <th class="py-4 px-6 font-bold">Date Published</th>
                            <th class="py-4 px-6 font-bold text-center">
                                Upvotes
                            </th>
                            <th class="py-4 px-6 font-bold text-center">
                                Comments
                            </th>
                            <th class="py-4 px-6 font-bold text-right">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody
                        class="divide-y divide-bro-border/60 text-sm font-medium text-bro-muted"
                    >
                        <tr
                            v-for="post in posts"
                            :key="post.id"
                            @click="
                                $router.push({
                                    name: 'posts.show',
                                    params: { id: post.id },
                                })
                            "
                            class="hover:bg-bro-bg/40 transition-colors duration-150"
                        >
                            <td class="py-4 px-6 max-w-xs">
                                <div class="flex flex-col items-start gap-1.5">
                                    <!-- 1. Title is wrapped in its own block to truncate cleanly -->
                                    <span
                                        class="font-bold text-bro-light truncate block w-full"
                                    >
                                        {{ post.title }}
                                    </span>

                                    <!-- 2. Category Badge sits perfectly underneath -->
                                    <span
                                        class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-bro-border/40 text-bro-muted text-[8px] font-black uppercase tracking-wider border border-bro-border select-none"
                                    >
                                        <Tag class="w-2 h-2 stroke-[2.5]" />
                                        <span>{{ post.category.name }}</span>
                                    </span>
                                </div>
                            </td>

                            <td class="py-4 px-6 text-xs text-bro-muted/60">
                                {{
                                    dayjs(post.created_at).format(
                                        "MMMM D, YYYY",
                                    )
                                }}
                            </td>
                            <td
                                class="py-4 px-6 text-center text-emerald-500/90 font-mono font-bold"
                            >
                                {{ post.upvotes_count || 0 }}
                            </td>
                            <td
                                class="py-4 px-6 text-center text-sky-400/90 font-mono font-bold"
                            >
                                {{ post.comments_count || 0 }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <button
                                        @click.stop="
                                            editingPost = post;
                                            showEditingModal = true;
                                        "
                                        type="button"
                                        class="p-1.5 bg-bro-bg border border-bro-border text-bro-muted/60 hover:text-bro-light hover:border-bro-muted/20 rounded-lg transition-all cursor-pointer"
                                    >
                                        <SquarePen class="w-3.5 h-3.5" />
                                    </button>
                                    <button
                                        @click.stop="destroy(post)"
                                        type="button"
                                        class="p-1.5 bg-bro-bg border border-bro-border text-bro-muted/60 hover:text-red-400 hover:border-red-900/50 hover:bg-red-950/20 rounded-lg transition-all cursor-pointer"
                                    >
                                        <Trash2 class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Stack Responsive Layout Engine -->
            <div
                v-if="posts.length > 0"
                class="block md:hidden divide-y divide-bro-border"
            >
                <RouterLink
                    v-for="post in posts"
                    :to="{ name: 'posts.show', params: { id: post.id } }"
                >
                    <div
                        class="p-5 space-y-4 bg-bro-surface hover:bg-bro-border"
                    >
                        <div class="space-y-1">
                            <h2
                                class="font-bold text-base text-bro-light line-clamp-2"
                            >
                                {{ post.title }}
                            </h2>
                            <p class="text-xs text-bro-muted/40 font-medium">
                                {{
                                    dayjs(post.created_at).format("MMMM-D-YYYY")
                                }}
                            </p>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <div
                                class="flex items-center gap-4 text-xs font-bold font-mono"
                            >
                                <span
                                    class="inline-flex items-center gap-1.5 text-emerald-500"
                                >
                                    <span>▲</span> {{ post.upvotes_count }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 text-sky-400"
                                >
                                    <span>💬</span> {{ post.comments_count }}
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                                <button
                                    @click.stop.prevent="
                                        editingPost = post;
                                        showEditingModal = true;
                                    "
                                    type="button"
                                    class="px-3 py-1.5 bg-bro-bg border border-bro-border text-xs font-bold text-bro-muted rounded-xl cursor-pointer"
                                >
                                    Edit
                                </button>
                                <button
                                    @click.stop.prevent="destroy(post)"
                                    type="button"
                                    class="px-3 py-1.5 bg-bro-bg border border-bro-border text-xs font-bold text-red-400 rounded-xl cursor-pointer"
                                >
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </RouterLink>
            </div>
        </div>

        <div
            v-else
            class="flex flex-col items-center justify-center p-16 bg-bro-surface border border-bro-border rounded-2xl text-center space-y-4"
        >
            <div
                class="w-12 h-12 rounded-2xl bg-bro-bg border border-bro-border flex items-center justify-center text-bro-crimson"
            >
                <FileText class="w-5 h-5 stroke-[1.5]" />
            </div>
            <h3 class="font-bold text-bro-light text-base">
                No experiences posted yet
            </h3>
        </div>
    </div>

    <!-- Load More Section Button -->

    <LoadMoreBtn
        v-if="nextPageUrl"
        :loading="loadingMore"
        :load-more="loadMore"
    />

    <PostFormModal
        :is-open="showCreateModal"
        :post="null"
        @close="showCreateModal = false"
        @saved="onPostSaved"
    />

    <PostFormModal
        :is-open="showEditingModal"
        :post="editingPost"
        @close="showEditingModal = false"
        @saved="onPostUpdated"
    />
</template>
