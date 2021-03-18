<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField(prop)">
      <el-checkbox-group v-model="curVal">
        <el-checkbox-button
        v-for="(item, key) in platformTypeCal"
        :key="key"
        :label="key">{{ item.label }}
        </el-checkbox-button>
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
      default: "ad_group.platform"
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
        label: "IOS",
        value: "ios",
        byteVal: 1
      },
      {
        label: "Android",
        value: "android",
        byteVal: 10
      },
      {
        label: "其他系统",
        value: "others",
        byteVal: 100
      }
    ];
    return {
      platformType
    };
  },
  watch: {
  },
  computed: {
    curVal: {
      get() {
        const res = []
        if (this.value.length === 3) {
          this.value
            .split("")
            .reverse()
            .forEach((val, idx) => {
              val = parseInt(val);
              if (val === 1) {
                res.push(idx);
              }
            });
        } else {
          this.$emit("input", "000");
        }
        return res
      },
      set(payload) {
        let res = this.platformType
          .map((item, idx) => {
            if (payload.indexOf(idx) !== -1) {
              return 1;
            } else {
              return 0;
            }
          })
          .reverse();
        this.$emit("input", res.join(""));
      }
    },
    platformTypeCal: function() {
      return this.platformType.filter(item => item.value !== 0)
    }
  },
  created() {}
};
</script>
