import CatalogAPI from "../api/catalog";
import DocumentAPI from "../api/document";

const LOADING_CATALOG = "LOADING_CATALOG",
    ERROR= "ERROR",
    //CATALOGS STATES
    CREATING_CATALOG_SUCCESS = "CREATING_CATALOG_SUCCESS",
    FETCHING_CATALOGS_SUCCESS = "FETCHING_CATALOGS_SUCCESS",
    UPDATING_CATALOG_SUCCESS = "UPDATING_CATALOG_SUCCESS",
    DELETING_CATALOG_SUCCESS = "DELETING_CATALOG_SUCCESS",
    //DOCUMENTS STATES
    CREATING_DOCUMENT_SUCCESS = "CREATING_DOCUMENT_SUCCESS",
    UPDATING_DOCUMENT_SUCCESS = "UPDATING_DOCUMENT_SUCCESS",
    DELETING_DOCUMENT_SUCCESS = "DELETING_DOCUMENT_SUCCESS";

export default {
    namespaced: true,
    state: {
        isLoading: false,
        error: null,
        catalogs: []
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
        hasCatalogs(state) {
            return state.catalog.length > 0;
        },
        getCatalogs(state) {
            return state.catalogs;
        }
    },
    mutations: {
        [LOADING_CATALOG](state) {
            state.isLoading = true;
            state.error = null;
        },
        [ERROR](state, error) {
            state.isLoading = false;
            state.error = error;
        },
        [FETCHING_CATALOGS_SUCCESS](state, catalogs) {
            state.isLoading = false;
            state.error = null;
            console.log(catalogs);
            state.catalogs = catalogs;
        },
        [CREATING_CATALOG_SUCCESS](state, catalog) {
            state.isLoading = false;
            state.error = null;
            state.catalogs = state.catalogs.map(e => searchAnd('insert', 'catalogs', e, catalog));
        },
        [UPDATING_CATALOG_SUCCESS](state, catalog) {
            state.isLoading = false;
            state.error = null;
            state.catalogs = state.catalogs.map(e => searchAnd('delete','catalogs', e, catalog));
            state.catalogs = state.catalogs.map(e => searchAnd('insert','catalogs', e, catalog));
        },
        [DELETING_CATALOG_SUCCESS](state, catalog) {
            state.isLoading = false;
            state.error = null;
            state.catalogs = state.catalogs.map(e => searchAnd('delete','catalogs', e, catalog));
        },
        // Documents mutations
        [CREATING_DOCUMENT_SUCCESS](state, documents) {
            state.isLoading = false;
            state.error = null;
            state.catalogs = state.catalogs.map(e => searchAnd('insertDoc','documents', e, documents));
        },
        [UPDATING_DOCUMENT_SUCCESS](state, documents) {
            state.isLoading = false;
            state.error = null;
            state.catalogs = state.catalogs.map(e => searchAnd('replaceDoc','documents', e, documents));
        },
        [DELETING_DOCUMENT_SUCCESS](state, documents) {
            state.isLoading = false;
            state.error = null;
            state.catalogs = state.catalogs.map(e => searchAnd('deleteDoc','documents', e, documents));
        }
    },
    actions: {
        async catalogPost({ commit }, payload) {
            commit(LOADING_CATALOG);
            console.log('post');
            try {
                console.log(payload);
                let response = await CatalogAPI.catalogPost(payload);
                console.log(response);
                commit(CREATING_CATALOG_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async catalogGetAll({ commit }) {
            commit(LOADING_CATALOG);
            console.log('getAll');
            try {
                let response = await CatalogAPI.catalogGetAll();
                commit(FETCHING_CATALOGS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async catalogGetById({commit}, id){
            commit(LOADING_CATALOG);
            console.log('getOne id');
            try{
                let response = await CatalogAPI.catalogGetById(id);
                console.log(response);
                commit(FETCHING_CATALOGS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
            return null;
        },
        async catalogGetByName({commit}, slug){
            commit(LOADING_CATALOG);
            console.log('getOne slug');
            try{
                let response = await CatalogAPI.catalogGetByName(slug);
                console.log(response);
                commit(FETCHING_CATALOGS_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
            return null;
        },
        async catalogUpdate({commit}, payload){
            commit(LOADING_CATALOG);
            console.log('put');
            try {
                console.log(payload);
                let response = await CatalogAPI.catalogUpdate(payload);
                console.log(response);
                commit(UPDATING_CATALOG_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async catalogDeleteById({commit}, id){
            commit(LOADING_CATALOG);
            console.log('delete');
            try{
                let response = await CatalogAPI.catalogDeleteById(id);
                console.log(response);
                commit(DELETING_CATALOG_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        // Documents actions
        async documentPost({commit}, payload){
            commit(LOADING_CATALOG);
            console.log('post doc');
            try{
                let response = await DocumentAPI.documentPost(payload);
                console.log(response);
                commit(CREATING_DOCUMENT_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async documentUpdate({commit}, payload){
            commit(LOADING_CATALOG);
            console.log('put doc');
            try{
                let response = await DocumentAPI.documentUpdate(payload);
                console.log(response);
                commit(UPDATING_DOCUMENT_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        },
        async documentDeleteById({commit}, id){
            commit(LOADING_CATALOG);
            console.log('del doc');
            try{
                let response = await DocumentAPI.documentDeleteById(payload);
                console.log(response);
                commit(DELETING_DOCUMENT_SUCCESS, response.data);
                return response.data;
            } catch (error) {
                commit(ERROR, error);
                return null;
            }
        }
    }
};
function searchAnd(state, from, catalog, pCatalog){
    let statment = '';
    switch (from) {
        case 'catalogs':
            statment = (catalog.id == pCatalog.parent.id);
            break;
        case 'documents':
            statment = (catalog.id == pCatalog.catalogId);
            break;
    }
    console.log(statment, from);
    if(statment) {
        console.log(catalog.id, pCatalog.id);
        let index;
        switch (state) {
            case 'insertDoc':
                if(!Array.isArray(pCatalog.documents))
                    catalog.documents = [];
                pCatalog.documents.forEach(x => catalog.documents.push(x));
                break;
            case 'insert':
                if(!Array.isArray(catalog.childs))
                    catalog.childs = [];
                catalog.childs.push(pCatalog);
                break;
            case 'replace':
                index = catalog.childs.findIndex(x => x.id == pCatalog.id);
                catalog.childs[index] = pCatalog;
                break;
            case 'delete':
                index = catalog.childs.findIndex(x => x.id == pCatalog.id);
                catalog.childs.splice(index, 1);
                break;
        }
    } else if(catalog.childsCount > 0) catalog.childs.map(e => searchAnd(state, from, e, pCatalog));
    console.log(catalog);
    return catalog;
}