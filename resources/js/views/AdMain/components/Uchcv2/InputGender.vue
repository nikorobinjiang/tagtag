<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('gender')">
      <el-radio-group v-model="curVal">
        <el-radio-button
          v-for="(item, key) in options"
          :key="key"
          :label="item.value">{{ item.text}}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-gender",
  props: {
    value: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "性别"
    }
  },
  data() {
    return {
      options: [
        {
          text: "不限",
          value: "-1"
        },
        {
          text: "男",
          value: "1"
        },
        {
          text: "女",
          value: "0"
        }
      ]
    };
  },
  computed: {
    curVal: {
      get() {
        if (this.options.map(item => item.value).indexOf(this.value) === -1) {
          this.$emit('input', "-1")
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
