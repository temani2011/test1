import SecurityAPI from "../api/security";

const AUTHENTICATING = "AUTHENTICATING",
    AUTHENTICATING_SUCCESS = "AUTHENTICATING_SUCCESS",
    AUTHENTICATING_ERROR = "AUTHENTICATING_ERROR",
    PROVIDING_DATA_ON_REFRESH_SUCCESS = "PROVIDING_DATA_ON_REFRESH_SUCCESS",
    LOGOUT_ACTION = "LOGOUT_ACTION",
    LOGOUT_SUCCESS = "LOGOUT_SUCCESS",
    LOGOUT_ERROR = "LOGOUT_ERROR";

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        isAuthenticated: false,
        user: null
    },
    getters: {
        isLoading(state) {
            return state.isLoading;
        },
        hasError(state) {
            return state.error !== null;
        },
        error(state) {
            return state.error;
        },
        isAuthenticated(state) {
            return state.isAuthenticated;
        },
        hasRole(state) {
            return role => {
                if(state.user == null) return false; //??
                return state.user.roles[0].indexOf(role) !== -1;
            }
        },
        getUserData(state){
            if(state.user == null) return false; //??
            else return state.user;
        }
    },
    mutations: {
        [AUTHENTICATING](state) {
            state.isLoading = true;
            state.error = null;
            state.isAuthenticated = false;
            state.user = null;
        },
        [AUTHENTICATING_SUCCESS](state, user) {
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = true;
            state.user = user;
        },
        [AUTHENTICATING_ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
            state.isAuthenticated = false;
            state.user = null;
        },
        [PROVIDING_DATA_ON_REFRESH_SUCCESS](state, payload) {
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = payload.isAuthenticated;
            state.user = payload.user;
        },
        [LOGOUT_ACTION](state){
            state.isLoading = true;
            state.error = null;
            state.isAuthenticated = false;
        },
        [LOGOUT_SUCCESS](state){
            state.isLoading = false;
            state.error = null;
            state.isAuthenticated = false;
            state.user = null;
        },
        [LOGOUT_ERROR](state, error){
            state.isLoading = false;
            state.error = error;
            state.isAuthenticated = true;
        }
    },
    actions: {
        async login({commit}, payload) {
            commit(AUTHENTICATING);
            try {
                let response = await SecurityAPI.login(payload.login, payload.password);
                console.log(response);
                commit(AUTHENTICATING_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(AUTHENTICATING_ERROR, error);
                return null;
            }
        },
        async logout({commit}){
            commit(LOGOUT_ACTION);
            try {
                let response = await SecurityAPI.logout();
                commit(LOGOUT_SUCCESS);
                console.log(response);
                return response;
            } catch (error) {
                commit(LOGOUT_ERROR, error);
                return null;
            }
        },
        onRefresh({commit}, payload) {
            commit(PROVIDING_DATA_ON_REFRESH_SUCCESS, payload);
        }
    }
}