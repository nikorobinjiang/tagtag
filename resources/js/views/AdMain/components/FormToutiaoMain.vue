<template>
  <div
    v-if="isMounted"
    v-loading="loading"
    v-bind:class="['ad-main-form-toutiao-main']">

    <h4 id="campaign">广告组</h4>

    <el-form-item label="推广目的">
      <el-radio disabled value='checked' label="checked" v-if="parentRefs.InputAdGroup.curItem.landing_type_label">
        {{ parentRefs.InputAdGroup.curItem.landing_type_label }}
      </el-radio>
    </el-form-item>
    <el-form-item label="预算">
      <el-radio disabled value='checked' label="checked" v-if="parentRefs.InputAdGroup.curItem.budget_mode_label">
        {{ parentRefs.InputAdGroup.curItem.budget_mode_label }}
        <span v-if="parentRefs.InputAdGroup.curItem.budget_mode !== 'BUDGET_MODE_INFINITE'">
          {{ parentRefs.InputAdGroup.curItem.budget }}
        </span>
      </el-radio>
    </el-form-item>
    <el-form-item label="广告组名称">
      <span style="color: #c0c4cc">{{ parentRefs.InputAdGroup.curItem.label }}</span>
    </el-form-item>

    <h4 id="adgroup">广告计划</h4>

    <el-form-item label="计划名称（广告名称）" prop="ad_name">
      <el-input v-model="ad_name"></el-input>
    </el-form-item>

    <el-form-item label="下载链接（壳地址）">
      <el-input disabled v-model="downAddr">
        <el-button slot="append" v-clipboard="{ text: downAddr }">复制</el-button>
      </el-input>
    </el-form-item>

    <template v-if="['OCPC', 'OCPM'].indexOf(settlement) !== -1">
      <InputConvert
        id="adgroup-adconvert"
        :formScope="distribute"
        v-model="convert_id"
        :distribute="distribute"
        :promoteId="promote_id"
        :gameId="game_id">
      </InputConvert>
    </template>

    <InputDistrict
      :formScope="distribute"
      v-model="district">
    </InputDistrict>

    <InputGender
      :formScope="distribute"
      v-model="gender">
    </InputGender>

    <InputAge
      :formScope="distribute"
      v-model="age">
    </InputAge>

    <InputAudienceInfo
      id="adgroup-targeting"
      :formScope="distribute"
      :promoteId="promote_id"
      :retargetingTagsInclude.sync="retargeting_tags_include"
      :retargetingTagsExclude.sync="retargeting_tags_exclude">
    </InputAudienceInfo>

    <InputAdTags
      :formScope="distribute"
      v-model="ad_tags">
    </InputAdTags>

    <InputAppType
      v-if="false"
      :formScope="distribute"
      v-model="app_type">
    </InputAppType>

    <InputPlatform
      :formScope="distribute"
      v-model="platform">
    </InputPlatform>

    <InputNetwork
      :formScope="distribute"
      v-model="ac">
    </InputNetwork>

    <InputOsvAndroid
      v-if="['Android', 'H5'].indexOf(appType) !== -1"
      :formScope="distribute"
      v-model="android_osv">
    </InputOsvAndroid>
    <InputOsvIos
      v-else-if="appType === 'iOS'"
      :formScope="distribute"
      v-model="ios_osv">
    </InputOsvIos>

    <InputBudget
      id="adgroup-budget"
      :formScope="distribute"
      :settlement="settlement"
      v-model="budget">
    </InputBudget>

    <InputSchedule
      id="adgroup-schedule"
      :formScope="distribute"
      :scheduleType.sync="schedule_type"
      :startTime.sync="start_time"
      :endTime.sync="end_time">
    </InputSchedule>

    <InputWeektime
      :formScope="distribute"
      v-model="schedule_time">
    </InputWeektime>

    <InputFlowControlMode
      :formScope="distribute"
      v-model="flow_control_mode">
    </InputFlowControlMode>

    <InputBid
      v-if="['OCPC', 'OCPM'].indexOf(settlement) === -1"
      :formScope="distribute"
      v-model="bid">
    </InputBid>

    <InputCpaBid
      id="adgroup-bid"
      v-if="['OCPC', 'OCPM'].indexOf(settlement) !== -1"
      :formScope="distribute"
      v-model="cpa_bid">
    </InputCpaBid>

    <h4 id="creative">广告创意</h4>

    <InputAdInventory
      :formScope="distribute"
      :smartInventory.sync="smart_inventory"
      :inventoryType.sync="inventory_type">
    </InputAdInventory>

    <InputSelectMode
      id="creative-mode"
      :disabled="! canSelectmode"
      v-model="selectmode">
    </InputSelectMode>

    <MaterialBox
      :formScope="distribute"
      v-model="material_box"
      :promoteId="promote_id"
      :selectmode="calSelectmode"
      :mediaId="mediaId"
      :gameId="gameId">
    </MaterialBox>

    <InputCommentDisabled
      v-model="is_comment_disable">
    </InputCommentDisabled>

    <InputVideoDetailClose
      v-model="close_video_detail">
    </InputVideoDetailClose>

    <InputAppWebUrl
      id="creative-web-url"
      v-if="appType !== 'iOS'"
      :formScope="distribute"
      v-model="web_url">
    </InputAppWebUrl>

    <InputAppName
      :formScope="distribute"
      v-model="app_name">
    </InputAppName>

    <InputAppSubtitle
      :formScope="distribute"
      v-model="sub_title">
    </InputAppSubtitle>

    <InputAdCategory
      :formScope="distribute"
      v-model="ad_category">
    </InputAdCategory>

    <InputKeywords
      :formScope="distribute"
      placeholder="必填，空格分隔，最多二十个，每个标签不超过10个字符"
      v-model="ad_keywords">
    </InputKeywords>

  </div>
</template>

<script>
import { mapGetters, mapState } from "vuex";
import { mapStateGetSet } from "@/js/utils";
import { FormScope, xStore } from "@/js/mixins";
import { Injector } from "../mixins";

import InputCpaBid from "@/js/views/AdMain/components/ToutiaoPlan/InputCpaBid";
import InputConvert from "@/js/views/AdMain/components/ToutiaoPlan/InputConvert";
import InputDistrict from "@/js/views/AdMain/components/ToutiaoPlan/InputDistrict";
import InputGender from "@/js/views/AdMain/components/ToutiaoPlan/InputGender";
import InputAge from "@/js/views/AdMain/components/ToutiaoPlan/InputAge";
import InputAudienceInfo from "@/js/views/AdMain/components/ToutiaoPlan/InputAudienceInfo";
import InputAdTags from "@/js/views/AdMain/components/ToutiaoPlan/InputAdTags";
import InputAppType from "@/js/views/AdMain/components/ToutiaoPlan/InputAppType";
import InputPlatform from "@/js/views/AdMain/components/ToutiaoPlan/InputPlatform";
import InputNetwork from "@/js/views/AdMain/components/ToutiaoPlan/InputNetwork";
import InputOsvAndroid from "@/js/views/AdMain/components/ToutiaoPlan/InputOsvAndroid";
import InputOsvIos from "@/js/views/AdMain/components/ToutiaoPlan/InputOsvIos";
import InputBudget from "@/js/views/AdMain/components/ToutiaoPlan/InputBudget";
import InputSchedule from "@/js/views/AdMain/components/ToutiaoPlan/InputSchedule";
import InputWeektime from "@/js/views/AdMain/components/ToutiaoPlan/InputWeektime";
import InputFlowControlMode from "@/js/views/AdMain/components/ToutiaoPlan/InputFlowControlMode";
import InputBid from "@/js/views/AdMain/components/ToutiaoPlan/InputBid";

import InputAdInventory from "@/js/views/AdMain/components/ToutiaoCreative/InputAdInventory";
import InputSelectMode from "@/js/views/AdMain/components/ToutiaoCreative/InputSelectMode";
import MaterialBox from "@/js/views/AdMain/components/Normal/MaterialBox";
import InputAppWebUrl from "@/js/views/AdMain/components/ToutiaoCreative/InputAppWebUrl";
import InputAppName from "@/js/views/AdMain/components/ToutiaoCreative/InputAppName";
import InputAppSubtitle from "@/js/views/AdMain/components/ToutiaoCreative/InputAppSubtitle";
import InputCommentDisabled from "@/js/views/AdMain/components/ToutiaoCreative/InputCommentDisabled";
import InputVideoDetailClose from "@/js/views/AdMain/components/ToutiaoCreative/InputVideoDetailClose";
import InputAdCategory from "@/js/views/AdMain/components/ToutiaoCreative/InputAdCategory";
import InputKeywords from "@/js/views/AdMain/components/ToutiaoCreative/InputKeywords";

export default {
  mixins: [FormScope, xStore, Injector],
  name: "form-toutiao-main",
  components: {
    InputCpaBid,
    InputConvert,
    InputDistrict,
    InputGender,
    InputAge,
    InputAudienceInfo,
    InputAdTags,
    InputAppType,
    InputPlatform,
    InputNetwork,
    InputOsvAndroid,
    InputOsvIos,
    InputBudget,
    InputSchedule,
    InputWeektime,
    InputFlowControlMode,
    InputBid,

    InputAdInventory,
    InputSelectMode,
    MaterialBox,
    InputAppWebUrl,
    InputAppName,
    InputAppSubtitle,
    InputCommentDisabled,
    InputVideoDetailClose,
    InputAdCategory,
    InputKeywords
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
      distribute: "Toutiao",
      isMounted: false,
      loading: true
    };
  },
  methods: {},
  watch: {},
  computed: {
    ...mapGetters("adNormal", ["mediaList"]),
    ...mapState("adNormal", {
      stateNormal: state => state,
      formNormal: state => state.form,
      mediaId: state => state.form.media_id,
      downAddr: state => state.form.down_addr,
      settlement: state => state.form.settlement,
      appType: state => state.form.app_type
    }),
    ...mapState("adToutiao", {
      stateToutiao: state => state,
      formToutiao: state => state.form
    }),
    material_box: {
      get: function() {
        return {
          is_multi_materials: this.formNormal.is_multi_materials,
          position_id: this.formNormal.position_id,
          style_ids: this.formNormal.style_ids,
          material_info: this.formNormal.material_info,
          procedural_content: this.formToutiao.procedural_content
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
          this.stateToutiao.getActionName("form.procedural_content"),
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
      is_multi_materials: {
        get: state => state.form.is_multi_materials,
        set: "form.is_multi_materials"
      },
      style_ids: {
        get: state => state.form.style_ids,
        set: "form.style_ids"
      },
      position_id: {
        get: state => state.form.position_id,
        set: "form.position_id"
      },
      canSelectmode: {
        get: state => state.form.can_selectmode,
        set: "form.can_selectmode"
      }
    }),
    ...mapStateGetSet("adToutiao", {
      cpa_bid: {
        get: state => state.form.cpa_bid,
        set: "form.cpa_bid"
      },
      convert_id: {
        get: state => state.form.convert_id,
        set: "form.convert_id"
      },
      age: {
        get: state => state.form.age,
        set: "form.age"
      },
      retargeting_tags_include: {
        get: state => state.form.retargeting_tags_include,
        set: "form.retargeting_tags_include"
      },
      retargeting_tags_exclude: {
        get: state => state.form.retargeting_tags_exclude,
        set: "form.retargeting_tags_exclude"
      },
      district: {
        get: state => {
          return {
            district: state.form.district,
            location_type: state.form.location_type,
            city: state.form.city
          };
        },
        set: function(payload, dispatch) {
          dispatch("form.district", payload.district);
          dispatch("form.location_type", payload.location_type);
          dispatch("form.city", payload.city);
        }
      },
      gender: {
        get: state => state.form.gender,
        set: "form.gender"
      },
      ad_tags: {
        get: state => {
          return {
            ad_tag_type: state.form.ad_tag_type,
            ad_tag: state.form.ad_tag
          };
        },
        set: function(payload, dispatch) {
          dispatch("form.ad_tag_type", payload.ad_tag_type);
          dispatch("form.ad_tag", payload.ad_tag);
        }
      },
      app_type: {
        get: state => state.form.app_type,
        set: "form.app_type"
      },
      platform: {
        get: state => state.form.platform,
        set: "form.platform"
      },
      ac: {
        get: state => state.form.ac,
        set: "form.ac"
      },
      android_osv: {
        get: state => state.form.android_osv,
        set: "form.android_osv"
      },
      ios_osv: {
        get: state => state.form.ios_osv,
        set: "form.ios_osv"
      },
      budget: {
        get: state => {
          return {
            budget_mode: state.form.budget_mode,
            budget: state.form.budget
          };
        },
        set: function(payload, dispatch) {
          dispatch("form.budget_mode", payload.budget_mode);
          dispatch("form.budget", payload.budget);
        }
      },
      schedule_type: {
        get: state => state.form.schedule_type,
        set: "form.schedule_type"
      },
      start_time: {
        get: state => state.form.start_time,
        set: "form.start_time"
      },
      end_time: {
        get: state => state.form.end_time,
        set: "form.end_time"
      },
      schedule_time: {
        get: state => state.form.schedule_time,
        set: "form.schedule_time"
      },
      flow_control_mode: {
        get: state => state.form.flow_control_mode,
        set: "form.flow_control_mode"
      },
      selectmode: {
        get: state => state.form.selectmode,
        set: "form.selectmode"
      },
      bid: {
        get: state => state.form.bid,
        set: "form.bid"
      },
      is_comment_disable: {
        get: state => state.form.is_comment_disable,
        set: "form.is_comment_disable"
      },
      close_video_detail: {
        get: state => state.form.close_video_detail,
        set: "form.close_video_detail"
      },
      smart_inventory: {
        get: state => state.form.smart_inventory,
        set: "form.smart_inventory"
      },
      inventory_type: {
        get: state => state.form.inventory_type,
        set: "form.inventory_type"
      },
      web_url: {
        get: state => state.form.web_url,
        set: "form.web_url"
      },
      app_name: {
        get: state => state.form.app_name,
        set: "form.app_name"
      },
      sub_title: {
        get: state => state.form.sub_title,
        set: "form.sub_title"
      },
      ad_category: {
        get: state => state.form.ad_category,
        set: "form.ad_category"
      },
      ad_keywords: {
        get: state => state.form.ad_keywords,
        set: "form.ad_keywords"
      }
    }),
    calSelectmode: function() {
      if (!this.canSelectmode) {
        return "customize";
      }
      return this.selectmode;
    }
  },
  mounted() {
    this.loading = false;
    this.isMounted = true;
  }
};
</script>
