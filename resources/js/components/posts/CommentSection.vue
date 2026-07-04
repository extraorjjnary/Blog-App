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
    <section class="space-y-8">
        <h2
            class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3"
        >
            Comments
            <span class="text-sm font-medium text-slate-400">{{
                comments.length
            }}</span>
        </h2>

        <form
            class="bg-white border border-slate-100 p-6 rounded-2xl shadow-sm"
            @submit.prevent="submitComment"
        >
            <textarea
                v-model="content"
                rows="3"
                placeholder="Share your thoughts on this story..."
                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-900 text-sm focus:outline-hidden focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400"
            ></textarea>
            <div class="mt-4 flex justify-end">
                <button
                    type="submit"
                    :disabled="loading"
                    class="px-5 py-2.5 bg-slate-800 text-white text-sm font-semibold rounded-xl hover:bg-slate-950 transition-colors cursor-pointer"
                >
                    {{ loading ? "Posting.." : "Post Comment" }}
                </button>
            </div>
        </form>

        <CommentItem
            @updated="onCommentUpdated"
            @deleted="onCommentDeleted"
            v-for="comment in comments"
            :comment="comment"
        />
    </section>
</template>
