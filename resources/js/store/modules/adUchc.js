import moment from "moment";
import {
  mapKeys
} from 'lodash';
import {
  Message
} from 'element-ui';
import {
  storeModuleExtra
} from '@/js/utils';

let modulePrefix = 'adUchc';

let formInit = {
  // 推广计划 Campaign
  campaign: {
    campaignName: "", // String 推广计划名称, 长度限制：最大30个字节，1个中文按2个字节计算
    adResourceId: 1, // long 推广资源, 取值范围：1：UC头条 2：UC精准 4：应用商店
    budget: "-1", // String 必填 账户每日预算: -1 不限制预算
    rate: 0, // int 计划匀加速投放功能设置 0：匀速 1：加速
    startDate: "", // int 推广投放开始日期, 不能小于今天
    endDate: "", // int 推广投放结束日期, 不能小于startDate, 20990101 表示结束日期不限
    schedule_time: '', // String 广告投放时段
  },

  // 推广单元 Adgroup
  adgroup: {
    adgroupName: "", // String 推广单元名称, 长度限制：最大30个字节，1个中文按2个字节计算
    generalizeType: 1, // Int 推广方式, 1：打开页面, 2：APP下载

    // 自定义人群定向 start
    audience: [], // long[] 人群包定向id集合, 自定义人群定向选择不限时可为空数组
    audience_targeting: "-1", // String 自定义人群定向, -1 : 不限 1 : 定向用户群 2 : 排除用户群
    all_region: '1', // String 投放地域定向, 1：不限 0：省市定向 2：区县定向
    region: [], // int[] 地域id集合, 投放地域定向为不限时可为空数组
    gender: "-1", // String 性别定向, -1：不限 1：男 0：女
    age: "-1", // String 年龄定向范围
    user_targeting: "-1", // String 兴趣与行为自定义
    interest: [], // int[] 兴趣定向, user_targeting选择自定义后, 可选
    word: [], // String[] 关键关键词定向, user_targeting选择自定义后，可选
    intelli_targeting: 0, // int 默认开启用户智能定向
    // 自定义人群定向 end

    platform: "000", // String 操作系统|投放平台
    network_env: "11", // String 网络环境
    convertMonitorType: 1, // int 转化监测类型|转化类型
    adconvertId: 0, // long 转化id|转化名称
    optimizationTarget: 3, // int 优化目标|转化目标, 1：点击 2：展现 3：转化
    chargeType: 1, // int 计费方式, 1-CPC 2-CPM 优化目标为转化，计费方式只能为 CPC
    bid: "", // String 出价|第一阶段出价
    secondBid: "", // String 第二阶段出价
  },

  // 创意
  selectmode: 'customize', // 创意生成方式 procedural | customize
  procedural_content: {}, // 程序化创意元数据
  creativeSource: "", // String 推广来源, 推广来源字符范围为 1-16
  target_url: '', // String 点击URL, 默认填写落地页壳地址，为不可编辑状态
}

let adUchc = {
  namespaced: true,
  state: {
    getActionName: (field) => {
      return [modulePrefix, field].join('/');
    },
    getFormSetMutations: (field) => {
      return [modulePrefix + field].join('/').toUpperCase();
    },
    form: Object.assign({}, formInit),
    rules: mapKeys({
      'campaign.campaignName': [{
        required: true,
        message: '请填写推广计划名称',
        trigger: 'blur'
      }],
      'campaign.startDate': [{
        validator: (rule, value, callback) => {
          setTimeout(() => {
            if (moment(value).startOf('day').unix() < moment().startOf('day').unix()) {
              const msg = "开始日期必须大于当前时间";
              Message.error(msg);
              callback(new Error(msg));
            } else {
              callback();
            }
          }, 1000);
        },
        trigger: "blur"
      }],
      'campaign.schedule_time': [{
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
      'adgroup.adgroupName': [{
        required: true,
        message: '请填写推广单元名称',
        trigger: 'blur'
      }],
      'adgroup.platform': [{
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
      'adgroup.convertMonitorType': [{
        required: true,
        message: '请选择转化类型',
        trigger: 'blur'
      }],
      'adgroup.adconvertId': [{
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
      'adgroup.bid': [{
          required: true,
          message: '请填写第一阶段出价',
          trigger: 'blur'
        },
        {
          type: 'number',
          message: '第一阶段出价必须为数字值'
        }
      ],
      'adgroup.secondBid': [{
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
      return 'Uchc.' + key;
    }),
    status: {},
    data: {
      adGroupName: '', // 广告组名称
      adGroupList: [], // 广告组列表
      adTplList: [] // 广告模板列表
    },
  },
  getters: {
    formCheck: state => {
      let msg = {};
      return msg;
    }
  },
  mutations: {},
  actions: {}
};

storeModuleExtra(adUchc, Object.assign({}, formInit));

export default adUchc