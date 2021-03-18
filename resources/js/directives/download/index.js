import Main from './main';

const main = Main();

const install = function (Vue) {
  if (Vue.prototype.$isServer) return;
  Vue.directive('download', main);
}

if (window.Vue) {
  window.download = main;
  Vue.use(install);
}

main.install = install;

export default main;