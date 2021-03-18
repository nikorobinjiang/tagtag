<template>
  <el-form
    v-loading="loading"
    ref="form"
    :model="form"
    :rules="rules"
    :disabled="requestAction === 'show'"
    label-width="100px"
    size="mini"
    v-bind:class="['ad-main-form', 'ad-main-form-normal', 'ad-main-form-step-' + step]">

    <h3 v-if="requestAction !== 'create'">{{ formTitle }}</h3>

    <FormNormalInit
      v-if="step === 0 || ['edit', 'show'].indexOf(requestAction) !== -1"
      v-model="normal_init"
      :xLoading.sync="loading"
      :step="step">
    </FormNormalInit>

    <FormNormalMain
      v-if="step === 1"
      ref="adMainFormNormal"
      v-model="normal_main"
      :xLoading.sync="loading"
      :mediaId="form.media_id"
      :downAddr="state.form.down_addr"
      :gameDomainUrl="state.form.game_domain_url"
      :gameList="gameList">
    </FormNormalMain>

    <el-form-item v-show="!loading && step < 1">
      <el-button v-if="false" type="primary" @click="stepPrev">上一步</el-button>
      <el-button type="primary" @click="stepNext">下一步</el-button>
    </el-form-item>

    <el-form-item v-show="!loading && step == 1 && !formItemDisabled">
      <el-button type="primary" @click="batchShow = !batchShow" v-if="requestAction === 'create'">批量创建</el-button>
      <el-button type="primary" @click="stepSubmit(1)" v-if="requestAction === 'create'">保存并继续创建</el-button>
      <el-button type="primary" @click="stepSubmit(0)">保存</el-button>
      <el-button v-if="requestAction === 'create'" @click="cancelForm">取消</el-button>
    </el-form-item>

    <el-dialog
      title=""
      :visible.sync="batchShow"
      :modal="false">
      <el-form :model="form">
        <el-form-item label="批量创建条数">
          <el-input-number size="small" v-model="batchCount" controls-position="right" :min="0"></el-input-number>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button size="mini" @click="handleBatchAction(0)">取 消</el-button>
        <el-button size="mini" type="primary" @click="handleBatchAction(1)">确 定</el-button>
      </div>
    </el-dialog>
  </el-form>
</template>

<script>
import { mapGetters, mapState } from "vuex";

import { getBasicData } from "@/js/api/common";
import {
  editItem,
  showItem,
  storeItem,
  updateItem
} from "@/js/api/advertising";
import { mapStateGetSet } from "@/js/utils";
import { xStore } from "@/js/mixins";
import { Provider } from "./mixins";

import FormNormalInit from "./components/FormNormalInit";
import FormNormalMain from "./components/FormNormalMain";

export default {
  mixins: [xStore, Provider],
  name: "gmp-ad-main-form",
  components: {
    FormNormalInit,
    FormNormalMain
  },
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    requestAction: {
      type: [String],
      required: true
    },
    itemId: {
      default: null
    }
  },
  data: function() {
    return {
      loading: true,
      step: 0,
      gameList: [],
      batchShow: false,
      batchCount: 0,
      continueCreate: false,
    };
  },
  methods: {
    cancelForm() {
      this.$emit("update:visible", false);
    },
    clearForm() {
      this.step = 0;
      this.$store.dispatch(this.state.getActionName("clear"));
      this.$store.dispatch(this.state.getActionName("form.media_id"), null);
      this.$store.dispatch(this.state.getActionName("form.agent_id"), null);
      this.$store.dispatch(this.state.getActionName("form.promote_id"), null);
      this.$store.dispatch(this.state.getActionName("form.game_id"), null);
      this.$store.dispatch(this.state.getActionName("form.settlement"), "");
      this.$store.dispatch(
        this.state.getActionName("form.style_ids") + "@replace",
        []
      );
      this.$store.dispatch(
        this.state.getActionName("form.style_infos") + "@replace",
        []
      );
      this.$store.dispatch(
        this.state.getActionName("form.template_info") + "@replace",
        {}
      );
      this.$store.dispatch(
        this.state.getActionName("form.material_info") + "@replace",
        {}
      );
    },
    stepPrev: function(event) {
      this.step--;
    },
    stepNext: function(event) {
      switch (this.step) {
        case 0:
          if (!this.form.media_id) {
            this.$message({
              message: "请选择所属媒体",
              center: true
            });
          } else if (!this.form.agent_id) {
            this.$message({
              message: "请选择代理商",
              center: true
            });
          } else if (!this.form.promote_id) {
            this.$message({
              message: "请选择媒体账号",
              center: true
            });
          } else {
            this.step++;
          }
          break;
        default:
          this.step++;
          break;
      }
    },
    stepSubmit: function(continueCreate) {
      this.loading = true;
      this.continueCreate = continueCreate;
      (this.requestAction === "edit"
        ? updateItem(this.form.ad_id, this.form)
        : storeItem(this.form)
      )
        .then(response => {
          this.$message({
            message: response.data.message,
            center: true
          });
          if (response.data.result === "success") {
            if (this.continueCreate === 1) {
              this.$refs.adMainFormNormal.clearMaterialInfo();
            } else {
              this.$emit("update:visible", false);
            }
            this.$emit(
              "reloadList",
              this.form.distribute === "Normal" ? null : "tabSync"
            );
          }
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
          this.loading = false;
        });
    },
    handleBatchAction: function(confirm) {
      this.continueCreate = false;
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
            .then(response => {
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
            .catch(error => {
              this.$message.error(failureMsg);
              this.loading = false;
            });
        }

        promise
          .then(_ => {
            if (failureMsg === "") {
              this.$message({
                message: "你成功创建了" + successCount + "条广告",
                type: "success",
                onClose: _ => {
                  location.reload();
                }
              });
            } else {
              this.loading = false;
              this.$message.warning(failureMsg);
            }
          })
          .catch(error => {
            this.loading = false;
          });
      }
      this.batchShow = false;
    }
  },
  watch: {
    step: function(val, oldVal) {
      if (val === 1) {
        // 修改框的高度
        var height = Math.ceil($(document).height() * 0.8);
        $(".am-modal-dialog").css("height", height + "px");
        $(".am-modal-bd").css("height", height - 50 + "px");
        // 可选游戏列表
        getBasicData({ gameList: this.normal_init }).then(response => {
          this.gameList = response.data.gameList;
        });
      }
    }
  },
  computed: {
    state() {
      return this.$store.state.adNormal;
    },
    distribute() {
      return this.state.form.distribute;
    },
    // form() {
    //   let res = Object.assign({}, this.state.form);
    //   return res;
    // },
    ...mapState("adNormal", {
      form: state => state.form
    }),
    rules() {
      let res = Object.assign(
        {},
        this.state.rules,
        this.state["rule" + this.distribute]
      );
      return res;
    },
    ...mapStateGetSet("adNormal", {
      normal_init: {
        get: state => {
          return {
            distribute: state.form.distribute,
            ad_name: state.form.ad_name,
            media_id: state.form.media_id,
            agent_id: state.form.agent_id,
            promote_id: state.form.promote_id,
            distribute: state.form.distribute
          };
        },
        set: function(payload, dispatch) {
          dispatch("form.media_id", payload.media_id);
          dispatch("form.agent_id", payload.agent_id);
          dispatch("form.promote_id", payload.promote_id);
          dispatch("form.distribute", payload.distribute);
        }
      },
      normal_main: {
        get: state => {
          return {
            ad_id: state.form.ad_id,
            ad_name: state.form.ad_name,
            media_id: state.form.media_id,
            game_id: state.form.game_id,
            promote_id: state.form.promote_id,
            distribute: state.form.distribute,
            has_watermark: state.form.has_watermark,
            position_id: state.form.position_id,
            settlement: state.form.settlement,
            is_multi_materials: state.form.is_multi_materials,
            wxmp_tracking: state.form.wxmp_tracking,
            apk_addr: state.form.apk_addr,
            apk_domain: state.form.apk_domain,
            style_ids: state.form.style_ids,
            material_info: state.form.material_info,
            has_landing_page: state.form.has_landing_page,
            template_id: state.form.template_id,
            template_info: state.form.template_info,
            shell_url: state.form.shell_url,
            ad_url: state.form.ad_url,
            app_type: state.form.app_type,
            lp_company_full: state.form.lp_company_full,

            track_url: state.form.track_url,

            positionList: state.data.positionList,
            settlementList: state.data.settlementList,
            styleList: state.data.styleList,
            hasLandingPageList: state.data.hasLandingPageList
          };
        },
        set: function(payload, dispatch) {
          dispatch("form.game_id", payload.game_id);
          dispatch("form.position_id", payload.position_id);
          dispatch("form.settlement", payload.settlement);
          dispatch("form.has_watermark", payload.has_watermark);
          dispatch("form.is_multi_materials", payload.is_multi_materials);
          dispatch("form.apk_addr", payload.apk_addr);
          dispatch("form.apk_domain", payload.apk_domain);
          dispatch("form.style_ids", payload.style_ids);
          dispatch("form.material_info", payload.material_info);
          dispatch("form.has_landing_page", payload.has_landing_page);
          dispatch("form.template_id", payload.template_id);
          dispatch("form.shell_url", payload.shell_url);
          dispatch("form.ad_url", payload.ad_url);
          dispatch("form.app_type", payload.app_type);
        }
      }
    })
  },
  created() {
    this.xStoreLoadExtraConfig();
    let promises = [];
    // 重载数据
    if (this.itemId) {
      promises.push(
        (this.requestAction === "edit"
          ? editItem(this.itemId)
          : showItem(this.itemId)
        )
          .then(response => {
            this.$store.dispatch(this.state.getActionName("clear"));
            let it = response.data.it;
            if (it.material_info.length <= 0) {
              it.material_info = {};
            }
            it.style_ids = Object.keys(it.material_info)
              .filter(function(val) {
                return !isNaN(val);
              })
              .map(val => {
                return parseInt(val);
              });
            this.$store.dispatch(this.state.getActionName("assign_form"), it);

            if (this.requestAction === "create") {
              this.$store.dispatch(
                this.state.getActionName("form.material_info"),
                {}
              );
              this.$store.dispatch(
                this.state.getActionName("form.style_ids"),
                []
              );
            }
            this.step = 1;
          })
          .then(_ => {
            return Promise.resolve();
          })
      );
    } else {
      this.clearForm();
    }
    promises.push(
      getBasicData({ domainDownList: "" })
        .then(response => {
          this.$set(this.xStore.data, "domainDownList", []);
          this.xStore.data.domainDownList = response.data.domainDownList;
        })
        .then(_ => {
          return Promise.resolve();
        })
    );

    Promise.all(promises).then(_ => {
      this.loading = false;
    });
  }
};
</script>

<style>
body label,
body span,
body div {
  font-family: Microsoft YaHei;
}
</style>