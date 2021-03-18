<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('adResourceId')">
        <el-switch
          v-model="curVal"
          :active-value="1"
          :inactive-value="2">
        </el-switch>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "InputSlotBidding",
  props: {
    value: {
      type: Number,
      required: true
    },
    label: {
      type: [String],
      default: "最大化获量"
    }
  },
  data() {
    return {
    };
  },
  computed: {
    curVal: {
      get() {
        if ([1, 2].indexOf(this.value) === -1) {
          this.$emit('input', 2)
        }
        return this.value
      },
      set(payload) {
        this.$emit('input', payload)
      }
    }
  },
  methods: {},
  watch: {
  },
  created() {
  }
};
</script>
