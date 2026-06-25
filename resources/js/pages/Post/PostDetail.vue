<script setup>
import { onMounted, ref, watchEffect } from "vue";
import api from "../../services/api";
import { useRoute, useRouter } from "vue-router";
import dayjs from "dayjs";
import { formatDistanceToNow } from "date-fns";
import BaseLoader from "../../components/ui/BaseLoader.vue";
import BaseError from "../../components/ui/BaseError.vue";
import PostFormModal from "./PostFormModal.vue";
import { useAuthStore } from "../../stores/AuthStore.js";
import { useReaction } from "../../composables/useReaction.js";
import { useGuestId } from "../../composables/useGuestId.js";

// post looks like this:
// {
//     id,
//     user_id,
//     title,
//     content,
//     user: {},
//     comments: [user],
//     reactions: [user],
//     upvotes_count:
//     donwvotes_coint:
// }

const auth = useAuthStore();

const post = ref(null);

const { react } = useReaction();
const { guestId } = useGuestId();

const router = useRouter();
const route = useRoute();

const loading = ref(false);
const errorMessage = ref(null);

const fetchPost = async (id) => {
    loading.value = true;
    errorMessage.value = null;

    try {
        const response = await api.get(`/posts/${id}`);
        post.value = response.data;
        upvotesCount.value = response.data.upvotes_count;
        downvotesCount.value = response.data.downvotes_count;

        // find current user or guest existing reaction
        const existing = post.value.reactions?.find((reaction) =>
            auth.user
                ? reaction.user_id === auth.user.id
                : reaction.guest_identifier === guestId,
        );

        userReaction.value = existing?.reaction_type ?? null;
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            "Failed to fetch post. Please check your connection.";
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    const id = route.params.id;

    fetchPost(id);
});

// Modal
const showModal = ref(false);

const onPostUpdated = (updatedPost) => {
    showModal.value = false;
    post.value = updatedPost;
};

const deleteLoading = ref(false);

// deleting a post
const destroy = async () => {
    deleteLoading.value = true;
    errorMessage.value = null;
    try {
        const response = await api.delete(`/posts/${post.value.id}`);

        router.push({ name: "posts.index" });
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message || "Failed to delete post";
    } finally {
        deleteLoading.value = false;
    }
};

// reaction
const userReaction = ref(null);
const upvotesCount = ref(0);
const downvotesCount = ref(0);

const reaction = async (type) => {
    errorMessage.value = null;
    try {
        const response = await react(post.value, type);
        upvotesCount.value = response.data.upvotes_count;
        downvotesCount.value = response.data.downvotes_count;
        userReaction.value = response.data.user_reaction;
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message || "Failed to react post";
    }
};
</script>

<template>
    <BaseLoader v-if="loading" />

    <div v-if="post" class="max-w-4xl mx-auto space-y-12">
        <BaseError v-if="errorMessage" :error-messages="errorMessage" />
        <article
            class="bg-white border border-slate-100 p-8 rounded-2xl shadow-sm"
        >
            <header class="mb-10 pb-8 border-b border-slate-100">
                <h1
                    class="text-4xl font-extrabold text-slate-950 tracking-tighter leading-tight mb-5"
                >
                    {{ post.title }}
                </h1>

                <div
                    class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-slate-500"
                >
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-indigo-600 text-lg"
                        >
                            {{ post.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <span class="font-semibold text-slate-900"
                            >{{ post.user.name }} (Author)</span
                        >
                    </div>
                    <span class="text-slate-300">|</span>
                    <time
                        :datetime="dayjs(post.created_at).format('YYYY-MM-DD')"
                        title="Published on October 27, 2023"
                        >{{ dayjs(post.created_at).format("MMM-D-YYYY") }}</time
                    >
                    <span class="text-slate-300">|</span>
                    <span
                        class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 text-xs px-3 py-1 rounded-full font-medium"
                    >
                        Career & Growth
                    </span>

                    <div
                        v-if="auth.user?.id === post.user_id"
                        class="flex items-center gap-2"
                    >
                        <button
                            @click="showModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-100 text-slate-600 text-xs px-3 py-1 rounded-full font-semibold transition-all cursor-pointer"
                        >
                            <svg
                                class="w-3.5 h-3.5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                />
                            </svg>
                            <span>Edit Post</span>
                        </button>

                        <button
                            @click="destroy"
                            :disabled="deleteLoading"
                            type="button"
                            class="inline-flex items-center gap-1.5 bg-slate-50 border border-slate-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 text-slate-600 text-xs px-3 py-1 rounded-full font-semibold transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <svg
                                class="w-3.5 h-3.5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                />
                            </svg>
                            <span>{{
                                deleteLoading ? "Deleting..." : "Delete"
                            }}</span>
                        </button>
                    </div>
                </div>
            </header>

            <div
                class="prose prose-slate max-w-none text-slate-700 leading-relaxed space-y-5"
            >
                <p>
                    {{ post.content }}
                </p>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-100">
                <div class="text-center mb-6">
                    <h3
                        class="text-sm font-semibold text-slate-500 uppercase tracking-widest"
                    >
                        How relatable is this experience?
                    </h3>
                </div>

                <div class="flex justify-center items-center gap-4">
                    <button
                        @click="reaction('upvote')"
                        :class="[
                            userReaction === 'upvote'
                                ? 'bg-emerald-600 text-white border-emerald-600 shadow-md shadow-emerald-200 scale-105'
                                : 'bg-emerald-50/50 border-emerald-100 text-emerald-700 hover:bg-emerald-50 hover:border-emerald-200 hover:-translate-y-0.5',
                        ]"
                        class="group flex items-center gap-3 px-6 py-3 border rounded-full font-semibold transition-all duration-200 ease-in-out cursor-pointer select-none"
                    >
                        <svg
                            :class="
                                userReaction === 'upvote'
                                    ? 'text-white scale-110'
                                    : 'text-emerald-500 group-hover:scale-110'
                            "
                            class="w-6 h-6 transition-transform"
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
                        <span class="text-lg font-bold">Relatable</span>
                        <span
                            :class="
                                userReaction === 'upvote'
                                    ? 'text-emerald-100'
                                    : 'text-emerald-600'
                            "
                            class="text-lg font-medium"
                        >
                            {{ upvotesCount }}
                        </span>
                    </button>

                    <button
                        @click="reaction('downvote')"
                        :class="[
                            userReaction === 'downvote'
                                ? 'bg-rose-600 text-white border-rose-600 shadow-md shadow-rose-200 scale-105'
                                : 'bg-rose-50/50 border-rose-100 text-rose-700 hover:bg-rose-50 hover:border-rose-200 hover:-translate-y-0.5',
                        ]"
                        class="group flex items-center gap-3 px-6 py-3 border rounded-full font-semibold transition-all duration-200 ease-in-out cursor-pointer select-none"
                    >
                        <svg
                            :class="
                                userReaction === 'downvote'
                                    ? 'text-white scale-110'
                                    : 'text-rose-400 group-hover:scale-110'
                            "
                            class="w-6 h-6 transition-transform"
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
                        <span class="text-lg font-bold">Not Relatable</span>
                        <span
                            :class="
                                userReaction === 'downvote'
                                    ? 'text-rose-100'
                                    : 'text-rose-600'
                            "
                            class="text-lg font-medium"
                        >
                            {{ downvotesCount }}
                        </span>
                    </button>
                </div>
            </div>
        </article>

        <section class="space-y-8">
            <h2
                class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3"
            >
                Comments
                <span class="text-sm font-medium text-slate-400">{{
                    post.comments.length
                }}</span>
            </h2>

            <div
                class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm"
            >
                <textarea
                    rows="3"
                    placeholder="Share your thoughts on this story..."
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400"
                ></textarea>
                <div class="mt-4 flex justify-end">
                    <button
                        class="px-5 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-950 transition-colors cursor-pointer"
                    >
                        Post Comment
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                <div
                    v-for="comment in post.comments"
                    class="flex items-start gap-4 p-5 bg-white border border-slate-50 rounded-2xl"
                >
                    <div
                        class="shrink-0 w-10 h-10 rounded-full bg-slate-100 border border-slate-200 flex items-center justify-center font-bold text-emerald-600"
                    >
                        {{
                            (comment.user?.name || comment.guest_name)
                                ?.charAt(0)
                                ?.toUpperCase()
                        }}
                    </div>
                    <div class="grow">
                        <div
                            class="flex items-center justify-between gap-3 mb-2"
                        >
                            <span
                                class="font-semibold text-sm text-slate-900"
                                >{{
                                    comment.user?.name || comment.guest_name
                                }}</span
                            >
                            <span class="text-xs text-slate-400"
                                >{{
                                    formatDistanceToNow(
                                        new Date(comment.created_at),
                                        { suffix: true },
                                    )
                                }}
                                ago</span
                            >
                        </div>
                        <p class="text-sm text-slate-700">
                            {{ comment.content }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <PostFormModal
        :is-open="showModal"
        :post="post"
        @close="showModal = false"
        @saved="onPostUpdated"
    />
</template>
