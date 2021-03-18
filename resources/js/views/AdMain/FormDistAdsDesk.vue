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

    <el-row>
      <el-col :span="24">
        <el-form-item v-if="['edit', 'show'].indexOf(requestAction) !== -1" label="广告名称">
          <el-input disabled v-model="form.ad_name"></el-input>
        </el-form-item>
      </el-col>

      <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 24 : 24">
        <el-form-item  label="代理商" prop="agent_id">
          <el-select :disabled="['edit', 'show'].indexOf(requestAction) !== -1" v-model="form.agent_id" filterable>
            <el-option
              v-for="item in agentList"
              :key="item.value"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>

      <el-col
        v-if="['edit', 'show'].indexOf(requestAction) !== -1 && ['iOS', 'WXMP'].indexOf(appType) === -1"
        :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 24 : 24">
        <el-form-item label="apk壳地址"> {{ appType }}
          <el-input disabled v-model="form.down_addr">
            <el-button slot="append" v-clipboard="{ text: form.down_addr }">复制</el-button>
          </el-input>
        </el-form-item>
      </el-col>

      <el-col :span="24">
        <el-form-item label="投放游戏" prop="game_id">
          <el-select :disabled="['edit', 'show'].indexOf(requestAction) !== -1" v-model="form.game_id" filterable>
            <el-option
              v-for="(item, key) in gameList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>

      <el-col :span="12">
        <el-form-item label="是否有水印" prop="has_watermark">
          <el-select :disabled="['edit', 'show'].indexOf(requestAction) !== -1" v-model="form.has_watermark">
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

      <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 12 : 12"
        v-if="['edit', 'show'].indexOf(requestAction) !== -1 && ['iOS', 'WXMP'].indexOf(appType) === -1">
        <el-form-item label="apk地址">
          <el-input :disabled="['edit', 'show'].indexOf(requestAction) !== -1" v-model="form.apk_addr">
            <el-button slot="append" v-clipboard="{ text: form.apk_addr }">复制</el-button>
          </el-input>
        </el-form-item>
      </el-col>
    
      <template v-if="appType !== 'WXMP'">
        <el-col v-if="requestAction !== 'create'" :span="24">
          <InputAppBundleID
            :gameList="gameList"
            v-model="form.game_id">
          </InputAppBundleID>
        </el-col>

        <el-col v-if="appType === 'iOS'" :span="24">
          <InputAppUrl
            :gameList="gameList"
            v-model="form.game_id">
          </InputAppUrl>
        </el-col>
        <el-col :span="24" v-if="false && ['edit', 'show'].indexOf(requestAction) !== -1">
          <InputAppTrackUrl
            v-model="form.track_url">
          </InputAppTrackUrl>
        </el-col>
      </template>
    </el-row>

    <el-form-item v-if="['edit', 'show'].indexOf(requestAction) !== -1 && appType !== 'iOS'" label="安装包历史">
      <el-row
        v-for="(item, key) in downHistories"
        :key="key">
        <span>
          <a class="width-80-nowrap" target="_blank" :href=" item.down_url?item.down_url:'javascript:void:;'">
            {{ item.down_url?basename(item.down_url):'无' }}
          </a>
          <span v-if="key > 0 && !formItemDisabled" @click="delDownHistory(key, item)">删除</span>
        </span>
      </el-row>
    </el-form-item>

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

import { selMedia, getBasicData, getBasicRelData } from "@/js/api/common";
import {
  editItem,
  showItem,
  storeItem,
  updateItem
} from "@/js/api/AdMain/DistAdsDesk";
import { mapStateGetSet, basename } from "@/js/utils";
import { xStore } from "@/js/mixins";
import { Provider } from "./mixins";

import FormNormalInit from "./components/FormNormalInit";
import FormNormalMain from "./components/FormNormalMain";
import InputAppBundleID from "./components/Normal/InputAppBundleID";
import InputAppTrackUrl from "./components/Normal/InputAppTrackUrl";
import InputAppUrl from "./components/Normal/InputAppUrl";

export default {
  mixins: [xStore, Provider],
  name: "gmp-ad-main-form-dist-ads-desk",
  components: {
    FormNormalInit,
    FormNormalMain,
    InputAppBundleID,
    InputAppTrackUrl,
    InputAppUrl
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
      agentList: [],
      downHistories: [],
      batchShow: false,
      batchCount: 0,
      continueCreate: false,
    };
  },
  methods: {
    basename: function(str) {
      return basename(str);
    },
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
    watermarkList() {
      return this.xStore.data.watermarkList;
    },
    appType: function() {
      let res = null;
      this.gameList.forEach(item => {
        if (item.value === this.form.game_id) {
          res = item.app_type;
        }
      });
      if (!res) {
        res = this.form.app_type;
      }
      return res;
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
      }
    })
  },
  created() {
    this.xStoreLoadExtraConfig();
    var promises = [];
    // 重载数据
    if (this.itemId) {
      promises.push(
        (this.requestAction === "edit"
          ? editItem(this.itemId)
          : showItem(this.itemId)
        )
          .then(response => {
            this.$store.dispatch(this.state.getActionName("clear"));
            var it = response.data.data.it;
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
            return selMedia({
              media_id: this.form.media_id
            })
              .then(response => {
                this.agentList = response.data.agentList;
              })
              .catch(error => {
              });
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

    promises.push(
      getBasicRelData({
        downHistories: {
          ad_id: this.itemId
        }
      }).then(response => {
        this.downHistories = response.data.downHistories;
      }).then(_ => {
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