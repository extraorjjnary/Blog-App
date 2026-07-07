<script setup>
import { ref } from "vue";
import CommentItem from "./CommentItem.vue";
import api from "../../services/api.js";
import { useGuest } from "../../composables/useGuest.js";
import BaseError from "../ui/BaseError.vue";

const { guestName } = useGuest();

const props = defineProps({ post: Object });

const content = ref("");

// Submitting a comment
const loading = ref(false);
const errorMessage = ref(null);
const submitComment = async () => {
    loading.value = true;
    errorMessage.value = null;
    try {
        const response = await api.post(`/posts/${props.post.id}/comments`, {
            guest_name: guestName,
            content: content.value,
        });
        onCommentSaved(response.data);
        content.value = "";
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            "Failed to comment this post. Please check your connection.";
    } finally {
        loading.value = false;
    }
};

// handle after create, update, delete fetching
const comments = ref([...props.post.comments]); // copy the props comments to not break the One way data flow

const onCommentSaved = (newComment) => {
    comments.value.unshift(newComment);
};

const onCommentUpdated = (updatedComment) => {
    const index = comments.value.findIndex(
        (comment) => comment.id === updatedComment.id,
    );

    comments.value.splice(index, 1, updatedComment); // reactive update: modify the original array
};

const onCommentDeleted = (commentId) => {
    comments.value = comments.value.filter(
        (comment) => comment.id !== commentId,
    );
};
</script>

<template>
    <section class="space-y-6">
        <h2
            class="text-xl font-bold text-bro-light tracking-tight flex items-center gap-3"
        >
            Discussion
            <span
                class="text-xs px-2 py-0.5 rounded-md bg-bro-border font-bold text-bro-muted border border-bro-border/60"
            >
                {{ comments.length }}
            </span>
        </h2>

        <BaseError v-if="errorMessage" :error-messages="errorMessage" />

        <form
            class="bg-bro-surface border border-bro-border p-5 rounded-2xl shadow-sm space-y-4"
            @submit.prevent="submitComment"
        >
            <textarea
                v-model="content"
                rows="3"
                placeholder="Share your perspective or thoughts..."
                class="w-full px-4 py-3 bg-bro-bg border border-bro-border rounded-xl text-bro-light text-sm focus:outline-hidden focus:border-bro-crimson transition-all placeholder-bro-muted/40 resize-none font-medium"
            ></textarea>

            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="loading || !content.trim()"
                    class="px-5 py-2.5 bg-bro-crimson hover:bg-red-800 disabled:bg-bro-border disabled:text-bro-muted/40 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-colors cursor-pointer disabled:cursor-not-allowed shadow-md shadow-red-950/20"
                >
                    {{ loading ? "Posting.." : "Post Comment" }}
                </button>
            </div>
        </form>

        <div class="space-y-4">
            <CommentItem
                @updated="onCommentUpdated"
                @deleted="onCommentDeleted"
                v-for="comment in comments"
                :key="comment.id"
                :comment="comment"
            />
        </div>
    </section>
</template>
