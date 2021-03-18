import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/agent/agent',
    method: 'GET',
    params: query
  })
}

export const createItem = function () {
  return VueStore.getters.zonePrefix + '/agent/agent/create'
}

export const editItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/agent/agent/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/agent/agent',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/agent/agent/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}