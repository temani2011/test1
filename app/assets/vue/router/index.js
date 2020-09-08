import Vue from "vue";
import VueRouter from "vue-router";
import store from "../store";
import Home from "../views/Home";
import Login from "../views/Login";
import Posts from "../views/Posts";
import News from "../views/News";
import ArticleCreate from "../components/News/ArticleCreate";
import ArticleEdit from "../components/News/ArticleEdit";
import ArticleSelect from "../components/News/ArticleSelect";
import User from "../views/User";
import UserSelect from "../components/User/UserSelect";
import UserCreate from "../components/User/UserCreate";
import UserEdit from "../components/User/UserEdit";
import UserDelete from "../components/User/UserDelete";
import Catalog from "../views/Catalog";

Vue.use(VueRouter);

let router = new VueRouter({
    mode: "history",
    routes: [
        { path: "/home", component: Home },
        { path: "/login", component: Login },
        { path: "/posts", component: Posts, meta: { requiresAuth: true } },
        { path: "/news", component: News },
        { path: '/news/new', component: ArticleCreate, meta: { requiresAuth: true } },
        { path: "/news/:id", component: ArticleSelect },
        { path: '/news/:id/edit', component: ArticleEdit, meta: { requiresAuth: true }  },
        { path: "/user/new", component: UserCreate },
        { path: "/users", component: User, meta: { requiresAuth: true } },
        { path: "/user/:id", component: UserSelect, meta: { requiresAuth: true } },
        { path: "/user/:id/edit", component: UserEdit, meta: { requiresAuth: true } },
        { path: "/catalogs", component: Catalog, meta: { requiresAuth: true } },
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