<script setup>
import { onMounted, ref } from "vue";
import api from "../../services/api";
import { useRoute, useRouter } from "vue-router";
import dayjs from "../../../utils/dayjs.js";
import BaseLoader from "../../components/ui/BaseLoader.vue";
import BaseError from "../../components/ui/BaseError.vue";
import PostFormModal from "../../components/posts/PostFormModal.vue";
import { useAuthStore } from "../../stores/AuthStore.js";
import { useReaction } from "../../composables/useReaction.js";
import { useGuest } from "../../composables/useGuest.js";
import CommentSection from "../../components/posts/CommentSection.vue";

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
const { guestId } = useGuest();

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

        // Find current user or guest existing reaction
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

// Modal Toggles
const showModal = ref(false);

const onPostUpdated = (updatedPost) => {
    showModal.value = false;
    post.value = updatedPost;
};

const deleteLoading = ref(false);

// Deleting a post
const destroy = async () => {
    if (!confirm("Are you sure you want to delete this experience, bro?"))
        return;

    deleteLoading.value = true;
    errorMessage.value = null;
    try {
        await api.delete(`/posts/${post.value.id}`);

        router.push({ name: "posts.index" });
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message || "Failed to delete post";
    } finally {
        deleteLoading.value = false;
    }
};

// Reactions Reactive State
const userReaction = ref(null);
const upvotesCount = ref(0);
const downvotesCount = ref(0);
const reactionLoading = ref(false);

const reaction = async (type) => {
    if (reactionLoading.value) return;
    reactionLoading.value = true;
    errorMessage.value = null;

    const previousUserReaction = userReaction.value;
    const previousUpvotesCount = upvotesCount.value;
    const previousDownvotesCount = downvotesCount.value;

    // OPTIMISTIC UI

    // user toggle the same reaction = toggle off
    if (userReaction.value === type) {
        userReaction.value = null;
        if (type === "upvote") {
            upvotesCount.value--;
        } else {
            downvotesCount.value--;
        }
    }
    // brand new reaction
    else if (!userReaction.value) {
        if (type === "upvote") {
            upvotesCount.value++;
        } else {
            downvotesCount.value++;
        }
        userReaction.value = type;
    }
    // switching reaction
    else {
        if (userReaction.value === "upvote") {
            // avoid causing -1 if upvotes count value = 0
            if (upvotesCount.value) upvotesCount.value--;
            downvotesCount.value++;
        } else {
            // avoid causing -1 if downvotes count value = 0
            if (downvotesCount.value) downvotesCount.value--;
            upvotesCount.value++;
        }
        userReaction.value = type;
    }

    try {
        const response = await react(post.value, type);
        upvotesCount.value = response.data.upvotes_count;
        downvotesCount.value = response.data.downvotes_count;
        userReaction.value = response.data.user_reaction;
    } catch (error) {
        // Rollback state if server request drops
        userReaction.value = previousUserReaction;
        upvotesCount.value = previousUpvotesCount;
        downvotesCount.value = previousDownvotesCount;

        errorMessage.value =
            error.response?.data?.message ||
            "Failed to sync reaction with server.";
    } finally {
        reactionLoading.value = false;
    }
};
</script>

<template>
    <BaseLoader v-if="loading" />

    <div v-if="post" class="max-w-4xl mx-auto space-y-12 text-bro-light">
        <BaseError v-if="errorMessage" :error-messages="errorMessage" />

        <article
            class="bg-bro-surface border border-bro-border p-8 rounded-2xl shadow-md"
        >
            <header class="mb-10 pb-8 border-b border-bro-border">
                <h1
                    class="text-4xl font-extrabold text-bro-light tracking-tighter leading-tight mb-5"
                >
                    {{ post.title }}
                </h1>

                <div
                    class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm text-bro-muted"
                >
                    <div class="flex items-center gap-2.5">
                        <div
                            class="w-9 h-9 rounded-full bg-bro-bg border border-bro-border flex items-center justify-center font-bold text-bro-crimson text-lg"
                        >
                            {{ post.user.name.charAt(0).toUpperCase() }}
                        </div>
                        <span class="font-semibold text-bro-light"
                            >{{ post.user.name }}
                            <span class="text-xs text-bro-muted/60 font-normal"
                                >(Author)</span
                            ></span
                        >
                    </div>
                    <span class="text-bro-border">|</span>
                    <time
                        :datetime="dayjs(post.created_at).format('YYYY-MM-DD')"
                        >{{
                            dayjs(post.created_at).format("MMMM D, YYYY")
                        }}</time
                    >
                    <span class="text-bro-border">|</span>
                    <span
                        class="inline-flex items-center gap-1.5 bg-bro-bg text-bro-muted text-xs px-3 py-1 rounded-full font-medium border border-bro-border"
                    >
                        Brotherhood Feed
                    </span>

                    <div
                        v-if="auth.user?.id === post.user_id"
                        class="flex items-center gap-2 ml-auto"
                    >
                        <button
                            @click="showModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 bg-bro-bg border border-bro-border hover:bg-bro-border hover:text-bro-light text-bro-muted text-xs px-3 py-1.5 rounded-xl font-semibold transition-all cursor-pointer"
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
                            <span>Edit</span>
                        </button>

                        <button
                            @click="destroy"
                            :disabled="deleteLoading"
                            type="button"
                            class="inline-flex items-center gap-1.5 bg-bro-bg border border-bro-border hover:bg-red-950/40 hover:text-red-400 hover:border-red-900/50 text-bro-muted text-xs px-3 py-1.5 rounded-xl font-semibold transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
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
                class="text-bro-muted leading-relaxed text-base space-y-6 whitespace-pre-line wrap-break-word font-medium"
            >
                {{ post.content }}
            </div>

            <div class="mt-12 pt-8 border-t border-bro-border">
                <div class="text-center mb-6">
                    <h3
                        class="text-xs font-bold text-bro-muted/40 uppercase tracking-widest"
                    >
                        How relatable is this experience?
                    </h3>
                </div>

                <div class="flex justify-center items-center gap-4">
                    <button
                        :disabled="reactionLoading"
                        @click="reaction('upvote')"
                        :class="[
                            userReaction === 'upvote'
                                ? 'bg-bro-crimson text-white border-bro-crimson shadow-lg shadow-bro-crimson/20 scale-105 font-bold'
                                : 'bg-bro-bg border-bro-border text-emerald-500 hover:bg-bro-surface hover:border-emerald-500/30 hover:-translate-y-0.5',
                        ]"
                        class="group flex items-center gap-3 px-6 py-3 border rounded-full font-semibold transition-all duration-150 ease-out cursor-pointer select-none active:scale-95"
                    >
                        <svg
                            :class="[
                                userReaction === 'upvote'
                                    ? 'text-white scale-110 rotate-[-10deg]'
                                    : 'text-emerald-500 group-hover:scale-110 group-hover:rotate-6',
                            ]"
                            class="w-6 h-6 transition-transform duration-200"
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

                        <span class="text-sm">Relatable</span>
                        <span class="text-sm font-black transition-colors">
                            {{ upvotesCount }}
                        </span>
                    </button>

                    <button
                        :disabled="reactionLoading"
                        @click="reaction('downvote')"
                        :class="[
                            userReaction === 'downvote'
                                ? 'bg-bro-crimson text-white border-bro-crimson shadow-lg shadow-bro-crimson/20 scale-105 font-bold'
                                : 'bg-bro-bg border-bro-border text-rose-500 hover:bg-bro-surface hover:border-rose-500/30 hover:-translate-y-0.5',
                        ]"
                        class="group flex items-center gap-3 px-6 py-3 border rounded-full font-semibold transition-all duration-150 ease-out cursor-pointer select-none active:scale-95"
                    >
                        <svg
                            :class="[
                                userReaction === 'downvote'
                                    ? 'text-white scale-110 rotate-10'
                                    : 'text-rose-400 group-hover:scale-110 group-hover:rotate-6',
                            ]"
                            class="w-6 h-6 transition-transform duration-200"
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

                        <span class="text-sm">Not Relatable</span>
                        <span class="text-sm font-black transition-colors">
                            {{ downvotesCount }}
                        </span>
                    </button>
                </div>
            </div>
        </article>

        <CommentSection :post="post" />
    </div>

    <PostFormModal
        :is-open="showModal"
        :post="post"
        @close="showModal = false"
        @saved="onPostUpdated"
    />
</template>
