import dataToutiao from '../data/Toutiao';
import {
  storeModuleExtra
} from '@/js/utils';

let modulePrefix = 'adToutiao';

let formInit = {
  // 地域类型，前者为省市，后者为区县。当city有数据时，必填。
  // 允许值: "CITY", "COUNTY"
  district: 'ALL',
  // 地域定向城市或者区县列表(当传递省份ID时,旗下市县ID可省略不传), 详见【附件-city.json】
  city: [],
  // 受众位置类型 {
  // 4: 正在该地区的用户
  // 2: 居住在该地区的用户
  // 1: 到该地区旅行的用户
  // 3: 该地区内的所有用户
  // }
  location_type: 'CURRENT', // 受众位置类型
  gender: 'NONE',
  age: ['NOLIMIT'],
  retargeting_tags_include: [], // 定向人群包列表，内容为人群包id。
  retargeting_tags_exclude: [], // 排除人群包列表，内容为人群包id。
  ad_tag_type: 0,
  // 兴趣分类
  ad_tag: [],
  // 兴趣关键词
  interest_tags: [],
  // 广告应用下载类型(当campaign的landing_type=APP时,必填), 详见【附录-广告应用下载类型】
  // 允许值: "APP_ANDROID", "APP_IOS""
  app_type: '',
  // 受众平台(当推广目的landing_type=APP时,不填,且为保证投放效果,平台类型定向PC与移动端互斥), 详见【附录-受众平台类型】
  // 允许值: "ANDROID", "IOS", "PC"
  platform: ['NOLIMIT'],
  // 受众网络类型
  ac: ['NOLIMIT'],
  // 受众最低android版本(当推广应用下载Android时选填,其余情况不填), 详见【附录-受众android版本】
  // 允许值: "0.0", "2.0", "2.1", "2.2", "2.3", "3.0", "3.1", "3.2", "4.0","4.1", "4.2", "4.3", "4.4", "4.5", "5.0"
  android_osv: '0.0',
  // 受众最低ios版本(当推广应用下载iOS时选填,其余情况不填), 详见【附录-受众ios版本】
  // 允许值: "0.0", "4.0", "4.1", "4.2", "4.3", "5.0", "5.1", "6.0", "7.0","7.1", "8.0", "8.1", "8.2", "9.0", "NONE"
  ios_osv: '0.0',
  // 广告投放时间类型, 详见【附录-广告投放时间类型】
  // 允许值: "SCHEDULE_FROM_NOW", "SCHEDULE_START_END"
  schedule_type: 'SCHEDULE_FROM_NOW',
  // 广告投放起始时间
  start_time: '',
  // 广告投放结束时间
  end_time: '',
  // 广告投放时段
  schedule_time: '',
  // 广告投放速度类型, 详见【附录-广告投放速度类型】
  // 允许值: "FLOW_CONTROL_MODE_FAST", "FLOW_CONTROL_MODE_SMOOTH", "FLOW_CONTROL_MODE_BALANCE"
  flow_control_mode: 'FLOW_CONTROL_MODE_FAST',

  // 结算方式
  settlement: 'CPM',
  // 广告预算类型
  budget_mode: 'BUDGET_MODE_DAY',
  // 广告预算
  budget: 0,
  // 广告出价 目标转化出价
  bid: 0,
  // ocpc、ocpm广告第二阶段广告出价。对于OCPC和OCPM出价是必填项，CPC和CPM不是必填项。
  // 取值范围: ≥ 0
  cpa_bid: 0,
  // 转化id
  convert_id: null,

  // 设置投放位置
  smart_inventory: 1,
  // 创意投放位置
  inventory_type: [],
  // 创意生成方式
  selectmode: 'customize', // procedural | customize
  // 程序化创意元数据
  procedural_content: {},
  // 创意展现方式
  creative_display_mode: 'CREATIVE_DISPLAY_MODE_CTR',
  // 是否关闭评论
  // 允许值: 0, 1
  is_comment_disable: 0,
  // 是否关闭视频详情页落地页(勾选该选项后,视频详情页中不默认弹出落地页,仅对视频广告生效)
  // 允许值: 0, 1
  close_video_detail: 0,
  // 创意分类
  ad_category: null,
  // 创意标签
  ad_keywords: [],

  // 应用下载详情页
  web_url: '',
  // 应用名
  app_name: '',
  // 副标题
  sub_title: '',
}

let adToutiao = {
  namespaced: true,
  state: {
    getActionName: (field) => {
      return [modulePrefix, field].join('/');
    },
    getFormSetMutations: (field) => {
      return [modulePrefix + field].join('/').toUpperCase();
    },
    form: Object.assign({}, formInit),
    rules: {
      budget: [{
          required: true,
          message: '',
          trigger: 'blur'
        },
        {
          type: 'number',
          message: '日预算必须为数字值'
        }
      ],
      bid: [{
          required: true,
          message: '',
          trigger: 'blur'
        },
        {
          type: 'number',
          message: '目标转化出价必须为数字值'
        }
      ],
      cpa_bid: [{
          required: true,
          message: '',
          trigger: 'blur'
        },
        {
          type: 'number',
          message: '目标转化出价必须为数字值'
        }
      ],
      inventory_type: [{
        required: true,
        message: '请填选择投放位置',
        trigger: 'blur'
      }],
      web_url: [{
        required: true,
        message: '请填写应用下载详情页',
        trigger: 'blur'
      }],
      app_name: [{
        required: true,
        message: '请填写应用名',
        trigger: 'blur'
      }],
      ad_category: [{
        required: true,
        message: '请选择创意分类',
        trigger: 'blur'
      }],
      ad_keywords: [{
        required: true,
        message: '请选择创意标签',
        trigger: 'blur'
      }],
    },
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
      // 转化元素
      if (['OCPC', 'OCPM'].indexOf(state.form.settlement) !== -1) {
        if (isNaN(state.form.convert_id) || state.form.convert_id <= 0) {
          msg['convert_id'] = '请选择转化元素';
        }
        if (isNaN(state.form.convert_id) || state.form.convert_id <= 0) {
          msg['convert_id'] = '请选择转化元素';
        }
      }
      // 程序化生成
      if (state.form.selectmode === 'procedural') {
        let procedural_content = state.form.procedural_content;
        // 素材样式检查
        let style_ids = Object.keys(procedural_content).filter(item => {
          return !isNaN(item);
        });
        if (style_ids.length <= 0) {
          msg['style_ids'] = '请选择素材样式';
        }
        // 标题检查
        let texts = [];
        if (procedural_content.ext !== undefined && procedural_content.ext.text !== undefined) {
          texts = procedural_content.ext.text;
        }
        if (texts.length > 0) {
          texts.forEach(item => {
            if (item.length < 6 || item.length > 25) {
              msg['procedural_content_text_length'] = '创意标题应为6~25个字符';
            }
          });
        } else {
          msg['procedural_content_text'] = '请填写创意标题';
        }
      }
      return msg;
    }
  },
  mutations: {},
  actions: {}
};

storeModuleExtra(adToutiao, Object.assign({}, formInit));

export default adToutiao