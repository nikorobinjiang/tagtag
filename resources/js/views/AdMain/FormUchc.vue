<template>
<div>
  <div class="nav-right">
    <ol class="list">
      <li><a href="#base">基础信息</a></li>
      <li>
        <a href="#campaign">推广计划</a>
        <ol class="list">
          <li><a href="#campaign-budget">日预算</a></li>
        </ol>
      </li>
      <li>
        <a href="#adgroup">推广单元</a>
        <ol class="list">
          <li><a href="#adgroup-audience">自定义人群包</a></li>
          <li><a href="#adgroup-adconvert">转化名称</a></li>
          <li><a href="#adgroup-bid">第一阶段出价</a></li>
        </ol>
      </li>
      <li>
        <a href="#creative">创意</a>
        <ol class="list">
          <li><a href="#creative-styles">素材样式</a></li>
        </ol>
      </li>
    </ol>
  </div>

  <el-form
    v-if="isMounted"
    v-loading="loading"
    ref="adMainForm"
    :model="form"
    :rules="rules"
    :disabled="requestAction==='show'"
    label-width="120px"
    size="mini"
    v-bind:class="['ad-main-form', 'ad-main-form-uchc']">

    <h3>{{ formTitle }}</h3>

    <h4 id="base">基础信息</h4>

    <el-form-item label="广告名称">
      {{ form.ad_name }}
    </el-form-item>

    <el-form-item label="投放游戏" prop="game_id">
      <el-select v-model="game_id" filterable>
        <el-option
          v-for="(item, key) in xStore.data.gameList"
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
      <el-select v-if="false" disabled v-model="form.promote_id" filterable>
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

      <el-form-item label="apk壳地址">
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

    <InputAdTpl
      v-model="ad_tpl_id"
      :distribute="distribute">
    </InputAdTpl>

    <FormUchcMain
      v-if="!loadingUchcMain"
      :parentRefs="$refs"
      :formScope="distribute"
      :gameId="game_id">
    </FormUchcMain>
  
    <el-form-item v-show="!formItemDisabled">
      <el-button type="primary" @click="stepSubmit()">保存</el-button>
    </el-form-item>
  </el-form>
</div>
</template>

<script>
import { values } from "lodash";

import { getTpl } from "@/js/api/common";
import { editItem, showItem, storeItem, updateItem } from "@/js/api/advertising";
import { FormScope, xStore } from "@/js/mixins";
import { Provider } from "./mixins";
import { mapGetters, mapState } from "vuex";
import { mapStateGetSet } from "@/js/utils";

import InputAppId from "./components/Normal/InputAppId";
import InputAppUrl from "./components/Normal/InputAppUrl";
import InputAppBundleID from "./components/Normal/InputAppBundleID";

import InputApkDomain from "./components/Normal/InputApkDomain";
import InputAdGroup from "./components/Normal/InputAdGroup";
import InputAdTpl from "./components/Normal/InputAdTpl";
import FormUchcMain from "./components/FormUchcMain";

export default {
  mixins: [FormScope, xStore, Provider],
  name: "gmp-ad-main-form-uchc",
  components: {
    InputAppId,
    InputAppUrl,
    InputAppBundleID,
    InputApkDomain,
    InputAdGroup,
    InputAdTpl,
    FormUchcMain
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
      distribute: "Uchc",
      isMounted: false,
      loading: true,
      loadingUchcMain: false
    };
  },
  methods: {
    setpClose: function() {
      $(".am-close").click();
    },
    stepSubmit: function() {
      this.$refs.adMainForm.validate(valid => {
        let uchcFormCheckMsg = values(this.uchcFormCheck);
        if (valid && uchcFormCheckMsg.length === 0) {
          this.loading = true;
          (this.requestAction === "edit"
            ? updateItem(this.form.ad_id, this.form)
            : storeItem(this.form)
          )
            .then(response => {
              this.$message({
                message: response.data.message,
                type: response.data.result === "success" ? "" : "error",
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
          if (uchcFormCheckMsg.length > 0) {
            this.$message({
              message: uchcFormCheckMsg.join("，") + "。",
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
  watch: {},
  computed: {
    watermarkList() {
      return this.xStore.data.watermarkList;
    },
    gameList() {
      return this.xStore.data.gameList;
    },
    ...mapGetters("adUchc", { uchcFormCheck: "formCheck" }),
    ...mapState("adNormal", {
      stateNormal: state => state,
      formNormal: state => state.form
    }),
    ...mapState("adUchc", {
      stateUchc: state => state,
      formUchc: state => state.form
    }),
    form() {
      return Object.assign({}, this.formNormal, { Uchc: this.formUchc });
    },
    rules() {
      return Object.assign({}, this.stateNormal.rules, {
        Uchc: this.stateUchc.rules
      });
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
          this.loadingUchcMain = true;
          getTpl(this.distribute, payload)
            .then(response => {
              dispatch("form.ad_tpl_id", payload);
              this.$store.dispatch(
                this.stateUchc.getActionName("form.campaign") + "@assign",
                response.data.tpl.campaign
              );
              this.$store.dispatch(
                this.stateUchc.getActionName("form.adgroup") + "@assign",
                response.data.tpl.adgroup
              );
              this.$store.dispatch(
                this.stateUchc.getActionName("form.creativeSource"),
                response.data.tpl.creativeSource
              );
              this.loadingUchcMain = false;
            })
            .catch(error => {
              console.log(error);
              this.loadingUchcMain = false;
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
        // console.log(JSON.parse(this.it));
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

        // 第三方广告平台
        this.$store.dispatch(this.stateUchc.getActionName("clear"));
        // this.$store.dispatch(
        //   this.stateUchc.getActionName("form.settlement"),
        //   it.settlement
        // );
        if (it.uchc) {
          if (!it.uchc.campaign) {
            this.$delete(it.uchc, "campaign");
          }
          if (!it.uchc.adgroup) {
            this.$delete(it.uchc, "adgroup");
          }
        } else {
          this.$delete(it, "uchc");
        }
        if (it.uchc) {
          this.$store.dispatch(
            this.stateUchc.getActionName("assign_form"),
            it.uchc
          );
          this.$delete(it, "uchc");
        }

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
