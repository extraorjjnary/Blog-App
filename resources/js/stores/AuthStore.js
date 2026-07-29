import { defineStore } from "pinia";
import { computed, ref } from "vue";
import api from "../services/api";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(JSON.parse(localStorage.getItem("user")));

    const isLoggedIn = computed(() => !!user.value);

    const login = async (credentials) => {
        await api.get("/sanctum/csrf-cookie");
        const response = await api.post("/login", credentials);

        user.value = response.data.user;

        localStorage.setItem("user", JSON.stringify(response.data.user));
        localStorage.removeItem("guest_id");
        localStorage.removeItem("guest_name");
    };

    const register = async (credentials) => {
        await api.get("/sanctum/csrf-cookie");
        const response = await api.post("/register", credentials);

        user.value = response.data.user;

        localStorage.setItem("user", JSON.stringify(response.data.user));
        localStorage.removeItem("guest_id");
        localStorage.removeItem("guest_name");
    };

    const logout = async () => {
        await api.post("/logout");

        user.value = null;

        localStorage.removeItem("user");
    };

    return { user, isLoggedIn, login, register, logout };
});
