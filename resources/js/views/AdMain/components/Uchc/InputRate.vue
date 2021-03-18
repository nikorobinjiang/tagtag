<template>
  <el-form-item :label="label" :prop="getFormScopedField('rate')">
    <el-radio-group v-model="curVal">
      <el-radio-button
        v-for="(item, key) in rateType"
        :key="key"
        :label="item.value">{{ item.text}}
      </el-radio-button>
    </el-radio-group>
  </el-form-item>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-rate",
  props: {
    value: {
      type: [Number],
      required: true
    },
    label: {
      type: [String],
      default: "投放速度"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? 0 : this.value,
      rateType: [
        {
          text: "匀速",
          value: 0
        },
        {
          text: "加速",
          value: 1
        }
      ]
    };
  },
  watch: {
    curVal: {
      handler: function() {
        this.$emit("input", this.curVal);
      },
      deep: true
    }
  }
};
</script>
