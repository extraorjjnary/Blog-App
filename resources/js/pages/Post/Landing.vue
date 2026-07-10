<script setup>
import { onMounted, ref } from "vue";
import { useAuthStore } from "../../stores/AuthStore";
import { useRouter } from "vue-router";
import api from "../../services/api";
import BaseError from "../../components/ui/BaseError.vue";
import BaseLoader from "../../components/ui/BaseLoader.vue";
import dayjs from "../../../utils/dayjs.js";

const post = ref(null);

const router = useRouter();

const auth = useAuthStore();

const goToExplore = () => router.push({ name: "posts.index" });

const goToCreate = () => {
    if (!auth.isLoggedIn) {
        router.push({ name: "login" });
    } else {
        router.push({ name: "posts.index" });
    }
};

const loading = ref(false);
const errorMessage = ref(null);

const fetchLatestPost = async () => {
    loading.value = true;
    errorMessage.value = null;
    try {
        const response = await api.get("/posts?limit=1");
        post.value = response.data.data[0];
    } catch (error) {
        errorMessage.value =
            error.response?.data?.message || "Failed to fetch latest post.";
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchLatestPost();
});
</script>

<template>
    <BaseLoader v-if="loading" />
    <section
        v-else
        class="relative bg-bro-bg text-bro-light overflow-hidden min-h-[85vh] flex items-center"
    >
        <div
            class="absolute top-0 left-1/4 w-96 h-96 bg-bro-crimson/5 rounded-full blur-3xl pointer-events-none"
        ></div>

        <div
            class="max-w-7xl mx-auto px-6 py-16 md:py-24 w-full grid grid-cols-1 lg:grid-cols-12 gap-12 items-center relative z-10"
        >
            <div class="lg:col-span-7 space-y-8 text-center lg:text-left">
                <h1
                    class="text-4xl sm:text-5xl md:text-6xl font-black tracking-tight text-white leading-[1.1]"
                >
                    Real Experiences. <br />
                    <span
                        class="text-transparent bg-clip-text bg-linear-to-r from-bro-crimson to-bro-crimson-hover"
                    >
                        Welcome to BroCore.
                    </span>
                </h1>

                <p
                    class="text-base sm:text-lg text-bro-muted max-w-xl mx-auto lg:mx-0 font-medium leading-relaxed"
                >
                    A dedicated space for men to share relatable struggles, life
                    lessons, and funny moments. Grow, laugh, and navigate life
                    alongside a community that actually gets it.
                </p>

                <div
                    class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-2"
                >
                    <button
                        @click="goToExplore"
                        type="button"
                        class="w-full sm:w-auto px-8 py-4 bg-bro-crimson hover:bg-bro-crimson-hover text-white font-bold rounded-xl shadow-lg shadow-bro-crimson/10 transition-all duration-150 hover:-translate-y-0.5 active:scale-95 cursor-pointer select-none"
                    >
                        Explore Experiences
                    </button>
                    <button
                        @click="goToCreate"
                        type="button"
                        class="w-full sm:w-auto px-8 py-4 bg-bro-surface hover:bg-bro-surface/80 text-bro-light font-bold rounded-xl border border-bro-border transition-all duration-150 hover:-translate-y-0.5 active:scale-95 cursor-pointer select-none"
                    >
                        Share Your Story
                    </button>
                </div>
            </div>

            <BaseError v-if="errorMessage" :error-messages="errorMessage" />
            <!-- Right Column: Visual Component Display -->
            <div
                v-if="post"
                class="lg:col-span-5 flex justify-center items-center"
            >
                <div
                    class="w-full max-w-md bg-bro-surface border border-bro-border rounded-2xl overflow-hidden shadow-2xl"
                >
                    <!-- Header -->
                    <div
                        class="px-5 py-4 border-b border-bro-border flex items-center justify-between"
                    >
                        <div>
                            <p
                                class="text-[11px] uppercase tracking-widest text-bro-muted font-bold"
                            >
                                Latest Experience
                            </p>
                            <p class="text-xs text-bro-muted mt-1">
                                Community Preview
                            </p>
                        </div>
                    </div>

                    <!-- Featured Post -->
                    <div class="p-6 space-y-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-full bg-bro-bg border border-bro-border flex items-center justify-center font-bold text-bro-crimson"
                            >
                                {{ post.user.name.charAt(0).toUpperCase() }}
                            </div>

                            <div>
                                <p class="font-bold text-bro-light text-sm">
                                    {{ post.user.name }}
                                </p>
                                <p class="text-xs text-bro-muted">
                                    {{ dayjs(post.created_at).fromNow() }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h3
                                class="text-lg font-extrabold text-bro-light leading-snug"
                            >
                                {{ post.title }}
                            </h3>

                            <p
                                class="text-sm text-bro-muted leading-relaxed line-clamp-3 wrap-break-word"
                            >
                                {{ post.content }}
                            </p>
                        </div>

                        <!-- Stats -->
                        <div
                            class="flex items-center gap-5 pt-2 text-sm font-semibold"
                        >
                            <span
                                class="inline-flex items-center gap-2 text-emerald-500"
                            >
                                👍 {{ post.upvotes_count }}
                            </span>

                            <span
                                class="inline-flex items-center gap-2 text-emerald-500"
                            >
                                👎 {{ post.downvotes_count }}
                            </span>

                            <span
                                class="inline-flex items-center gap-2 text-sky-400"
                            >
                                💬 {{ post.comments_count }}
                            </span>
                        </div>
                    </div>

                    <!-- replace the entire Categories section with this -->
                    <div
                        class="px-6 py-4 border-t border-bro-border bg-bro-bg/40 flex items-center justify-between"
                    >
                        <div class="text-center">
                            <p
                                class="text-lg font-extrabold text-bro-crimson font-mono"
                            >
                                {{ post.upvotes_count + post.downvotes_count }}
                            </p>
                            <p
                                class="text-[10px] uppercase tracking-widest text-bro-muted font-bold mt-0.5"
                            >
                                Total Reactions
                            </p>
                        </div>

                        <div class="w-px h-8 bg-bro-border"></div>

                        <div class="text-center">
                            <p
                                class="text-lg font-extrabold text-bro-light font-mono"
                            >
                                {{ post.comments_count }}
                            </p>
                            <p
                                class="text-[10px] uppercase tracking-widest text-bro-muted font-bold mt-0.5"
                            >
                                Brothers Responded
                            </p>
                        </div>

                        <div class="w-px h-8 bg-bro-border"></div>

                        <RouterLink
                            :to="{
                                name: 'posts.show',
                                params: { id: post.id },
                            }"
                            class="text-[11px] font-bold text-bro-crimson hover:text-bro-crimson-hover uppercase tracking-wider transition-colors"
                        >
                            Read →
                        </RouterLink>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
