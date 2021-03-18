<template>
  <div
    class="input-block style-select-material">
    <el-dialog
      v-if="isMounted"
      title="选择素材"
      :visible="isDisplay"
      @close="handleClose('cancel')"
      width="50%"
      append-to-body>
      <el-tabs v-model="activeTabLevel_1" @tab-click="handleTabClick">
        <el-tab-pane
          v-for="(field, key) in styleInfo[selectType]"
          :key="key"
          :label="field.name">
          <el-tabs>
            <el-tab-pane label="视频封面" v-if="field.has_cover">
              <el-row>
                <el-col :xs="24" :sm="6">
                  <div class="el-input el-input--mini el-input-group el-input-group--prepend">
                  <div class="el-input-group__prepend">设计师</div>
                  <el-select
                    @change="handleSelectChange()"
                    v-model="search_designer[key][selectType + '_cover']"
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
                <el-col :xs="24" :sm="6">
                  <el-input size="mini" placeholder="请输入内容" v-model="search_name[key][selectType + '_cover']">
                    <template slot="prepend">素材名称</template>
                  </el-input>
                </el-col>
                <el-col :xs="24" :sm="6">
                  <el-input size="mini" placeholder="请输入内容" v-model="search_tag[key][selectType + '_cover']">
                    <template slot="prepend">素材标签</template>
                  </el-input>
                </el-col>
                <el-col :xs="24" :sm="6">
                  <el-button size="mini" type="primary" @click="searchMaterial(key, selectType + '_cover', field.name)">检索</el-button>
                </el-col>
              </el-row>
              <el-row>
                <el-col :xs="24" :sm="4">
                  <label>已选素材</label>
                </el-col>
                <el-col :xs="24" :sm="20">
                  <el-row>
                    <el-col :xs="12" :sm="6">
                      <material-card
                        v-if="selectedItems[field.name] && selectedItems[field.name][selectType + '_cover']"
                        type="img"
                        :material="selectedItems[field.name][selectType + '_cover']"
                        :showHeader="false">
                        <template slot="header"></template>
                        <template slot="bottom">
                          <span class="bottom-text-lt" :title="selectedItems[field.name][selectType + '_cover'].name">{{ selectedItems[field.name][selectType + '_cover'].name }}</span>
                          <span class="bottom-text-rt" :title="selectedItems[field.name][selectType + '_cover'].designer_name">{{ selectedItems[field.name][selectType + '_cover'].designer_name }}</span>
                        </template>
                      </material-card>
                    </el-col>
                  </el-row>
                </el-col>
              </el-row>
              <el-row>
                <el-col :xs="24" :sm="4">
                  <label>素材库</label>
                </el-col>
                <el-col :xs="24" :sm="20">
                  <el-row>
                    <el-checkbox
                      @change="handleWithSimilarChange(key, 'video_cover', field)"
                      v-model="with_similar[key][selectType + '_cover']">展示同比例的素材
                    </el-checkbox>
                  </el-row>
                  <el-row v-if="selectList[field.name]" class="style-select-material-list">
                    <el-radio-group v-model="selected[field.name][selectType + '_cover']">
                      <el-col :xs="12" :sm="6"
                        v-for="(material, index) in selectList[field.name][selectType + '_cover']"
                        :key="index">
                        <material-card
                          type="img"
                          :material="material"
                          :downloadInfo="{url: material.preview}"
                          :actionDel="_ => {delVideo(styleId, 'img', groupIdx, imgKey)}">
                          <template slot="header">
                            <el-radio
                              @change="handleSelectedChange(field.name, selectType + '_cover', material)"
                              :label="material.id">{{ "" }}
                            </el-radio>
                          </template>
                          <template slot="bottom">
                            <span class="bottom-text-lt" :title="material.name">{{ material.name }}</span>
                            <span class="bottom-text-rt" :title="material.designer_name">{{ material.designer_name }}</span>
                          </template>
                        </material-card>
                      </el-col>
                    </el-radio-group>
                  </el-row>
                </el-col>
              </el-row>
            </el-tab-pane>
            <el-tab-pane label="视频">
              <el-row>
                <el-col :xs="24" :sm="6">
                  <div class="el-input el-input--mini el-input-group el-input-group--prepend">
                  <div class="el-input-group__prepend">设计师</div>
                  <el-select
                    @change="handleSelectChange()"
                    v-model="search_designer[key][selectType]"
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
                <el-col :xs="24" :sm="6">
                  <el-input size="mini" placeholder="请输入内容" v-model="search_name[key][selectType]">
                    <template slot="prepend">素材名称</template>
                  </el-input>
                </el-col>
                <el-col :xs="24" :sm="6">
                  <el-input size="mini" placeholder="请输入内容" v-model="search_tag[key][selectType]">
                    <template slot="prepend">素材标签</template>
                  </el-input>
                </el-col>
                <el-col :xs="24" :sm="6">
                  <el-button size="mini" type="primary" @click="searchMaterial(key, selectType, field.name)">检索</el-button>
                </el-col>
              </el-row>
              <el-row>
                <el-col :xs="24" :sm="4">
                  <label>已选素材</label>
                </el-col>
                <el-col :xs="24" :sm="20">
                  <el-row>
                    <el-col :xs="12" :sm="6">
                      <material-card
                        v-if="selectedItems[field.name] && selectedItems[field.name][selectType]"
                        type="video"
                        :material="selectedItems[field.name][selectType]"
                        :downloadInfo="{url: selectedItems[field.name][selectType].file_url}"
                        :showHeader="false">
                        <template slot="header"></template>
                        <template slot="bottom">
                          <span class="bottom-text-lt" :title="selectedItems[field.name][selectType].name">{{ selectedItems[field.name][selectType].name }}</span>
                          <span class="bottom-text-rt" :title="selectedItems[field.name][selectType].designer_name">{{ selectedItems[field.name][selectType].designer_name }}</span>
                        </template>
                      </material-card>
                    </el-col>
                  </el-row>
                </el-col>
              </el-row>
              <el-row>
                <el-col :xs="24" :sm="4">
                  <label>素材库</label>
                </el-col>
                <el-col :xs="24" :sm="20">
                  <el-row>
                    <el-checkbox
                      @change="handleWithSimilarChange(key, 'video', field)"
                      v-model="with_similar[key][selectType]">展示同比例的素材
                    </el-checkbox>
                  </el-row>
                  <el-row v-if="selectList[field.name]" class="style-select-material-list">
                    <el-radio-group v-model="selected[field.name][selectType]">
                      <el-col :xs="12" :sm="6"
                        v-for="(material, index) in selectList[field.name][selectType]"
                        :key="index">
                        <material-card
                          type="video"
                          :material="material"
                          :downloadInfo="{url: material.file_url}"
                          :actionDel="_ => {delVideo(styleId, 'img', groupIdx, imgKey)}">
                          <template slot="header">
                            <el-radio
                              @change="handleSelectedChange(field.name, selectType, material)"
                              :label="material.id">{{ "" }}
                            </el-radio>
                          </template>
                          <template slot="bottom">
                            <span class="bottom-text-lt" :title="material.name">{{ material.name }}</span>
                            <span class="bottom-text-rt" :title="material.designer_name">{{ material.designer_name }}</span>
                          </template>
                        </material-card>
                      </el-col>
                    </el-radio-group>
                  </el-row>
                </el-col>
              </el-row>
            </el-tab-pane>
          </el-tabs>
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
  name: "style-select-video",
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
      activeTabLevel_1: 0,
      search_designer: [],
      search_name: [],
      search_tag: [],
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
    searchMaterial: function(key, search_type, search_field) {
      searchMaterial({
        type: search_type,
        game_id: this.gameId,
        style_id: this.styleId,
        search_field: search_field,
        search_designer: this.search_designer[key][search_type],
        search_name: this.search_name[key][search_type],
        search_tag: this.search_tag[key][search_type],
        with_similar: this.with_similar[key][search_type] ? 1 : 0
      })
        .then(response => {
          if (!this.selectList[search_field]) {
            this.selectList[search_field] = {};
          }
          Vue.set(
            this.selectList[search_field],
            search_type,
            response.data[search_type + "s"]
          );
          this.$forceUpdate();
          this.$message({
            message: response.data.message,
            center: true
          });
        })
        .catch(error => {
          console.log(error);
        });
    },
    delVideo: function(field, item) {
      Vue.delete(this.selectedItems[field], item);
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
    handleWithSimilarChange(key, search_type, field) {
      this.searchMaterial(key, search_type, field.name);
      this.$forceUpdate();
    },
    handleSelectedChange(field, key, material) {
      if (this.selectedItems[field] === undefined) {
        this.selectedItems[field] = {};
      }
      this.selectedItems[field][key] = material;
      this.$forceUpdate();
    },
    handleTabClick(tab, event) {},
    handleClose(action) {
      this.transferMaterial(action);
    }
  },
  watch: {
    with_similar: {
      handler: function(val, oldVal) {
        this.$forceUpdate();
      },
      deep: true
    }
  },
  computed: {},
  mounted() {
    this.styleInfo[this.selectType].forEach(item => {
      this.selected[item.name] = {};
      this.selectedItems[item.name] = {};
    });
    if (this.styleItems) {
      this.selectedItems = JSON.parse(JSON.stringify(this.styleItems));
    }
    this.styleInfo[this.selectType].forEach((field, key) => {
      this.search_designer[key] = {
        video_cover: "",
        video: ""
      };
      this.search_name[key] = {
        video_cover: "",
        video: ""
      };
      this.search_tag[key] = {
        video_cover: "",
        video: ""
      };
      this.with_similar[key] = {
        video_cover: false,
        video: false
      };
      this.searchMaterial(key, "video_cover", field.name);
      this.searchMaterial(key, "video", field.name);
    });
    this.isMounted = true;
  }
};
</script>