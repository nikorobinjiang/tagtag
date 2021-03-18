<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-checkbox-group
        v-model="curVal">
        <el-checkbox-button
          v-for="(item,key) in ageType"
          :key="key"
          :label="key">{{ item.text }}</el-checkbox-button>
      </el-checkbox-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-age",
  props: {
    value: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "年龄"
    }
  },
  data() {
    let curVal = [];
    if (this.value.length === 6) {
      this.value
        .split("")
        .reverse()
        .forEach((val, idx) => {
          val = parseInt(val);
          if (val === 1) {
            curVal.push(idx + 1);
          }
        });
    }
    return {
      curVal,
      ageType: [
        {
          text: "不限",
          value: 0
        },
        {
          text: "<=18",
          value: "[0,18]"
        },
        {
          text: "19-24",
          value: "[19,24]"
        },
        {
          text: "25-29",
          value: "[25,29]"
        },
        {
          text: "30-39",
          value: "[30,39]"
        },
        {
          text: "40-49",
          value: "[40,49]"
        },
        {
          text: ">=50",
          value: "[50,100]"
        }
      ]
    };
  },
  methods: {},
  computed: {},
  watch: {
    curVal: {
      handler: function(val, oldVal) {
        if (oldVal === undefined) {
          oldVal = [];
        }
        if (val.indexOf(0) !== -1 && oldVal.indexOf(0) !== -1) {
          this.curVal = val.filter(item => {
            return item !== 0;
          });
        } else if (val.indexOf(0) !== -1 && val.length > 1) {
          this.curVal = [0];
        } else if (
          val.indexOf(0) === -1 &&
          val.length === Object.keys(this.ageType).length - 1
        ) {
          this.curVal = [0];
        } else if (val.length === 0) {
          this.curVal = [0];
        } else {
          this.curVal = val;
        }

        if (this.curVal.length === 0 || this.curVal[0] === 0) {
          this.$emit("input", "-1");
        } else {
          let commitVal = Object.keys(this.ageType)
            .map(key => {
              key = parseInt(key);
              return val.indexOf(key) !== -1 ? "1" : "0";
            })
            .reverse();
          commitVal.pop();
          this.$emit("input", commitVal.join(""));
        }
      },
      immediate: true,
      deep: true
    }
  },
  mounted() {}
};
</script>
