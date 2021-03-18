<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('adgroup.convertMonitorType')">
      <el-select
        v-model="curVal"
        filterable>
        <el-option
          v-for="(item, key) in options"
          :key="key"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope, xStore } from "@/js/mixins";

export default {
  mixins: [FormScope, xStore],
  name: "input-convert-monitor-type",
  props: {
    value: {
      type: [Number],
      required: true
    },
    label: {
      type: [String],
      default: "转化类型"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null || this.value === 0
          ? null
          : this.value,
      options: [
        {
          label: "激活",
          value: 1
        }
      ]
    };
  },
  methods: {},
  watch: {
    curVal: function(val, oldVal) {
      this.$emit("input", val ? val : 0);
    }
  },
  computed: {},
  mounted() {}
};
</script>
