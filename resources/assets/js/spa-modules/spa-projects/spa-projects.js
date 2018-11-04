import Vue from 'vue';
import router from './spa-projects-router.js';
import ElementUI from 'element-ui';
import store from './../../vuex/store.js';
require('../../../js/bootstrap');

Vue.use(ElementUI)
Vue.config.productionTip = false;
Vue.component('projects-container', require('./ProjectsContainer.vue'));
var app_porojects = new Vue({
    router,
    store,

    created() {
        store.dispatch('bootstrap')
    }

}).$mount('#spa-projects')
