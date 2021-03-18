<template>
  <el-form
    v-loading="loading"
    ref="form"
    :model="form"
    :rules="rules"
    label-width="100px"
    size="mini"
    :class="['ad-main-form', 'ad-main-form-normal']"
  >
    <span>目前支持渠道 今日头条</span>
    <h3>投放内容</h3>
    <el-row>
      <el-col :span="12">
        <el-form-item label="投放项目" prop="game_group_id">
          <el-select v-model="form.game_group_id" placeholder="请选择投放项目" filterable>
            <el-option
              v-for="(item, key) in gameGroupList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="投放游戏" prop="game_id">
          <el-select v-model="form.game_id" placeholder="请选择投放游戏" filterable>
            <el-option
              v-for="(item, key) in gameList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
    </el-row>

    <h3>下载链接相关</h3>
    <el-row>
      <el-col :span="12">
        <el-form-item label="是否有水印" prop="has_watermark">
          <el-select v-model="form.has_watermark">
            <el-option
              v-for="(item, key) in watermarkList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span="12" v-if="appType !== 'iOS'">
        <el-form-item label="域名" prop="ad_site_id">
          <el-select v-model="form.ad_site_id" filterable>
            <el-option
              v-for="(item, key) in adSiteList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span="12">
        <el-form-item label="代理商" prop="agent_id">
          <el-select
            :disabled="['edit', 'show'].indexOf(requestAction) !== -1"
            v-model="form.agent_id"
            placeholder="请选择代理商"
            filterable
          >
            <el-option
              v-for="item in agentList"
              :key="item.value"
              :title="item.label"
              :label="item.label"
              :value="item.value"
            >
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
    </el-row>

    <el-form-item>
      <el-button type="primary" @click="stepSubmit(false)">保存</el-button>
      <el-button v-if="requestAction === 'create'" @click="cancelForm"
        >取消</el-button
      >
    </el-form-item>
  </el-form>
</template>

<script>
import { mapGetters, mapState, mapActions } from "vuex";
import { mapStateGetSet } from "@/js/utils";

import { getBasicData, selMedia, selPosition } from "@/js/api/common";
import { storeItem, updateItem } from "@/js/api/AdMain/DistAdsDesk";
import { xStore } from "@/js/mixins";
import { Provider } from "./mixins";

import InputIsMultiMaterials from "./components/Normal/InputIsMultiMaterials";
import InputApkDomain from "./components/Normal/InputApkDomain";
import InputStyles from "./components/Normal/InputStyles";
import StyleBox from "./components/Normal/StyleBox";
import LandingPageBox from "./components/Normal/LandingPageBox";

export default {
  mixins: [xStore, Provider],
  name: "gmp-ad-main-form-create-ads-desk",
  components: {
    InputIsMultiMaterials,
    InputApkDomain,
    InputStyles,
    StyleBox,
    LandingPageBox,
  },
  props: {
    visible: {
      type: Boolean,
      default: false,
    },
    requestAction: {
      type: [String],
      required: true,
    },
    itemId: {
      default: null,
    },
  },
  data: function () {
    return {
      loading: true,
      step: 0,
      promoteList: [],
      positionList: [],
      settlementList: [],
      agentList: [],
      batchShow: false,
      batchCount: 0,
      continueCreate: false,
    };
  },
  methods: {
    ...mapActions("adNormal", [
      "storageSaveForm",
      "storageLoadForm",
      "storageRemoveForm",
    ]),
    cancelForm() {
      this.$emit("update:visible", false);
    },
    resetForm() {
      this.storageRemoveForm();
      this.$store.dispatch(this.state.getActionName("clear"));
      this.$nextTick(function () {
        this.$refs.form.clearValidate();
      });
    },
    stepSubmit: function (continueCreate) {
      this.loading = true;
      this.continueCreate = continueCreate;
      (this.requestAction === "edit"
        ? updateItem(this.form.ad_id, this.form)
        : storeItem(this.form)
      )
        .then((response) => {
          this.$message({
            message: response.data.message,
            center: true,
          });
          if (response.data.result === "success") {
            this.storageSaveForm();
            if (this.continueCreate === 1) {
              this.$refs.adMainFormNormal.clearMaterialInfo();
            } else {
              this.cancelForm();
            }
            this.$emit(
              "reloadList",
              this.form.distribute === "Normal" ? null : "tabSync"
            );
          }
          this.loading = false;
        })
        .catch((error) => {
          console.log(error);
          this.loading = false;
        });
    },
    handleBatchAction: function (confirm) {
      this.continueCreate = 0;
      if (confirm && this.batchCount > 0) {
        this.loading = true;
        let promise = null;
        let successCount = 0;
        let failureCount = 0;
        let failureMsg = "";

        for (let index = 0; index < this.batchCount; index++) {
          if (promise == null) {
            promise = storeItem(this.form);
          }
          promise = promise
            .then((response) => {
              // 对上一个返回结果判断
              if (response.data["result"] === "success") {
                successCount++;
              } else {
                failureCount++;
                failureMsg = response.data["message"];
                return false;
              }
              // 产生新的请求
              return index + 1 < this.batchCount ? storeItem(this.form) : true;
            })
            .catch((error) => {
              this.$message.error(failureMsg);
              this.loading = false;
            });
        }

        promise
          .then((_) => {
            if (failureMsg === "") {
              this.$message({
                message: "你成功创建了" + successCount + "条广告",
                type: "success",
                onClose: (_) => {
                  this.$emit("reloadList", null);
                },
              });
            } else {
              this.loading = false;
              this.$message.warning(failureMsg);
            }
          })
          .catch((error) => {
            this.loading = false;
          });
      }
      this.batchShow = false;
    },
    // 所属媒体联动
    handleMediaIdChange: function (val, oldVal) {
      if (!val) {
        return;
      }
      this.loading = true;
      this.promoteList = [];
      this.positionList = [];
      selMedia({
        media_id: val,
      })
        .then((response) => {
          let settlementList = [];
          for (let key in response.data.settlementList) {
            if (!this.form.settlement) {
              this.form.settlement = response.data.settlementList[key];
            }
            settlementList.push({
              label: response.data.settlementList[key],
              value: response.data.settlementList[key],
            });
          }
          this.settlementList = settlementList;
          this.promoteList = response.data.promoteList;
          this.positionList = response.data.positionList;
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          console.log(error);
        });
      if (oldVal !== null) {
        this.form.promote_id = null;
        this.form.position_id = null;
        this.form.settlement = "";
      }
    },
    handlePositionIdChange: function (val, oldVal) {
      if (val && (val !== oldVal || this.styleList.length <= 0)) {
        this.loading = true;
        this.styleList = [];
        selPosition({
          position_id: val,
        })
          .then((response) => {
            this.styleList = response.data.styleList;
            if (oldVal) {
              this.form.style_ids = [];
            }
            this.loading = false;
          })
          .catch((error) => {
            console.log(error);
            this.loading = false;
          });
      }
    },
    reloadAgentList: function () {
      selMedia({
        media_id: this.form.media_id,
        game_id: this.form.game_id,
      })
        .then((response) => {
          this.agentList = response.data.agentList;
          if (this.agentList && this.agentList.length > 0) {
            this.form.agent_id = this.agentList[0].value;
          } else {
            this.form.agent_id = null;
          }
          this.loading = false;
        })
        .catch((error) => {
          this.loading = false;
          console.log(error);
        });
    },
  },
  watch: {
    "form.media_id": {
      handler: function (val, oldVal) {
        this.handleMediaIdChange(val, oldVal);
        this.reloadAgentList();
      },
      immediate: true,
    },
    "form.game_id": {
      handler: function (val, oldVal) {
        this.reloadAgentList();
      },
      immediate: true,
    },
    "form.position_id": {
      handler: function (val, oldVal) {
        this.handlePositionIdChange(val, oldVal);
      },
      immediate: true,
    },
  },
  computed: {
    adSiteList() {
      return this.xStore.data.adSiteList;
    },
    watermarkList() {
      return this.xStore.data.watermarkList;
    },
    gameGroupList() {
      return this.xStore.data.gameGroupList;
    },
    gameList() {
      var items = this.xStore.data.gameList.filter((item, idx) => {
        return item.group_id === this.form.game_group_id;
      });
      if (items.length > 0) {
        this.form.game_id = items[0].value;
      }

      return items;
    },
    appType: function () {
      let res = null;
      this.gameList.forEach((item) => {
        if (item.value === this.form.game_id) {
          res = item.app_type;
        }
      });
      if (!res) {
        res = this.form.app_type;
      }
      return res;
    },
    styleBox: {
      get: function () {
        return {
          material_info: this.form.material_info,
        };
      },
      set: function (val) {
        this.form.material_info = val.material_info;
      },
    },
    landingBox: {
      get: function () {
        return {
          template_id: this.form.template_id,
          template_info: this.form.template_info,
        };
      },
      set: function (val) {
        this.form.template_id = val.template_id;
        this.form.template_info = val.template_info;
      },
    },
    isMultiMaterials: {
      get() {
        return this.form.is_multi_materials;
      },
      set(val) {
        let needReset = false;
        if (this.form.style_ids.length > 1) {
          needReset = true;
        } else {
          for (const key in this.form.style_ids) {
            const id = this.form.style_ids[key];
            if (
              this.form.material_info.hasOwnProperty(id) &&
              this.form.material_info[id].length > 1
            ) {
              needReset = true;
            }
          }
        }
        if (needReset) {
          this.$confirm("该操作将会清空所有的素材样式", "", {
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            type: "warning",
          })
            .then((_) => {
              this.form.template_id = 0;
              this.form.template_info = {};
              this.form.is_multi_materials = val;
              this.form.material_info = {};
              this.form.style_ids = [];
            })
            .catch((_) => {});
        } else {
          this.form.is_multi_materials = val;
        }
      },
    },
    couldLandingPage: function () {
      // if (this.appType === 'iOS') {
      if (this.form.distribute === "Toutiao") {
        this.hasLandingPage = 0;
        return false;
      }
      // }
      return true;
    },
    hasLandingPage: {
      get() {
        return this.form.has_landing_page;
      },
      set(val) {
        if (val === 0 && this.form.template_id > 0) {
          this.$confirm("该操作将清空选择的素材", "", {
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            type: "warning",
          })
            .then((_) => {
              this.form.template_id = 0;
              this.form.template_info = {};
              this.form.has_landing_page = val;
            })
            .catch((_) => {});
        } else {
          this.form.has_landing_page = val;
        }
      },
    },
    state() {
      return this.$store.state.adNormal;
    },
    distribute() {
      return this.state.form.distribute;
    },
    rules() {
      let res = Object.assign(
        {},
        this.state.rules,
        this.state["rule" + this.distribute]
      );
      return res;
    },
    ...mapState("adNormal", {
      hasLandingPageList: (state) => state.data.hasLandingPageList,
    }),
    ...mapStateGetSet("adNormal", {
      styleList: {
        get: (state) => state.data.styleList,
        set: "data.styleList",
      },
      form: {
        get: (state) => state.form,
        set: "form",
      },
    }),
  },
  created() {
    this.xStoreLoadExtraConfig();
    // 重载数据
    this.$store.dispatch(this.state.getActionName("clear"));
    this.storageLoadForm();
    this.loading = false;
    this.form.media_id = 0;
    this.form.game_group_id = 0;

    this.reloadAgentList();
  },
};
</script>

<style>
body label,
body span,
body div {
  font-family: Microsoft YaHei;
}
</style>