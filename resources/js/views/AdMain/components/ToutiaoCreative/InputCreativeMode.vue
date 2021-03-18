<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-radio-group v-model="creative_display_mode" @change="handleCreativeDisplayModeChange">
        <el-radio
        v-for="(item, key) in creativeCreateType"
        :key="key"
        border
        :label="item.value">{{ item.text }}</el-radio>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-creative-mode",
  props: {
    state: {},
    label: {
      type: [String],
      default: "创意展现方式"
    }
  },
  data() {
    return {
      creativeCreateType: [
        {
          value: "CREATIVE_DISPLAY_MODE_CTR",
          text: "优选模式"
        },
        {
          value: "CREATIVE_DISPLAY_MODE_RANDOM",
          text: "轮播模式"
        }
      ]
    };
  },
  methods: {
    handleCreativeDisplayModeChange(val) {
      // if (val === "CREATIVE_DISPLAY_MODE_CTR") this.creative_display_mode = [];
    }
  },
  computed: {
    creative_display_mode: {
      get() {
        return this.state.form.creative_display_mode;
      },
      set(val) {
        this.$store.dispatch(
          this.state.getActionName("form.creative_display_mode"),
          val
        );
      }
    }
  }
};
</script>
