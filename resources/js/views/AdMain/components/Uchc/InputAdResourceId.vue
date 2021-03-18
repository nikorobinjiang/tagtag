<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('adResourceId')">
      <el-radio-group disabled v-model="curVal" size="small">
        <el-radio v-for="item in options"
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
  name: "input-ad-resource-id",
  props: {
    value: {
      type: [Number],
      required: true
    },
    label: {
      type: [String],
      default: "推广资源"
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? 1 : this.value,
      options: [
        {
          label: "UC头条",
          value: 1
        }
      ]
    };
  },
  computed: {},
  methods: {},
  watch: {
    curVal: {
      handler: function() {
        this.$emit("input", this.curVal);
      },
      deep: true
    }
  },
  created() {
    if (!this.curVal) {
      this.curVal = 1;
    }
  }
};
</script>
