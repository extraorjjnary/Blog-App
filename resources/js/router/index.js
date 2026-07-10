import { createRouter, createWebHistory, useRoute } from "vue-router";

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    linkActiveClass: "font-bold",
    routes: [
        {
            path: "/",
            component: () => import("../components/layouts/DefaultLayout.vue"), // Contains Navbar + Inner RouterView
            children: [
                {
                    path: "",
                    name: "landing",
                    component: () => import("../pages/Post/Landing.vue"),
                },
                {
                    path: "/dashboard",
                    name: "dashboard",
                    component: () => import("../pages/Post/Dashboard.vue"),
                    meta: { requiresAuth: true },
                },
                {
                    path: "/posts",
                    name: "posts.index",
                    component: () => import("../pages/Post/Posts.vue"),
                },
                {
                    path: "posts/:id",
                    name: "posts.show",
                    component: () => import("../pages/Post/PostDetail.vue"),
                },
            ],
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
    ],
});

router.beforeEach((to, from) => {
    const user = localStorage.getItem("user");

    const requiresAuth = to.meta.requiresAuth;
    const isGuestRoute = ["register", "login"].includes(to.name);

    if (requiresAuth && !user) {
        return { name: "login" };
    } else if (isGuestRoute && user) {
        return { name: "posts.index" };
    }
    return true;
});

export default router;
