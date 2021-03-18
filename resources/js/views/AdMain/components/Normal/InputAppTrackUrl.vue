<template>
  <div class="input-block">
    <el-form-item :label="label">
      <div>
        <span>{{ value }}</span>
        <el-button v-if="value.length > 0" type="text" size="mini" v-clipboard="{ text: value }">复制</el-button>
      </div>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope, xStore } from "@/js/mixins";

export default {
  mixins: [FormScope, xStore],
  name: "input-app-track-url",
  props: {
    value: {
      type: String,
      default: ""
    },
    label: {
      type: [String],
      default: "监测地址"
    }
  },
  data() {
    return {};
  },
  methods: {
    // handleInput(event) {
    //   this.$emit("input", event);
    // },
    // handleChange(event) {
    //   this.$emit("change", event);
    // }
  },
  watch: {},
  computed: {},
  mounted() {}
};
</script>
