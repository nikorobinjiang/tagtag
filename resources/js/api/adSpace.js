import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/ad_space',
    method: 'GET',
    params: query
  })
}

export const createItem = function () {
  return VueStore.getters.zonePrefix + '/advertising/ad_space/create'
}

export const editItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/ad_space/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/ad_space',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/ad_space/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}