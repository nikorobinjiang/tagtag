import Clipboard from 'clipboard';

import {
  initHL,
  addHL
} from './hl'

if (!Clipboard) {
  throw new Error('you shold npm install `clipboard` --save at first ');
}

let clipboard_success = null;
let clipboard_error = null;

const bindClipboard = (el, value) => {
  el.removeAttribute('disabled');
  const clipboard = new Clipboard(el, {
    text() {
      return value.text;
    },
    action() {
      return value.action === 'cut' ? 'cut' : 'copy';
    }
  });
  el.__gmvd_clipboard_success = (typeof value.success === "function") ? value.success : clipboard_success;
  el.__gmvd_clipboard_error = (typeof value.error === "function") ? value.error : clipboard_error;

  initHL(el, value);

  clipboard.on('success', e => {
    const callback = el.__gmvd_clipboard_success;
    callback && callback(e);
    addHL(el);
  }).on('error', e => {
    const callback = el.__gmvd_clipboard_error;
    callback && callback(e);
    // 释放内存
    unbindClipboard(el);
  });

  el.__gmvd_clipboard = clipboard;
};

const unbindClipboard = el => {
  el.__gmvd_clipboard.destroy();
  delete el.__gmvd_clipboard;
  delete el.__gmvd_clipboard_success;
  delete el.__gmvd_clipboard_error;
};

const rebindClipboard = (el, value) => {
  unbindClipboard(el);
  bindClipboard(el, value);
}

export default (cb_success, cb_error) => {
  clipboard_success = cb_success ? cb_success : null;
  clipboard_error = cb_error ? cb_error : null;
  return {
    bind: function (el, binding) {
      if (binding.value) {
        bindClipboard(el, binding.value);
      }
    },
    update: function (el, binding) {
      if (binding.value) {
        rebindClipboard(el, binding.value);
      } else {
        unbindClipboard(el);
      }
    },
    unbind: function (el) {
      if (el) {
        unbindClipboard(el);
      }
    }
  };
};