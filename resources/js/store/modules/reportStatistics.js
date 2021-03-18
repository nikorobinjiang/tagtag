import {
  storeModuleExtra
} from '@/js/utils';

let modulePrefix = 'reportStatistics';

let formInit = {
  
};

let reportStatistics = {
  namespaced: true,
  state: {
    getActionName: (field) => {
      return [modulePrefix, field].join('/');
    },
    getFormSetMutations: (field) => {
      return [modulePrefix + field].join('/').toUpperCase();
    },
    form: Object.assign({}, formInit),
    status: {},
    statistics: {
      picture: {
        total: 0,
        used_count: 0,
        used_rate: 0,
        cost: 0,
        show: 0,
        click: 0,
        click_rate: 0,
        convert: 0,
        convert_rate: 0,
        convert_cost: 0,
        add_num: 0
      },
      video: {
        total: 0,
        used_count: 0,
        used_rate: 0,
        cost: 0,
        show: 0,
        click: 0,
        click_rate: 0,
        convert: 0,
        convert_rate: 0,
        convert_cost: 0,
        add_num: 0
      }
    }
  },
  mutations: {
    setStatisticsPicture(state,data){
      state.statistics.picture=data;
    },
    setStatisticsVideo(state,data){
      state.statistics.video=data;
    }
  },
  actions: {
    setStatisticsPicture(context,data){
      context.commit('setStatisticsPicture',data);
    },
    setStatisticsVideo(context,data){
      context.commit('setStatisticsVideo',data);
    }
  }
}

storeModuleExtra(reportStatistics, Object.assign({}, formInit));

export default reportStatistics