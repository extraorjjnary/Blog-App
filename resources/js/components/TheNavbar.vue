<script setup>
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../stores/AuthStore";

const auth = useAuthStore();
const router = useRouter();

// Dummy logout handler - you'll wire your Sanctum Axios call here later!
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
    <nav class="bg-white border-b border-slate-100 sticky top-0 z-50 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left Side: Brand Logo & Navigation Links -->
                <div class="flex">
                    <div class="shrink-0 flex items-center">
                        <!-- RouterLink keeps it an SPA (no full page reload!) -->
                        <RouterLink
                            to="/"
                            class="text-xl font-bold text-indigo-600 tracking-tight"
                        >
                            DevBlog<span class="text-slate-800">.io</span>
                        </RouterLink>
                    </div>

                    <!-- Desktop Navigation Menu -->
                    <div
                        class="hidden sm:ml-8 sm:flex sm:space-x-4 items-center"
                    >
                        <RouterLink
                            to="/"
                            class="px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50 transition-colors"
                            active-class="text-indigo-600 bg-indigo-50/50"
                        >
                            Dashboard
                        </RouterLink>
                        <RouterLink
                            v-if="auth.isLoggedIn"
                            to=""
                            class="px-3 py-2 rounded-md text-sm font-medium text-slate-700 hover:text-indigo-600 hover:bg-slate-50 transition-colors"
                            active-class="text-indigo-600 bg-indigo-50/50"
                        >
                            New Post
                        </RouterLink>
                    </div>
                </div>

                <!-- Right Side: User Profile & Actions **Display only this when the user is auth** -->
                <div v-if="auth.isLoggedIn" class="flex items-center space-x-4">
                    <div class="hidden sm:flex flex-col text-right">
                        <span class="text-sm font-medium text-slate-700"
                            >Utol Developer</span
                        >
                        <span class="text-xs text-slate-400">Author</span>
                    </div>

                    <!-- Logout Button (Ready for your Axios method later) -->
                    <button
                        @click="logout"
                        class="inline-flex items-center px-3.5 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 transition-all cursor-pointer"
                    >
                        Logout
                    </button>
                </div>

                <!-- Auth Actions  **Display only this when the user is guest** -->
                <div
                    v-if="!auth.isLoggedIn"
                    class="flex items-center space-x-4"
                >
                    <!-- Register Button  -->
                    <RouterLink
                        to="/register"
                        class="inline-flex items-center px-3.5 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 transition-all cursor-pointer"
                    >
                        Register
                    </RouterLink>

                    <!-- Login Button **Display only this when the user is guest** -->
                    <RouterLink
                        to="/login"
                        class="inline-flex items-center px-3.5 py-2 border border-slate-200 rounded-lg text-sm font-medium text-slate-600 bg-white hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100 transition-all cursor-pointer"
                    >
                        Login
                    </RouterLink>
                </div>
            </div>
        </div>
    </nav>
</template>
