import UserAPI from "../api/user";

const LOADING_USER = "LOADING_USER",
    ERROR= "ERROR",
    CREATING_USER_SUCCESS = "CREATING_USER_SUCCESS",
    FETCHING_USERS_SUCCESS = "FETCHING_USERS_SUCCESS",
    UPDATING_USER_SUCCESS = "UPDATING_USER_SUCCESS",
    DELETING_USER_SUCCESS = "DELETING_USER_SUCCESS";

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        users: []
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
        hasUsers(state) {
            return state.users.length > 0;
        },
        getUsers(state) {
            return state.users;
        }
    },
    mutations: {
        [LOADING_USER](state) {
            state.isLoading = true;
            state.error = null;
        },
        [ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        [FETCHING_USERS_SUCCESS](state, users) {
            state.isLoading = false;
            state.error = null;
            if(Array.isArray(users))
                state.users = users;
            else state.users.unshift(users);
        },
        [CREATING_USER_SUCCESS](state, user) {
            state.isLoading = false;
            state.error = null;
            state.users.unshift(user);
        },
        [UPDATING_USER_SUCCESS](state, user) {
            state.isLoading = false;
            state.error = null;
            const index = state.users.findIndex(userItem => userItem.id === user.id);
            state.users.splice(index, 1, user);
        },
        [DELETING_USER_SUCCESS](state, user) {
            state.isLoading = false;
            state.error = null;
            const index = state.users.findIndex(userItem => userItem.id === user.id);
            console.log(index);
            state.users.splice(index, 1);
        }
    },
    actions: {
        async addUser({ commit }, payload) {
            commit(LOADING_USER);
            console.log('post, user:' + payload);
            try {
                let response = await UserAPI.addUser(payload);
                console.log(response);
                commit(CREATING_USER_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async getAllUsers({ commit }) {
            commit(LOADING_USER);
            console.log('getAll users');
            try {
                let response = await UserAPI.getAllUsers();
                commit(FETCHING_USERS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async getUserById({commit}, id){
            commit(LOADING_USER);
            console.log('getOne user');
            try{
                let response = await UserAPI.getUserById(id);
                console.log(response);
                commit(FETCHING_USERS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
            return null;
        },
        async editUser({commit}, payload){
            commit(LOADING_USER);
            console.log('edit, user:' + payload);
            try {
                let response = await UserAPI.editUser(payload);
                console.log(response);
                commit(UPDATING_USER_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async deleteUser({commit}, id){
            commit(LOADING_USER);
            console.log('delete user:'+ id);
            try{
                let response = await UserAPI.deleteUser(id);
                console.log(response);
                commit(DELETING_USER_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        }
    }
};