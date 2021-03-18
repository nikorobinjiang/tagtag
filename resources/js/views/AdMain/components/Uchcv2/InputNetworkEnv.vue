<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('network_env')">
      <el-radio-group v-model="curVal">
        <el-radio-button
          v-for="(item, key) in options"
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
      options: [
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
  computed: {
    curVal: {
      get() {
        if (this.options.map(item => item.value).indexOf(this.value) === -1) {
          this.$emit('input', '11')
        }
        return this.value
      },
      set(payload) {
        this.$emit('input', payload)
      }
    }
  },
  watch: {
  }
};
</script>
