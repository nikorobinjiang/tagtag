<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('sub_title')">
      <el-input
        placeholder="请输入附加创意区标题，限12字以内"
        maxlength="12"
        v-model="curVal"
        @input="handleInput"
        @change="handleChange">
      </el-input>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-app-subtitle",
  props: {
    value: {},
    label: {
      type: [String],
      default: "副标题"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? "" : this.value
    };
  },
  methods: {
    handleInput(event) {
      this.$emit("input", this.curVal);
    },
    handleChange(event) {
      this.$emit("change", this.curVal);
    }
  },
  computed: {}
};
</script>
