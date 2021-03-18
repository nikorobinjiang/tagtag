<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('ad_group.convertType')">
      <el-select
        v-model="curVal"
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
import { FormScope, xStore } from "@/js/mixins";

export default {
  mixins: [FormScope, xStore],
  name: "InputConvertType",
  props: {
    value: {
      type: [Number],
      required: true
    },
    label: {
      type: [String],
      default: "转化类型"
    }
  },
  data() {
    return {
      options: [
        {
          label: "激活",
          value: 1
        }
      ]
    };
  },
  methods: {},
  watch: {
    curVal: function(val, oldVal) {
      this.$emit("input", val ? val : 0);
    }
  },
  computed: {
    curVal: {
      get() {
        if ([1].indexOf(this.value) === -1) {
          this.$emit('input', 1)
        }
        return this.value
      },
      set(payload) {
        this.$emit('input', payload)
      }
    }
  },
  mounted() {}
};
</script>
