<template>
    <div class="input-block">
        <el-form-item :label="label" :prop="getFormScopedField('pricingOpt')">
          <el-radio-group v-model="pricingOpt">
            <el-radio-button label="transform" >转化</el-radio-button>
            <el-radio-button label="play">有效播放</el-radio-button>
            <el-radio-button label="click">点击</el-radio-button>
            <el-radio-button label="display">展示</el-radio-button>
          </el-radio-group>
        </el-form-item>
    </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-download-pricing",
  props: {
    value: {},
    label: {
      type: [String],
      default: "投放目标"
    }
  },
  data() {
    return {
      pricingOpt: "",
      curVal:
        this.value === undefined || this.value === null
          ? { pricingOpt: "", external_action: "", optimizeTarget: "" }
          : this.value
    };
  },
  watch: {
    pricingOpt: function(val, oldVal) {
      switch (val) {
        case "transform":
          curVal.external_action = "active";
          curVal.optimizeTarget = "CPM";
          break;
        case "play":
          curVal.optimizeTarget = "CPV";
          break;
        case "click":
          curVal.optimizeTarget = "CPC";
          break;
        case "display":
          curVal.optimizeTarget = "CPM";
          break;
        default:
          curVal.optimizeTarget = "";
          break;
      }
      this.$emit("input", this.curVal);
    }
  },
  computed: {}
};
</script>
