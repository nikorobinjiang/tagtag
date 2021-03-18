<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-radio-group
        v-model="curVal"
        @input="handleInput">
        <el-radio-button
        v-for="(item, key) in flowControlModeType"
        :key="key"
        :label="item.value">{{ item.label}}
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
  name: "input-flow-control-mode",
  props: {
    value: {
      type: [String]
    },
    label: {
      type: [String],
      default: "投放方式"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? "" : this.value,
      flowControlModeType: dataToutiao.flowControlModeType
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
  computed: {},
  mounted() {}
};
</script>
