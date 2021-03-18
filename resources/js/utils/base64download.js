export const downloadBlob = (data, filename) => {
  if (!data) {
    return;
  }
  let url = window.URL.createObjectURL(new Blob([data]));
  let link = document.createElement("a");
  link.style.display = "none";
  link.href = url;
  link.setAttribute("download", filename);

  document.body.appendChild(link);
  link.click();
};

export const downloadFile = function (url, filename) {
  let dlLink = document.createElementNS(
    "http://www.w3.org/1999/xhtml",
    "a"
  );
  dlLink.href = url;
  dlLink.download = filename ? filename : '';
  let evt = document.createEvent("HTMLEvents");
  evt.initEvent("click", false, false);
  // var evt = document.createEvent("MouseEvents");
  // evt.initMouseEvent(
  //     "click",
  //     true,
  //     false,
  //     window,
  //     0,
  //     0,
  //     0,
  //     0,
  //     0,
  //     false,
  //     false,
  //     false,
  //     false,
  //     0,
  //     null
  // );
  dlLink.dispatchEvent(evt);
  dlLink.click();
}