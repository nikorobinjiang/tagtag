import request from '@/js/utils/request';
import {
  requestDelay
} from '@/js/utils/request';

export function batchGetData(query) {
  return requestDelay({
    url: VueStore.getters.zonePrefix + '/distribute/batch_api/get_data',
    method: 'GET',
    params: query
  })
}

export function batchSetData(ad_ids, query) {
  return requestDelay({
    url: VueStore.getters.zonePrefix + '/distribute/batch_api/set_data',
    method: 'POST',
    data: Object.assign({}, {
        ad_ids
      },
      query)
  })
}

export function batchSetStatus(ad_ids, status) {
  return request({
    url: VueStore.getters.zonePrefix + '/distribute/batch_api/set_status',
    method: 'POST',
    data: {
      ad_ids,
      status
    }
  })
}