import Vue from "vue";
import App from "./App";
import router from "./router";
import store from "./store";

/*
// Offline
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.min.js';
import "jquery/dist/jquery.min.js";
import 'mdbvue';
import 'mdbvue/lib/css/mdb.min.css';
*/

new Vue({
    components: { App },
    template: "<App/>",
    router,
    store
}).$mount("#app");