<template>
  <el-form
    v-loading="loading"
    :model="form"
    :rules="rules"
    size="mini"
    ref="scatForm"
    label-width="100px">
    <el-form-item label="媒体" prop="media_id">
      <el-select :disabled="requestAction == 'edit'" v-model="form.media_id" filterable>
        <el-option
          v-for="item in mediaList"
          :key="item.value"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>

    <el-form-item label="媒体账号" prop="promote_id">
      <el-select :disabled="requestAction == 'edit'" v-model="form.promote_id" filterable>
        <el-option
          v-for="item in promoteList"
          v-if="item.hasToutiao"
          :key="item.value"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>

    <el-form-item label="推广目的">
      <el-radio-group v-model="form.landing_type">
        <el-radio
        v-for="(item, key) in data.landingType"
        :key="key"
        :label="item.value">{{ item.text }}
        </el-radio>
      </el-radio-group>
    </el-form-item>

    <el-form-item label="预算">
      <el-row type="flex">
        <el-col :span="8">
          <el-radio-group v-model="form.budget_mode" @change="handleBudgetModeSelectChange">
            <el-radio
            v-for="(item, key) in data.budgetMode"
            :key="key"
            :label="item.value">{{ item.text }}
            </el-radio>
          </el-radio-group>
        </el-col>
        <el-col :span="8" v-if="form.budget_mode === 'BUDGET_MODE_DAY'">
          <el-select v-model="form.budget_mode">
            <el-option
            v-for="(item, key) in data.budgetMode"
            v-if="item.value === form.budget_mode"
            :key="key"
            label="日预算" :value="item.value"></el-option>
          </el-select>
        </el-col>
        <el-col :span="8" v-if="form.budget_mode === 'BUDGET_MODE_DAY'">
          <el-input-number v-model="form.budget" controls-position="right" :min="100"></el-input-number>
        </el-col>
      </el-row>
      
    </el-form-item>

    <el-form-item label="广告组名称" prop="campaign_name">
        <el-input v-model="form.campaign_name"></el-input>
    </el-form-item>

    <el-form-item>
      <el-button @click="cancelForm">取消</el-button>
      <el-button type="primary" :disabled="submitDisabled" @click="submitForm">确认</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { editItem, storeItem, updateItem } from "@/js/api/toutiaoGroup";
import { getBasicData } from "@/js/api/common";
import { FormCommon } from "@/js/mixins";

export default {
  mixins: [FormCommon],
  name: "gmp-tpl-toutiao-group-form",
  props: {},
  components: {},
  data: function() {
    return {
      editItem,
      storeItem,
      updateItem,

      mediaList: [],
      promoteList: [],
      submitDisabled: false,
      data: {
        landingType: [
          {
            value: "APP",
            text: "应用下载"
          }
        ],
        budgetMode: [
          {
            value: "BUDGET_MODE_INFINITE",
            text: "不限"
          },
          {
            value: "BUDGET_MODE_DAY",
            text: "日预算"
          }
        ]
      },
      formInit: {
        promote_id: null,
        media_id: null,
        campaign_name: "",
        landing_type: "APP",
        budget_mode: "BUDGET_MODE_DAY",
        budget: 0
      },
      form: {},
      rules: {
        promote_id: [
          { required: true, message: "请选择媒体", trigger: "change" }
        ],
        media_id: [
          { required: true, message: "请选择媒体账号", trigger: "change" }
        ],
        campaign_name: [
          { required: true, message: "请输入广告组名称", trigger: "blur" },
          { min: 1, max: 64, message: "长度在 1 到 64 个字符", trigger: "blur" }
        ]
      }
    };
  },
  methods: {
    // 提交 form
    submitForm() {
      this.$refs.scatForm.validate(valid => {
        if (valid) {
          this.submitDisabled = true;
          (this.requestAction === "edit"
            ? this.updateItem(this.itemId, this.form)
            : this.storeItem(this.form)
          )
            .then(response => {
              if (response.data.should_confirm) {
                this.$confirm(response.data.message, "提示", {
                  confirmButtonText: "确定",
                  cancelButtonText: "取消",
                  type: "warning"
                })
                  .then(() => {
                    this.form.force_store = true;
                    this.submitForm();
                  })
                  .catch(() => {});
              } else {
                this.$alert("保存成功", "", {
                  confirmButtonText: "确定",
                  callback: _ => {
                    location.reload();
                  }
                });
                setTimeout(function() {
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
    },
    handleBudgetModeSelectChange(val) {
      this.form.budget_mode = val;
    },
    // 媒体联动
    handleMediaIdChange: function(val, oldVal) {
      if (!val) {
        return;
      }
      this.promoteList = [];
      getBasicData({
        promoteList: {
          media_id: this.form.media_id,
          distribute: "Toutiao"
        }
      })
        .then(response => {
          this.promoteList = response.data.promoteList;
        })
        .catch(error => {
          console.log(error);
        });
    }
  },
  watch: {
    "form.media_id": function(val, oldVal) {
      this.handleMediaIdChange(val, oldVal);
    }
  },
  computed: {},
  mounted() {
    this.mediaList = [];
    this.form.media_id = null;
    getBasicData({
      mediaList: {
        distribute: "Toutiao"
      }
    })
      .then(response => {
        this.mediaList = response.data.mediaList;
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>
