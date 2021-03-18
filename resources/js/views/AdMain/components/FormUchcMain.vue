<template>
  <div
    v-if="isMounted"
    v-loading="loading"
    v-bind:class="['ad-main-form-toutiao-main']">

    <h4 id="campaign">推广计划</h4>

    <el-form-item label="推广计划名称" :prop="getFormScopedField('campaign.campaignName')">
      <el-input v-model="campaign.campaignName"></el-input>
    </el-form-item>

    <InputAdResourceId
      v-model="campaign.adResourceId">
    </InputAdResourceId>

    <InputBudget
      id="campaign-budget"
      v-model="campaign.budget">
    </InputBudget>

    <InputRate
      v-model="campaign.rate">
    </InputRate>

    <InputSchedule
      :formScope="distribute"
      :startDate.sync="campaign.startDate"
      :endDate.sync="campaign.endDate">
    </InputSchedule>

    <InputWeektime
      :formScope="distribute"
      v-model="campaign.schedule_time">
    </InputWeektime>

    <h4 id="adgroup">推广单元</h4>

    <el-form-item label="推广单元名称" :prop="getFormScopedField('adgroup.adgroupName')">
      <el-input v-model="adgroup.adgroupName"></el-input>
    </el-form-item>

    <InputGeneralizeType
      :appType="appType"
      :disabled="true"
      v-model="adgroup.generalizeType">
    </InputGeneralizeType>

    <InputAudienceInfo
      id="adgroup-audience"
      :promoteId="promote_id"
      :audience.sync="adgroup.audience"
      :audienceTargeting.sync="adgroup.audience_targeting">
    </InputAudienceInfo>

    <InputDistrict
      v-model="district">
    </InputDistrict>

    <InputGender
      v-model="adgroup.gender">
    </InputGender>

    <InputAge
      v-model="adgroup.age">
    </InputAge>

    <InputUserTarget
      :promoteId="promote_id"
      :userTargeting.sync="adgroup.user_targeting"
      :interest.sync="adgroup.interest"
      :word.sync="adgroup.word"
      :intelliTargeting.sync="adgroup.intelli_targeting">
    </InputUserTarget>

    <InputPlatform
      :formScope="distribute"
      :appType="appType"
      :generalizeType="adgroup.generalizeType"
      v-model="adgroup.platform">
    </InputPlatform>

    <InputNetworkEnv
      v-model="adgroup.network_env">
    </InputNetworkEnv>

    <InputConvertMonitorType
      :formScope="distribute"
      v-model="adgroup.convertMonitorType">
    </InputConvertMonitorType>

    <InputAdconvertId
      id="adgroup-adconvert"
      :formScope="distribute"
      :appType="appType"
      :promoteId="promote_id"
      v-model="adgroup.adconvertId">
    </InputAdconvertId>

    <InputOptimizationTarget
      v-model="adgroup.optimizationTarget">
    </InputOptimizationTarget>

    <InputChargeType
      :optimizationTarget="adgroup.optimizationTarget"
      v-model="adgroup.chargeType">
    </InputChargeType>

    <el-form-item id="adgroup-bid" label="第一阶段出价" :prop="getFormScopedField('adgroup.bid')">
      <el-input-number
        v-model="adgroup.bid"
        controls-position="right"
        :step="0.5"
        :min="adgroup.optimizationTarget === 2 ? 8 : 0.5">
        </el-input-number>
        <template v-if="adgroup.optimizationTarget === 2">
          <span>元/千次展现</span>
          <label>请输入8-300之间的数字，精确到小数点后2位，单元出价需小于预算。</label>
        </template>
        <template v-else>
          <span>元/转化</span>
          <label>请输入0.50-999.99之间的数字，精确到小数点后2位，单元出价需小于预算。</label>
        </template>
    </el-form-item>

    <el-form-item label="第二阶段出价" v-if="adgroup.optimizationTarget === 3" :prop="getFormScopedField('adgroup.secondBid')">
      <el-input-number
        v-model="adgroup.secondBid"
        controls-position="right"
        :step="0.5"
        :min="1">
        </el-input-number>
        <span>元/转化</span>
        <label>请输入1-999.99之间的数字，精确到小数点后2位</label>
    </el-form-item>

    <h4 id="creative">广告创意</h4>

    <MaterialBox
      :formScope="distribute"
      v-model="material_box"
      :selectmode="'customize'"
      :promoteId="promote_id"
      :mediaId="mediaId"
      :gameId="gameId"
      @changePosition="handleChangePosition">
    </MaterialBox>

    <el-form-item label="推广来源" :prop="getFormScopedField('creativeSource')">
      <el-input v-model="creativeSource"></el-input>
    </el-form-item>

    <InputHasLandingPage
      :formScope="distribute"
      v-model="has_landing_page">
    </InputHasLandingPage>

    <LandingPageBox
      :formScope="distribute"
      v-if="has_landing_page"
      v-model="landing_box"
      :gameId="game_id"
      :lpCompanyFull="lp_company_full"
      :shellUrl="shell_url"
      :adUrl="ad_url">
    </LandingPageBox>

    <template v-if="['Android', 'H5'].indexOf(appType) !== -1">
      <el-form-item label="点击URL" prop="target_url">
        <el-input disabled v-model="target_url"></el-input>
      </el-form-item>
    </template>

    <template v-else-if="appType === 'iOS'">
      <InputAppId
        :gameList="xStore.data.gameList"
        v-model="game_id">
      </InputAppId>
      <InputAppTrackUrl
        v-model="formNormal.track_url">
      </InputAppTrackUrl>
      <!-- 打开页面 -->
      <template v-if="adgroup.generalizeType === 1">
        <InputAppUrl
          label="目标网址"
          :gameList="xStore.data.gameList"
          v-model="game_id">
        </InputAppUrl>
      </template>
      <!-- APP下载 -->
      <template v-else-if="adgroup.generalizeType === 2">
        <InputAppUrl
          label="点击 URL"
          :gameList="xStore.data.gameList"
          v-model="game_id">
        </InputAppUrl>
        <InputAppUrl
          label="下载 URL"
          :gameList="xStore.data.gameList"
          v-model="game_id">
        </InputAppUrl>
      </template>
    </template>
  </div>
</template>

<script>
import moment from "moment";

import { mapGetters, mapState } from "vuex";
import { mapStateGetSet } from "@/js/utils";
import { FormScope, xStore } from "@/js/mixins";
import { Injector } from "../mixins";

import InputAppId from "./Normal/InputAppId";
import InputAppTrackUrl from "./Normal/InputAppTrackUrl";
import InputAppUrl from "./Normal/InputAppUrl";

import InputAdResourceId from "./Uchc/InputAdResourceId";
import InputBudget from "./Uchc/InputBudget";
import InputRate from "./Uchc/InputRate";
import InputSchedule from "./Uchc/InputSchedule";
import InputWeektime from "./Uchc/InputWeektime";

import InputGeneralizeType from "./Uchc/InputGeneralizeType";
import InputAudienceInfo from "./Uchc/InputAudienceInfo";
import InputDistrict from "./Uchc/InputDistrict";
import InputGender from "./Uchc/InputGender";
import InputAge from "./Uchc/InputAge";
import InputUserTarget from "./Uchc/InputUserTarget";
import InputPlatform from "./Uchc/InputPlatform";
import InputNetworkEnv from "./Uchc/InputNetworkEnv";
import InputConvertMonitorType from "./Uchc/InputConvertMonitorType";
import InputAdconvertId from "./Uchc/InputAdconvertId";
import InputOptimizationTarget from "./Uchc/InputOptimizationTarget";
import InputChargeType from "./Uchc/InputChargeType";

import MaterialBox from "@/js/views/AdMain/components/Normal/MaterialBox";
import InputHasLandingPage from "@/js/views/AdMain/components/Normal/InputHasLandingPage";
import LandingPageBox from "@/js/views/AdMain/components/Normal/LandingPageBox";

export default {
  mixins: [FormScope, xStore, Injector],
  name: "form-uchc-main",
  components: {
    InputAppId,
    InputAppTrackUrl,
    InputAppUrl,

    InputAdResourceId,
    InputBudget,
    InputRate,
    InputSchedule,
    InputWeektime,

    InputGeneralizeType,
    InputAudienceInfo,
    InputDistrict,
    InputGender,
    InputAge,
    InputUserTarget,
    InputPlatform,
    InputNetworkEnv,
    InputConvertMonitorType,
    InputAdconvertId,
    InputOptimizationTarget,
    InputChargeType,

    MaterialBox,
    InputHasLandingPage,
    LandingPageBox
  },
  props: {
    parentRefs: {},
    gameId: {
      type: [Number],
      required: true
    }
  },
  data() {
    return {
      distribute: "Uchc",
      isMounted: false,
      loading: true
    };
  },
  methods: {
    handleChangePosition: function(position) {
      if (position) {
        switch (position.promotion_mode) {
          case 'open_page':
            // 打开页面
            this.adgroup.generalizeType = 1;
            break;
          case 'dl_app':
            // APP下载
            this.adgroup.generalizeType = 2;
            break;
          default:
            break;
        }
      }
    }
  },
  computed: {
    ...mapGetters("adNormal", ["mediaList"]),
    ...mapState("adNormal", {
      stateNormal: state => state,
      formNormal: state => state.form,
      mediaId: state => state.form.media_id,
      downAddr: state => state.form.down_addr,
      settlement: state => state.form.settlement,
      lp_company_full: state => state.form.lp_company_full,
      shell_url: state => state.form.shell_url,
      ad_url: state => state.form.ad_url,
      appType: state => state.form.app_type
    }),
    ...mapState("adUchc", {
      stateUchc: state => state,
      formUchc: state => state.form,
      campaign: state => state.form.campaign,
      adgroup: state => state.form.adgroup
    }),
    material_box: {
      get: function() {
        return {
          is_multi_materials: this.formNormal.is_multi_materials,
          position_id: this.formNormal.position_id,
          style_ids: this.formNormal.style_ids,
          material_info: this.formNormal.material_info,
          procedural_content: this.formUchc.procedural_content
        };
      },
      set: function(payload, dispatch) {
        this.$store.dispatch(
          this.stateNormal.getActionName("form.is_multi_materials"),
          payload.is_multi_materials
        );
        this.$store.dispatch(
          this.stateNormal.getActionName("form.position_id"),
          payload.position_id
        );
        this.$store.dispatch(
          this.stateNormal.getActionName("form.style_ids"),
          payload.style_ids
        );
        this.$store.dispatch(
          this.stateNormal.getActionName("form.material_info"),
          payload.material_info
        );
        this.$store.dispatch(
          this.stateUchc.getActionName("form.procedural_content"),
          payload.procedural_content
        );
      }
    },
    ...mapStateGetSet("adNormal", {
      ad_name: {
        get: state => state.form.ad_name,
        set: "form.ad_name"
      },
      promote_id: {
        get: state => state.form.promote_id,
        set: "form.promote_id"
      },
      game_id: {
        get: state => state.form.game_id,
        set: "form.game_id"
      },
      app_type: {
        get: state => state.form.app_type,
        set: "form.app_type"
      },
      is_multi_materials: {
        get: state => state.form.is_multi_materials,
        set: "form.is_multi_materials"
      },
      has_landing_page: {
        get: state => state.form.has_landing_page,
        set: "form.has_landing_page"
      },
      landing_box: {
        get: state => {
          return {
            template_id: state.form.template_id,
            template_info: state.form.template_info
          };
        },
        set: function(payload, dispatch) {
          dispatch("form.template_id", payload.template_id);
          dispatch("form.template_info", payload.template_info);
        }
      },
      style_ids: {
        get: state => state.form.style_ids,
        set: "form.style_ids"
      },
      position_id: {
        get: state => state.form.position_id,
        set: "form.position_id"
      },
      target_url: {
        get: state => state.form.target_url,
        set: "form.target_url"
      }
    }),
    ...mapStateGetSet("adUchc", {
      campaign: {
        get: state => state.form.campaign,
        set: "form.campaign"
      },
      adgroup: {
        get: state => state.form.adgroup,
        set: "form.adgroup"
      },
      secondBid: {
        get: state => state.form.secondBid,
        set: "form.secondBid"
      },
      creativeSource: {
        get: state => state.form.creativeSource,
        set: "form.creativeSource"
      }
    }),
    schedule_date: {
      get: function() {
        return {
          startDate: this.campaign.startDate,
          endDate: this.campaign.endDate
        };
      },
      set: function(val) {
        this.campaign.startDate = val.startDate;
        this.campaign.endDate = val.endDate;
        this.$store.dispatch(
          this.stateUchc.getActionName("form.campaign"),
          this.campaign
        );
      }
    },
    district: {
      get: function() {
        return {
          all_region: this.adgroup.all_region,
          region: this.adgroup.region
        };
      },
      set: function(val) {
        this.adgroup.all_region = val.all_region;
        this.adgroup.region = val.region;
        this.$store.dispatch(
          this.stateUchc.getActionName("form.adgroup"),
          this.adgroup
        );
      }
    },
    hasSyncCampaign: function() {
      return this.formUchc.campaignId > 0;
    },
    hasSyncAdgroup: function() {
      return this.formUchc.adgroupId > 0;
    }
  },
  watch: {
    campaign: {
      handler: function() {
        this.$store.dispatch(
          this.stateUchc.getActionName("form.campaign"),
          this.campaign
        );
      },
      deep: true
    },
    adgroup: {
      handler: function() {
        this.$store.dispatch(
          this.stateUchc.getActionName("form.adgroup"),
          this.adgroup
        );
      },
      deep: true
    },
    "adgroup.optimizationTarget": {
      handler: function(val) {
        if (val == 2 && this.adgroup.bid < 8) {
          this.adgroup.bid = 8;
        }
      }
    }
  },
  mounted() {
    if (this.campaign.campaignName === "") {
      this.campaign.campaignName = moment().format("MMDD") + this.ad_name;
    }
    if (this.adgroup.adgroupName === "") {
      this.adgroup.adgroupName = moment().format("MMDD") + this.ad_name;
    }
    this.loading = false;
    this.isMounted = true;
  }
};
</script>
