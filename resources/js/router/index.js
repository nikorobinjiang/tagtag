import Vue from 'vue';
import Router from 'vue-router';

import AdMain from './AdMain';

Vue.use(Router);


export default new Router({
  // mode: 'history', // require service support
  scrollBehavior: () => ({
    y: 0
  }),
  routes: [].concat(AdMain)
});