<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('has_landing_page')">
      <el-radio-group
        v-model="curVal">
        <el-radio
          v-for="(item, key) in options"
          v-if="item.show"
          :key="key"
          :label="item.value">{{ item.label }}
        </el-radio>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-has-landing-page",
  props: {
    value: {
      type: [Number],
      default: null
    },
    label: {
      type: [String],
      default: "落地页"
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? 1 : this.value,
      options: [
        {
          label: "是",
          value: 1,
          show: true
        },
        {
          label: "否",
          value: 0,
          show: true
        }
      ]
    };
  },
  methods: {},
  watch: {
    curVal: {
      handler: function() {
        this.$emit("input", this.curVal);
      },
      deep: true
    }
  },
  computed: {},
  mounted() {}
};
</script>
