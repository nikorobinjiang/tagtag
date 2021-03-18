<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>广告模板</el-breadcrumb-item>
          <el-breadcrumb-item>今日头条</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-form">
      <el-form ref="form" :model="form" :rules="rules" label-width="100px" size="mini">
        <h2>{{ formTitle }}模板</h2>

        <el-form-item label="模板名称" prop="title">
          <el-input v-model="title" value=""></el-input>
        </el-form-item>

        <h4>广告计划</h4>

        <InputDistrict
          v-model="district">
        </InputDistrict>

        <InputGender
          v-model="gender">
        </InputGender>

        <InputAge
          v-model="age">
        </InputAge>

        <InputAdTags
          v-model="ad_tags">
        </InputAdTags>

        <InputAppType
          v-if="false"
          v-model="app_type">
        </InputAppType>

        <InputPlatform
          v-model="platform">
        </InputPlatform>

        <InputNetwork
          v-model="ac">
        </InputNetwork>

        <InputOsvAndroid
          v-model="android_osv">
        </InputOsvAndroid>
        <InputOsvIos
          v-model="ios_osv">
        </InputOsvIos>

        <InputSchedule
          :scheduleType.sync="schedule_type"
          :startTime.sync="start_time"
          :endTime.sync="end_time">
        </InputSchedule>

        <InputWeektime
          v-model="schedule_time">
        </InputWeektime>

        <InputFlowControlMode
          v-model="flow_control_mode">
        </InputFlowControlMode>

        <h4>广告创意</h4>

        <InputAdInventory
          :smartInventory.sync="smart_inventory"
          :inventoryType.sync="inventory_type">
        </InputAdInventory>

        <InputSelectMode
          v-model="selectmode">
        </InputSelectMode>

        <InputCommentDisabled
          v-model="is_comment_disable">
        </InputCommentDisabled>

        <InputVideoDetailClose
          v-model="close_video_detail">
        </InputVideoDetailClose>

        <InputAdCategory
          v-model="ad_category">
        </InputAdCategory>

        <InputKeywords
          placeholder="空格分隔，最多二十个，每个标签不超过10个字符"
          v-model="ad_keywords">
        </InputKeywords>

        <el-form-item>
          <el-button class="gm-cancel" v-if="false">取消</el-button>
          <el-button type="primary" :disabled="submitDisabled" @click="submitForm">确认</el-button>
        </el-form-item>
      </el-form>
    </el-row>
  </div>
</template>

<script>
import { mixinCommon } from "./mixins";
import { mapStateGetSet } from "@/js/utils";

import InputDistrict from "@/js/views/AdMain/components/ToutiaoPlan/InputDistrict";
import InputGender from "@/js/views/AdMain/components/ToutiaoPlan/InputGender";
import InputAge from "@/js/views/AdMain/components/ToutiaoPlan/InputAge";
import InputAdTags from "@/js/views/AdMain/components/ToutiaoPlan/InputAdTags";
import InputAppType from "@/js/views/AdMain/components/ToutiaoPlan/InputAppType";
import InputPlatform from "@/js/views/AdMain/components/ToutiaoPlan/InputPlatform";
import InputNetwork from "@/js/views/AdMain/components/ToutiaoPlan/InputNetwork";
import InputOsvAndroid from "@/js/views/AdMain/components/ToutiaoPlan/InputOsvAndroid";
import InputOsvIos from "@/js/views/AdMain/components/ToutiaoPlan/InputOsvIos";
import InputFlowControlMode from "@/js/views/AdMain/components/ToutiaoPlan/InputFlowControlMode";
import InputSchedule from "@/js/views/AdMain/components/ToutiaoPlan/InputSchedule";
import InputWeektime from "@/js/views/AdMain/components/ToutiaoPlan/InputWeektime";

import InputAdInventory from "@/js/views/AdMain/components/ToutiaoCreative/InputAdInventory";
import InputSelectMode from "@/js/views/AdMain/components/ToutiaoCreative/InputSelectMode";
import InputCommentDisabled from "@/js/views/AdMain/components/ToutiaoCreative/InputCommentDisabled";
import InputVideoDetailClose from "@/js/views/AdMain/components/ToutiaoCreative/InputVideoDetailClose";
import InputAdCategory from "@/js/views/AdMain/components/ToutiaoCreative/InputAdCategory";
import InputKeywords from "@/js/views/AdMain/components/ToutiaoCreative/InputKeywords";

export default {
  mixins: [mixinCommon],
  name: "gmp-tpl-toutiao-tpl-form",
  components: {
    InputDistrict,
    InputGender,
    InputAge,
    InputAdTags,
    InputAppType,
    InputPlatform,
    InputNetwork,
    InputOsvAndroid,
    InputOsvIos,
    InputFlowControlMode,
    InputSchedule,
    InputWeektime,
    InputAdInventory,
    InputSelectMode,
    InputCommentDisabled,
    InputVideoDetailClose,
    InputAdCategory,
    InputKeywords
  },
  props: {
    requestAction: {
      type: [String],
      required: true
    },
    formAction: {
      type: [String],
      required: true
    },
    formMethod: {
      type: [String],
      required: true
    },
    it: {
      default: null
    }
  },
  data: function() {
    return {
      submitDisabled: false,
      data: {}
    };
  },
  methods: {
    submitForm(btn) {
      this.submitDisabled = true;
      (this.formMethod === "PUT" ? axios.put : axios.post)(this.formAction, {
        data: this.form
      })
        .then(response => {
          if (response.data.result == "error") {
            this.$alert(response.data.message, "", {
              confirmButtonText: "确定"
            });
          } else {
            this.$alert("保存成功", "", {
              confirmButtonText: "确定",
              callback: _ => {
                location.reload();
              }
            });
            setTimeout(function() {
              location.reload();
            }, 3000);
          }
          this.submitDisabled = false;
        })
        .catch(error => {
          console.log("error", error);
          this.submitDisabled = false;
        });
    }
  },
  computed: {
    state() {
      return this.$store.state.tplToutiaoTpl;
    },
    form() {
      return this.state.form;
    },
    rules() {
      return this.state.rules;
    },
    ...mapStateGetSet("tplToutiaoTpl", {
      title: {
        get: state => state.form.title,
        set: "form.title"
      },
      gender: {
        get: state => state.form.gender,
        set: "form.gender"
      },
      age: {
        get: state => state.form.age,
        set: "form.age"
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
      schedule_time: {
        get: state => state.form.schedule_time,
        set: "form.schedule_time"
      },
      flow_control_mode: {
        get: state => state.form.flow_control_mode,
        set: "form.flow_control_mode"
      },
      smart_inventory: {
        get: state => state.form.smart_inventory,
        set: "form.smart_inventory"
      },
      inventory_type: {
        get: state => state.form.inventory_type,
        set: "form.inventory_type"
      },
      selectmode: {
        get: state => state.form.selectmode,
        set: "form.selectmode"
      },
      is_comment_disable: {
        get: state => state.form.is_comment_disable,
        set: "form.is_comment_disable"
      },
      close_video_detail: {
        get: state => state.form.close_video_detail,
        set: "form.close_video_detail"
      },
      ad_category: {
        get: state => state.form.ad_category,
        set: "form.ad_category"
      },
      ad_keywords: {
        get: state => state.form.ad_keywords,
        set: "form.ad_keywords"
      }
    })
  },
  created() {
    this.$store.dispatch(this.state.getActionName("clear"));
    if (this.it) {
      let it = JSON.parse(this.it);
      this.$store.dispatch(this.state.getActionName("assign_form"), it);
    }
  }
};
</script>
