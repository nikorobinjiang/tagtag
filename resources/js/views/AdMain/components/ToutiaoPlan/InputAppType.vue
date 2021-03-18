<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('app_type')">
      <el-radio-group
        v-model="curVal"
        @change="handleChange">
        <el-radio-button
          v-for="(item, key) in options"
          v-if="item.show"
          :key="key"
          :label="key">{{ item.text }}
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
  name: "input-app-type",
  props: {
    value: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "广告应用下载类型"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null || this.value === ""
          ? "APP_ANDROID"
          : this.value,
      options:
        dataToutiao.appType === undefined || dataToutiao.appType === null
          ? {}
          : dataToutiao.appType
    };
  },
  methods: {
    handleChange() {
      this.$emit("input", this.curVal);
    }
  },
  computed: {},
  mounted() {
    this.$emit("input", this.curVal);
  }
};
</script>
