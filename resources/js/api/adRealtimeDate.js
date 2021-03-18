import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/realtime_data',
    method: 'GET',
    params: query
  })
}
