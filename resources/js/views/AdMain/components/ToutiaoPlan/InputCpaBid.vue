<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('cpa_bid')">
      <el-input-number v-model="curVal" controls-position="right" :min="0"></el-input-number>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-cpi-bid",
  props: {
    value: {
      type: [Number],
      default: 0
    },
    label: {
      type: [String],
      default: "目标转化出价" // 第二阶段广告出价
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? 0 : this.value
    };
  },
  methods: {},
  watch: {
    curVal: {
      handler: function(val, oldVal) {
        this.$emit("input", this.curVal);
      },
      immediate: true,
      deep: true
    }
  },
  computed: {},
  mounted() {}
};
</script>
