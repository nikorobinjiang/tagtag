import {
  storeModuleExtra
} from '@/js/utils';
// 数据存储

let modulePrefix = 'adIndex';

let adIndex = {
  namespaced: true,
  state: {
    getActionName: (field) => {
      return [modulePrefix, field].join('/');
    },
    getFormSetMutations: (field) => {
      return [modulePrefix + field].join('/').toUpperCase();
    },

    data: {},
    status: {}
  },
  mutations: {},
  actions: {},
  getters: {}
};

storeModuleExtra(adIndex, null);

export default adIndex;