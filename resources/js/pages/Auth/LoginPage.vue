<script setup>
import { ref } from "vue";
import BaseCard from "../../components/Auth/BaseCard.vue";
import BaseInput from "../../components/Auth/BaseInput.vue";
import BaseButton from "../../components/Auth/BaseButton.vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/AuthStore.js";

const router = useRouter();

const auth = useAuthStore();

// Reactive state to hold your input values
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
        router.push("/");
    } catch (err) {
        error.value = err.response?.data?.message || "Login failed.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <BaseCard
        title="Welcome back, Utol"
        sub-title="Sign in to manage your developer blog."
    >
        <p v-if="error" class="text-red-500 mb-4">{{ error }}</p>
        <!-- Form Section -->
        <form class="mt-8 space-y-6" @submit.prevent="login">
            <!-- Default Slot: The Form Content  -->

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

            <!-- Submit Button -->
            <BaseButton :is-loading="loading" @click="login">
                {{ loading ? "Signing In..." : "Sign In" }}
            </BaseButton>
        </form>

        <!-- Footer / Toggle Link -->
        <template #footer>
            <p class="text-sm text-slate-500">
                Don't have an Account?

                <RouterLink
                    to="/register"
                    class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors"
                >
                    Register here
                </RouterLink>
            </p>
        </template>
    </BaseCard>
</template>
