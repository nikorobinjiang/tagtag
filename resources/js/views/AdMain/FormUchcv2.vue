<template>
  <el-form
    v-if="isMounted"
    v-loading="loading"
    ref="adMainForm"
    :model="form"
    :rules="rules"
    :disabled="requestAction==='show'"
    label-width="120px"
    size="mini"
    v-bind:class="['ad-main-form', 'ad-main-form-uchcv2']">

    <h3>{{ formTitle }}</h3>

    <h4>基础信息</h4>

    <el-form-item label="广告名称">
      {{ adForm.ad_name }}
    </el-form-item>

    <el-form-item label="投放游戏" prop="game_id">
      <el-select v-model="adForm.game_id" filterable>
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
      {{ adForm.media_name }}
      <el-select v-if="false" disabled v-model="adForm.media_id" filterable>
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
      {{ adForm.promote_account }}
      <el-select v-if="false" disabled v-model="adForm.promote_id" filterable>
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
      {{ adForm.agent_name }}
      <el-select v-if="false" disabled v-model="adForm.agent_name" filterable>
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
      {{ adForm.settlement }}
      <el-select v-if="false" disabled v-model="adForm.settlement">
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

    <template v-if="['Android', 'H5'].indexOf(adForm.app_type) !== -1">
      <InputApkDomain
        v-model="adForm.apk_domain">
      </InputApkDomain>

      <el-form-item label="apk壳地址">
        <el-input disabled v-model="adForm.down_addr">
          <el-button slot="append" v-clipboard="{ text: adForm.down_addr }">复制</el-button>
        </el-input>
      </el-form-item>

      <el-form-item label="apk地址">
        <el-input v-model="adForm.apk_addr">
          <el-button slot="append" v-clipboard="{ text: adForm.apk_addr }">复制</el-button>
        </el-input>
      </el-form-item>
    </template>

    <el-row v-if="adForm.app_type === 'iOS'">
      <InputAppId
        :gameList="xStore.data.gameList"
        v-model="adForm.game_id">
      </InputAppId>
      <InputAppUrl
        :gameList="xStore.data.gameList"
        v-model="adForm.game_id">
      </InputAppUrl>
    </el-row>

    <InputAdTpl
      v-model="adForm.ad_tpl_id"
      :distribute="distribute"
      @change="handleChangeAdTplId">
    </InputAdTpl>

    <FormUchcv2Main
      v-if="!myLoading"
      :parentRefs="$refs"
      :formScope="distribute"
      :ad.sync="adForm"
      :uchcv2.sync="uchcv2Form">
    </FormUchcv2Main>
  
    <el-form-item v-show="!formItemDisabled">
      <el-button type="primary" @click="stepSubmit(0)">保存</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { cloneDeep, values, assign, has, get } from "lodash";

import { getTpl } from "@/js/api/common";
import { editItem, showItem, storeItem, updateItem } from "@/js/api/advertising";
import { FormScope, xStore } from "@/js/mixins";
import { Provider } from "./mixins";
import { mapGetters, mapState } from "vuex";
import { mapStateGetSet } from "@/js/utils";

import InputAppId from "./components/Normal/InputAppId";
import InputAppUrl from "./components/Normal/InputAppUrl";

import InputApkDomain from "./components/Normal/InputApkDomain";
import InputAdGroup from "./components/Normal/InputAdGroup";
import InputAdTpl from "./components/Normal/InputAdTpl";
import FormUchcv2Main from "./components/FormUchcv2Main";

export default {
  mixins: [FormScope, xStore, Provider],
  name: "gmp-ad-main-form-uchcv2",
  components: {
    InputAppId,
    InputAppUrl,
    InputApkDomain,
    InputAdGroup,
    InputAdTpl,
    FormUchcv2Main
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
      distribute: "Uchcv2",
      isMounted: false,
      loading: true,
      myLoading: true,
      adForm: undefined,
      uchcv2Form: undefined,
    };
  },
  watch: {
  },
  computed: {
    watermarkList() {
      return this.xStore.data.watermarkList;
    },
    ...mapGetters("adUchcv2", { uchcFormCheck: "formCheck" }),
    ...mapState("adNormal", {
      stateNormal: state => state,
      defaultAdForm: state => state.defaultForm,
    }),
    ...mapState("adUchcv2", {
      stateUchcv2: state => state,
      defaultUchcv2Form: state => state.defaultForm,
    }),
    form() {
      return assign({}, this.adForm, { Uchcv2: this.uchcv2Form });
    },
    rules() {
      return assign({}, this.stateNormal.rules, {
        Uchcv2: this.stateUchcv2.rules
      });
    }
  },
  created() {
    this.adForm = cloneDeep(this.defaultAdForm)
    this.uchcv2Form = cloneDeep(this.defaultUchcv2Form)

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

        if (get(it, 'uchcv2')) {
          if (!get(it, 'uchcv2.ad_group')) {
            this.$delete(it.uchcv2, "ad_group");
          }
          if (!get(it, 'uchcv2.campaign')) {
            this.$delete(it.uchcv2, "campaign");
          }
        } else {
          this.$delete(it, "uchcv2");
        }

        if (it.uchcv2) {
          this.$set(this.$data, 'uchcv2Form', assign(this.uchcv2Form, it.uchcv2))
          this.$delete(it, "uchcv2");
        }

        // 普通
        this.$set(this.$data, 'adForm', assign(this.adForm, it))
        if (this.requestAction === "create") {
          this.$set(this.adForm, 'material_info', [])
          this.$set(this.adForm, 'style_ids', [])
        }

        this.loading = false;
        this.isMounted = true;
      }).finally(() => {
        this.myLoading = false
      });
    }
  },
  methods: {
    setpClose: function() {
      $(".am-close").click();
    },
    stepSubmit: function(continue_create) {
      this.$refs.adMainForm.validate(valid => {
        let uchcFormCheckMsg = values(this.uchcFormCheck);
        if (valid && uchcFormCheckMsg.length === 0) {
          this.loading = true;
          this.form.continue_create = continue_create;
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
    },
    handleChangeAdTplId() {
      this.$nextTick(() => {
        if (!this.adForm.ad_tpl_id) {
          return
        }
        this.loadingUchcv2Main = true;
        getTpl(this.distribute, this.adForm.ad_tpl_id)
          .then(response => {
            if (has(response, 'data.tpl')) {
              this.$set(
                this.uchcv2Form,
                'campaign',
                assign({}, this.uchcv2Form.campaign, response.data.tpl.campaign)
              )
              this.adForm.creativeSource = response.data.tpl.creativeSource
            }
          })
          .catch(error => {
            console.log(error);
          })
          .finally(() => {
            this.loadingUchcv2Main = false;
          });
      })
    }
  },
};
</script>
