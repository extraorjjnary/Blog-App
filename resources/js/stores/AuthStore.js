import { defineStore } from "pinia";
import { computed, ref } from "vue";
import api from "../services/api";

export const useAuthStore = defineStore("auth", () => {
    const user = ref(JSON.parse(localStorage.getItem("user")));

    const isLoggedIn = computed(() => !!user.value);

    const login = async (credentials) => {
        await api.get("/axios/csrf-cookie");
        const response = await api.post("/login", credentials);

        // set the reactive user state
        user.value = response.data.user;

        // persist to localStorage as backup
        localStorage.setItem("user", JSON.stringify(response.data.user));
    };

    const register = async (credentials) => {
        await api.get("/axios/csrf-cookie");
        const response = await api.post("/register", credentials);

        // set the reactive user state
        user.value = response.data.user;

        // persist to localStorage as backup
        localStorage.setItem("user", JSON.stringify(response.data.user));
    };

    const logout = async () => {
        await api.post("/logout");

        user.value = null;

        localStorage.removeItem("user");
    };

    return { user, isLoggedIn, login, register, logout };
});
