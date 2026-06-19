<script setup>
import { ref } from "vue";
import BaseCard from "../../components/Auth/BaseCard.vue";
import BaseInput from "../../components/Auth/BaseInput.vue";
import BaseButton from "../../components/Auth/BaseButton.vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/AuthStore.js";

const router = useRouter();
const auth = useAuthStore();

const credentials = ref({
    email: "",
    password: "",
});

const loading = ref(false);
const error = ref(null);

const login = async () => {
    loading.value = true;
    error.value = null;

    try {
        await auth.login(credentials.value);
        router.push({ name: "posts.index" });
    } catch (err) {
        error.value = err.response?.data?.message || "Login failed.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 relative flex flex-col">
        <header
            class="absolute top-0 left-0 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center z-10"
        >
            <RouterLink
                :to="{ name: 'posts.index' }"
                class="text-xl font-bold text-indigo-600 tracking-tight hover:opacity-80 transition-opacity"
            >
                BroCore<span class="text-slate-800">.io</span>
            </RouterLink>
        </header>

        <main class="grow flex items-center justify-center pt-16 pb-6">
            <BaseCard
                title="Welcome back, Browskie"
                sub-title="Sign in to manage your developer blog."
            >
                <p v-if="error" class="text-red-500 mb-4">{{ error }}</p>

                <form class="mt-8 space-y-6" @submit.prevent="login">
                    <BaseInput
                        id="email"
                        v-model="credentials.email"
                        label="Email Address"
                        type="email"
                        placeholder="you@example.com"
                    />

                    <BaseInput
                        id="password"
                        v-model="credentials.password"
                        label="Password"
                        type="password"
                        placeholder="••••••••"
                    />

                    <BaseButton :is-loading="loading">
                        {{ loading ? "Signing In..." : "Sign In" }}
                    </BaseButton>
                </form>

                <template #footer>
                    <div class="space-y-4">
                        <p class="text-sm text-slate-500">
                            Don't have an Account?
                            <RouterLink
                                to="/register"
                                class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors"
                            >
                                Register here
                            </RouterLink>
                        </p>

                        <div
                            class="pt-3 border-t border-slate-100 flex justify-center"
                        >
                            <RouterLink
                                :to="{ name: 'posts.index' }"
                                class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-indigo-600 transition-colors"
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
                                        d="M10 19l-7-7m0 0l7-7m-7 7h18"
                                    />
                                </svg>
                                Back to Recent Experiences
                            </RouterLink>
                        </div>
                    </div>
                </template>
            </BaseCard>
        </main>
    </div>
</template>
