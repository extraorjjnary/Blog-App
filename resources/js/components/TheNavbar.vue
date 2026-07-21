<script setup>
import { useErrorHandler } from "../composables/useErrorHandler";
import { useAuthStore } from "../stores/AuthStore";
import { useRouter } from "vue-router";
import BaseError from "./ui/BaseError.vue";
import { ref } from "vue";
const { getErrorMessage } = useErrorHandler();

const auth = useAuthStore();
const router = useRouter();

const errorMessage = ref(null);
const logout = async () => {
    errorMessage.value = null;
    try {
        await auth.logout();
        router.push({ name: "landing" });
    } catch (error) {
        errorMessage.value = getErrorMessage(error, "Failed to logout.");
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
                            :to="{ name: 'landing' }"
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
                            :to="{ name: 'dashboard' }"
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

                    <BaseError
                        v-if="errorMessage"
                        :error-messages="errorMessage"
                    />
                </div>

                <!-- Authenticated State -->
                <div v-if="auth.isLoggedIn" class="flex items-center space-x-4">
                    <div
                        class="flex items-center gap-2.5 px-3 py-1.5 bg-bro-surface/60 border border-bro-border rounded-xl"
                    >
                        <!-- User Initial Avatar Circle -->
                        <div
                            class="w-7 h-7 rounded-lg bg-bro-crimson/15 border border-bro-crimson/30 flex items-center justify-center text-bro-crimson font-black text-xs uppercase shrink-0"
                        >
                            {{ auth.user.name.charAt(0) }}
                        </div>

                        <div class="hidden sm:flex flex-col text-left">
                            <span
                                class="text-xs font-bold text-bro-light leading-tight"
                            >
                                {{ auth.user.name }}
                            </span>
                            <span
                                class="text-[10px] font-medium text-bro-muted/70 leading-tight truncate max-w-30"
                            >
                                {{ auth.user.email }}
                            </span>
                        </div>
                    </div>

                    <button
                        @click="logout"
                        class="inline-flex items-center px-3 py-1.5 border border-bro-border rounded-xl text-xs font-semibold text-bro-muted bg-bro-bg hover:bg-rose-950/30 hover:text-rose-400 hover:border-rose-800/40 transition-all cursor-pointer active:scale-95 shadow-xs"
                        title="Log out of your account"
                    >
                        Logout
                    </button>
                </div>

                <div v-else class="flex items-center gap-2.5">
                    <RouterLink
                        :to="{ name: 'register' }"
                        class="inline-flex items-center px-3.5 py-1.5 border border-bro-border rounded-xl text-xs font-semibold text-bro-muted hover:text-bro-light bg-bro-bg hover:bg-bro-surface transition-all cursor-pointer active:scale-95 shadow-xs"
                    >
                        Register
                    </RouterLink>

                    <RouterLink
                        :to="{ name: 'login' }"
                        class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-bro-crimson hover:bg-bro-crimson/90 transition-all shadow-md shadow-bro-crimson/20 active:scale-95 cursor-pointer"
                    >
                        Login
                    </RouterLink>
                </div>
            </div>
        </div>
    </nav>
</template>
