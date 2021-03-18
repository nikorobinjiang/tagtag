<template>
  <div class="input-block landing-page-box" v-if="isMounted">
    <el-form-item label="落地页选择">
      <el-button v-if="Object.keys(landingPage).length <= 0" type="text" @click="handleLandingPageSelect()">选择素材</el-button>
      <el-row v-if="Object.keys(landingPage).length > 0">
        <el-col :xs="12" :sm="6">
          <el-card>
            <div slot="header" class="clearfix">
              <span class="bottom-text" :title="landingPage.name">{{ landingPage.name }}</span>
              <el-button
                type="text"
                @click="landingPage = {}; template_info = {}; template_id = 0">
                <span class="el-icon-delete"></span>
              </el-button>
              <el-button
                type="text"
                v-download="{ url: landingPage.file_url }">
                <span class="el-icon-download"></span>
              </el-button>
            </div>
            <img
              :src="landingPage.preview"
              :title="landingPage.name"
              v-preview
              class="image">
            <div class="bottom">
              <span :title="landingPage.designer_name" class="bottom-text">{{ landingPage.designer_name }}</span>
                <div class="bottom clearfix">
              </div>
            </div>
          </el-card>
        </el-col>
        <el-col :xs="12" :sm="18" v-if="['show', 'edit'].indexOf(requestAction) !== -1">
          <el-input disabled class="landing-page-input" v-model="lpCompanyFull">
            <template slot="prepend">公司名称：</template>
          </el-input>
          <el-input disabled class="landing-page-input" v-if="false">
            <template slot="prepend">落地页域名：</template>
          </el-input>
          <el-input disabled class="landing-page-input" v-model="shellUrl">
            <template slot="prepend">落地页壳地址：</template>
            <el-button slot="append" v-clipboard="{ text: shellUrl }">复制</el-button>
          </el-input>
          <el-input disabled class="landing-page-input" v-model="adUrl">
            <template slot="prepend">落地页地址：</template>
            <el-button slot="append" v-clipboard="{ text: adUrl }">复制</el-button>
          </el-input>
        </el-col>
      </el-row>
    </el-form-item>

    <LandingPageSelect
      v-if="isDisplaySelector"
      :isDisplay="isDisplaySelector"
      :gameId="gameId"
      @transferLandingPage="transferLandingPage">
    </LandingPageSelect>
  </div>
</template>

<script>
import { xStore } from "@/js/mixins";
import { Injector } from "../../mixins";
import LandingPageSelect from "./LandingPageSelect";

export default {
  mixins: [xStore, Injector],
  name: "landing-page-box",
  components: {
    LandingPageSelect
  },
  props: {
    value: {},
    gameId: {},
    lpCompanyFull: {},
    shellUrl: {},
    adUrl: {}
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null
          ? {
              template_id: null,
              template_info: {}
            }
          : this.value,
      isMounted: false,
      isDisplaySelector: false,
      landingPage: {}
    };
  },
  methods: {
    transferLandingPage: function(action, value) {
      if (action === "confirm") {
        this.landingPage = value;
        this.template_id = value.id;
        this.template_info = value;
      }
      this.isDisplaySelector = false;
    },
    handleLandingPageSelect: function(idx, type) {
      this.isDisplaySelector = true;
    }
  },
  watch: {
    curVal: {
      handler: function(val) {
        this.$emit("input", val);
      },
      deep: true
    }
  },
  computed: {
    template_id: {
      get() {
        return this.curVal.template_id;
      },
      set(val) {
        this.curVal.template_id = val;
      }
    },
    template_info: {
      get() {
        return this.curVal.template_info;
      },
      set(val) {
        this.curVal.template_info = val;
      }
    }
  },
  mounted() {
    if (this.template_info) {
      this.landingPage = this.template_info;
    }
    this.isMounted = true;
  }
};
</script>
