<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('budget')">
      <el-col :span="3" v-if="false">
        <el-select
          v-model="curVal.budget_mode"
          placeholder="请选择">
          <el-option
            v-for="(item, key) in budgetType"
            v-if="item.value !== 'BUDGET_MODE_INFINITE'"
            :key="key"
            :label="item.text"
            :value="item.value">
          </el-option>
        </el-select>
      </el-col>
      <el-col :span="12">
        <el-input-number
          v-model="curVal.budget"
          controls-position="right"
          :min="getMinBudget()">
        </el-input-number>
      </el-col>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-budget",
  props: {
    value: {
      type: [Object],
      required: true
    },
    settlement: {
      type: [String],
      default: ""
    },
    label: {
      type: [String],
      default: "日预算"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null
          ? { budget_mode: "", budget: 0 }
          : this.value,
      budgetType: dataToutiao.budgetType
    };
  },
  methods: {
    getMinBudget() {
      if (this.curVal.budget_mode === "BUDGET_MODE_DAY") {
        if (['OCPC', 'OCPM'].indexOf(this.settlement) !== -1) {
          return 300;
        } else {
          return 100;
        }
      } else {
        return 0;
      }
    }
  },
  watch: {
    curVal: {
      handler: function() {
        this.curVal.budget_mode = "BUDGET_MODE_DAY";
        this.$emit("input", this.curVal);
      },
      deep: true
    }
  },
  computed: {}
};
</script>
