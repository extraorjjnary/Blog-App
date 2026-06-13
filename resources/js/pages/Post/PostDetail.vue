<script setup>
import { onMounted, ref } from "vue";
import api from "../../services/api";
import { useRoute } from "vue-router";
import dayjs from "dayjs";
import { useReactionCounter } from "../../composables/useReactionCounter";
import { formatDistanceToNow } from "date-fns";
import BaseLoader from "../../components/ui/BaseLoader.vue";
import BaseError from "../../components/ui/BaseError.vue";

// post looks like this:
// {
//     id,
//     user_id,
//     title,
//     content,
//     user: {},
//     comments: [],
//     reactions: [],
// }

const post = ref(null);

const { upVoteCount, downVoteCount } = useReactionCounter(post);

const route = useRoute();

const loading = ref(false);
const errorMessage = ref(null);

const fetchPost = async (id) => {
    loading.value = true;
    errorMessage.value = null;
    try {
        const response = await api.get(`/posts/${id}`);
        post.value = response.data;
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

// reaction type counter: upvote, downvote
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
                        class="group flex items-center gap-3 px-6 py-3 bg-emerald-50/50 border border-emerald-100 rounded-full text-emerald-700 hover:bg-emerald-50 hover:border-emerald-200 hover:-translate-y-0.5 transition-all cursor-pointer"
                    >
                        <svg
                            class="w-6 h-6 text-emerald-500 group-hover:scale-110 transition-transform"
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
                        <span class="text-lg font-medium text-emerald-600">{{
                            upVoteCount
                        }}</span>
                    </button>

                    <button
                        class="group flex items-center gap-3 px-6 py-3 bg-rose-50/50 border border-rose-100 rounded-full text-rose-700 hover:bg-rose-50 hover:border-rose-200 hover:-translate-y-0.5 transition-all cursor-pointer"
                    >
                        <svg
                            class="w-6 h-6 text-rose-400 group-hover:scale-110 transition-transform"
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
                        <span class="text-lg font-medium text-rose-600">{{
                            downVoteCount
                        }}</span>
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
</template>
