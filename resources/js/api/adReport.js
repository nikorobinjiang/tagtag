import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/report',
    method: 'GET',
    params: query
  })
}

export function showItemUrl(date, query) {
  return VueStore.getters.zonePrefix + '/advertising/report/' + date;
}

export function showItem(query) {
  return request({
    //url: VueStore.getters.zonePrefix + '/advertising/report/ ' + date,
    url: '',
    method: 'GET',
    params: query
  })
}