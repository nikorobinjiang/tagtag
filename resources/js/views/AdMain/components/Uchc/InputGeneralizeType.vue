<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('generalizeType')">
      <el-radio-group v-model="curVal"  :disabled="disabled" size="small">
        <el-radio v-for="item in options"
          v-if="item.scope.indexOf(appType) !== -1"
          :key="item.value"
          :label="item.value">{{ item.label}}
        </el-radio>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-generalize-type",
  props: {
    appType: {
      type: String,
      default: "Android"
    },
    disabled: {
      type: Boolean,
      required: true
    },
    value: {
      type: [Number],
      required: true
    },
    label: {
      type: [String],
      default: "推广方式"
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? 1 : this.value,
      options: [
        {
          label: "打开页面",
          value: 1,
          scope: ["Android", "H5", "iOS"]
        },
        {
          label: "APP下载",
          value: 2,
          scope: ["iOS"]
        }
      ]
    };
  },
  computed: {},
  methods: {},
  watch: {
    value: function(val, oldVal) {
      if (val !== oldVal) {
        this.curVal = val === undefined || val === null ? 1 : val;
      }
    },
    curVal: {
      handler: function() {
        this.$emit("input", this.curVal);
      },
      immediate: true,
      deep: true
    }
  }
};
</script>
