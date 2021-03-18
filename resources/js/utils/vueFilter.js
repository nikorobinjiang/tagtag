import Vue from 'vue';

const formatSize = function (size) {
  if (isNaN(size)) {
    return '-';
  }
  if (size > 1024 * 1024 * 1024 * 1024) {
    return (size / 1024 / 1024 / 1024 / 1024).toFixed(2) + ' TB'
  } else if (size > 1024 * 1024 * 1024) {
    return (size / 1024 / 1024 / 1024).toFixed(2) + ' GB'
  } else if (size > 1024 * 1024) {
    return (size / 1024 / 1024).toFixed(2) + ' MB'
  } else if (size > 1024) {
    return (size / 1024).toFixed(2) + ' KB'
  }
  return size.toString() + ' B'
};

const formatDistributeMsg = function (str) {
  const res = str.split(/[\n；。]/).filter(item => {
    return item.trim().length > 0;
  }).join("。<br />");
  return res.length > 0 ? res + '。' : '';
};

Vue.filter('formatSize', formatSize);
Vue.filter('formatDistributeMsg', formatDistributeMsg);

if (!Vue.prototype.$scat) {
  Vue.prototype.$scat = {};
}
if (!Vue.prototype.$scat.filters) {
  Vue.prototype.$scat.filters = {};
}

Vue.prototype.$scat.filters.formatSize = formatSize
Vue.prototype.$scat.filters.formatDistributeMsg = formatDistributeMsg