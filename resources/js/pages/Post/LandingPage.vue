<script setup>
import { ref } from "vue";
import { useAuthStore } from "../../stores/AuthStore";
import { useRouter } from "vue-router";

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
</script>

<template>
    <section
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

            <!-- Right Column: Visual Component Display -->
            <div
                class="lg:col-span-5 flex justify-center items-center relative"
            >
                <div
                    class="absolute inset-0 bg-linear-to-tr from-bro-crimson/5 to-transparent rounded-3xl transform rotate-3 scale-105 border border-bro-border pointer-events-none"
                ></div>

                <div
                    class="w-full max-w-md bg-bro-surface border border-bro-border p-8 rounded-2xl shadow-2xl relative group transition-all duration-300 hover:border-bro-muted/20"
                >
                    <div
                        class="absolute inset-0 opacity-5 bg-[radial-gradient(#9E9E9E_1px,transparent_1px)] bg-size:[16px_16px] rounded-2xl"
                    ></div>

                    <div class="relative space-y-6 z-10">
                        <div
                            class="flex items-center gap-3 bg-bro-bg p-3.5 border border-bro-border rounded-xl transition-transform duration-200 hover:translate-x-1"
                        >
                            <div
                                class="w-10 h-10 rounded-lg bg-bro-crimson/10 border border-bro-crimson/20 flex items-center justify-center text-bro-crimson"
                            >
                                <svg
                                    class="w-5 h-5 stroke-2"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M14 10h4.757a4.5 4.5 0 00-4.5-4.5H13M10 14H5.243a4.5 4.5 0 004.5 4.5H11m4 4h4.757a4.5 4.5 0 01-4.5-4.5H15M10 14V10"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p
                                    class="text-xs text-bro-muted font-semibold uppercase tracking-wider"
                                >
                                    Latest Reflection
                                </p>
                                <p class="text-sm font-bold text-bro-light">
                                    "Overcoming burnout in my 20s..."
                                </p>
                            </div>
                        </div>

                        <div
                            class="py-4 flex justify-center items-center gap-6 relative"
                        >
                            <div
                                class="absolute w-full h-0.5 bg-bro-border left-0 top-1/2 -translate-y-1/2 z-0"
                            ></div>
                            <div
                                class="w-12 h-12 rounded-full bg-bro-bg border-2 border-bro-border flex items-center justify-center shadow-xl relative z-10 hover:scale-110 transition-transform duration-200"
                            >
                                <span
                                    class="text-base opacity-75 filter grayscale"
                                    >💼</span
                                >
                            </div>
                            <div
                                class="w-16 h-16 rounded-full bg-bro-bg border-2 border-bro-crimson flex items-center justify-center shadow-bro-crimson/5 shadow-2xl relative z-10 hover:scale-110 transition-transform duration-200"
                            >
                                <span class="text-2xl">🔥</span>
                            </div>
                            <div
                                class="w-12 h-12 rounded-full bg-bro-bg border-2 border-bro-border flex items-center justify-center shadow-xl relative z-10 hover:scale-110 transition-transform duration-200"
                            >
                                <span
                                    class="text-base opacity-75 filter grayscale"
                                    >🏋️‍♂️</span
                                >
                            </div>
                        </div>

                        <div class="text-center">
                            <p class="text-xs text-bro-muted font-medium">
                                Join
                                <span class="text-bro-crimson font-bold"
                                    >12,450+ bros</span
                                >
                                sharing genuine insight today.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
