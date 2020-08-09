import NewsAPI from "../api/news";

const LOADING_NEWS = "LOADING_NEWS",
    ERROR= "ERROR",
    CREATING_NEWS_SUCCESS = "CREATING_NEWS_SUCCESS",
    //CREATING_NEWS_ERROR = "CREATING_NEWS_ERROR",
    //FETCHING_NEWS = "FETCHING_NEWS",
    FETCHING_NEWS_SUCCESS = "FETCHING_NEWS_SUCCESS",
    //FETCHING_NEWS_ERROR = "FETCHING_NEWS_ERROR",
    //UPDATING_NEWS = "UPDATING_NEWS",
    UPDATING_NEWS_SUCCESS = "UPDATING_NEWS_SUCCESS",
    //UPDATING_NEWS_ERROR = "UPDATING_NEWS_ERROR",
   //DELETING_NEWS = "DELETING_NEWS",
    DELETING_NEWS_SUCCESS = "DELETING_NEWS_SUCCESS";
    //DELETING_NEWS_ERROR = "DELETING_NEWS_ERROR";

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        news: []
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
        hasNews(state) {
            return state.news.length > 0;
        },
        getNews(state) {
            return state.news;
        }
    },
    mutations: {
        [LOADING_NEWS](state) {
            state.isLoading = true;
            state.error = null;
        },
        [ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },

        //[CREATING_NEWS, UPDATING_NEWS, DELETING_NEWS](state) {
        // [CREATING_NEWS](state){
        //     state.isLoading = true;
        //     state.error = null;
        // },
        //[CREATING_NEWS_ERROR, FETCHING_NEWS_ERROR, UPDATING_NEWS_ERROR, DELETING_NEWS_ERROR](state, error) {
        [FETCHING_NEWS_SUCCESS](state, news) {
            state.isLoading = false;
            state.error = null;
            console.log(news);
            if(Array.isArray(news))
                state.news = news;
            else state.news.unshift(news);
        },
        // [FETCHING_NEWS_ERROR](state){
        //     state.isLoading = false;
        //     state.error = error;
        //     state.news = [];
        // },
        [CREATING_NEWS_SUCCESS](state, news) {
            state.isLoading = false;
            state.error = null;
            state.news.unshift(news);
        },
        [UPDATING_NEWS_SUCCESS](state, news) {
            state.isLoading = false;
            state.error = null;
            state.news.unshift(news);
            const index = state.news.findIndex(newsItem => newsItem.id === news.id);
            console.log(index);
            Vue.set(state.news, index, news);
        },
        [DELETING_NEWS_SUCCESS](state, news) {
            state.isLoading = false;
            state.error = null;
            const index = state.news.findIndex(newsItem => newsItem.id === news.id);
            console.log(index);
            state.news.splice(index, 1);
        }
    },
    actions: {
        async postNews({ commit }, payload) {
            commit(LOADING_NEWS);
            console.log('post');
            try {
                console.log(payload);
                let response = await NewsAPI.postNews(payload);
                console.log(response);
                commit(CREATING_NEWS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async getAllNews({ commit }) {
            commit(LOADING_NEWS);
            console.log('getAll');
            try {
                let response = await NewsAPI.getAllNews();
                commit(FETCHING_NEWS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async getNews({commit}, id){
            commit(LOADING_NEWS);
            console.log('getOne');
            try{
                let response = await NewsAPI.getNews(id);
                console.log(response);
                commit(FETCHING_NEWS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
            return null;
        },
        async putNews({commit}, payload){
            commit(LOADING_NEWS);
            console.log('put');
            try {
                console.log(payload);
                let response = await NewsAPI.putNews(payload);
                console.log(response);
                commit(UPDATING_NEWS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async deleteNews({commit}, id){
            commit(LOADING_NEWS);
            console.log('delete');
            try{
                let response = await NewsAPI.deleteNews(id);
                console.log(response);
                commit(DELETING_NEWS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        }
    }
};