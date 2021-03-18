/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

import Vue from 'vue';
// import router from './router';
import store from './store';
import ElementUI from 'element-ui';

import './utils/vueFilter';
import './icons';
import './views';

import gmvdClipboard from './directives/clipboard';
import gmvdDownload from './directives/download';
import gmvdNewtab from './directives/newtab';
import gmvdPreview from './directives/preview';

Vue.use(ElementUI);
Vue.use(gmvdClipboard);
Vue.use(gmvdDownload);
Vue.use(gmvdNewtab);
Vue.use(gmvdPreview);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

if (window.Vue === undefined) {
  window.Vue = Vue;
}
// if (window.VueRouter === undefined) {
//   window.VueRouter = router;
// }
if (window.VueStore === undefined) {
  window.VueStore = store;
}