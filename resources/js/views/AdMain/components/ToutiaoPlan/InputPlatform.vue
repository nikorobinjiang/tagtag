<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('platform')">
      <el-checkbox-group
        v-model="curVal"
        @change="handleChange">
        <el-checkbox-button
        v-for="(item, key) in platformType"
        v-if="item.show"
        :key="key"
        :label="key">{{ item.text }}
        </el-checkbox-button>
      </el-checkbox-group>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-platform",
  props: {
    value: {
      type: [Array],
      required: true
    },
    label: {
      type: [String],
      default: "平台"
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? [] : this.value,
      platformType:
        dataToutiao.platformType === undefined ||
        dataToutiao.platformType === null
          ? []
          : dataToutiao.platformType
    };
  },
  methods: {
    handleChange() {
      let val = this.curVal;
      let oldVal = this.value;
      if (val.indexOf("NOLIMIT") !== -1 && oldVal.indexOf("NOLIMIT") === -1) {
        this.curVal = ["NOLIMIT"];
      } else if (
        val.indexOf("NOLIMIT") !== -1 &&
        oldVal.indexOf("NOLIMIT") !== -1
      ) {
        this.curVal = val.filter(item => {
          return item !== "NOLIMIT";
        });
      } else if (this.curVal.length === 0) {
        this.curVal = ["NOLIMIT"];
      } else {
        this.curVal = val;
      }
      this.$emit("input", this.curVal);
    }
  },
  computed: {},
  mounted() {}
};
</script>
