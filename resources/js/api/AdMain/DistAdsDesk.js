import request from '@/js/utils/request'


export const showItem = function (id) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/dist_ads_desk/' + id + window.location.search,
    method: 'GET'
  })
}

export function editItem(id) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/dist_ads_desk/' + id + '/edit',
    method: 'GET'
  })
}

export function storeItem(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/dist_ads_desk',
    method: 'POST',
    data: {
      data: form
    }
  })
}

export function updateItem(id, form) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/dist_ads_desk/' + id,
    method: 'PUT',
    data: {
      data: form
    }
  })
}
