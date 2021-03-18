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
      <el-form ref="dataForm" :model="dataForm" :rules="rules" label-width="120px" size="mini">
        <el-form-item label="模板名称" prop="title">
          <el-input v-model="dataForm.title"></el-input>
        </el-form-item>

        <h4>广告计划</h4>

        <InputBudget
          v-model="dataForm.budget">
        </InputBudget>

        <InputDelivery
          v-model="dataForm.delivery">
        </InputDelivery>

        <InputWeektime
          prop="scheduleTime"
          v-model="dataForm.scheduleTime">
        </InputWeektime>

        <h4>广告单元</h4>

        <InputDistrict
          :all-region.sync="dataForm.allRegion"
          :region.sync="dataForm.region">
        </InputDistrict>

        <InputGender
          v-model="dataForm.gender">
        </InputGender>

        <InputAge
          v-model="dataForm.age">
        </InputAge>

        <InputUserTarget
          :interest.sync="dataForm.interest"
          :word.sync="dataForm.word">
        </InputUserTarget>

        <InputIntelliTargeting
          v-model="dataForm.intelliTargeting">
        </InputIntelliTargeting>

        <InputPlatform
          prop="platform"
          v-model="dataForm.platform">
        </InputPlatform>

        <InputNetworkEnv
          v-model="dataForm.networkEnv">
        </InputNetworkEnv>

        <h4>广告创意</h4>

        <el-form-item label="推广来源" prop="creativeSource">
          <el-input v-model="dataForm.creativeSource"/>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" :disabled="submitDisabled" @click="submitForm">保存</el-button>
        </el-form-item>
      </el-form>
    </el-row>
  </div>
</template>

<script>
import { cloneDeep, assign } from 'lodash'
import { mapState } from "vuex";

import { editItem, storeItem, updateItem } from "@/js/api/uchcv2Tpl";
import { getBasicData } from "@/js/api/common";

import InputBudget from "@/js/views/AdMain/components/Uchcv2/InputBudget";
import InputDelivery from "@/js/views/AdMain/components/Uchcv2/InputDelivery";
import InputWeektime from "@/js/views/AdMain/components/Uchcv2/InputWeektime";
import InputDistrict from "@/js/views/AdMain/components/Uchcv2/InputDistrict";
import InputGender from "@/js/views/AdMain/components/Uchcv2/InputGender";
import InputAge from "@/js/views/AdMain/components/Uchcv2/InputAge";
import InputUserTarget from "@/js/views/AdMain/components/Uchcv2/InputUserTarget";
import InputIntelliTargeting from "@/js/views/AdMain/components/Uchcv2/InputIntelliTargeting";
import InputPlatform from "@/js/views/AdMain/components/Uchcv2/InputPlatform";
import InputNetworkEnv from "@/js/views/AdMain/components/Uchcv2/InputNetworkEnv";

export default {
  name: "gmp-tpl-uchcv2-tpl-form",
  components: {
    InputBudget,
    InputDelivery,
    InputWeektime,
    InputDistrict,
    InputGender,
    InputAge,
    InputUserTarget,
    InputIntelliTargeting,
    InputPlatform,
    InputNetworkEnv
  },
  props: {
    formName: {
      type: [String],
      required: true
    },
    requestAction: {
      type: [String],
      required: true
    },
    visible: {
      type: Boolean,
      default: false
    },
    itemId: {
      default: null
    }
  },
  data: function() {
    return {
      loading: false, // 当前 form 加载状态
      submitDisabled: false, // 禁止提交按钮
      dataForm: cloneDeep(this.defaultForm)
    };
  },
  computed: {
    formTitle: function () {
      let title = '';
      if (this.requestAction === 'show') {
        title = '查看';
      } else if (this.requestAction === 'edit') {
        title = '编辑';
      } else if (this.requestAction === 'create') {
        title = '创建';
      }
      return title + this.formName;
    },
    ...mapState("tplUchcv2Tpl", {
      defaultForm: state => state.defaultForm,
      rules: state => state.rules
    })
  },
  methods: {
    // 取消 form
    cancelForm() {
      this.$emit("update:visible", false);
    },
    // 提交 form
    submitForm() {
      this.$refs.dataForm.validate(valid => {
        if (valid) {
          this.submitDisabled = true;
          (this.requestAction === "edit" ?
            updateItem(this.itemId, this.dataForm) :
            storeItem(this.dataForm)
          )
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
                setTimeout(function () {
                  location.reload();
                }, 2000);
              }
              this.submitDisabled = false;
            })
            .catch(error => {
              console.log("error", error);
              this.submitDisabled = false;
            });
        } else {
          this.$message({
            message: "请检查表单是否填写完整",
            center: true
          });
        }
      });
    }
  },
  watch: {
    itemId: {
      handler: function () {
        this.loading = true;
        // 加载编辑数据
        if (this.itemId) {
          editItem(this.itemId).then(response => {
            this.$set(this.$data, 'dataForm', assign({}, this.dataForm, response.data.it))
            this.loading = false;
          });
        } else {
          this.loading = false;
        }
      },
      immediate: true
    }
  },
  created() {
  }
};
</script>
