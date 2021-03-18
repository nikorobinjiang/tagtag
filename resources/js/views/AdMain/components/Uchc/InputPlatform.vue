<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField(prop)">
      <el-radio-group v-if="generalizeType === 2" v-model="curValMix">
        <el-radio
          v-for="(item, key) in platformType"
          v-if="item.value !== 'others'"
          :key="key"
          :label="item.mixVal">{{ item.label }}
        </el-radio>
      </el-radio-group>
      <el-checkbox-group v-else-if="generalizeType === 1" v-model="curVal">
        <el-checkbox
          v-for="(item, key) in platformType"
          v-if="item.value !== 0"
          :key="key"
          :label="key">{{ item.label }}
        </el-checkbox>
      </el-checkbox-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-platform",
  props: {
    prop: {
      type: String,
      default: "adgroup.platform"
    },
    appType: {
      type: String,
      default: "Android"
    },
    generalizeType: {
      type: Number,
      default: 1
    },
    value: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "操作系统"
    }
  },
  data() {
    let platformType = [
      {
        label: "iOS",
        value: "ios",
        mixVal: "001",
        byteVal: 1
      },
      {
        label: "Android",
        value: "android",
        mixVal: "010",
        byteVal: 10
      },
      {
        label: "其他系统",
        value: "others",
        mixVal: "100",
        byteVal: 100
      }
    ];
    return {
      curValMix:
        this.value === undefined || this.value === null ? "010" : this.value,
      curVal: this.reloadCurVal(this.value),
      platformType
    };
  },
  methods: {
    reloadMixCurVal(value) {
      let res = value;
      const count = this.platformType.filter(item => {
        return item.mixVal === value;
      }).length;
      if (count <= 0) {
        const item = this.platformType.filter(item => {
          return item.label === this.appType;
        });
        // console.log("reloadMixCurVal", item);
        if (item.length > 0) {
          res = item[0].mixVal;
        }
      }
      // console.log("reloadMixCurVal", count, value, res);
      return res;
    },
    reloadCurVal(value) {
      let res = [];
      if (value.length === 3) {
        value
          .split("")
          .reverse()
          .forEach((val, idx) => {
            val = parseInt(val);
            if (val === 1) {
              res.push(idx);
            }
          });
      }
      return res;
    },
    reloadValue() {
      if (this.generalizeType === 2) {
        this.curValMix = this.reloadMixCurVal(this.value);
        // this.$emit("input", this.curValMix);
      } else if (this.generalizeType === 1) {
        this.curVal = this.reloadCurVal(this.value);
        // this.$emit("input", this.curVal.join(""));
      }
    }
  },
  watch: {
    generalizeType: {
      handler: function(val, oldVal) {
        if (val !== oldVal) {
          this.reloadValue();
        }
      },
      immediate: true
    },
    curValMix: {
      handler: function(val) {
        if (this.generalizeType === 2) {
          this.$emit("input", val);
        }
      },
      deep: true,
      immediate: true,
    },
    curVal: {
      handler: function() {
        if (this.generalizeType === 1) {
          let res = this.platformType
            .map((item, idx) => {
              // idx = parseInt(idx);
              if (this.curVal.indexOf(idx) !== -1) {
                return 1;
              } else {
                return 0;
              }
            })
            .reverse()
            .join("");
          this.$emit("input", res.length === 3 ? res : "000");
        }
      },
      deep: true,
      immediate: true,
    }
  },
  computed: {},
  created() {}
};
</script>
