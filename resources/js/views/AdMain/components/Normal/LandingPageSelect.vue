<template>
  <div class="input-block style-select-material">
    <el-dialog
      v-if="isMounted"
      title="选择素材"
      :visible="isDisplay"
      @close="handleClose('cancel')"
      width="50%"
      append-to-body>
      <el-row>
        <el-col :xs="24" :sm="10">
          <div class="el-input el-input--mini el-input-group el-input-group--prepend">
            <div class="el-input-group__prepend">设计师</div>
            <el-select v-model="search_designer" placeholder="请选择设计师">
              <el-option
                v-for="designer in xStore.data.lpDesignerList"
                :key="designer.value"
                :title="designer.label"
                :label="designer.label"
                :value="designer.value">
              </el-option>
            </el-select>
          </div>
        </el-col>
        <el-col :xs="24" :sm="10">
          <el-input placeholder="请输入内容" size="mini" v-model="search_name">
            <template slot="prepend">素材名称</template>
          </el-input>
        </el-col>
        <el-col :xs="24" :sm="4">
          <el-button type="primary" size="mini" @click="searchLandingPage()">检索</el-button>
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
                v-if="selectItem"
                type="img"
                :material="selectItem"
                :showHeader="false">
                <template slot="header"></template>
                <template slot="bottom">
                  <span class="bottom-text-lt" :title="selectItem.name">{{ selectItem.name }}</span>
                  <span class="bottom-text-rt" :title="selectItem.designer_name">{{ selectItem.designer_name }}</span>
                </template>
              </MaterialCard>
              <el-card
                v-if="false && selectItem">
                <div slot="header" class="clearfix">
                  <span
                    v-if="false"
                    :title="selectItem.name">{{ selectItem.name }}
                  </span>
                </div>
                <img :src="selectItem.preview" class="image">
                <div class="bottom">
                  <span class="bottom-text-lt" :title="selectItem.name">{{ selectItem.name }}</span>
                  <span  class="bottom-text-rt" :title="selectItem.designer_name">{{ selectItem.designer_name }}</span>
                </div>
              </el-card>
            </el-col>
          </el-row>
        </el-col>
      </el-row>
      <el-row>
        <el-col :xs="24" :sm="4">
          <label>落地页素材库</label>
        </el-col>
        <el-col :xs="24" :sm="20">
          <el-row class="style-select-material-list">
            <el-col :xs="12" :sm="6"
              v-for="(material, index) in selectList"
              :key="index">
              <MaterialCard
                type="img"
                :material="material">
                <template slot="header">
                  <el-radio
                    v-model="selectItem"
                    :label="material">{{ "" }}
                  </el-radio>
                </template>
                <template slot="bottom">
                  <span class="bottom-text-lt" :title="material.name">{{ material.name }}</span>
                  <span class="bottom-text-rt" :title="material.designer_name">{{ material.designer_name }}</span>
                </template>
              </MaterialCard>
            </el-col>
          </el-row>
        </el-col>
      </el-row>
      <span slot="footer" class="dialog-footer">
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
  name: "landing-page-select",
  components: {
    MaterialCard
  },
  props: {
    isDisplay: {
      default: false
    },
    gameId: {}
  },
  data() {
    return {
      isMounted: false,
      designer: "",
      activeTabName: 0,
      search_designer: "",
      search_name: "",
      selectItem: null,
      selectList: []
    };
  },
  methods: {
    transferLandingPage: function(action) {
      this.$emit(
        "transferLandingPage",
        action, // cancel | confirm
        this.selectItem
      );
    },
    searchLandingPage: function() {
      searchMaterial({
        type: "lp",
        game_id: this.gameId,
        search_designer: this.search_designer,
        search_name: this.search_name
      })
        .then(response => {
          this.selectList = response.data["lps"];
          this.$forceUpdate();
        })
        .catch(error => {
          console.log(error);
        });
    },
    handleTabClick(tab, event) {},
    handleClose(action) {
      this.transferLandingPage(action);
    }
  },
  mounted() {
    this.searchLandingPage();
    this.isMounted = true;
  }
};
</script>
