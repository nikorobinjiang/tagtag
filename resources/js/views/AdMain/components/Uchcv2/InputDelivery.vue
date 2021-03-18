<template>
  <el-form-item :label="label" :prop="getFormScopedField('delivery')">
    <el-radio-group v-model="curVal">
      <el-radio-button
        v-for="(item, key) in options"
        :key="key"
        :label="item.value">{{ item.text}}
      </el-radio-button>
    </el-radio-group>
  </el-form-item>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "InputDelivery",
  props: {
    value: {
      type: [Number],
      required: true
    },
    label: {
      type: [String],
      default: "投放方式"
    }
  },
  data() {
    return {
      options: [
        {
          text: "匀速",
          value: 0
        },
        {
          text: "加速",
          value: 1
        }
      ]
    };
  },
  computed: {
    curVal: {
      get() {
        if (this.options.map(item => item.value).indexOf(this.value) === -1) {
          this.$emit('input', 0)
        }
        return this.value
      },
      set(payload) {
        this.$emit('input', payload)
      }
    }
  },
  watch: {
    curVal: {
      handler: function() {
        this.$emit("input", this.curVal);
      },
      deep: true
    }
  }
};
</script>
