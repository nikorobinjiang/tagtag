import { cloneDeep } from 'lodash'
import dataToutiao from '../data/Toutiao';

let formInit = {
  // 模板名称
  title: '',
  budget: 0, // 必填 账户每日预算: -1 不限制预算
  delivery: 0, // int 计划匀加速投放功能设置 0：匀速 1：加速
  scheduleTime: '', // 广告投放时段
  allRegion: '1', // 投放地域定向
  region: [], // 地域id集合
  gender: "-1", // 性别定向
  age: "-1", // 年龄定向范围
  interest: [], // 兴趣定向
  word: [], // 关键关键词定向
  intelliTargeting: 0, // int 默认开启用户智能定向
  platform: "000", // 投放平台
  networkEnv: "", // 网络环境
  creativeSource: "", // 推广来源
};

const state = {
  defaultForm: cloneDeep(formInit),
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
}

let adToutiaoTpl = {
  namespaced: true,
  state,
  mutations: {
  },
  actions: {
  }
}


export default adToutiaoTpl