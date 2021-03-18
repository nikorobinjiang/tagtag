import Vue from 'vue';
import Main from './main.vue'

const MainConstructor = Vue.extend(Main);

let instance;
let instances = [];
let seed = 1;

export default _ => {
  return {
    bind: function (el, binding, vnode) {
      // 插入dom
      if (instances.length <= 0) {
        let id = 'gmvd_preview_' + seed++;
        instance = new MainConstructor({
          el: document.createElement('div'),
          data: {},
        });
        instance.id = id;
        instance.vm = instance.$mount();
        document.body.appendChild(instance.vm.$el);
        instances.push(instance);
        window.preview = instance;
      }
      let file_url = binding.value ? binding.value.file_url : null;
      let file_type = binding.value ? binding.value.file_type : null;
      el.addEventListener('click', function (e) {
        e.stopPropagation();
        let options = {
          isTitleEnable: el.getAttribute('preview-title-enable') == "false" ? false : true,
          isHorizontalNavEnable: el.getAttribute('preview-nav-enable') == "true" ? true : false,
          show: true,
          loading: true,
          current: {
            title: el.title ? el.title : (el.alt ? el.alt : ''),
            el: el,
            index: 0,
            src: el.src ? el.src : '',
            file_url: file_url ? file_url : '',
            file_type: file_type ? file_type : '',
          },
        }
        instance.$data.options = Object.assign({},
          instance.$data.options,
          options, {
            data: {
              instance: this
            }
          });
      });
    },
    update: function (el, oldValue) {
      var previewItem = instance.$data.options.list.find(function (item) {
        return item.el === el;
      });
      if (!previewItem) return;
      previewItem.src = oldValue.value;
      previewItem.title = el.title ? el.title : (el.alt ? el.alt : '');
    },
    unbind: function (el) {
      if (el) {
        instance.$data.options.list.forEach(function (item, index) {
          if (el === item.el) {
            instance.$data.options.list.splice(index, 1);
          }
        });
      }
    }
  };
}