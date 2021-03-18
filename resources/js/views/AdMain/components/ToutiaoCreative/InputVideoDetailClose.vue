<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-checkbox
        v-model="curVal"
        @input="handleInput"
        @change="handleChange">
        关闭视频详情落地页
      </el-checkbox>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-video-detail-close",
  props: {
    value: {
      type: [Number],
      default: 0
    },
    label: {
      type: [String],
      default: ""
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null
          ? false
          : this.value ? true : false
    };
  },
  methods: {
    handleInput(event) {
      this.$emit("input", event ? 1 : 0);
    },
    handleChange(event) {
      this.$emit("change", event ? 1 : 0);
    }
  }
};
</script>

