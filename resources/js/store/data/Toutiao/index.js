import city from './city.json'
import ad_tag from './ad_tag.json'
import ad_category from './ad_category.json'

// type 为 object, options 为 array
export default {
  // 地域类型，前者为省市，后者为区县。当city有数据时，必填。
  // 允许值: "CITY", "COUNTY"
  districtType: {
    "ALL": {
      "text": "不限",
      "value": "all",
      "show": true
    },
    "CITY": {
      "text": "按省市",
      "value": "city",
      "show": true
    },
    "COUNTY": {
      "text": "按区县",
      "value": "county",
      "show": false
    },
    "LOCAL": {
      "text": "按商圈",
      "value": "local",
      "show": false
    },
    "OVERSEA": {
      "text": "按海外",
      "value": "oversea",
      "show": false
    }
  },
  // 受众位置类型，详见【附录-受众位置类型】
  locationType: {
    "CURRENT": {
      "text": "正在该地区的用户",
      "value": 4,
      "show": true
    },
    "HOME": {
      "text": "居住在该地区的用户",
      "value": 1,
      "show": false
    },
    "TRAVEL": {
      "text": "到该地区旅行的用户",
      "value": 2,
      "show": false
    },
    "ALL": {
      "text": "该地区内的所有用户",
      "value": 3,
      "show": false
    }
  },
  // 地域定向城市或者区县列表(当传递省份ID时,旗下市县ID可省略不传), 详见【附件-city.json】
  cityOptions: city,
  // 受众性别, 详见【附录-受众性别】
  // 允许值: "GENDER_FEMALE", "GENDER_MALE", "NONE"
  genderType: {
    "NONE": {
      "text": "不限",
      "value": 0
    },
    "GENDER_MALE": {
      "text": "男",
      "value": 1
    },
    "GENDER_FEMALE": {
      "text": "女",
      "value": 2
    }
  },
  // 受众年龄区间, 详见【附录-受众年龄区间】
  // 允许值: "AGE_BELOW_18", "AGE_BETWEEN_18_23", "AGE_BETWEEN_24_30","AGE_BETWEEN_31_40", "AGE_BETWEEN_41_49", "AGE_ABOVE_50"
  ageType: {
    "NOLIMIT": {
      "text": "不限",
      "value": 0,
    },
    "AGE_BELOW_18": {
      "text": "<18",
      "value": "[0,17]",
    },
    "AGE_BETWEEN_18_23": {
      "text": "18-23",
      "value": "[18,23]",
    },
    "AGE_BETWEEN_24_30": {
      "text": "24-30",
      "value": "[24,30]",
    },
    "AGE_BETWEEN_31_40": {
      "text": "31-40",
      "value": "[31,40]",
    },
    "AGE_BETWEEN_41_49": {
      "text": "41-49",
      "value": "[41,49]",
    },
    "AGE_ABOVE_50": {
      "text": "50+",
      "value": "[50,100]",
    }
  },
  adTagsType: {
    "NOLIMIT": {
      "text": "不限",
      "value": 0,
      "show": true
    },
    "CUSTOM": {
      "text": "添加兴趣分类",
      "value": 1,
      "show": true
    },
    "SYSTEM": {
      "text": "系统推荐",
      "value": 2,
      "show": false
    }
  },
  adTagsOptions: ad_tag.adtags,
  // 广告应用下载类型(当campaign的landing_type=APP时,必填), 详见【附录-广告应用下载类型】
  // 允许值: "APP_ANDROID", "APP_IOS"
  appType: {
    "APP_ANDROID": {
      "text": "Android",
      "show": true
    },
    "APP_IOS": {
      "text": "iOS",
      "show": true
    }
  },
  // 受众平台(当推广目的landing_type=APP时,不填,且为保证投放效果,平台类型定向PC与移动端互斥), 详见【附录-受众平台类型】
  // 允许值: "ANDROID", "IOS", "PC"
  platformType: {
    "NOLIMIT": {
      "text": "不限",
      "value": 0,
      "show": true
    },
    "IOS": {
      "text": "iOS",
      "value": 2,
      "show": true
    },
    "ANDROID": {
      "text": "Android",
      "value": 1,
      "show": true
    },
    "PC": {
      "text": "PC",
      "value": 16,
      "show": false
    }
  },
  // 受众网络类型, 详见【附录-受众网络类型】
  // 允许值: "WIFI", "2G", "3G", "4G"
  networkType: {
    "NOLIMIT": {
      "text": "不限",
      "value": 0
    },
    "WIFI": {
      "text": "WIFI",
      "value": 1
    },
    "2G": {
      "text": "2G",
      "value": 2
    },
    "3G": {
      "text": "3G",
      "value": 3
    },
    "4G": {
      "text": "4G",
      "value": 4
    }
  },
  // 广告投放速度类型, 详见【附录-广告投放速度类型】
  // 允许值: "FLOW_CONTROL_MODE_FAST", "FLOW_CONTROL_MODE_SMOOTH", "FLOW_CONTROL_MODE_BALANCE"
  flowControlModeType: [{
      label: "优先跑量",
      value: "FLOW_CONTROL_MODE_FAST"
    },
    {
      label: "优先低成本",
      value: "FLOW_CONTROL_MODE_SMOOTH"
    },
    {
      label: "均衡投放",
      value: "FLOW_CONTROL_MODE_BALANCE"
    }
  ],

  // 创意
  // 创意投放位置类型
  inventoryTypeMode: [{
      value: 1,
      text: "优选广告位(推荐)"
    },
    {
      value: 0,
      text: "指定位置"
    }
  ],
  // 创意投放位置,详见【附录-投放位置】
  inventoryType: [{
      value: "INVENTORY_FEED",
      text: "今日头条系"
    },
    {
      value: "INVENTORY_VIDEO_FEED",
      text: "西瓜信息流"
    },
    {
      value: "INVENTORY_HOTSOON_FEED",
      text: "火山信息流"
    },
    {
      value: "INVENTORY_AWEME_FEED",
      text: "抖音信息流"
    }
  ],
  industryCategory: ad_category,
  // 广告预算类型(创建后不可修改), 详见【附录-预算类型】
  // 允许值: "BUDGET_MODE_DAY", "BUDGET_MODE_TOTAL"
  budgetType: [{
      value: "BUDGET_MODE_INFINITE",
      text: "不限",
      show: false
    },
    {
      value: "BUDGET_MODE_DAY",
      text: "日预算",
      show: false
    },
    {
      value: "BUDGET_MODE_TOTAL",
      text: "总预算",
      show: false
    }
  ],
  settlementType:[
    {
      value: 1,
      text: "CPM"
    },
    {
      value: 2,
      text: "CPC"
    },
    {
      value: 7,
      text: "OCPC",
      tips: {
        title: "按点击付费(CPC)",
        content:
            "该方式根据转化进行优化，按照点击进行计费，为您优选流量，最大化转化效果"
      }
    },
    {
      value: 8,
      text: "CPA",
      tips: {
        title: "按转化付费(CPA)",
        content:
            "当您选择的优化目标是“转化”时，系统会按照转化数进行收费，无转化不予扣费"
      }
    },
    {
      value: 9,
      text: "OCPM",
      tips: {
        title: "按展示付费(CPM)",
        content:
            "该方式根据转化进行优化，按照展示进行计费，为您优选流量，最大化转化效果"
      }
    },
    {
      value: 10,
      text: "CPV"
    }
  ]
}