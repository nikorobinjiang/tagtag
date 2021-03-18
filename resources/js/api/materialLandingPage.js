import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/landing_page',
    method: 'GET',
    params: query
  })
}

export const createItem = function () {
  return VueStore.getters.zonePrefix + '/material/landing_page/create'
}

export const editItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/landing_page/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/landing_page',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/landing_page/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}

export function destroyItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/landing_page/' + id,
    method: 'DELETE',
    data: {
      data: form
    }
  })
}