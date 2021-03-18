import request from '@/js/utils/request'

export function fetchList(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/picture',
    method: 'GET',
    params: query
  })
}

export const createItem = function () {
  return VueStore.getters.zonePrefix + '/material/picture/create'
}

export const editItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/picture/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/picture',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/picture/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}

export function destroyItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/picture/' + id,
    method: 'DELETE',
    data: {
      data: form
    }
  })
}

export function downloadItem(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/material/pic_pack_download',
    method: 'GET',
    params: query
  })
}
