<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('network_env')">
      <el-radio-group v-model="curVal">
        <el-radio-button
          v-for="(item, key) in networkType"
          :key="key"
          :label="item.value">{{ item.label}}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-network-env",
  props: {
    value: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "网络环境"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? "11" : this.value,
      networkType: [
        {
          label: "不限",
          value: "11"
        },
        {
          label: "WIFI",
          value: "01"
        },
        {
          label: "数据网络",
          value: "10"
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
