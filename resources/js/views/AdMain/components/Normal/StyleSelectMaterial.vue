<template>
  <div class="input-block style-select-material">
    <el-dialog
      v-if="isMounted"
      title="选择素材"
      :visible="isDisplay"
      @close="handleClose('cancel')"
      width="50%"
      append-to-body>
      <el-tabs v-model="activeTabName" @tab-click="handleTabClick">
        <el-tab-pane
          v-for="(field, key) in styleInfo[selectType]"
          :key="key"
          :label="field.name">
          <el-row>
            <el-col :xs="24" :sm="7">
              <div class="el-input el-input--mini el-input-group el-input-group--prepend">
              <div class="el-input-group__prepend">设计师</div>
              <el-select
                @change="handleSelectChange()"
                v-model="search_designer[key]"
                placeholder="请选择设计师">
                <el-option
                  v-for="designer in xStore.data.designerList"
                  :key="designer.value"
                  :title="designer.label"
                  :label="designer.label"
                  :value="designer.value">
                </el-option>
              </el-select>
              </div>
            </el-col>
            <el-col :xs="24" :sm="7">
              <el-input size="mini" placeholder="请输入内容" v-model="search_name[key]">
                <template slot="prepend">素材名称</template>
              </el-input>
            </el-col>
            <el-col :xs="24" :sm="7">
              <el-input size="mini" placeholder="请输入内容" v-model="search_tag[key]">
                <template slot="prepend">素材标签</template>
              </el-input>
            </el-col>
            <el-col :xs="24" :sm="3">
              <el-button size="mini" type="primary" @click="searchMaterial(key, field.name)">检索</el-button>
            </el-col>
          </el-row>
          <el-row>
            <el-col :xs="24" :sm="4">
              <label>已选素材</label>
            </el-col>
            <el-col :xs="24" :sm="20">
              <el-row>
                <el-col :xs="12" :sm="6">
                  <MaterialCard
                    v-if="selectedItems[field.name]"
                    type="img"
                    :material="selectedItems[field.name]"
                    :showHeader="false">
                    <template slot="header"></template>
                    <template slot="bottom">
                      <span class="bottom-text-lt" :title="selectedItems[field.name].name">{{ selectedItems[field.name].name }}</span>
                      <span class="bottom-text-rt" :title="selectedItems[field.name].designer_name">{{ selectedItems[field.name].designer_name }}</span>
                    </template>
                  </MaterialCard>
                </el-col>
              </el-row>
            </el-col>
          </el-row>
          <el-row>
            <el-col :xs="24" :sm="4">
              <label>{{ getSelectName() }}素材库{{ field.name }}</label>
            </el-col>
            <el-col :xs="24" :sm="20">
              <el-row>
                <el-checkbox
                  @change="searchMaterial(key, field.name)"
                  v-model="with_similar[key]">展示同比例的素材
                </el-checkbox>
              </el-row>
              <el-row class="style-select-material-list">
                <el-radio-group v-model="selected[field.name]" class="style-select-material-list-group">
                  <el-col :xs="12" :sm="6"
                    v-for="(material, index) in selectList[field.name]"
                    :key="index">
                    <MaterialCard
                      type="img"
                      :material="material">
                      <template slot="header">
                        <el-radio
                          @change="handleSelectedChange(field.name, material)"
                          :label="material.id">{{ "" }}
                        </el-radio>
                      </template>
                      <template slot="bottom">
                        <span class="bottom-text-lt" :title="material.name">{{ material.name }}</span>
                        <span class="bottom-text-rt" :title="material.designer_name">{{ material.designer_name }}</span>
                      </template>
                    </MaterialCard>
                  </el-col>
                </el-radio-group>
              </el-row>
            </el-col>
          </el-row>
        </el-tab-pane>
      </el-tabs>
      <span slot="footer">
        <el-button size="mini" @click="handleClose('cancel')">取 消</el-button>
        <el-button size="mini" type="primary" @click="handleClose('confirm')">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import { searchMaterial } from "@/js/api/common";
import { xStore } from "@/js/mixins";

import MaterialCard from "./MaterialCard";

export default {
  mixins: [xStore],
  name: "style-select-material",
  components: {
    MaterialCard
  },
  props: {
    isDisplay: {
      default: false
    },
    gameId: {},
    styleId: {},
    selectType: {
      type: String,
      required: true
    }, // img | video
    styleInfo: {}, // 样式详情
    selMark: {}, // 事件标记
    styleItems: {} // 样式素材
  },
  data() {
    return {
      isMounted: false,
      designer: "",
      activeTabName: 0,
      search_designer: {},
      search_name: {},
      search_tag: {},
      with_similar: {},
      selected: {},
      selectedItems: {},
      selectList: {}
    };
  },
  methods: {
    transferMaterial: function(action) {
      this.$emit("transferMaterial", action, this.selectedItems, this.selMark);
    },
    searchMaterial: function(key, search_field) {
      searchMaterial({
        type: this.selectType,
        game_id: this.gameId,
        style_id: this.styleId,
        search_field: search_field,
        search_designer: this.search_designer[key],
        search_name: this.search_name[key],
        search_tag: this.search_tag[key],
        with_similar: this.with_similar[key] ? 1 : 0
      })
        .then(response => {
          Vue.set(
            this.selectList,
            search_field,
            response.data[this.selectType + "s"]
          );
          this.$message({
            message: response.data.message,
            center: true
          });
        })
        .catch(error => {
          console.log(error);
        });
    },
    delMaterial: function(field) {
      Vue.delete(this.selectedItems, field);
    },
    getSelectName: function() {
      if (this.selectType === "img") {
        return "图片";
      } else if (this.selectType === "video") {
        return "视频";
      }
    },
    handleSelectChange() {
      this.$forceUpdate();
    },
    handleSelectedChange(field, info) {
      Vue.set(this.selectedItems, field, info);
    },
    handleTabClick(tab, event) {},
    handleClose(action) {
      this.transferMaterial(action);
    }
  },
  computed: {},
  mounted() {
    if (this.styleItems) {
      this.selectedItems = JSON.parse(JSON.stringify(this.styleItems));
    }
    this.styleInfo[this.selectType].forEach((field, key) => {
      this.searchMaterial(key, field.name);
      this.search_designer[key] = "";
    });
    this.isMounted = true;
  }
};
</script>