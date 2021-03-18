<template>
  <div
    v-if="isMounted"
    class="input-block style-box-procedural">
    <el-form-item label="创意素材" v-if="styleIds.length > 0">
      <el-tabs v-model="activeTab">
        <el-tab-pane
          v-for="(style, styleId) in styleList" :key="styleId"
          :label="style.name" :name="styleId">

          <el-row v-if="style.style_info.img && style.style_info.img.length > 0 && proceduralContent[styleId]">
            <el-col :xs="4" :sm="2">
              <el-button type="text" @click="handleAddMaterialGroup(styleId, 'img')">添加</el-button>
            </el-col>
            <el-col :xs="10" :sm="5"
              v-for="(groupImg, groupIdx) in proceduralContent[styleId].img"
              :key="groupIdx">
              <el-button type="text" @click="handleDisplayMaterialSelect(styleId, 'img', groupIdx)">选择素材</el-button>
              <el-button type="text" icon="el-icon-delete" @click="handleDelMaterialGroup(styleId, 'img', groupIdx)"></el-button>
              <material-card
                v-for="(img, imgKey) in groupImg"
                :key="imgKey"
                type="img"
                :material="img"
                :downloadInfo="{url: img.preview}"
                :actionDel="_ => {delMaterial(styleId, 'img', groupIdx, imgKey)}">
              </material-card>
            </el-col>
          </el-row>

          <el-row v-if="style.style_info.video && style.style_info.video.length > 0 && proceduralContent[styleId]">
            <el-col :xs="4" :sm="2">
              <el-button type="text" @click="handleAddMaterialGroup(styleId, 'video')">添加</el-button>
            </el-col>
            <el-col :xs="10" :sm="5"
              v-for="(groupVideo, groupIdx) in proceduralContent[styleId].video"
              :key="groupIdx">
              <el-button type="text" @click="handleDisplayMaterialSelect(styleId, 'video', groupIdx)">选择素材</el-button>
              <el-button type="text" icon="el-icon-delete" @click="handleDelMaterialGroup(styleId, 'video', groupIdx)"></el-button>
              <template
                v-for="(video, videoKey) in groupVideo">
                <material-card
                  v-for="(member, memberKey) in video"
                  :key="memberKey"
                  :type="memberKey"
                  :material="member"
                  :downloadInfo="{url: member.file_url}"
                  :actionDel="_ => {delVideo(styleId, 'video', groupIdx, videoKey, memberKey)}">
                </material-card>
              </template>
            </el-col>
          </el-row>

        </el-tab-pane>
      </el-tabs>
    </el-form-item>

    <el-form-item label="创意标题" v-if="styleIds.length > 0">
      <el-row>
        <el-col :xs="4" :sm="2">
          <el-button type="text" @click="handleAddText">添加</el-button>
        </el-col>
        <el-col :xs="20" :sm="22">
          <el-input
            v-for="(text, idx) in proceduralText" :key="idx"
            v-model="proceduralText[idx]" placeholder="请输入内容">
            <template slot="append">
              <label @click="handleDelText(idx)">删除({{ proceduralText[idx].length }}/25)</label>
            </template>
          </el-input>
        </el-col>
      </el-row>
    </el-form-item>

    <keep-alive
      v-if="isDisplaySelector">
      <component
        v-bind:is="selectType=='img'?'style-select-material':'style-select-video'"
        :isDisplay="isDisplaySelector"
        :gameId="gameId"
        :styleId="selectStyleId"
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
import { selStyle } from "@/js/api/common";
import { xStore } from "@/js/mixins";
import { buildMaterialInfo } from "@/js/utils";

import StyleSelectMaterial from "./StyleSelectMaterial";
import StyleSelectVideo from "./StyleSelectVideo";
import MaterialCard from "./MaterialCard";

export default {
  mixins: [xStore],
  name: "style-box-procedural",
  components: {
    StyleSelectMaterial,
    StyleSelectVideo,
    MaterialCard
  },
  props: {
    value: {
      type: [Object],
      required: true
    },
    promoteId: {
      type: [Number],
      required: true
    },
    styleIds: {
      type: [Array],
      default: []
    },
    gameId: {
      type: [Number],
      required: true
    }
  },
  data() {
    return {
      isMounted: false,
      curVal:
        this.value === undefined || this.value === null
          ? {
              material_info: {},
              procedural_content: {}
            }
          : this.value,
      activeTab: "",
      styleList: {},
      isDisplaySelector: false,
      selectStyleId: null,
      selectType: "", // img | video
      selectStyle: "",
      selectItems: {}
    };
  },
  methods: {
    delMaterial: function(styleId, type, groupIdx, field) {
      let materialNow = this.proceduralContent[styleId];
      Vue.delete(materialNow[type][groupIdx], field);
      if (Object.keys(materialNow[type][groupIdx]).length <= 0) {
        Vue.delete(materialNow[type], groupIdx);
      }
    },
    delVideo: function(styleId, type, groupIdx, field, member) {
      let materialNow = this.proceduralContent[styleId];
      Vue.delete(materialNow[type][groupIdx][field], member);
      if (Object.keys(materialNow[type][groupIdx][field]).length <= 0) {
        Vue.delete(materialNow[type][groupIdx], field);
      }
      if (Object.keys(materialNow[type][groupIdx]).length <= 0) {
        Vue.delete(materialNow[type], groupIdx);
      }
      if (Object.keys(materialNow[type]).length <= 0) {
        Vue.delete(materialNow, type);
      }
      this.$forceUpdate();
    },
    loadStyleList: function() {
      // this.isMounted = false;
      selStyle({
        style_id: this.styleIds
      })
        .then(response => {
          this.styleList = {};
          if (response.data.styleList && response.data.styleList.length > 0) {
            response.data.styleList.forEach(style => {
              this.$set(
                this.styleList,
                style.style_id,
                Object.assign({}, style)
              );
              if (this.proceduralContent[style.style_id] === undefined) {
                this.$set(this.proceduralContent, style.style_id, {});
              }
            });
          }
          this.buildMaterialInfo();
          // this.isMounted = true;
        })
        .catch(error => {
          console.log(error);
        });
    },
    handleAddMaterialGroup: function(styleId, type) {
      if (this.proceduralContent[styleId][type] === undefined) {
        this.$set(this.proceduralContent[styleId], type, []);
      }
      this.proceduralContent[styleId][type].push({});
    },
    handleDelMaterialGroup: function(styleId, type, groupIdx) {
      this.proceduralContent[styleId][type].splice(groupIdx, 1);
    },
    handleAddText: function() {
      this.proceduralText.push("");
    },
    handleDelText: function(idx) {
      this.$delete(this.proceduralText, idx);
    },
    handleDisplayMaterialSelect: function(styleId, type, groupIdx) {
      this.selectStyleId = styleId;
      this.selectType = type;
      this.selectStyle = this.styleList[styleId].style_info;
      this.isDisplaySelector = true;
      this.selMark = {
        styleId: styleId,
        type: type,
        idx: groupIdx
      };

      this.selectItems = this.proceduralContent[styleId][type][groupIdx];
    },
    transferMaterial: function(action, value, selMark) {
      if (action === "confirm") {
        let materialNow = this.proceduralContent[selMark.styleId];
        if (materialNow[selMark.type] === undefined) {
          materialNow[selMark.type] = [];
        }
        materialNow[selMark.type][selMark.idx] = value;
        this.$set(
          this.proceduralContent,
          selMark.styleId,
          Object.assign({}, materialNow)
        );
      }
      this.isDisplaySelector = false;
    },
    buildMaterialInfo: function() {
      this.curVal.material_info = buildMaterialInfo(
        this.styleList,
        this.proceduralContent
      );
    }
  },
  watch: {
    curVal: {
      handler: function(val) {
        this.$emit("input", val);
      },
      deep: true
    },
    styleIds: {
      handler: function(val, oldVal) {
        if (_.xor(val, oldVal).length <= 0) {
          return;
        }
        // 同步广告位样式
        this.loadStyleList();
        // 重置 tab 焦点
        let activeTab = parseInt(this.activeTab);
        let diffTabs = _.difference(val, oldVal);
        if (isNaN(activeTab) && val.indexOf(activeTab) === -1) {
          // 失去 tab
          this.activeTab = val.length > 0 ? val[0] + "" : null;
        } else if (diffTabs.length === 1) {
          // 新增 tab
          this.activeTab = diffTabs[0] + "";
        }
      },
      immediate: true
    },
    proceduralContent: {
      handler: function(val) {
        this.buildMaterialInfo();
      },
      deep: true
    }
  },
  computed: {
    proceduralContent: {
      get: function() {
        return this.curVal.procedural_content;
      },
      set: function(val) {
        this.curVal.procedural_content = val;
      }
    },
    proceduralText: {
      get: function() {
        if (this.curVal.procedural_content["ext"] === undefined) {
          this.$set(this.curVal.procedural_content, "ext", {});
        }
        if (this.curVal.procedural_content["ext"]["text"] === undefined) {
          this.$set(this.curVal.procedural_content.ext, "text", []);
        }
        return this.curVal.procedural_content.ext.text;
      },
      set: function(val) {
        this.$set(this.curVal.procedural_content.ext, "text", val);
        this.$forceUpdate();
      }
    }
  },
  mounted() {
    this.isMounted = true;
  }
};
</script>

<style lang="scss">
@import "~@/sass/mixins/mixins";

@include b(radio-group) {
  width: 100%;
}
@include b(icon-delete) {
  color: red;
}
</style>


