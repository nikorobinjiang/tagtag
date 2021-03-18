<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('gender')">
      <el-radio-group v-model="curVal">
        <el-radio-button
          v-for="(item, key) in genderType"
          :key="key"
          :label="item.value">{{ item.text}}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-gender",
  props: {
    value: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "性别"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? "-1" : this.value,
      genderType: [
        {
          text: "不限",
          value: "-1"
        },
        {
          text: "男",
          value: "1"
        },
        {
          text: "女",
          value: "0"
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
