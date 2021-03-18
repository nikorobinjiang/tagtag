<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('optimizationTarget')">
      <el-radio-group v-model="curVal" size="small">
        <el-radio v-for="item in options"
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
  name: "input-optimization-target",
  props: {
    value: {
      type: [Number],
      required: true
    },
    label: {
      type: [String],
      default: "优化目标"
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? 3 : this.value,
      options: [
        {
          label: "转化",
          value: 3
        },
        {
          label: "点击",
          value: 1
        },
        {
          label: "展现",
          value: 2
        }
      ]
    };
  },
  computed: {},
  methods: {},
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
