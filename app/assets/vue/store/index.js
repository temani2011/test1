import Vue from "vue";
import Vuex from "vuex";
import SecurityModule from "./security";
import PostModule from "./post";
import NewsModule from "./news";
import CommentModule from "./comment";
import UserModule from "./user";
import CatalogModule from "./catalog";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        security: SecurityModule,
        post: PostModule,
        news: NewsModule,
        comment: CommentModule,
        user: UserModule,
        catalog: CatalogModule
    }
});