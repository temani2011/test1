import Vue from "vue";
import App from "./App";
import router from "./router";
import store from "./store";

/* Offline
import 'bootstrap'
import 'mdbvue';
import 'mdbvue/lib/css/mdb.min.css'
*/

new Vue({
    components: { App },
    template: "<App/>",
    router,
    store
}).$mount("#app");