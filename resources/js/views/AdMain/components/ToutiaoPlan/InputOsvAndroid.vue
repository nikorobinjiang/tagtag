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
      default: "安卓版本"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null || this.value === '' ? "0.0" : this.value,
      androidosvOptions: [
        { value: "0.0", label: "不限" },
        { value: "5.0", label: "Android 5.0及以上" },
        { value: "4.5", label: "Android 4.5及以上" },
        { value: "4.4", label: "Android 4.4及以上" },
        { value: "4.3", label: "Android 4.3及以上" },
        { value: "4.2", label: "Android 4.2及以上" },
        { value: "4.1", label: "Android 4.1及以上" },
        { value: "4.0", label: "Android 4.0及以上" },
        { value: "3.2", label: "Android 3.2及以上" },
        { value: "3.1", label: "Android 3.1及以上" },
        { value: "3.0", label: "Android 3.0及以上" },
        { value: "2.3", label: "Android 2.3及以上" },
        { value: "2.2", label: "Android 2.2及以上" },
        { value: "2.1", label: "Android 2.1及以上" },
        { value: "2.0", label: "Android 2.0及以上" },
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
