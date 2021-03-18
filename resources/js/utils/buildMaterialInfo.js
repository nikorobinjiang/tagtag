const _ = require('lodash');

const styles = {
  "280": {
    "style_id": 280,
    "name": "今日头条横版视频",
    "style_info": {
      "text": [],
      "img": [],
      "video": [{
        "name": "视频",
        "format": "MP4",
        "max": "100",
        "width": "1280",
        "height": "720",
        "size_unit": "MB",
        "has_cover": "1",
        "cover_name": "视频封面",
        "cover_format": "JPG",
        "cover_max": "100",
        "cover_width": "1280",
        "cover_height": "720",
        "cover_size_unit": "KB"
      }]
    }
  },
  "283": {
    "style_id": 283,
    "name": "大图",
    "style_info": {
      "text": [{
        "name": "创意标题",
        "max_length": "32"
      }],
      "img": [{
        "name": "大图",
        "format": "JPG",
        "max": "",
        "width": "690",
        "height": "388",
        "size_unit": "KB"
      }],
      "video": []
    }
  },
  "284": {
    "style_id": 284,
    "name": "组图",
    "style_info": {
      "text": [{
        "name": "创意标题",
        "max_length": "32"
      }],
      "img": [{
        "name": "组图1",
        "format": "JPG",
        "max": "2048",
        "width": "228",
        "height": "150",
        "size_unit": "KB"
      }, {
        "name": "组图2",
        "format": "JPG",
        "max": "2048",
        "width": "228",
        "height": "150",
        "size_unit": "KB"
      }, {
        "name": "组图3",
        "format": "JPG",
        "max": "2048",
        "width": "228",
        "height": "150",
        "size_unit": "KB"
      }],
      "video": []
    }
  }
};
const contents = {
  "280": {
    "video": [{
      "视频": {
        "video": {
          "id": 429,
          "name": "视频1号",
          "file_url": "http://localhost:81/uploads/videos/2018/06/22/fpFkVrOYyE.mp4",
          "preview": "/images/demo.jpg",
          "designer_name": "无",
          "designer_type": 1,
          "designer_id": 48,
          "material_id": 80
        }
      }
    }, {
      "视频": {
        "video": {
          "id": 429,
          "name": "视频1号",
          "file_url": "http://localhost:81/uploads/videos/2018/06/22/fpFkVrOYyE.mp4",
          "preview": "/images/demo.jpg",
          "designer_name": "无",
          "designer_type": 1,
          "designer_id": 48,
          "material_id": 80
        }
      }
    }],
    "extra": {
      "has_video_cover_creative": true
    }
  },
  "283": {
    "img": [{
      "大图": {
        "id": 458,
        "name": "a1_001",
        "preview": "http://localhost:81/uploads/images\\2018\\07\\04\\j8QibWeoyo.png",
        "designer_name": "无",
        "designer_type": 1,
        "designer_id": 26,
        "material_id": 93
      }
    }, {
      "大图": {
        "id": 380,
        "name": "图片素材001111",
        "preview": "http://localhost:81/uploads/images/2018/06/07/uEFJsnHlAQ.png",
        "designer_name": "【运营】马彤辉",
        "designer_type": 0,
        "designer_id": 58,
        "material_id": 62
      }
    }]
  },
  "284": {
    "img": [{
      "组图1": {
        "id": 458,
        "name": "a1_001",
        "preview": "http://localhost:81/uploads/images\\2018\\07\\04\\j8QibWeoyo.png",
        "designer_name": "无",
        "designer_type": 1,
        "designer_id": 26,
        "material_id": 93
      },
      "组图2": {
        "id": 458,
        "name": "a1_001",
        "preview": "http://localhost:81/uploads/images\\2018\\07\\04\\j8QibWeoyo.png",
        "designer_name": "无",
        "designer_type": 1,
        "designer_id": 26,
        "material_id": 93
      },
      "组图3": {
        "id": 409,
        "name": "131",
        "preview": "http://localhost:81/uploads/images\\2018\\06\\13\\PMBayuFeyd.png",
        "designer_name": "无",
        "designer_type": 1,
        "designer_id": 46,
        "material_id": 74
      }
    }, {
      "组图1": {
        "id": 426,
        "name": "动图",
        "preview": "http://localhost:81/uploads/images/2018/06/21/uN81DQQOPI.jpg",
        "designer_name": "【运营】马彤辉",
        "designer_type": 0,
        "designer_id": 58,
        "material_id": 78
      },
      "组图2": {
        "id": 380,
        "name": "图片素材001111",
        "preview": "http://localhost:81/uploads/images/2018/06/07/uEFJsnHlAQ.png",
        "designer_name": "【运营】马彤辉",
        "designer_type": 0,
        "designer_id": 58,
        "material_id": 62
      }
    }]
  },
  "text": ["A", "B"]
};

export const buildMaterialInfo = (styles, contents) => {
  console.log(styles, contents);
  let res = {};
  Object.keys(styles).forEach(styleId => {
    if (contents[styleId] === undefined) {
      return;
    }

    let style = styles[styleId];
    let content = contents[styleId];
    let styleInfo = style["style_info"];
    if (!styleInfo) {
      return;
    }
    let styleTypes = Object.keys(styleInfo);

    let multis = {};
    // 素材
    if (Object.keys(content).length <= 0) {
      return;
    }
    multis = Object.assign({}, _.pick(content, ["img", "video"]));
    // 文字
    if (contents['ext'] !== undefined && contents['ext']["text"] !== undefined) {
      multis["text"] = contents['ext']["text"];
    }

    let packs = [];
    Object.keys(multis).forEach(type => {
      if (styleInfo[type].length > 0) {
        let fieldInfo = styleInfo[type][0];
        let parts = [];
        multis[type].forEach(typeVal => {
          let field = {};
          if (type === 'text') {
            let fieldContent = {};
            fieldContent[fieldInfo.name] = {
              "name": fieldInfo.name,
              "max_length": fieldInfo.max_length,
              "value": typeVal
            }
            field[type] = fieldContent;
          } else {
            field[type] = typeVal;
          }
          if (packs.length === 0) {
            parts.push(field);
          } else {
            packs.forEach(item => {
              parts.push(Object.assign({}, item, field));
            });
          }
        });
        packs = parts;
      }
    });
    res[styleId] = packs;
  });
  console.log(res);
  return res;
};

// console.log(buildMaterialInfo(styles, contents));
// export const buildMaterialInfo = proceduralMaterial;