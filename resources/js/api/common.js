import request from '@/js/utils/request';

const requestConfig = {
  retry: true,
  delay: 200
};

export function getBasicData(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/distribute/common_api/get_data',
    method: 'GET',
    params: query
  }, requestConfig);
}

export function getBasicRelData(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/distribute/common_api/get_rel_data',
    method: 'GET',
    params: query
  }, requestConfig);
}

export function getTpl(distribute, id) {
  return request({
    url: VueStore.getters.zonePrefix + '/distribute/common_api/get_tpl',
    method: 'GET',
    params: {
      distribute,
      id
    }
  })
}

// 今日头条

export function getToutiaoData(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/distribute/toutiao_api/get_data',
    method: 'GET',
    params: query
  }, requestConfig);
}

// UC 汇川

export function getUchcData(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/distribute/uchc_api/get_data',
    method: 'GET',
    params: query
  }, requestConfig);
}

export function checkUchcAccount(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/distribute/uchc_api/check_account',
    method: 'POST',
    data: form
  })
}

// UC 汇川 2.0
export function getUchcv2Data(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/distribute/uchcv2_api/get_data',
    method: 'GET',
    params: query
  }, requestConfig);
}

export function checkUchcv2Account(form) {
  return request({
    url: VueStore.getters.zonePrefix + '/distribute/uchcv2_api/check_account',
    method: 'POST',
    data: form
  })
}

// 广告表单

export function selMedia(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/api/sel_media',
    method: 'GET',
    params: query
  })
}

export function selAgent(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/api/sel_agent',
    method: 'GET',
    params: query
  })
}

export function selPosition(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/api/sel_position',
    method: 'GET',
    params: query
  })
}

export function selStyle(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/api/sel_style',
    method: 'GET',
    params: query
  })
}

export function searchMaterial(query) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/api/search_material',
    method: 'GET',
    params: query
  })
}

// type: material | material_annex | lp | lp_annex
export function checkDestroyMaterial(id, type) {
  return request({
    url: VueStore.getters.zonePrefix + '/advertising/helper/annex_remove_check',
    method: 'GET',
    params: {
      id,
      type
    }
  })
}

// 获取上传地址
export function getUploadUrl(action) {
  if (action && action.length > 0) {
    return VueStore.getters.zonePrefix + '/upload/' + action
  } else {
    return VueStore.getters.zonePrefix + '/upload'
  }
}

// 下载文件
export function getDownloadUrl(fileurl) {
  return VueStore.getters.zonePrefix + '/upload/download?url=' + fileurl;
}

// 下载文件
export function postDownload(fileurl) {
  return request({
    url: VueStore.getters.zonePrefix + '/upload/download',
    method: 'POST',
    data: {
      url: fileurl
    }
  })
}