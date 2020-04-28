import CommentAPI from "../api/comment";

const LOADING_COMMENTS = "LOADING_COMMENTS",
    ERROR= "ERROR",
    CREATING_COMMENT_SUCCESS = "CREATING_COMMENT_SUCCESS",
    FETCHING_COMMENTS_SUCCESS = "FETCHING_COMMENTS_SUCCESS";

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        comments: []
    },
    getters: {
        isLoading(state) {
            return state.isLoading;
        },
        hasError(state) {
            return state.error !== null;
        },
        getError(state) {
            return state.error;
        },
        hasComments(state) {
            return state.comments.length > 0;
        },
        getComments(state) {
            return state.comments;
        }
    },
    mutations: {
        [LOADING_COMMENTS](state) {
            state.isLoading = true;
            state.error = null;
            state.comments = [];
        },
        [ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        [FETCHING_COMMENTS_SUCCESS](state, comments) {
            state.isLoading = false;
            state.error = null;
            state.comments = comments;
        },
        [CREATING_COMMENT_SUCCESS](state, comments) {
            state.isLoading = false;
            state.error = null;
            state.comments.unshift(comments);
        }
    },
    actions: {
        async postComment({ commit }, payload) {
            commit(LOADING_COMMENTS);
            console.log('post comment, payload' + payload);
            try {
                let response = await CommentAPI.postComment(payload);
                console.log(response);
                commit(CREATING_COMMENT_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async getAllComments({ commit }, pid) {
            commit(LOADING_COMMENTS);
            console.log('getAll comments, pid:' + pid);
            try {
                let response = await CommentAPI.getAllComments(pid);
                commit(FETCHING_COMMENTS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async getComment({commit}, id){
            commit(LOADING_COMMENTS);
            console.log('getOne comment, slug:' + id);
            try{
                let response = await CommentAPI.getComment(id);
                console.log(response);
                commit(FETCHING_COMMENTS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        }
    }
};