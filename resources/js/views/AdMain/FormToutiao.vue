<template>
<div>
  <div class="nav-right">
    <ol class="list">
      <li><a href="#base">基础信息</a></li>
      <li>
        <a href="#campaign">广告组</a>
      </li>
      <li>
        <a href="#adgroup">广告计划</a>
        <ol class="list">
          <li><a href="#adgroup-adconvert">转化元素</a></li>
          <li><a href="#adgroup-targeting">人群包</a></li>
          <li><a href="#adgroup-budget">日预算</a></li>
          <li><a href="#adgroup-schedule">投放时段</a></li>
          <li><a href="#adgroup-bid">目标转化出价</a></li>
        </ol>
      </li>
      <li>
        <a href="#creative">广告创意</a>
        <ol class="list">
          <li><a href="#creative-mode">创意生成方式</a></li>
          <li><a href="#creative-styles">素材样式</a></li>
          <li><a href="#creative-web-url">应用下载详情页</a></li>
        </ol>
      </li>
    </ol>
  </div>

  <el-form
    v-loading="loading"
    ref="adMainForm"
    :model="form"
    :rules="rules"
    :disabled="requestAction==='show'"
    label-width="100px"
    size="mini" 
    v-bind:class="['ad-main-form', 'ad-main-form-toutiao']">

    <h3>{{ formTitle }}</h3>

    <template
      v-if="isMounted">
      <h4 id="base">基础信息</h4>

      <el-form-item label="广告名称">
        {{ form.ad_name }}
      </el-form-item>

      <el-form-item label="投放游戏" prop="game_id">
        <el-select v-model="game_id" filterable>
          <el-option
            v-for="(item, key) in gameList"
            :key="key"
            :title="item.label"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
      
      <el-form-item label="所属媒体">
        {{ form.media_name }}
        <el-select v-if="false" disabled v-model="form.media_id" filterable>
          <el-option
            v-for="item in xStore.data.mediaList"
            :key="item.value"
            :title="item.label"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item label="媒体账号">
        {{ form.promote_account }}
        <el-select v-if="false" disabled v-model="form.promote_account" filterable>
          <el-option
            v-for="item in xStore.data.promoteList"
            :key="item.value"
            :title="item.label"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item  label="代理商">
        {{ form.agent_name }}
        <el-select v-if="false" disabled v-model="form.agent_name" filterable>
          <el-option
            v-for="item in xStore.data.agentList"
            :key="item.value"
            :title="item.label"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>

      <el-form-item label="结算方式">
        {{ form.settlement }}
        <el-select v-if="false" disabled v-model="form.settlement">
          <el-option
            v-for="(item, key) in xStore.data.settlementList"
            :key="key"
            :title="item.label"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>

    <el-form-item label="是否有水印" prop="has_watermark">
      <el-select v-model="form.has_watermark" disabled>
        <el-option
          v-for="(item, key) in watermarkList"
          :key="key"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>

      <template v-if="['Android', 'H5'].indexOf(appType) !== -1">
        <InputApkDomain
          v-model="apk_domain">
        </InputApkDomain>

        <el-form-item label="壳地址">
          <el-input disabled v-model="form.down_addr">
            <el-button slot="append" v-clipboard="{ text: form.down_addr }">复制</el-button>
          </el-input>
        </el-form-item>

        <el-form-item label="apk地址">
          <el-input v-model="form.apk_addr">
            <el-button slot="append" v-clipboard="{ text: form.apk_addr }">复制</el-button>
          </el-input>
        </el-form-item>
      </template>

      <el-row>
        <el-col :span="24">
          <InputAppBundleID
            :gameList="gameList"
            v-model="form.game_id">
          </InputAppBundleID>
        </el-col>
      </el-row>

      <el-row v-if="appType === 'iOS'">
        <InputAppId
          :gameList="gameList"
          v-model="form.game_id">
        </InputAppId>
        <InputAppUrl
          :gameList="gameList"
          v-model="form.game_id">
        </InputAppUrl>
      </el-row>

      <InputTrackUrl
        v-model="form.track_url">
      </InputTrackUrl>

      <InputAdGroup
        v-model="ad_group_id"
        :distribute="distribute"
        :promoteId="promote_id"
        ref="InputAdGroup">
      </InputAdGroup>

      <InputAdTpl
        v-model="ad_tpl_id"
        :distribute="distribute">
      </InputAdTpl>

      <FormToutiaoMain
        v-if="!loadingToutiaoMain"
        :parentRefs="$refs"
        :gameId="game_id">
      </FormToutiaoMain>
    
      <el-form-item v-show="!formItemDisabled">
        <el-button type="primary" @click="stepSubmit()">保存</el-button>
      </el-form-item>
    </template>
  </el-form>
</div>
</template>

<script>
import { values } from "lodash";

import { getTpl } from "@/js/api/common";
import {
  editItem,
  showItem,
  storeItem,
  updateItem
} from "@/js/api/advertising";
import { FormScope, xStore } from "@/js/mixins";
import { Provider } from "./mixins";
import { mapGetters, mapState } from "vuex";
import { mapStateGetSet } from "@/js/utils";

import InputAppId from "./components/Normal/InputAppId";
import InputAppUrl from "./components/Normal/InputAppUrl";
import InputAppBundleID from "./components/Normal/InputAppBundleID";

import InputTrackUrl from "./components/Normal/InputTrackUrl";

import InputApkDomain from "./components/Normal/InputApkDomain";
import InputAdGroup from "./components/Normal/InputAdGroup";
import InputAdTpl from "./components/Normal/InputAdTpl";
import FormToutiaoMain from "./components/FormToutiaoMain";

export default {
  mixins: [FormScope, xStore, Provider],
  name: "gmp-ad-main-form-toutiao",
  components: {
    InputAppId,
    InputAppUrl,
    InputAppBundleID,
    InputTrackUrl,
    InputApkDomain,
    InputAdGroup,
    InputAdTpl,
    FormToutiaoMain
  },
  props: {
    requestAction: {
      type: [String],
      required: true
    },
    itemId: {
      default: null
    }
  },
  data() {
    return {
      distribute: "Toutiao",
      isMounted: false,
      loading: true,
      loadingToutiaoMain: false
    };
  },
  methods: {
    setpClose: function() {
      $(".am-close").click();
    },
    stepSubmit: function() {
      this.$refs.adMainForm.validate(valid => {
        let toutiaoFormCheckMsg = values(this.toutiaoFormCheck);
        if (valid && toutiaoFormCheckMsg.length === 0) {
          this.loading = true;
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
                location.reload();
              }
              this.loading = false;
            })
            .catch(error => {
              console.log(error);
              this.loading = false;
            });
        } else {
          console.log("error submit!!");
          if (toutiaoFormCheckMsg.length > 0) {
            this.$message({
              message: toutiaoFormCheckMsg.join("，") + "。",
              type: "error",
              dangerouslyUseHTMLString: true,
              center: true
            });
          }
          return false;
        }
      });
    }
  },
  watch: {
    // game_id: function(val) {
    //   let game = null;
    //   this.gameList.forEach(item => {
    //     if (item.value === val) {
    //       game = item;
    //     }
    //   });
    //   if (game && game.app_type === "iOS") {
    //     this.appType = game.app_type;
    //   } else {
    //     this.appType = "Android";
    //   }
    // }
  },
  computed: {
    watermarkList() {
      return this.xStore.data.watermarkList;
    },
    gameList() {
      return this.xStore.data.gameList;
    },
    ...mapGetters("adToutiao", { toutiaoFormCheck: "formCheck" }),
    ...mapState("adNormal", {
      stateNormal: state => state,
      formNormal: state => state.form
    }),
    ...mapState("adToutiao", {
      stateToutiao: state => state,
      formToutiao: state => state.form
    }),
    form() {
      return Object.assign({}, this.formNormal, { Toutiao: this.formToutiao });
    },
    rules() {
      return Object.assign(
        {},
        this.stateNormal.rules,
        {
          Toutiao: this.stateToutiao.rules
        },
        {
          ad_group_id: [
            {
              required: true,
              message: "",
              trigger: "blur"
            },
            {
              validator: (rule, value, callback) => {
                if (!value) {
                  return callback(new Error("广告组不能为空"));
                }
                setTimeout(() => {
                  if (!Number.isInteger(value)) {
                    callback(new Error("请选择广告组"));
                  } else {
                    if (value <= 0) {
                      callback(new Error("请选择广告组"));
                    } else {
                      callback();
                    }
                  }
                }, 1000);
              },
              trigger: "blur"
            }
          ]
        }
      );
    },
    ...mapStateGetSet("adNormal", {
      game_id: {
        get: state => state.form.game_id,
        set: function(payload, dispatch) {
            dispatch("form.game_id", payload);
            let game = null;
            this.gameList.forEach(item => {
              if (item.value === payload) {
                game = item;
              }
            });
            if (game && game.app_type === "iOS") {
              this.appType = game.app_type;
            } else {
              this.appType = "Android";
            }
        }
      },
      appType: {
        get: state => state.form.app_type,
        set: "form.app_type"
      },
      apk_domain: {
        get: state => state.form.apk_domain,
        set: "form.apk_domain"
      },
      promote_id: {
        get: state => state.form.promote_id,
        set: "form.promote_id"
      },
      ad_group_id: {
        get: state => state.form.ad_group_id,
        set: "form.ad_group_id"
      },
      ad_tpl_id: {
        get: state => state.form.ad_tpl_id,
        set: function(payload, dispatch) {
          let oldVal = this.ad_tpl_id;
          this.loadingToutiaoMain = true;
          getTpl(this.distribute, payload)
            .then(response => {
              dispatch("form.ad_tpl_id", payload);
              this.$store.dispatch(
                this.stateToutiao.getActionName("assign_form"),
                response.data.tpl
              );
              this.loadingToutiaoMain = false;
            })
            .catch(error => {
              console.log(error);
              this.loadingToutiaoMain = false;
            });
        }
      }
    })
  },
  created() {
    this.xStoreLoadExtraConfig();
    // 重载数据
    if (this.itemId) {
      (this.requestAction === "edit"
        ? editItem(this.itemId)
        : showItem(this.itemId)
      ).then(response => {
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

        // 头条
        this.$store.dispatch(this.stateToutiao.getActionName("clear"));
        this.$store.dispatch(
          this.stateToutiao.getActionName("form.settlement"),
          it.settlement
        );
        this.$store.dispatch(
          this.stateToutiao.getActionName("assign_form"),
          it.toutiao
        );
        this.$delete(it, "toutiao");

        // 普通
        this.$store.dispatch(this.stateNormal.getActionName("clear"));
        this.$store.dispatch(this.stateNormal.getActionName("assign_form"), it);
        if (this.requestAction === "create") {
          this.$store.dispatch(
            this.stateNormal.getActionName("form.material_info"),
            {}
          );
          this.$store.dispatch(
            this.stateNormal.getActionName("form.style_ids"),
            []
          );
        }

        this.loading = false;
        this.isMounted = true;
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.nav-right {
  height: 300px;
  position: fixed;
  /* float: right; */
  /* width: 100%; */
  right: 50px;
  z-index: 2000;
  span {
    display: block;
  }
  a {
    text-decoration: none;
  }
}
</style>
