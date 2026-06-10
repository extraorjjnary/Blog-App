import { createRouter, createWebHistory } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    linkActiveClass: "font-bold",
    routes: [
        {
            path: "/",
            name: "landing",
            component: () => import("../pages/LandingPage.vue"),
        },
        {
            path: "/register",
            name: "register",
            component: () => import("../pages/Auth/RegisterPage.vue"),
        },
        {
            path: "/login",
            name: "login",
            component: () => import("../pages/Auth/LoginPage.vue"),
        },
        {
            path: "/dashboard",
            name: "dashboard",
            component: () => import("../pages/DashboardPage.vue"),
            meta: { requiresAuth: true },
        },
    ],
});

router.beforeEach((to, from) => {
    const user = localStorage.getItem("user");

    const requiresAuth = to.meta.requiresAuth;
    const isGuestRoute = ["register", "login", "landing"].includes(to.name);

    if (requiresAuth && !user) {
        return "/login";
    } else if (isGuestRoute && user) {
        return "/dashboard";
    }
    return true;
});

export default router;
