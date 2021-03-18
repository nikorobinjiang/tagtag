import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/tpl/uchc_tpl',
    method: 'GET',
    params: query
  })
}

export const createItem = function () {
  return VueStore.getters.zonePrefix + '/tpl/uchc_tpl/create'
}

export const editItemUrl = function (id) {
  return VueStore.getters.zonePrefix + '/tpl/uchc_tpl/' + id + '/edit'
}

export const editItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/tpl/uchc_tpl/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/tpl/uchc_tpl',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/tpl/uchc_tpl/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}

export function destroyItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/tpl/uchc_tpl/' + id,
    method: 'DELETE',
    data: {
      data: form
    }
  })
}