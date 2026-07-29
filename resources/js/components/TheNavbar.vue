<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { Menu, X } from "@lucide/vue";
import BaseError from "./ui/BaseError.vue";
import { useAuthStore } from "../stores/AuthStore";
import { useErrorHandler } from "../composables/useErrorHandler";

const auth = useAuthStore();
const router = useRouter();

const { getErrorMessage } = useErrorHandler();

const mobileMenuOpen = ref(false);

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
                <!-- Left: Logo & Desktop Navigation -->
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

                    <!-- Desktop Navigation Links -->
                    <div
                        class="hidden sm:ml-8 sm:flex sm:space-x-4 items-center"
                    >
                        <RouterLink
                            :to="{ name: 'posts.index' }"
                            class="px-3 py-2 rounded-lg text-sm font-medium text-bro-muted hover:text-bro-light hover:bg-bro-bg/50 transition-colors"
                            active-class="text-bro-crimson bg-bro-bg"
                        >
                            Explore
                        </RouterLink>
                        <RouterLink
                            v-if="auth.isLoggedIn"
                            :to="{ name: 'dashboard' }"
                            class="px-3 py-2 rounded-lg text-sm font-medium text-bro-muted hover:text-bro-light hover:bg-bro-bg/50 transition-colors"
                            active-class="text-bro-crimson bg-bro-bg"
                        >
                            Dashboard
                        </RouterLink>
                    </div>
                </div>

                <!-- Right: Auth Actions + Hamburger -->
                <div class="flex items-center gap-2.5">
                    <!-- Auth user info — desktop only -->
                    <div
                        v-if="auth.isLoggedIn"
                        class="hidden sm:flex items-center gap-2.5 px-3 py-1.5 bg-bro-surface/60 border border-bro-border rounded-xl"
                    >
                        <div
                            class="w-7 h-7 rounded-lg bg-bro-crimson/15 border border-bro-crimson/30 flex items-center justify-center text-bro-crimson font-black text-xs uppercase shrink-0"
                        >
                            {{ auth.user.name.charAt(0) }}
                        </div>
                        <div class="flex flex-col text-left">
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

                    <!-- Logout — desktop only -->
                    <button
                        v-if="auth.isLoggedIn"
                        @click="logout"
                        class="hidden sm:inline-flex items-center px-3 py-1.5 border border-bro-border rounded-xl text-xs font-semibold text-bro-muted bg-bro-bg hover:bg-rose-950/30 hover:text-rose-400 hover:border-rose-800/40 transition-all cursor-pointer active:scale-95 shadow-xs"
                    >
                        Logout
                    </button>

                    <!-- Guest buttons — desktop only -->
                    <template v-if="!auth.isLoggedIn">
                        <RouterLink
                            :to="{ name: 'register' }"
                            class="hidden sm:inline-flex items-center px-3.5 py-1.5 border border-bro-border rounded-xl text-xs font-semibold text-bro-muted hover:text-bro-light bg-bro-bg hover:bg-bro-surface transition-all cursor-pointer active:scale-95 shadow-xs"
                        >
                            Register
                        </RouterLink>
                        <RouterLink
                            :to="{ name: 'login' }"
                            class="hidden sm:inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-bro-crimson hover:bg-bro-crimson/90 transition-all shadow-md shadow-bro-crimson/20 active:scale-95 cursor-pointer"
                        >
                            Login
                        </RouterLink>
                    </template>

                    <!-- Hamburger Button — mobile only -->
                    <button
                        @click="mobileMenuOpen = !mobileMenuOpen"
                        class="sm:hidden p-2 rounded-lg border border-bro-border bg-bro-bg text-bro-muted hover:text-bro-light hover:bg-bro-surface transition-all cursor-pointer"
                    >
                        <Menu v-if="!mobileMenuOpen" class="w-5 h-5" />

                        <X v-else class="w-5 h-5" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <Transition
            enter-from-class="opacity-0 -translate-y-2"
            enter-to-class="opacity-100 translate-y-0"
            enter-active-class="transition duration-200"
            leave-active-class="transition duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-2"
        >
            <div
                v-if="mobileMenuOpen"
                class="sm:hidden border-t border-bro-border bg-bro-surface px-4 py-4 space-y-2"
            >
                <RouterLink
                    :to="{ name: 'posts.index' }"
                    @click="mobileMenuOpen = false"
                    class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium text-bro-muted hover:text-bro-light hover:bg-bro-bg transition-colors"
                    active-class="text-bro-crimson bg-bro-bg"
                >
                    Explore
                </RouterLink>

                <RouterLink
                    v-if="auth.isLoggedIn"
                    :to="{ name: 'dashboard' }"
                    @click="mobileMenuOpen = false"
                    class="flex items-center px-3 py-2.5 rounded-xl text-sm font-medium text-bro-muted hover:text-bro-light hover:bg-bro-bg transition-colors"
                    active-class="text-bro-crimson bg-bro-bg"
                >
                    Dashboard
                </RouterLink>

                <div class="border-t border-bro-border my-2"></div>

                <!-- Auth user info on mobile -->
                <div
                    v-if="auth.isLoggedIn"
                    class="flex items-center gap-3 px-3 py-2"
                >
                    <div
                        class="w-8 h-8 rounded-lg bg-bro-crimson/15 border border-bro-crimson/30 flex items-center justify-center text-bro-crimson font-black text-sm shrink-0"
                    >
                        {{ auth.user.name.charAt(0) }}
                    </div>
                    <div>
                        <p class="text-xs font-bold text-bro-light">
                            {{ auth.user.name }}
                        </p>
                        <p class="text-[10px] text-bro-muted/70">
                            {{ auth.user.email }}
                        </p>
                    </div>
                </div>

                <!-- Logout on mobile -->
                <button
                    v-if="auth.isLoggedIn"
                    @click="
                        logout();
                        mobileMenuOpen = false;
                    "
                    class="w-full flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold text-rose-400 hover:bg-rose-950/30 hover:border-rose-800/40 border border-transparent transition-all cursor-pointer"
                >
                    Logout
                </button>

                <!-- Guest buttons on mobile -->
                <template v-if="!auth.isLoggedIn">
                    <RouterLink
                        :to="{ name: 'register' }"
                        @click="mobileMenuOpen = false"
                        class="flex items-center justify-center px-3 py-2.5 rounded-xl text-sm font-semibold text-bro-muted border border-bro-border hover:text-bro-light hover:bg-bro-bg transition-all"
                    >
                        Register
                    </RouterLink>
                    <RouterLink
                        :to="{ name: 'login' }"
                        @click="mobileMenuOpen = false"
                        class="flex items-center justify-center px-3 py-2.5 rounded-xl text-sm font-bold text-white bg-bro-crimson hover:bg-bro-crimson/90 transition-all shadow-md shadow-bro-crimson/20"
                    >
                        Login
                    </RouterLink>
                </template>
                <BaseError v-if="errorMessage" :error-messages="errorMessage" />
            </div>
        </Transition>
    </nav>
</template>
