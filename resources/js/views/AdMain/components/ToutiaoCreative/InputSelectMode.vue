<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('selectmode')">
      <el-radio-group
        :disabled="disabled"
        v-model="curVal"
        @input="handleInput"
        @change="handleChange">
        <el-radio-button
        v-for="(item, key) in options"
        :key="key"
        :label="item.value">{{ item.text }}</el-radio-button>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-select-mode",
  props: {
    disabled: {
      type: [Boolean],
      default: false
    },
    value: {
      type: [String],
      default: ""
    },
    label: {
      type: [String],
      default: "创意生成方式"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? "" : this.value,
      options: [
        {
          value: "procedural",
          text: "程序化创意"
        },
        {
          value: "customize",
          text: "自定义组合创意"
        }
      ]
    };
  },
  methods: {
    handleInput(event) {
      this.$emit("input", event);
    },
    handleChange(event) {
      this.$emit("change", event);
    }
  }
};
</script>
