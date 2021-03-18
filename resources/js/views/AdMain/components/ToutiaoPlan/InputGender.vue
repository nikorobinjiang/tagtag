<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('gender')">
      <el-radio-group v-model="curVal">
        <el-radio-button
          v-for="(item, key) in genderType"
          :key="key"
          :label="key">{{ item.text}}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
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
        this.value === undefined || this.value === null
          ? "NONE"
          : this.value,
      genderType:
        dataToutiao.genderType === undefined || dataToutiao.genderType === null
          ? []
          : dataToutiao.genderType
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
