import dataToutiao from '../data/Toutiao';
import {
  storeModuleExtra
} from '@/js/utils';

let modulePrefix = 'tplUchcTpl';

let formInit = {
  // 模板名称
  title: '',
  budget: "-1", // 必填 账户每日预算: -1 不限制预算
  rate: 0, // int 计划匀加速投放功能设置 0：匀速 1：加速
  schedule_time: '', // 广告投放时段
  all_region: '1', // 投放地域定向
  region: [], // 地域id集合
  gender: "-1", // 性别定向
  age: "-1", // 年龄定向范围
  user_targeting: "-1", // 兴趣与行为自定义
  interest: [], // 兴趣定向
  word: [], // 关键关键词定向
  intelli_targeting: 0, // int 默认开启用户智能定向
  platform: "000", // 投放平台
  network_env: "", // 网络环境
  creativeSource: "", // 推广来源
};

let adToutiaoTpl = {
  namespaced: true,
  state: {
    getActionName: (field) => {
      return [modulePrefix, field].join('/');
    },
    getFormSetMutations: (field) => {
      return [modulePrefix + field].join('/').toUpperCase();
    },
    form: Object.assign({}, formInit),
    // 前端验证
    rules: {
      title: [{
        required: true,
        message: '',
        trigger: 'blur'
      }],
      creativeSource: [
        {
          min: 1,
          max: 16,
          message: '推广来源字符范围为1-16',
          trigger: 'blur'
        }
      ],
    },
    status: {},
    data: {
      Toutiao: dataToutiao
    },
  },
  mutations: {
    assignForm: (state, message) => {
      state.form = Object.assign(state.form, message);
    }
  },
  actions: {
    assignForm: ({
      commit
    }, message) => {
      commit((modulePrefix + key).toUpperCase(), message)
    }
  }
}

storeModuleExtra(adToutiaoTpl, Object.assign({}, formInit));

export default adToutiaoTpl