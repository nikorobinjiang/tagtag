<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('ac')">
      <el-checkbox-group
        v-model="curVal">
        <el-checkbox-button
        v-for="(item, key) in networkType"
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
  name: "input-network",
  props: {
    value: {
      type: [Array],
      required: true
    },
    label: {
      type: [String],
      default: "网络"
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? [] : this.value,
      networkType:
        dataToutiao.networkType === undefined ||
        dataToutiao.networkType === null
          ? []
          : dataToutiao.networkType
    };
  },
  methods: {},
  watch: {
    curVal: {
      handler: function(val, oldVal) {
        if (oldVal === undefined) {
          oldVal = [];
        }
        if (val.indexOf("NOLIMIT") !== -1 && oldVal.indexOf("NOLIMIT") !== -1) {
          this.curVal = val.filter(item => {
            return item !== "NOLIMIT";
          });
        } else if (val.indexOf("NOLIMIT") !== -1 && val.length > 1) {
          this.curVal = ["NOLIMIT"];
        } else if (
          val.indexOf("NOLIMIT") === -1 &&
          val.length === Object.keys(this.networkType).length - 1
        ) {
          this.curVal = ["NOLIMIT"];
        } else if (val.length === 0) {
          this.curVal = ["NOLIMIT"];
        } else {
          this.curVal = val;
        }

        if (val.indexOf("NOLIMIT") !== -1) {
          this.$emit(
            "input",
            Object.keys(this.networkType).filter(key => {
              return key !== "NOLIMIT";
            })
          );
        } else {
          this.$emit("input", this.curVal);
        }
      },
      immediate: true,
      deep: true
    }
  },
  computed: {},
  mounted() {}
};
</script>
