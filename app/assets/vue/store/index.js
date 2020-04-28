import Vue from "vue";
import Vuex from "vuex";
import SecurityModule from "./security";
import PostModule from "./post";
import NewsModule from "./news";
import CommentModule from "./comment";

Vue.use(Vuex);

export default new Vuex.Store({
    modules: {
        security: SecurityModule,
        post: PostModule,
        news: NewsModule,
        comment: CommentModule
    }
});