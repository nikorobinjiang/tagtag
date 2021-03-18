<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('position_id')">
      <el-select
        v-model="curVal"
        :disabled="disabled"
        @input="handleInput"
        @change="handleChange"
        filterable>
        <el-option
          v-for="(item, key) in options"
          :key="key"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope, xStore } from "@/js/mixins";

export default {
  mixins: [FormScope, xStore],
  name: "input-position",
  props: {
    value: {
      type: [Number],
      default: null
    },
    options: {},
    disabled: {
      type: [Boolean],
      default: false
    },
    label: {
      type: [String],
      default: "广告位"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? null : this.value
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
  watch: {
    value: function(val, oldVal) {
      if (val != oldVal) {
        this.curVal =
          val === undefined || val === null ? null : val;
      }
    }
  },
  computed: {},
  mounted() {}
};
</script>
