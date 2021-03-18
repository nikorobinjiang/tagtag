import request from '@/js/elementAdmin/utils/request'

export function fetchList(query) {
  return request({
    url: '/getData',
    method: 'get',
    params: query
  })
}
