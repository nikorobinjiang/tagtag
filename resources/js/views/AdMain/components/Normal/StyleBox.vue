<template>
  <div class="input-block style-box">
    <el-row v-for="(style, styleKey) in styleInfo" :key="styleKey">
      <el-col :span="20">
        <el-form-item label="" v-if="styleKey === 0">
          <label for="">{{ style.name }}</label>
        </el-form-item>

        <el-form-item label="文字" v-if="style.text && style.text.length > 0">
          <StyleInputText
            :formScope="formScope"
            :promoteId="promoteId"
            :creativeId="style.creative_id"
            :styleKey="styleKey"
            :textList="style.text"
            @transferText="transferText">
          </StyleInputText>
        </el-form-item>

        <el-form-item label="图片" v-if="style.img.length > 0">
          <el-row v-if="!formItemDisabled">
            <el-button type="text" @click="handleDisplayMaterialSelect(styleKey, 'img')">选择素材 </el-button>
          </el-row>
          <el-row>
            <el-col :xs="12" :sm="6"
              v-for="(img, imgKey) in materialInfo[styleKey].img"
              :key="imgKey">
              <el-card>
                <div slot="header" class="clearfix">
                  <span>{{ img.name }}</span>
                  <el-button
                    type="text"
                    v-if="!formItemDisabled"
                    @click="delMaterial(styleKey, 'img', imgKey, img)">
                    <span class="el-icon-delete"></span>
                  </el-button>
                  <el-button
                    type="text"
                    v-if="!formItemDisabled"
                    v-download="{ url: img.preview }">
                    <span class="el-icon-download"></span>
                  </el-button>
                </div>
                <img :src="img.preview" :title="img.name" v-preview class="image">
                <div class="bottom">
                  <span :title="img.designer_name" class="bottom-text">{{ img.designer_name }}</span>
                </div>
              </el-card>
            </el-col>
          </el-row>
        </el-form-item>

        <el-form-item label="视频" v-if="style.video.length > 0">
          <el-row v-if="!formItemDisabled">
            <el-button type="text" @click="handleDisplayMaterialSelect(styleKey, 'video')">选择素材</el-button>
            <el-checkbox v-if="false && formScope === 'Toutiao'" v-model="materialInfo[styleKey].extra.has_video_cover_creative">封面创意</el-checkbox>
          </el-row>
          <el-row
            type="flex"
            v-for="(video, videoKey) in materialInfo[styleKey].video"
            :key="videoKey">
            <el-col
              :xs="12" :sm="6"
              v-if="video.video && Object.keys(video.video).length > 0">
              <el-card>
                <div slot="header" class="clearfix">
                  <span class="bottom-text" :title="video.video.name">{{ video.video.name }}</span>
                  <el-button
                    type="text"
                    v-if="!formItemDisabled"
                    @click="delVideo(styleKey, 'video', videoKey, 'video')">
                    <span class="el-icon-delete"></span>
                  </el-button>
                  <el-button
                    type="text"
                    v-if="!formItemDisabled"
                    v-download="{ url: video.video.file_url }">
                    <span class="el-icon-download"></span>
                  </el-button>
                </div>
                <img :src="video.video.preview" :title="video.video.name" v-preview="{file_url: video.video.file_url}" class="image">
                <div class="bottom">
                  <span :title="video.video.designer_name" class="bottom-text">{{ video.video.designer_name }}</span>
                    <div class="bottom clearfix">
                  </div>
                </div>
              </el-card>
            </el-col>
            <el-col
              :xs="12" :sm="6"
              v-if="video.video_cover && Object.keys(video.video_cover).length > 0">
              <el-card>
                <div slot="header" class="clearfix">
                  <span class="bottom-text" :title="video.video_cover.name">{{ video.video_cover.name }}</span>
                  <el-button
                    type="text"
                    v-if="!formItemDisabled"
                    @click="delVideo(styleKey, 'video', videoKey, 'video_cover')">
                    <span class="el-icon-delete"></span>
                  </el-button>
                  <el-button
                    type="text"
                    v-if="!formItemDisabled"
                    v-download="{ url: video.video_cover.preview }">
                    <span class="el-icon-download"></span>
                  </el-button>
                </div>
                <img :src="video.video_cover.preview" :title="video.video_cover.name" v-preview class="image">
                <div class="bottom">
                  <span :title="video.video_cover.designer_name" class="bottom-text">{{ video.video_cover.designer_name }}</span>
                    <div class="bottom clearfix">
                  </div>
                </div>
              </el-card>
            </el-col>
          </el-row>
        </el-form-item>
      </el-col>

      <el-col :span="4">
        <el-button
          type="primary" size="mini" round
          @click="addStyle(style.name)"
          v-if="!formItemDisabled && isMultiMaterials && styleKey === 0 && styleIds.indexOf(styleId) !== -1">
          添加
        </el-button>
        <el-button
          type="primary" size="mini" round
          @click="delStyle(styleKey)"
          v-else-if="!materialInfo[styleKey].id && !formItemDisabled && isMultiMaterials && styleIds.indexOf(styleId) === -1">
          删除
        </el-button>
        <el-button
          type="primary" size="mini" round
          @click="delStyle(styleKey)"
          v-else-if="!materialInfo[styleKey].id && !formItemDisabled && isMultiMaterials && styleKey !== 0">
          删除
        </el-button>
      </el-col>
    </el-row>

    <keep-alive
      v-if="isDisplaySelector">
      <component
        v-bind:is="selectType=='img'?'StyleSelectMaterial':'StyleSelectVideo'"
        :isDisplay="isDisplaySelector"
        :gameId="gameId"
        :styleId="styleId"
        :selectType="selectType"
        :styleInfo="selectStyle"
        :selMark="selMark"
        :styleItems="selectItems"
        @transferMaterial="transferMaterial"
      ></component>
    </keep-alive>
  </div>
</template>

<script>
import { flatten, values } from 'lodash';
import { selStyle } from "@/js/api/common";
import { renewObject } from "@/js/utils";
import { FormScope, xStore } from "@/js/mixins";
import { Injector } from "../../mixins";

import StyleInputText from "./StyleInputText";
import StyleSelectMaterial from "./StyleSelectMaterial";
import StyleSelectVideo from "./StyleSelectVideo";

export default {
  mixins: [FormScope, xStore, Injector],
  name: "style-box",
  components: {
    StyleInputText,
    StyleSelectMaterial,
    StyleSelectVideo
  },
  props: {
    value: {},
    promoteId: {
      required: true
    },
    styleId: {
      type: Number,
      required: true
    },
    styleList: {
      type: Array,
      required: true
    },
    isMultiMaterials: {},
    gameId: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null
          ? {
              material_info: {}
            }
          : this.value,
      isDisplaySelector: false,
      styleInfo: [],
      styleEmpty: {},
      selectType: "", // img | video
      selectStyle: "",
      selMark: {},
      selectItems: {},
      materialInfo: [],
      materialEmpty: {
        extra: {
          has_video_cover_creative: false // 废弃，暂时没有使用
        }
      }
    };
  },
  methods: {
    getStyleSize: function() {
      return 0;
    },
    delMaterial: function(idx, field, key, item) {
      let materialNow = this.materialInfo[idx];
      Vue.delete(materialNow[field], key);
      if (Object.keys(materialNow[field]).length <= 0) {
        Vue.delete(materialNow, field);
      }
      this.$forceUpdate();
    },
    delVideo: function(idx, field, key, item) {
      let materialNow = this.materialInfo[idx];
      Vue.delete(materialNow[field][key], item);
      if (Object.keys(materialNow[field][key]).length <= 0) {
        Vue.delete(materialNow[field], key);
      }
      if (Object.keys(materialNow[field]).length <= 0) {
        Vue.delete(materialNow, field);
      }
      this.$forceUpdate();
    },
    addStyle: function(name) {
      if (this.styleInfo.length < 10) {
        this.styleInfo.push(renewObject(this.styleEmpty));
        this.materialInfo.push(renewObject(this.materialEmpty));
        Vue.set(this.material_info, this.styleId, this.materialInfo);
        this.material_info = this.material_info;
      } else {
        this.$message({
          message: "最多可增加十个同种素材要求",
          center: true
        });
      }
    },
    delStyle: function(key) {
      let doDeleteStyle = () => {
        this.styleInfo = _.remove(this.styleInfo, function(style, idx) {
          return idx !== key;
        });
        this.materialInfo = _.remove(this.materialInfo, function(style, idx) {
          return idx !== key;
        });
        Vue.set(this.material_info, this.styleId, this.materialInfo);
        this.material_info = this.material_info;
        this.$message({
          type: "success",
          message: "删除成功!"
        });
      };
      let materialInfoKeys = Object.keys(this.materialInfo[key]);
      if (
        materialInfoKeys.length <= 0 ||
        (materialInfoKeys.length === 1 &&
          materialInfoKeys.indexOf("id") !== -1) ||
        (materialInfoKeys.length === 1 &&
          materialInfoKeys.indexOf("extra") !== -1)
      ) {
        doDeleteStyle();
      } else {
        this.$confirm("删除该样式将清除素材", "", {
          confirmButtonText: "确定",
          cancelButtonText: "取消",
          type: "warning"
        })
          .then(_ => {
            doDeleteStyle();
          })
          .catch(e => {
            this.$message({
              type: "info",
              message: "已取消删除"
            });
          });
      }
    },
    transferMaterial: function(action, value, selMark) {
      if (action === "confirm") {
        let materialNow = this.materialInfo[selMark.idx];
        Vue.set(materialNow, selMark.type, value);
        this.materialInfo.splice(selMark.idx, 1, materialNow);
        Vue.set(this.material_info, this.styleId, this.materialInfo);
        this.material_info = this.material_info;
      }
      this.isDisplaySelector = false;
    },
    transferText: function(idx, field, value) {
      let materialNow = this.materialInfo[idx];
      if (!materialNow["text"]) {
        this.$set(materialNow, "text", {});
      }
      if (value.value) {
        materialNow["text"][field] = value;
      } else {
        Vue.delete(materialNow["text"], field);
      }
      if (Object.keys(materialNow["text"]).length <= 0) {
        Vue.delete(materialNow, "text");
      }
      this.materialInfo.splice(idx, 1, materialNow);
      Vue.set(this.material_info, this.styleId, this.materialInfo);
      this.material_info = this.material_info;
    },
    handleDisplayMaterialSelect: function(idx, type) {
      this.selectType = type;
      this.selectStyle = this.styleInfo[idx];
      this.isDisplaySelector = true;
      this.selMark = {
        type: type,
        idx: idx
      };
      try {
        if (this.curVal.material_info[this.styleId][idx][type]) {
          this.selectItems = JSON.parse(
            JSON.stringify(this.curVal.material_info[this.styleId][idx][type])
          );
        }
      } catch (error) {}
    }
    // handleChangeExtra: function(val, idx, type) {
    //   let materialNow = Object.assign({}, this.materialInfo[idx]);
    //   let extra = materialNow.extra === undefined ? {} : materialNow.extra;
    //   this.$set(extra, type, val);
    //   this.$set(materialNow, "extra", extra);
    //   this.materialInfo.splice(idx, 1, materialNow);
    //   this.$set(this.material_info, this.styleId, this.materialInfo);
    //   this.material_info = Object.assign({}, this.material_info);
    // }
  },
  watch: {
    // materialInfo: {
    //   handler: function(val, oldVal) {
    //     this.$set(this.material_info, this.styleId, this.materialInfo);
    //     this.material_info = Object.assign({}, this.material_info);
    //   },
    //   deep: true
    // }
  },
  computed: {
    style_name: function() {
      return this.styleInfo.name;
    },
    material_ids: function() {
      if (this.material_info) {
        const ids = flatten(values(this.material_info)).filter(item => {
          return item.id;
        }).map(item => {
          return item.id;
        });
        return values(ids);
      } else {
        return [];
      }
    },
    material_info: {
      get() {
        return this.curVal.material_info;
      },
      set(val) {
        this.curVal.material_info = val;
      }
    },
    styleIds: function() {
      return this.styleList.map(item => {
        return item.value;
      });
    }
  },
  mounted() {
    selStyle({
      style_id: this.styleId
    })
      .then(response => {
        this.styleEmpty = response.data.style_info;
        let materialInfo = [];
        if (
          this.material_info.hasOwnProperty(this.styleId) &&
          this.material_info[this.styleId].length > 0
        ) {
          this.material_info[this.styleId].forEach(item => {
            if (Object.keys(item).length > 0) {
              let styleNew = Object.assign({creative_id: item.creative_id}, renewObject(this.styleEmpty));
              // 回填 text 数据
              if (styleNew.text) {
                styleNew.text = styleNew.text.map((text, textIdx) => {
                  text = Object.assign({}, text);
                  if (
                    item.text &&
                    item.text[text.name] &&
                    item.text[text.name].value
                  ) {
                    let tmp = {
                      value: item.text[text.name].value
                    };
                    if (item.text[text.name].wildcardIds) {
                      tmp["wildcardIds"] = item.text[text.name].wildcardIds;
                    }
                    text = Object.assign({}, text, tmp);
                  }
                  return text;
                });
              }
              if (
                Object.prototype.toString.call(item.extra) !== "[object Object]"
              ) {
                this.$delete(item, "extra");
                item = Object.assign({}, renewObject(this.materialEmpty), item);
              }
              this.styleInfo.push(styleNew);
              this.materialInfo.push(item);
            }
          });
        } else {
          this.styleInfo.push(Object.assign({}, renewObject(this.styleEmpty)));
          this.materialInfo.push(
            Object.assign({}, renewObject(this.materialEmpty))
          );
        }
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>

