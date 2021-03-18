import request from '@/js/utils/request';
import {
  requestDelay
} from '@/js/utils/request';

export function batchRepkg(ids, data) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/helper/re_pkg_batch',
    method: 'GET',
    params: {
      ids: ids,
      data: data
    }
  })
}

export function removeDwonHistory(ad_id, history_id) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/api/remove_down_history',
    method: 'GET',
    params: {
      ad_id: ad_id,
      history_id: history_id
    }
  })
}

export function downloadAdMaterial(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/helper/download_ad_material',
    method: 'POST',
    data: form
  })
}

export function fetchList(query) {
  return requestDelay({
    url: VueStore.getters.zonePrefix + '/advertising/advertising',
    method: 'GET',
    params: query
  }, 200);
}

export const createItem = function () {
  return VueStore.getters.zonePrefix + '/advertising/advertising/create'
}

export const showItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/advertising/' + id + window.location.search,
    method: 'GET'
  })
}

export const editItemUrl = function (id) {
  return VueStore.getters.zonePrefix + '/advertising/advertising/' + id + '/edit'
}

export const editItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/advertising/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/advertising',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/advertising/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}

export function destroyItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/advertising/' + id,
    method: 'DELETE',
    data: {
      data: form
    }
  })
}