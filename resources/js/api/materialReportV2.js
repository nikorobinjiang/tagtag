import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/material_report_v2',
    method: 'GET',
    params: query
  })
}

export function fetchMaterialList(query){
  return request({
    url: VueStore.getters.zonePrefix + '/material/designer_material_detail',
    method: 'GET',
    params: query
  })
}

export function fetchCreativeDetail(query){
  return request({
    url: VueStore.getters.zonePrefix + '/material/creative_detail',
    method: 'GET',
    params: query
  })
}

export function fetchReportStatistic(query){
  return request({
    url: VueStore.getters.zonePrefix + '/material/reportStatistic',
    method: 'GET',
    params: query
  })
}

export function ItemDetail(id) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/material_report_v2/' + id +'/show',
    method: 'GET',
    data: {
      
    }
  })
}

export function ItemToutiaoDetail(id) {
    return request({
      url: VueStore.getters.zonePrefix + '/material/material_report_v2_detail_toutiao?ad_id=' + id,
      method: 'GET',
      data: {
        data: form
      }
    })
  }