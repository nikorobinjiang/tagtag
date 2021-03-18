<template>
  <div v-loading="loading">
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>广告模板</el-breadcrumb-item>
          <el-breadcrumb-item>UC头条</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-form" v-if="!loading">
      <el-form ref="scatForm" :model="form" :rules="rules" label-width="120px" size="mini">
        <el-form-item label="模板名称" prop="title">
          <el-input v-model="title" value=""></el-input>
        </el-form-item>

        <h4>广告计划</h4>

        <InputBudget
          v-model="budget">
        </InputBudget>

        <InputRate
          v-model="rate">
        </InputRate>

        <InputWeektime
          prop="schedule_time"
          v-model="schedule_time">
        </InputWeektime>

        <h4>广告单元</h4>

        <InputDistrict
          v-model="district">
        </InputDistrict>

        <InputGender
          v-model="gender">
        </InputGender>

        <InputAge
          v-model="age">
        </InputAge>

        <InputUserTarget
          :userTargeting.sync="user_targeting"
          :interest.sync="interest"
          :word.sync="word"
          :intelliTargeting.sync="intelli_targeting">
        </InputUserTarget>

        <InputPlatform
          prop="platform"
          v-model="platform">
        </InputPlatform>

        <InputNetworkEnv
          v-model="network_env">
        </InputNetworkEnv>

        <h4>广告创意</h4>

        <el-form-item label="推广来源" prop="creativeSource">
          <el-input v-model="creativeSource" value=""></el-input>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" :disabled="submitDisabled" @click="submitForm">保存</el-button>
        </el-form-item>
      </el-form>
    </el-row>
  </div>
</template>

<script>
import { editItem, storeItem, updateItem } from "@/js/api/uchcTpl";
import { getBasicData } from "@/js/api/common";
import { FormStore } from "@/js/mixins";
import { mapStateGetSet } from "@/js/utils";

import InputBudget from "@/js/views/AdMain/components/Uchc/InputBudget";
import InputRate from "@/js/views/AdMain/components/Uchc/InputRate";
import InputWeektime from "@/js/views/AdMain/components/Uchc/InputWeektime";
import InputDistrict from "@/js/views/AdMain/components/Uchc/InputDistrict";
import InputGender from "@/js/views/AdMain/components/Uchc/InputGender";
import InputAge from "@/js/views/AdMain/components/Uchc/InputAge";
import InputUserTarget from "@/js/views/AdMain/components/Uchc/InputUserTarget";
import InputPlatform from "@/js/views/AdMain/components/Uchc/InputPlatform";
import InputNetworkEnv from "@/js/views/AdMain/components/Uchc/InputNetworkEnv";

export default {
  mixins: [FormStore],
  name: "gmp-tpl-uchc-tpl-form",
  components: {
    InputBudget,
    InputRate,
    InputWeektime,
    InputDistrict,
    InputGender,
    InputAge,
    InputUserTarget,
    InputPlatform,
    InputNetworkEnv
  },
  props: {},
  data: function() {
    return { editItem, storeItem, updateItem };
  },
  computed: {
    state() {
      return this.$store.state.tplUchcTpl;
    },
    rules() {
      return this.state.rules;
    },
    ...mapStateGetSet("tplUchcTpl", {
      form: {
        get: state => state.form,
        set: "form"
      },
      title: {
        get: state => state.form.title,
        set: "form.title"
      },
      budget: {
        get: state => state.form.budget,
        set: "form.budget"
      },
      rate: {
        get: state => state.form.rate,
        set: "form.rate"
      },
      schedule_time: {
        get: state => state.form.schedule_time,
        set: "form.schedule_time"
      },
      district: {
        get: state => {
          return {
            all_region: state.form.all_region,
            region: state.form.region
          };
        },
        set: function(payload, dispatch) {
          dispatch("form.all_region", payload.all_region);
          dispatch("form.region", payload.region);
        }
      },
      gender: {
        get: state => state.form.gender,
        set: "form.gender"
      },
      age: {
        get: state => state.form.age,
        set: "form.age"
      },
      user_targeting: {
        get: state => state.form.user_targeting,
        set: "form.user_targeting"
      },
      interest: {
        get: state => state.form.interest,
        set: "form.interest"
      },
      word: {
        get: state => state.form.word,
        set: "form.word"
      },
      intelli_targeting: {
        get: state => state.form.intelli_targeting,
        set: "form.intelli_targeting"
      },
      platform: {
        get: state => state.form.platform,
        set: "form.platform"
      },
      network_env: {
        get: state => state.form.network_env,
        set: "form.network_env"
      },
      creativeSource: {
        get: state => state.form.creativeSource,
        set: "form.creativeSource"
      }
    })
  },
  methods: {},
  created() {}
};
</script>
