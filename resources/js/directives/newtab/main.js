const newtabMain = function (url) {
  window.open(url)
}

const funcBind = (el, value) => {
  let url = value.url ? value.url : value;
  if (el.__gmvd_newtab_click_func) {
    funcUnbind(el);
  }

  el.__gmvd_newtab_click_func = function (e) {
    e.stopPropagation();
    newtabMain(url);
  }
  el.addEventListener('click', el.__gmvd_newtab_click_func);
};

const funcUnbind = el => {
  el.removeEventListener('click', el.__gmvd_newtab_click_func);
};

export default _ => {
  return {
    bind: function (el, binding) {
      if (binding.value) {
        funcBind(el, binding.value);
      }
    },
    update: function (el, binding) {
      if (binding.value) {
        funcBind(el, binding.value);
      }
    },
    unbind: function (el) {
      funcUnbind(el);
    }
  };
};