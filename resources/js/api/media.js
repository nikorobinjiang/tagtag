import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/media/media',
    method: 'GET',
    params: query
  })
}
export function fetchAdpositionList(query){
  return request({
    url: VueStore.getters.zonePrefix + '/media/adposition',
    method: 'GET',
    params: query
  })
}
export function fetchPromoteList(query){
  return request({
    url: VueStore.getters.zonePrefix + '/media/media_promote',
    method: 'GET',
    params: query
  })
}
export const createItem = function () {
  return VueStore.getters.zonePrefix + '/media/media/create'
}

export const editItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/media/media/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/media/media',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/media/media/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}