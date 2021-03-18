import dataToutiao from '../data/Toutiao';
import {
  storeModuleExtra
} from '@/js/utils';

let modulePrefix = 'tplToutiaoTpl';

let formInit = {
  // 模板名称
  title: '',
  // 地域类型，前者为省市，后者为区县。当city有数据时，必填。
  // 允许值: "CITY", "COUNTY"
  district: 'ALL',
  // 地域定向城市或者区县列表(当传递省份ID时,旗下市县ID可省略不传), 详见【附件-city.json】
  city: ['NOLIMIT'],
  // 受众位置类型 {
  // 4: 正在该地区的用户
  // 2: 居住在该地区的用户
  // 1: 到该地区旅行的用户
  // 3: 该地区内的所有用户
  // }
  location_type: 'CURRENT',
  gender: 'NONE',
  age: ['NOLIMIT'],
  ad_tag_type: 0,
  // 兴趣分类
  ad_tag: [],
  // 兴趣关键词
  interest_tags: [],
  // 广告应用下载类型(当campaign的landing_type=APP时,必填), 详见【附录-广告应用下载类型】
  // 允许值: "APP_ANDROID", "APP_IOS""
  app_type: 'APP_ANDROID',
  // 受众平台(当推广目的landing_type=APP时,不填,且为保证投放效果,平台类型定向PC与移动端互斥), 详见【附录-受众平台类型】
  // 允许值: "ANDROID", "IOS", "PC"
  platform: ['NOLIMIT'],
  // 受众网络类型
  ac: ['NOLIMIT'],
  // 受众最低android版本(当推广应用下载Android时选填,其余情况不填), 详见【附录-受众android版本】
  // 允许值: "0.0", "2.0", "2.1", "2.2", "2.3", "3.0", "3.1", "3.2", "4.0","4.1", "4.2", "4.3", "4.4", "4.5", "5.0"
  android_osv: '0.0',
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

  // 是否是优选广告位
  smart_inventory: 1,
  // 创意投放位置
  inventory_type: [],
  // 创意生成方式
  selectmode: 'customize',
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