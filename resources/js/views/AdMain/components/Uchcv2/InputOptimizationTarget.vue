<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('optTarget')">
      <el-radio-group v-model="curVal" :disabled="disabled" size="small">
        <el-radio-button v-for="item in options"
          :key="item.value"
          :label="item.value">{{ item.label}}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-optimization-target",
  props: {
    value: {
      type: Number,
      required: true
    },
    campaign: {
      type: Object,
      required: true
    },
    label: {
      type: [String],
      default: "优化目标"
    }
  },
  data() {
    return {
      options: [
        {
          label: "转化",
          value: 3
        },
        {
          label: "点击",
          value: 1
        },
        {
          label: "展现",
          value: 2
        }
      ]
    };
  },
  computed: {
    curVal: {
      get() {
        if (this.options.map(item => item.value).indexOf(this.value) === -1) {
          this.$emit('input', 3)
        }
        return this.value
      },
      set(payload) {
        this.$emit('input', payload)
      }
    },
    disabled() {
      return this.campaign.campaignId ? false : true
    }
  },
  methods: {},
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
