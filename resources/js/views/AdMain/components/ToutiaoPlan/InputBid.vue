<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('bid')">
      <el-input-number
        v-model="curVal"
        controls-position="right"
        :min="0"
        @input="handleInput"
        @change="handleChange">>
      </el-input-number>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-bid",
  props: {
    value: {
      type: [Number],
      default: 0
    },
    label: {
      type: [String],
      default: "目标转化出价"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? 0 : this.value
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
  computed: {}
};
</script>
