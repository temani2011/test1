import Vue from "vue";
import VueRouter from "vue-router";
import store from "../store";
import Home from "../views/Home";
import Login from "../views/Login";
import Posts from "../views/Posts";
import News from "../views/News";
import NewsArticle from "../views/NewsArticle";
import User from "../views/User";

Vue.use(VueRouter);

let router = new VueRouter({
    mode: "history",
    routes: [
        { path: "/home", component: Home, meta: { requiresAuth: true } },
        { path: "/login", component: Login },
        { path: "/posts", component: Posts, meta: { requiresAuth: true } },
        { path: "/news", component: News },
        { path: "/news/:id", name: 'article', component: NewsArticle, props: true },
        { path: "/user/:id", name: 'user', component: User, props: true, meta: { requiresAuth: true } },
        { path: "*", redirect: "/home" }
    ],
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.requiresAuth)) {
        // this route requires auth, check if logged in
        // if not, redirect to login page.
        if (store.getters["security/isAuthenticated"]) {
            next();
        } else {
            next({
                path: "/login",
                query: { redirect: to.fullPath }
            });
        }
    } else {
        next(); // make sure to always call next()!
    }
});

export default router;