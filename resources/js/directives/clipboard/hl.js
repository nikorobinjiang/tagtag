import store from "store";

const storeKey = 'scat_clipboard_ext_hl';
let storeHL = store.get(storeKey);
if (!storeHL) {
  storeHL = {};
}

// 初始化元素复制标记
export function initHL(el, val) {
  if (val.hlScope && val.hlName) {
    el.__gmvd_clipboard_hl_use = true;
    el.__gmvd_clipboard_hl_scope = val.hlScope ? val.hlScope : null;
    el.__gmvd_clipboard_hl_name = val.hlName ? val.hlName : null;
    if (!storeHL[val.hlScope]) {
      storeHL[val.hlScope] = [];
    }
    saveHL();
    styleHL(el);
  } else {
    el.__gmvd_clipboard_hl_use = false;
  }
}

// 添加元素复制标记
export function addHL(el) {
  if (el.__gmvd_clipboard_hl_use) {
    if (storeHL[el.__gmvd_clipboard_hl_scope].indexOf(el.__gmvd_clipboard_hl_name) === -1) {
      storeHL[el.__gmvd_clipboard_hl_scope].push(el.__gmvd_clipboard_hl_name);
    }
    saveHL();
    styleHL(el);
  }
}

// 添加元素复制样式
export function styleHL(el) {
  if (el.__gmvd_clipboard_hl_use) {
    if (storeHL[el.__gmvd_clipboard_hl_scope].indexOf(el.__gmvd_clipboard_hl_name) !== -1) {
      el.style.color = 'Coral';
    }
  }
}

function saveHL() {
  store.set(storeKey, storeHL);
}