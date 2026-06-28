<script setup>
import { ref } from "vue";
import CommentItem from "./CommentItem.vue";
import api from "../../services/api.js";
import { useGuest } from "../../composables/useGuest.js";
import BaseError from "../ui/BaseError.vue";

const { guestName } = useGuest();

const props = defineProps({ post: Object });
const emit = defineEmits(["saved"]);

const content = ref("");

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
        emit("saved", response.data);
        content.value = "";
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message ||
            "Failed to comment this post. Please check your connection.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <section class="space-y-8">
        <h2
            class="text-2xl font-bold text-slate-900 tracking-tight flex items-center gap-3"
        >
            Comments
            <span class="text-sm font-medium text-slate-400">{{
                post.comments.length
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

        <CommentItem v-for="comment in post.comments" :comment="comment" />
    </section>
</template>
