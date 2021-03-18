import request from '@/js/utils/request'

const requestConfig = {
  retry: true,
  delay: 1000
};

export function fetchInfo(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/',
    method: 'GET',
    params: query
  }, requestConfig);
}

export function fetchData(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/home/getData',
    method: 'get',
    params: query
  }, requestConfig);
}
