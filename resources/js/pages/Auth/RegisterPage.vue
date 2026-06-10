<script setup>
import { ref } from "vue";
import BaseCard from "../../components/Auth/BaseCard.vue";
import BaseInput from "../../components/Auth/BaseInput.vue";
import BaseButton from "../../components/Auth/BaseButton.vue";
import api from "../../services/api.js";
import { useRouter } from "vue-router";

const router = useRouter();

// Reactive state to hold registration form parameters
const form = ref({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const loading = ref(false);
const error = ref(null);

const regsiter = async () => {
    loading.value = true;
    error.value = null;

    try {
        await api.get("/sanctum/csrf-cookie");
        const response = await api.post("/register", form.value);
        localStorage.setItem("user", JSON.stringify(response.data.user));

        router.push("/dashboard");
    } catch (err) {
        error.value = err.response?.data?.message || "Registration failed.";
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <BaseCard
        title="Create Author Account"
        sub-title="Get started with your fresh SPA blog instance."
    >
        <p v-if="error" class="text-red-500 mb-4">{{ error }}</p>

        <!-- Form Section -->
        <form class="mt-8 space-y-6" @submit.prevent="register">
            <!-- Default Slot: The Form Content  -->

            <BaseInput
                id="name"
                v-model="form.name"
                label="Full Name"
                placeholder="John Doe"
            />

            <BaseInput
                id="email"
                v-model="form.email"
                label="Email Address"
                type="email"
                placeholder="you@example.com"
            />

            <BaseInput
                id="password"
                v-model="form.password"
                label="Password"
                type="password"
                placeholder="••••••••"
            />

            <BaseInput
                id="password_confirmation"
                v-model="form.password_confirmation"
                label="Password Confirmation"
                type="password"
                placeholder="••••••••"
            />

            <!-- Submit Button -->
            <BaseButton :is-loading="loading" @click="regsiter">
                {{ loading ? "Registering..." : "Register Account" }}
            </BaseButton>
        </form>

        <!-- Footer / Toggle Link -->
        <template #footer>
            <p class="text-sm text-slate-500">
                Already have an Account?

                <RouterLink
                    to="/login"
                    class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors"
                >
                    Login instead
                </RouterLink>
            </p>
        </template>
    </BaseCard>
</template>
