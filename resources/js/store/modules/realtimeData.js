import {
  storeModuleExtra
} from '@/js/utils';
// 数据存储

let modulePrefix = 'realtimeData';

let realtimeData = {
  namespaced: true,
  state: {
    getActionName: (field) => {
      return [modulePrefix, field].join('/');
    },
    getFormSetMutations: (field) => {
      return [modulePrefix + field].join('/').toUpperCase();
    },

		pv:0,
		ips:0,
		loads:0,
		clicks:0,
		downs:0,
		api:'',
		status: {},
		trendtimes:[],
		trendcount:[]
  },
  mutations: {
		PV(state, n) {
			state.pv=n;
		},
		IPS(state, n) {
			state.ips=n;
		},
		LOADS(state, n) {
			state.loads=n;
		},
		CLICKS(state, n) {
			state.clicks=n;
		},
		DOWNS(state, n) {
			state.downs=n;
		},
		API(state, api) {
			state.api=api;
		},
		TRENDTIMES(state, trendtimes) {
			state.trendtimes=trendtimes;
		},
		TRENDCOUNT(state, trendcount) {
			state.trendcount=trendcount;
		}
  },
  actions: {
		PV(context,n) {
			context.commit('PV',n);
		},
		IPS(context, n) {
			context.commit('IPS',n);
		},
		LOADS(context, n) {
			context.commit('LOADS',n);
		},
		CLICKS(context, n) {
			context.commit('CLICKS',n);
		},
		DOWNS(context, n) {
			context.commit('DOWNS',n);
		},
		API(context, api) {
			context.commit('API',api);
		},
		TRENDTIMES(context, trendtimes) {
			context.commit('TRENDTIMES',trendtimes);
		},
		TRENDCOUNT(context, trendcount) {
			context.commit('TRENDCOUNT',trendcount);
		}
  },
  getters: {}
};

storeModuleExtra(realtimeData, null);

export default realtimeData;