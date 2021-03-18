import {
  Message
} from 'element-ui';
import Main from './main';

// 设置默认值回调函数
const main = Main(_ => {
  Message.success('复制成功');
}, _ => {
  Message.warning('目前暂时不支持该浏览器，请手动复制');
});

const install = function (Vue) {
  if (Vue.prototype.$isServer) return;
  Vue.directive('newtab', main);
}

if (window.Vue) {
  window.newtab = main;
  Vue.use(install);
}

main.install = install;

export default main;