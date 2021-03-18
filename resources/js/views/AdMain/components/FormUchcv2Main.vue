<template>
  <div
    v-if="isMounted"
    v-loading="loading"
    v-bind:class="['ad-main-form-toutiao-main']">

    <h4>推广组</h4>

    <el-form-item label="推广组名称" :prop="getFormScopedField('ad_group.name')">
      <el-input v-model="adGroupCal.name"></el-input>
    </el-form-item>

    <InputObjectiveType
      v-model="adGroupCal.objectiveType"
      :appType="adCal.app_type">
    </InputObjectiveType>

    <h4>推广计划</h4>

    <el-form-item label="计划名称" :prop="getFormScopedField('campaign.name')">
      <el-input v-model="campaignCal.name"></el-input>
    </el-form-item>

    <h5>推广目标</h5>

    <InputOptimizationTarget
      v-model="campaignCal.optTarget"
      :campaign="campaignCal">
    </InputOptimizationTarget>

    <InputDelivery
      v-model="campaignCal.delivery">
    </InputDelivery>

    <h5>转化方式</h5>

    <InputConvertType
      :formScope="distribute"
      v-model="campaignCal.convertType">
    </InputConvertType>

    <InputAdConvertId
      :formScope="distribute"
      :promoteId="adCal.promote_id"
      v-model="campaignCal.adConvertId">
    </InputAdConvertId>

    <h5>定向设置</h5>

    <InputAudienceInfo
      :promoteId="adCal.promote_id"
      :audience.sync="campaignCal.audience"
      :audienceTargeting.sync="campaignCal.audienceTargeting">
    </InputAudienceInfo>

    <InputDistrict
      :all-region.sync="campaignCal.allRegion"
      :region.sync="campaignCal.region">
    </InputDistrict>

    <InputGender
      v-model="campaignCal.gender">
    </InputGender>

    <InputAge
      v-model="campaignCal.age">
    </InputAge>

    <InputPlatform
      :formScope="distribute"
      v-model="campaignCal.platform">
    </InputPlatform>

    <InputNetworkEnv
      v-model="campaignCal.networkEnv">
    </InputNetworkEnv>

    <InputUserTarget
      :promoteId="adCal.promote_id"
      :interest.sync="campaignCal.interest"
      :word.sync="campaignCal.word">
    </InputUserTarget>

    <InputIntelliTargeting
      v-model="campaignCal.intelliTargeting">
    </InputIntelliTargeting>

    <h5>排期和出价</h5>

    <InputBudget
      v-model="campaignCal.budget">
    </InputBudget>

    <InputSchedule
      :startDate.sync="campaignCal.startDate"
      :endDate.sync="campaignCal.endDate">
    </InputSchedule>

    <InputWeektime
      :formScope="distribute"
      v-model="campaignCal.scheduleTime">
    </InputWeektime>

    <InputChargeType
      :optimizationTarget="campaignCal.optTarget"
      v-model="campaignCal.chargeType">
    </InputChargeType>

    <el-form-item
      label="第一阶段出价"
      :prop="getFormScopedField('campaign.bid')">
      <el-input-number
        v-model="campaignCal.bid"
        controls-position="right"
        :step="0.5"
        :min="campaignCal.optTarget === 2 ? 8 : 0.5">
        </el-input-number>
        <template v-if="campaignCal.optTarget === 2">
          <span>元/千次展现</span>
          <label>请输入8-300之间的数字，精确到小数点后2位，单元出价需小于预算。</label>
        </template>
        <template v-else>
          <span>元/转化</span>
          <label>请输入0.50-999.99之间的数字，精确到小数点后2位，单元出价需小于预算。</label>
        </template>
    </el-form-item>

    <el-form-item
      label="第二阶段出价"
      v-if="campaignCal.optTarget === 3"
      :prop="getFormScopedField('campaign.optBid')">
      <el-input-number
        v-model="campaignCal.optBid"
        controls-position="right"
        :step="0.5"
        :min="1">
        </el-input-number>
        <span>元/转化</span>
        <label>请输入1-999.99之间的数字，精确到小数点后2位</label>
    </el-form-item>
    
    <InputSlotBidding
      v-model="campaignCal.slotBidding">
    </InputSlotBidding>

    <h4>广告创意</h4>

    <MaterialBox
      :formScope="distribute"
      v-model="material_box"
      :selectmode="'customize'"
      :promoteId="adCal.promote_id"
      :mediaId="adCal.media_id"
      :gameId="adCal.game_id">
    </MaterialBox>

    <el-form-item label="推广来源" :prop="getFormScopedField('creativeSource')">
      <el-input v-model="uchcv2Cal.creativeSource"></el-input>
    </el-form-item>

    <template v-if="['Android', 'H5'].indexOf(adCal.app_type) !== -1">
      <InputHasLandingPage
        :formScope="distribute"
        v-model="adCal.has_landing_page">
      </InputHasLandingPage>

      <LandingPageBox
        :formScope="distribute"
        v-if="adCal.has_landing_page"
        v-model="landing_box"
        :gameId="adCal.game_id"
        :lpCompanyFull="adCal.lp_company_full"
        :shellUrl="adCal.shell_url"
        :adUrl="adCal.ad_url">
      </LandingPageBox>

      <el-form-item label="点击URL" prop="target_url">
        <el-input disabled v-model="adCal.target_url"></el-input>
      </el-form-item>
    </template>

    <template v-else-if="adCal.app_type === 'iOS'">
      <InputAppId
        :gameList="xStore.data.gameList"
        v-model="adCal.game_id">
      </InputAppId>
      <InputAppTrackUrl
        v-model="adCal.track_url">
      </InputAppTrackUrl>
      <!-- 打开页面 -->
      <template v-if="adGroupCal.generalizeType === 1">
        <InputAppUrl
          label="目标网址"
          :gameList="xStore.data.gameList"
          v-model="adCal.game_id">
        </InputAppUrl>
      </template>
      <!-- APP下载 -->
      <template v-else-if="adGroupCal.generalizeType === 2">
        <InputAppUrl
          label="点击 URL"
          :gameList="xStore.data.gameList"
          v-model="adCal.game_id">
        </InputAppUrl>
        <InputAppUrl
          label="下载 URL"
          :gameList="xStore.data.gameList"
          v-model="adCal.game_id">
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

import InputSlotBidding from "./Uchcv2/InputSlotBidding";
import InputBudget from "./Uchcv2/InputBudget";
import InputDelivery from "./Uchcv2/InputDelivery";
import InputSchedule from "./Uchcv2/InputSchedule";
import InputWeektime from "./Uchcv2/InputWeektime";

import InputObjectiveType from "./Uchcv2/InputObjectiveType";
import InputAudienceInfo from "./Uchcv2/InputAudienceInfo";
import InputDistrict from "./Uchcv2/InputDistrict";
import InputGender from "./Uchcv2/InputGender";
import InputAge from "./Uchcv2/InputAge";
import InputUserTarget from "./Uchcv2/InputUserTarget";
import InputIntelliTargeting from "./Uchcv2/InputIntelliTargeting";
import InputPlatform from "./Uchcv2/InputPlatform";
import InputNetworkEnv from "./Uchcv2/InputNetworkEnv";
import InputConvertType from "./Uchcv2/InputConvertType";
import InputAdConvertId from "./Uchcv2/InputAdConvertId";
import InputOptimizationTarget from "./Uchcv2/InputOptimizationTarget";
import InputChargeType from "./Uchcv2/InputChargeType";

import MaterialBox from "@/js/views/AdMain/components/Normal/MaterialBox";
import InputHasLandingPage from "@/js/views/AdMain/components/Normal/InputHasLandingPage";
import LandingPageBox from "@/js/views/AdMain/components/Normal/LandingPageBox";

export default {
  mixins: [FormScope, xStore, Injector],
  name: "FormUchcv2Main",
  components: {
    InputAppId,
    InputAppTrackUrl,
    InputAppUrl,

    InputSlotBidding,
    InputBudget,
    InputDelivery,
    InputSchedule,
    InputWeektime,

    InputObjectiveType,
    InputAudienceInfo,
    InputDistrict,
    InputGender,
    InputAge,
    InputUserTarget,
    InputIntelliTargeting,
    InputPlatform,
    InputNetworkEnv,
    InputConvertType,
    InputAdConvertId,
    InputOptimizationTarget,
    InputChargeType,

    MaterialBox,
    InputHasLandingPage,
    LandingPageBox
  },
  props: {
    parentRefs: {},
    ad: {
      type: Object,
      required: true
    },
    uchcv2: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      distribute: "Uchcv2",
      isMounted: false,
      loading: true
    };
  },
  methods: {},
  computed: {
    adCal: {
      get() {
        return this.ad
      },
      set(payload) {
        this.$emit('update:ad', payload)
      }
    },
    uchcv2Cal: {
      get() {
        return this.uchcv2
      },
      set(payload) {
        this.$emit('update:uchcv2', payload)
      }
    },
    adGroupCal: {
      get() {
        return this.uchcv2Cal.ad_group
      },
      set(payload) {
        this.$set(this.uchcv2Cal, 'ad_group', payload)
      }
    },
    campaignCal: {
      get() {
        return this.uchcv2Cal.campaign
      },
      set(payload) {
        this.$set(this.uchcv2Cal, 'campaign', payload)
      }
    },
    material_box: {
      get() {
        return {
          is_multi_materials: this.adCal.is_multi_materials,
          position_id: this.adCal.position_id,
          style_ids: this.adCal.style_ids,
          material_info: this.adCal.material_info,
          procedural_content: this.uchcv2Cal.procedural_content
        };
      },
      set(payload) {
        this.adCal.is_multi_materials = payload.is_multi_materials
        this.adCal.position_id = payload.position_id
        this.adCal.style_ids = payload.style_ids
        this.adCal.material_info = payload.material_info
        this.uchcv2Cal.procedural_content = payload.procedural_content
      }
    },
    landing_box: {
      get() {
        return {
          template_id: this.adCal.template_id,
          template_info: this.adCal.template_info
        };
      },
      set(payload) {
        this.adCal.template_id = payload.template_id
        this.adCal.template_info = payload.template_info
      }
    },
    schedule_date: {
      get() {
        return {
          startDate: this.campaignCal.startDate,
          endDate: this.campaignCal.endDate
        };
      },
      set: function(payload) {
        this.campaignCal.startDate = payload.startDate;
        this.campaignCal.endDate = payload.endDate;
        this.$store.dispatch(
          this.stateUchc.getActionName("form.campaign"),
          this.campaign
        );
      }
    },
    district: {
      get: function() {
        return {
          allRegion: this.campaignCal.allRegion,
          region: this.campaignCal.region
        };
      },
      set: function(payload) {
        this.campaignCal.allRegion = payload.allRegion;
        this.campaignCal.region = payload.region;
      }
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
    "adGroupCal.optTarget": {
      handler: function(val) {
        if (val == 2 && this.adGroupCal.bid < 8) {
          this.adGroupCal.bid = 8;
        }
      }
    }
  },
  mounted() {
    if (this.adGroupCal.name === "") {
      this.adGroupCal.name = moment().format("MMDD") + this.adCal.ad_name;
    }
    if (this.campaignCal.name === "") {
      this.campaignCal.name = moment().format("MMDD") + this.adCal.ad_name;
    }
    this.loading = false;
    this.isMounted = true;
  }
};
</script>
