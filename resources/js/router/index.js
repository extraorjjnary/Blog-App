import { createRouter, createWebHistory, useRoute } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    linkActiveClass: "font-bold",
    routes: [
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

        // Post Crud

        {
            path: "/",
            name: "posts.index",
            component: () => import("../pages/Post/Posts.vue"),
        },

        {
            path: "/posts/:id",
            name: "posts.show",
            component: () => import("../pages/Post/PostDetail.vue"),
        },
    ],
});

router.beforeEach((to, from) => {
    const user = localStorage.getItem("user");

    const requiresAuth = to.meta.requiresAuth;
    const isGuestRoute = ["register", "login"].includes(to.name);

    if (requiresAuth && !user) {
        return "/login";
    } else if (isGuestRoute && user) {
        return "/";
    }
    return true;
});

export default router;
