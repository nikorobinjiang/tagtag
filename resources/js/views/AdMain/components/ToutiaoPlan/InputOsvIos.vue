<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('android-osv')">
      <el-select
        v-model="curVal"
        @input="handleInput"
        @change="handleChange">
        <el-option
          v-for="(item,key) in androidosvOptions"
          :key="key"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-android-osv",
  props: {
    value: {
      type: [String, Number],
      default: "0.0"
    },
    label: {
      type: [String],
      default: "iOS版本"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null || this.value === ""
          ? "0.0"
          : this.value,
      androidosvOptions: [
        { value: "0.0", label: "不限" },
        { value: "9.0", label: "iOS 9.0 及以上" },
        { value: "8.2", label: "iOS 8.2 及以上" },
        { value: "8.0", label: "iOS 8.0 及以上" },
        { value: "7.1", label: "iOS 7.1 及以上" },
        { value: "7.0", label: "iOS 7.0 及以上" },
        { value: "6.0", label: "iOS 6.0 及以上" },
        { value: "5.1", label: "iOS 5.1 及以上" },
        { value: "5.0", label: "iOS 5.0 及以上" },
        { value: "4.3", label: "iOS 4.3 及以上" },
        { value: "4.2", label: "iOS 4.2 及以上" },
        { value: "8.1", label: "iOS 8.1 及以上" },
        { value: "4.1", label: "iOS 4.1 及以上" },
        { value: "4.0", label: "iOS 4.0 及以上" },
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
  },
  watch: {},
  computed: {},
  mounted() {}
};
</script>
