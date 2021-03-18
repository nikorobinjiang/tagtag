<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('budget')">
      <el-radio-group v-model="budgetSelectVal" size="small">
        <el-radio label="nolimit">不限</el-radio>
        <el-radio label="limit">
          <el-input-number
            :disabled="curVal < 0"
            v-model="curVal"
            controls-position="right">
          </el-input-number>元
        </el-radio>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-budget",
  props: {
    value: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "日预算"
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? -1 : this.value
    };
  },
  computed: {
    budgetSelectVal: {
      get: function() {
        return this.curVal >= 0 ? "limit" : "nolimit";
      },
      set: function(val) {
        this.curVal = val === "nolimit" ? -1 : 0;
      }
    }
  },
  methods: {},
  watch: {
    curVal: {
      handler: function() {
        this.$emit("input", this.curVal + "");
      },
      deep: true
    }
  }
};
</script>
