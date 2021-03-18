import { getDownloadUrl } from "@/js/api/common";

const downloadFile = function (url, filename) {
  let dlLink = document.createElementNS(
    "http://www.w3.org/1999/xhtml",
    "a"
  );
  dlLink.href = url;
  dlLink.download = filename ? filename : '';
  dlLink.target = '_blank';
  if (true) {
    let evt = document.createEvent("HTMLEvents");
    evt.initEvent("click", false, false);
    dlLink.dispatchEvent(evt);
  } else {
    var evt = document.createEvent("MouseEvents");
    evt.initMouseEvent(
      "click",
      true,
      false,
      window,
      0,
      0,
      0,
      0,
      0,
      false,
      false,
      false,
      false,
      0,
      null
    );
    dlLink.dispatchEvent(evt);
  }
  dlLink.click();
}

const downloadByRedirect = function (url, filename) {
  let dlLink = document.createElementNS(
    "http://www.w3.org/1999/xhtml",
    "a"
  );
  dlLink.href = getDownloadUrl(url);
  // dlLink.download = filename ? filename : '';
  dlLink.target = '_blank';
  if (true) {
    let evt = document.createEvent("HTMLEvents");
    evt.initEvent("click", false, false);
    dlLink.dispatchEvent(evt);
  } else {
    var evt = document.createEvent("MouseEvents");
    evt.initMouseEvent(
      "click",
      true,
      false,
      window,
      0,
      0,
      0,
      0,
      0,
      false,
      false,
      false,
      false,
      0,
      null
    );
    dlLink.dispatchEvent(evt);
  }
  dlLink.click();
}

const funcBind = (el, value) => {
  let url = value.url ? value.url : '';
  let filename = value.filename ? value.filename : '';
  if (el.__gmvd_download_click_func) {
    funcUnbind(el);
  }

  el.__gmvd_download_click_func = function (e) {
    e.stopPropagation();
    downloadByRedirect(url, filename);
  }
  el.addEventListener('click', el.__gmvd_download_click_func);
};

const funcUnbind = el => {
  el.removeEventListener('click', el.__gmvd_download_click_func);
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