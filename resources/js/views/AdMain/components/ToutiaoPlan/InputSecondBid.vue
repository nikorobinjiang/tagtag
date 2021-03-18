<template>
    <div class="input-block">
        <el-form-item :label="label">
            <el-radio-group v-model="age">
                <el-radio-button label="unlimited">不限</el-radio-button>
                <el-radio-button label="range">指定年龄段</el-radio-button>
            </el-radio-group>
        </el-form-item>
        <el-form-item label="" v-show="age !== 'unlimited'">
            <el-checkbox-group v-model="ageInfo">
                <el-checkbox
                v-for="item in ageType"
                :label="item.label"
                :key="item.value">{{ item.label }}
                </el-checkbox>
            </el-checkbox-group>
        </el-form-item>
    </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

// TODO: 目标转化出价
export default {
  mixins: [FormScope],
  name: "input-bid",
  props: {
    state: {},
    label: {
      type: [String],
      default: '目标转化出价'
    }
  },
  data() {
    return {
      ageType:
        this.state.typeOptions.ageType === undefined ||
        this.state.typeOptions.ageType === null
          ? []
          : this.state.typeOptions.ageType,
    };
  },
  computed: {
    age: {
      get() {
        return this.state.form.age;
      },
      set(val) {
        this.$store.dispatch(
          this.state.getActionName("form.age"),
          val
        );
      }
    },
    ageInfo: {
      get() {
        return this.state.form.ageInfo;
      },
      set(val) {
        this.$store.dispatch(
          this.state.getActionName("form.ageInfo"),
          val
        );
      }
    }
  }
};
</script>
