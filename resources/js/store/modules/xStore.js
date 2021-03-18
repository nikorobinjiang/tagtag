import {
  storeModuleExtra
} from '@/js/utils';
// 数据存储

let modulePrefix = 'xStore';

let xStore = {
  namespaced: true,
  state: {
    getActionName: (field) => {
      return [modulePrefix, field].join('/');
    },
    getFormSetMutations: (field) => {
      return [modulePrefix + field].join('/').toUpperCase();
    },
    hasLoad: false,
    adApiGetRelData: '', // 列表联动数据聚合接口

    data: {
      adSiteList: [], // 落地页面域名
      mediaList: [], // 媒体列表
      agentList: [], // 代理商列表
      promoteList: [], // 媒体账号列表
      gameGroupList: [], // 游戏项目列表
      gameList: [], // 游戏列表
      positionList: [], // 广告位
      settlementList: [], // 结算方式
      watermarkList: [
        {label: "无水印", value: 0},
        {label: "有水印", value: 1}
      ],
      gameTypeH5: '',
      gameTypeWXMP: '',
      styleList: [], // 广告位可选样式
      designerList: [], // 设计师
      lpDesignerListStr: [], // 落地页设计师
      materialList: [], // 搜索的素材列表, kv
      domainDownList: [], // Apk 域名， 反劫持域名
      landingPageList: [], // 落地页可选列表
      hasLandingPageList: [{
          label: "是",
          value: 1
        },
        {
          label: "否",
          value: 0
        }
      ] // 落地页

    },
    status: {}
  },
  mutations: {},
  actions: {},
  getters: {
    mediaList: (state) => {
      return state.data.mediaList;
    },
    agentList: (state) => {
      return state.data.agentList;
    },
    promoteList: (state) => {
      return state.data.promoteList;
    }
  }
};

storeModuleExtra(xStore, null);

export default xStore;