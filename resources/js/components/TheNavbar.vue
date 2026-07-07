<script setup>
import { useAuthStore } from "../stores/AuthStore";
import { useRouter } from "vue-router";

const auth = useAuthStore();
const router = useRouter();

const logout = async () => {
    try {
        await auth.logout();
        router.push("/");
    } catch (err) {
        console.error("Logout error:", err);
    }
};
</script>

<template>
    <nav
        class="bg-bro-surface border-b border-bro-border sticky top-0 z-50 shadow-md"
    >
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: Logo & Navigation -->
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <RouterLink
                            to="/"
                            class="text-xl font-black tracking-tight hover:opacity-90 transition-opacity"
                        >
                            <span class="text-bro-light">Bro</span
                            ><span class="text-bro-crimson">Core</span>
                        </RouterLink>
                    </div>

                    <!-- Navigation Links -->
                    <div
                        class="hidden sm:ml-8 sm:flex sm:space-x-4 items-center"
                    >
                        <RouterLink
                            to="/"
                            class="px-3 py-2 rounded-lg text-sm font-medium text-bro-muted hover:text-bro-light hover:bg-bro-bg/50 transition-colors"
                            active-class="text-bro-crimson bg-bro-bg"
                        >
                            Dashboard
                        </RouterLink>
                        <RouterLink
                            v-if="auth.isLoggedIn"
                            :to="{ name: 'posts.index' }"
                            class="px-3 py-2 rounded-lg text-sm font-medium text-bro-muted hover:text-bro-light hover:bg-bro-bg/50 transition-colors"
                            active-class="text-bro-crimson bg-bro-bg"
                        >
                            Share Your Story
                        </RouterLink>
                    </div>
                </div>

                <!-- Right Side: Auth Toggle Viewports -->
                <!-- Authenticated State -->
                <div v-if="auth.isLoggedIn" class="flex items-center space-x-4">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-sm font-medium text-bro-light"
                            >Utol Developer</span
                        >
                        <span class="text-xs text-bro-muted">Author</span>
                    </div>
                    <button
                        @click="logout"
                        class="inline-flex items-center px-3.5 py-2 border border-bro-border rounded-lg text-sm font-medium text-bro-muted bg-bro-bg hover:bg-rose-950/20 hover:text-rose-400 hover:border-rose-900/40 transition-all cursor-pointer active:scale-95"
                    >
                        Logout
                    </button>
                </div>

                <!-- Guest State -->
                <div v-else class="flex items-center space-x-3">
                    <RouterLink
                        to="/register"
                        class="inline-flex items-center px-3.5 py-2 border border-bro-border rounded-lg text-sm font-medium text-bro-light bg-bro-bg hover:bg-bro-surface transition-all active:scale-95"
                    >
                        Register
                    </RouterLink>
                    <RouterLink
                        to="/login"
                        class="inline-flex items-center px-3.5 py-2 rounded-lg text-sm font-bold text-bro-light bg-bro-crimson hover:bg-bro-crimson-hover transition-all shadow-md shadow-bro-crimson/10 active:scale-95"
                    >
                        Login
                    </RouterLink>
                </div>
            </div>
        </div>
    </nav>
</template>
