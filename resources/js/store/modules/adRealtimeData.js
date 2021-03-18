import createPersistedState from "vuex-persistedstate";

import {
  storeModuleExtra
} from '@/js/utils';

let formInit = {};

let adRealtimeData = {
  namespaced: true,
  plugins: [createPersistedState()],
  state: {
    data: {
      columns: []
    }
  },
  mutations: {},
  actions: {},
  getters: {}
};

storeModuleExtra(adRealtimeData, Object.assign({}, formInit));

export default adRealtimeData;