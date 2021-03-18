import {
  cloneDeep, mapKeys
} from 'lodash';
import {
  storeModuleExtra
} from '@/js/utils';

let modulePrefix = 'adUchc';

let formInit = {
  // 推广组 Ad_group
  ad_group: {
    name: "", // String 推广单元名称, 长度限制：最大30个字节，1个中文按2个字节计算
    objectiveType: 1, // Int 推广方式, 1：打开页面, 2：APP下载
  },

  // 推广计划 Campaign
  campaign: {
    name: "", // String 推广计划名称, 长度限制：最大30个字节，1个中文按2个字节计算
    type: -1, // int 标的物类型
    state: -1, // int 推广计划状态
    optTarget: -1, // int 优化目标
    delivery: -1, // int 投放方式
    objectives: {}, // Objective 推广对象内容
    targetUrl: '', // string 落地页链接
    schemaUrl: '', // string 直达链接
    siteId: -1, // long 建站id
    appKey: -1, // long app应用key
    packageKey: -1, // long app应用商店id
    appName: '', // string App名称
    convertType: -1, // int 转化类型
    adConvertId: -1, // long 转化id

    trackArgs: '', // string 链接追踪参数
    targetings: {}, // Targeting 定向条件
    audience: [], // long[] 人群包定向id集合
    audienceTargeting: '', // string 自定义人群定向
    allRegion: '', // string 投放地域定向
    region: [], // int[] 地域id集合
    gender: '', // string 性别定向
    age: '', // string 年龄定向范围
    interest: [], // int[] 兴趣定向
    word: [], // string[] 关键关键词定向
    url: [], // string[] 兴趣站点定向
    app: [], // string[] app名称定向
    appcategory: [], // long[] app分类定向
    networkEnv: '', // string 网络环境定向
    intelliTargeting: 500, // int 智能定向
    platform: '', // string 投放平台
    convertFilter: 0, // int 转化过滤

    budget: -1, // int 预算
    schedule: {}, // ScheduleType 推广计划设置的投放日期和推广时段
    startDate: -1, // int 推广投放开始日期
    endDate: -1, // int 推广投放结束日期
    scheduleTime: '', // string 推广时段

    chargeType: -1, // int 计费方式
    bids: {}, // BidType 出价
    bid: '', // string 第一阶段出价
    optBid: '', // string 第二阶段出价
    bidStage: 1, // int 出价阶段
    operateNum: 0, // int 操作百分比
    percentage: 0, // int 百分比
    slotBidding: 2, // int 智能参竞
  },

  // 创意
  selectmode: 'customize', // 创意生成方式 procedural | customize
  procedural_content: {}, // 程序化创意元数据
  creativeSource: "", // String 推广来源, 推广来源字符范围为 1-16
  target_url: '', // String 点击URL, 默认填写落地页壳地址，为不可编辑状态
}


const state = {
  getActionName: (field) => {
    return [modulePrefix, field].join('/');
  },
  getFormSetMutations: (field) => {
    return [modulePrefix + field].join('/').toUpperCase();
  },
  defaultForm: cloneDeep(formInit),
  form: Object.assign({}, formInit),
  rules: mapKeys({
    'ad_group.name': [{
      required: true,
      message: '请填写推广计划名称',
      trigger: 'blur'
    }],
    'campaign.scheduleTime': [{
      validator: (rule, value, callback) => {
        if (!value) {
          return callback(new Error("请选择投放时段"));
        }
        setTimeout(() => {
          if (value.indexOf('1') === -1) {
            callback(new Error("周一至周日推广时段至少设置一天，否则会报错。"));
          } else {
            callback();
          }
        }, 1000);
      },
      trigger: "blur"
    }],
    'campaign.name': [{
      required: true,
      message: '请填写推广单元名称',
      trigger: 'blur'
    }],
    'campaign.platform': [{
      required: true,
      message: '请选择操作系统',
      trigger: 'blur'
    }, {
      validator: (rule, value, callback) => {
        if (!value) {
          return callback(new Error("请选择操作系统"));
        }
        setTimeout(() => {
          if (value.indexOf('1') === -1) {
            callback(new Error("操作系统至少选择一个。"));
          } else {
            callback();
          }
        }, 1000);
      },
      trigger: "blur"
    }],
    'campaign.convertType': [{
      required: true,
      message: '请选择转化类型',
      trigger: 'blur'
    }],
    'campaign.adConvertId': [{
      required: true,
      message: '请选择转化名称',
      trigger: 'blur'
    }, {
      validator: (rule, value, callback) => {
        if (!value) {
          return callback(new Error("请选择转化名称"));
        }
        setTimeout(() => {
          if (value > 0) {
            callback();
          } else {
            callback(new Error("请选择转化名称"));
          }
        }, 1000);
      },
      trigger: "blur"
    }],
    'campaign.bid': [{
        required: true,
        message: '请填写第一阶段出价',
        trigger: 'blur'
      },
      {
        type: 'number',
        message: '第一阶段出价必须为数字值'
      }
    ],
    'campaign.optBid': [{
        required: true,
        message: '请填写第二阶段出价',
        trigger: 'blur'
      },
      {
        type: 'number',
        message: '第二阶段出价必须为数字值'
      }
    ],
    creativeSource: [{
        required: true,
        message: '请填写推广来源',
        trigger: 'blur'
      },
      {
        min: 1,
        max: 16,
        message: '推广来源字符范围为1-16',
        trigger: 'blur'
      }
    ],
  }, (val, key) => {
    return 'Uchcv2.' + key;
  }),
  status: {},
  data: {
    ad_GroupName: '', // 广告组名称
    ad_GroupList: [], // 广告组列表
    adTplList: [] // 广告模板列表
  },
}

const getters = {
  formCheck: state => {
    let msg = {};
    return msg;
  }
}

let adUchc = {
  namespaced: true,
  state,
  getters,
  mutations: {},
  actions: {}
};

storeModuleExtra(adUchc, Object.assign({}, formInit));

export default adUchc