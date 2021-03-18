import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/media/account',
    method: 'GET',
    params: query
  })
}

export const createItem = function () {
  return VueStore.getters.zonePrefix + '/media/account/create'
}

export const editItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/media/account/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/media/account',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/media/account/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}

export function destroyItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/media/account/' + id,
    method: 'DELETE',
    data: {
      data: form
    }
  })
}