import Vue from 'vue'
import Vuex from 'vuex'

import xStore from './modules/xStore'
import adIndex from './modules/adIndex'
import adNormal from './modules/adNormal'
import adToutiao from './modules/adToutiao'
import adUchc from './modules/adUchc'
import adUchcv2 from './modules/adUchcv2'
import adRealtimeData from './modules/adRealtimeData'
import realtimeData from './modules/realtimeData'
import tplToutiaoTpl from './modules/tplToutiaoTpl'
import tplUchcTpl from './modules/tplUchcTpl'
import reportStatistics from './modules/reportStatistics'
import tplUchcv2Tpl from './modules/tplUchcv2Tpl'

import getters from './getters'

Vue.use(Vuex)

const store = new Vuex.Store({
  modules: {
    xStore,
    adIndex,
    adNormal,
    adToutiao,
    adUchc,
    adUchcv2,
    adRealtimeData,
    realtimeData,
    tplToutiaoTpl,
    tplUchcTpl,
    reportStatistics,
    tplUchcv2Tpl,
  },
  getters,
  state: {
    zone: ''
  },
  mutations: {
    setZone(state, zone) {
      state.zone = zone;
    }
  }
})

export default store