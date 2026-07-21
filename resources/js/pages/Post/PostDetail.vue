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
import {
    SquarePen,
    ThumbsDown,
    ThumbsUp,
    Trash2,
    Tag,
    ArrowLeft,
} from "@lucide/vue";
import { useErrorHandler } from "../../composables/useErrorHandler.js";
const { getErrorMessage } = useErrorHandler();

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
        errorMessage.value = getErrorMessage(
            error,
            "Failed to fetch post. Please check your connection.",
        );
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
        errorMessage.value = getErrorMessage(error, "Failed to delete post");
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

        errorMessage.value = getErrorMessage(
            error,
            "Failed to sync reaction with server.",
        );
    } finally {
        reactionLoading.value = false;
    }
};
</script>

<template>
    <BaseLoader v-if="loading" />

    <div v-if="post" class="max-w-4xl mx-auto space-y-12 text-bro-light">
        <BaseError v-if="errorMessage" :error-messages="errorMessage" />

        <button
            @click="router.back()"
            class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-bro-surface hover:bg-bro-surface/80 border border-bro-border hover:border-bro-crimson/40 text-bro-muted hover:text-bro-crimson transition-all duration-200 text-xs font-semibold rounded-xl shadow-xs hover:shadow-md group cursor-pointer mb-6"
        >
            <ArrowLeft
                class="w-3.5 h-3.5 text-bro-muted group-hover:text-bro-crimson-hover group-hover:-translate-x-1 transition-all duration-200"
            />
            <span>Go back</span>
        </button>

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
                    <div
                        class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-bro-crimson/10 text-bro-crimson text-[10px] font-bold uppercase tracking-widest border border-bro-crimson/20 select-none"
                    >
                        <Tag class="w-4 h-3 text-bro-crimson stroke-3" />
                        <span>{{ post.category.name }}</span>
                    </div>

                    <div
                        v-if="auth.user?.id === post.user_id"
                        class="flex items-center gap-2 ml-auto"
                    >
                        <button
                            @click="showModal = true"
                            type="button"
                            class="inline-flex items-center gap-1.5 bg-bro-bg border border-bro-border hover:bg-bro-border hover:text-bro-light text-bro-muted text-xs px-3 py-1.5 rounded-xl font-semibold transition-all cursor-pointer"
                        >
                            <SquarePen
                                class="w-4 h-4 text-bro-crimson hover:text-bro-crimson-hover"
                            />
                            <span>Edit</span>
                        </button>

                        <button
                            @click="destroy"
                            :disabled="deleteLoading"
                            type="button"
                            class="inline-flex items-center gap-1.5 bg-bro-bg border border-bro-border hover:bg-red-950/40 hover:text-red-400 hover:border-red-900/50 text-bro-muted text-xs px-3 py-1.5 rounded-xl font-semibold transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            <Trash2
                                class="w-4 h-4 text-rose-500 hover:text-rose-600"
                            />
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

                <div class="flex justify-center items-center gap-3">
                    <!-- Relatable Button -->
                    <button
                        :disabled="reactionLoading"
                        @click="reaction('upvote')"
                        :class="
                            userReaction === 'upvote'
                                ? 'bg-emerald-500/10 border-emerald-500/40 text-emerald-400 shadow-lg shadow-emerald-950/30'
                                : 'bg-bro-surface border-bro-border text-bro-muted hover:border-emerald-500/20 hover:text-emerald-500 hover:bg-emerald-500/5'
                        "
                        class="group flex items-center gap-2.5 px-5 py-2.5 border rounded-xl font-semibold transition-all duration-150 cursor-pointer select-none active:scale-95 disabled:opacity-40"
                    >
                        <ThumbsUp
                            class="w-4 h-4 transition-all duration-150"
                            :class="
                                userReaction === 'upvote'
                                    ? 'fill-emerald-500 text-emerald-500 scale-110'
                                    : 'text-bro-muted group-hover:text-emerald-500'
                            "
                        />
                        <span
                            class="text-xs uppercase tracking-widest font-bold"
                        >
                            Relatable
                        </span>
                        <span
                            :class="
                                userReaction === 'upvote'
                                    ? 'text-emerald-400'
                                    : 'text-bro-muted'
                            "
                            class="text-sm font-black font-mono tabular-nums"
                        >
                            {{ upvotesCount }}
                        </span>
                    </button>

                    <!-- Divider -->
                    <div class="w-px h-8 bg-bro-border"></div>

                    <!-- Not Relatable Button -->
                    <button
                        :disabled="reactionLoading"
                        @click="reaction('downvote')"
                        :class="
                            userReaction === 'downvote'
                                ? 'bg-rose-500/10 border-rose-500/40 text-rose-400 shadow-lg shadow-rose-950/30'
                                : 'bg-bro-surface border-bro-border text-bro-muted hover:border-rose-500/20 hover:text-rose-500 hover:bg-rose-500/5'
                        "
                        class="group flex items-center gap-2.5 px-5 py-2.5 border rounded-xl font-semibold transition-all duration-150 cursor-pointer select-none active:scale-95 disabled:opacity-40"
                    >
                        <ThumbsDown
                            class="w-4 h-4 transition-all duration-150"
                            :class="
                                userReaction === 'downvote'
                                    ? 'fill-rose-500 text-rose-500 scale-110'
                                    : 'text-bro-muted group-hover:text-rose-500'
                            "
                        />
                        <span
                            class="text-xs uppercase tracking-widest font-bold"
                        >
                            Not Relatable
                        </span>
                        <span
                            :class="
                                userReaction === 'downvote'
                                    ? 'text-rose-400'
                                    : 'text-bro-muted'
                            "
                            class="text-sm font-black font-mono tabular-nums"
                        >
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
