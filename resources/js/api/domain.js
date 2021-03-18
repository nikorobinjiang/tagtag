import request from '@/js/utils/request'

export const urlCreateDown = '/domain/down/create'

export function fetchList(query) {
  return request({
    url: '/domain/data',
    method: 'get',
    params: query
  })
}

export function fetchPageList(query) {
  return request({
    url: '/domain/page',
    method: 'get',
    params: query
  })
}

export function fetchGameList(query) {
  return request({
    url: '/domain/game',
    method: 'get',
    params: query
  })
}

export function fetchDownList(query) {
  return request({
    url: '/domain/down',
    method: 'get',
    params: query
  })
}

export function storeDown(query) {
  return request({
    url: '/domain/down',
    method: 'POST',
    data: {
      data:query
    }
  })
}
export const createDown = function () {
  return VueStore.getters.zonePrefix + '/domain/down/create'
}

export const editDown = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/domain/down/' + id + '/edit',
    method: 'GET'
  })
}

export function updateDown(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/domain/down/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}

export const createPage = function () {
  return VueStore.getters.zonePrefix + '/domain/page/create'
}

export const editPage = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/domain/page/' + id + '/edit',
    method: 'GET'
  })
}

export function storePage(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/domain/page',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updatePage(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/domain/page/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}