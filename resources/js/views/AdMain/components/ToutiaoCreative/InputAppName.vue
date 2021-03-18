<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('app_name')">
      <el-input
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
  name: "input-app-web-url",
  props: {
    value: {},
    label: {
      type: [String],
      default: "应用名"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null
          ? ""
          : this.value
    };
  },
  methods: {
    handleInput(event) {
      this.$emit("input", this.curVal);
    },
    handleChange(event) {
      // this.$emit("change", this.curVal);
    }
  },
  computed: {}
};
</script>
