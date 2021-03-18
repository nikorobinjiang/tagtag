<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-radio-group v-model="curVal.settlement">
        <el-radio-button
        v-for="(item, key) in pricingOptions"
        v-if="item.text === 'CPM'"
        :key="key"
        :label="item.text">{{ item.text }}</el-radio-button>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";
import dataToutiao from "@/js/store/data/Toutiao";

export default {
  mixins: [FormScope],
  name: "input-settlement",
  props: {
    state: {},
    label: {
      type: [String],
      default: "结算方式"
    }
  },
  data() {
    return {
      curVal:
              this.value === undefined || this.value === null
                      ? { settlement: "" }
                      : this.value,
      pricingOptions:dataToutiao.settlementType
    };
  },
  computed: {

  }
};
</script>
