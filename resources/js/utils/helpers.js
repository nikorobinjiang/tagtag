export const basename = function (str) {
  if (str) {
    return _.split(str, "/").pop();
  } else {
    return "";
  }
}

export const renewObject = function () {
  if (arguments.length > 0) {
    let res = Object.assign({}, ...arguments);
    return JSON.parse(JSON.stringify(res));
  } else {
    return {};
  }
}