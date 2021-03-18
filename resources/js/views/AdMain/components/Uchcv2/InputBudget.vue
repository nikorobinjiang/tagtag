<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('budget')">
      <el-radio-group v-model="budgetSelectVal" size="small">
        <el-radio label="nolimit">不限</el-radio>
        <el-radio label="limit">
          <el-input-number
            v-model="curVal"
            :disabled="curVal < 0"
            :min="100"
            :max="1000000"
            :precision="2"
            controls-position="right">
          </el-input-number> 元
        </el-radio>
      </el-radio-group>
      <span class="tip">最小100元，最大100万元</span>
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
      type: Number,
      required: true
    },
    label: {
      type: [String],
      default: "日预算"
    }
  },
  data() {
    return {
    };
  },
  computed: {
    curVal: {
      get() {
        if (isNaN(this.value)) {
        this.$emit('input', -1)
        }
        return this.value
      },
      set(payload) {
        this.$emit('input', payload)
      }
    },
    budgetSelectVal: {
      get: function() {
        return this.curVal >= 0 ? "limit" : "nolimit";
      },
      set: function(val) {
        this.curVal = val === "nolimit" ? -1 : 100;
      }
    }
  },
  methods: {},
  watch: {
  }
};
</script>

<style lang="scss" scoped>
.tip {
  color: #999;
  font-size: 12px;
  margin-left: 15px;
}
</style>
