import store from "store";
import {
  cloneDeep,
  omit
} from 'lodash';
import {
  MakePy
} from "@/js/utils/pinyin";
import {
  storeModuleExtra
} from '@/js/utils';

const modulePrefix = 'adNormal';

const formInit = {
  ad_name: '',
  media_id: null,
  distribute: 'Normal',
  agent_id: null,
  promote_id: null,
  ad_group_id: null,
  ad_tpl_id: null,
  game_group_id: null,
  game_id: null,
  position_id: null,
  settlement: '',
  has_watermark: 0,
  is_multi_materials: false,
  apk_domain: '',
  style_ids: [],
  style_infos: [],
  template_id: 0,
  template_info: {}, // 落地页临时数据
  material_info: {},
  has_landing_page: 1,

  // 以下为辅助数据
  ad_url: '',
  apk_addr: '',
  shell_url: '',
  
  app_type: '',
  track_url: '',

  can_selectmode: false, // 是否可以进行程序化创意
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
  // 前端验证
  rules: {
    media_id: [{
      required: true,
      message: '',
      trigger: 'blur'
    }],
    agent_id: [{
      required: true,
      message: '请选择代理商',
      trigger: 'blur'
    }],
    promote_id: [{
      required: true,
      message: '',
      trigger: 'blur'
    }],
    game_id: [{
      required: true,
      message: '',
      trigger: 'blur'
    }],
    position_id: [{
      required: true,
      message: '',
      trigger: 'blur'
    }],
    style_ids: [{
      required: false,
      message: '',
      trigger: 'blur'
    }],
    settlement: [{
      required: true,
      message: '',
      trigger: 'blur'
    }],
  },
  // 主表中可能需要添加的额外字段
  ruleNormal: {},
  ruleToutiao: {
    ad_group_id: [{
      required: true,
      message: '',
      trigger: 'blur'
    }],
    // ad_tpl_id: [{
    //   required: true,
    //   message: '',
    //   trigger: 'blur'
    // }]
  },
  data: {
    mediaList: [], // 媒体列表
    agentList: [], // 代理商列表
    promoteList: [], // 媒体账号列表
    gameList: [], // 游戏列表
    positionList: [], // 广告位
    settlementList: [], // 结算方式
    styleList: [], // 广告位可选样式
    designerList: [], // 设计师
    lpDesignerListStr: [], // 落地页设计师
    materialList: [], // 搜索的素材列表, kv
    domainDownList: [], // Apk 域名， 反劫持域名
    landingPageList: [], // 落地页可选列表
    isMultiMaterialList: [{
        label: "否",
        value: 0
      },
      {
        label: "是",
        value: 1
      }
    ], // 支持多组素材
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
}

const adNormal = {
  namespaced: true,
  state,
  mutations: {},
  actions: {
    storageSaveForm({ state, commit, rootState }) {
      store.set('scat_store_ad_normal_form_create', state.form);
    },
    storageLoadForm({ state, commit, rootState }) {
      let form = store.get('scat_store_ad_normal_form_create');
      if (form) {
        form = omit(form, ['material_info', 'template_info', 'template_id', 'style_ids', 'style_infos']);
        commit('ASSIGN_FORM', form);
      }
    },
    storageRemoveForm({ state, commit, rootState }) {
      store.remove('scat_store_ad_normal_form_create');
    }
  },
  getters: {}
};

storeModuleExtra(adNormal, cloneDeep(formInit));

export default adNormal;