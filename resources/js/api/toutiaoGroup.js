import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/tpl/toutiao_group',
    method: 'GET',
    params: query
  })
}

export const syncItem = function (query) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/api/sync_ad_group',
    method: 'GET',
    params: query
  })
}

export const createItem = function () {
  return VueStore.getters.zonePrefix + '/tpl/toutiao_group/create'
}

export const editItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/tpl/toutiao_group/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/tpl/toutiao_group',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/tpl/toutiao_group/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}