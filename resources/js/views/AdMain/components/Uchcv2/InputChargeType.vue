<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('chargeType')">
      <el-radio-group v-model="curVal" size="small">
        <el-radio v-for="item in options"
          disabled
          :key="item.value"
          :label="item.value">{{ item.label}}
        </el-radio>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-charge-type",
  props: {
    value: {
      type: [Number],
      required: true
    },
    optimizationTarget: {
      type: Number,
      required: true
    },
    label: {
      type: [String],
      default: "计费方式"
    }
  },
  data() {
    return {
      options: [
        {
          label: "CPC",
          value: 1
        },
        {
          label: "CPM",
          value: 2
        }
      ]
    };
  },
  computed: {
    curVal: {
      get() {
        if ([1, 2].indexOf(this.value) === -1) {
          this.$emit('input', 1)
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
    curVal: {
      handler: function() {
        this.$emit("input", this.curVal);
      },
      immediate: true,
      deep: true
    },
    optimizationTarget: {
      handler: function(val) {
        if ([1, 3].indexOf(val) !== -1) {
          this.curVal = 1;
        } else {
          this.curVal = 2;
        }
      },
      immediate: true
    }
  }
};
</script>
