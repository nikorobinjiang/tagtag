<template>
  <el-form-item :label="label" :prop="getFormScopedField('delivery')">
    <el-switch
      v-model="curVal"
      :active-value="500"
      :inactive-value="0">
    </el-switch>
  </el-form-item>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "InputIntelliTargeting",
  props: {
    value: {
      type: Number,
      required: true
    },
    label: {
      type: [String],
      default: "智能定向"
    }
  },
  data() {
    return {
    };
  },
  watch: {
  },
  computed: {
    curVal: {
      get() {
        if (isNaN(this.value)) {
          this.$emit("input", 0);
        }
        return this.value
      },
      set(payload) {
        this.$emit("input", payload);
      }
    }
  }
};
</script>
