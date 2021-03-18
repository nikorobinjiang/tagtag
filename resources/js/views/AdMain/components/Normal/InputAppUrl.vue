<template>
  <div class="input-block">
    <el-form-item :label="label">
      <div>
        <span>{{ displayText }}</span>
        <el-button v-if="displayText.length > 0" type="text" size="mini" v-clipboard="{ text: displayText }">复制</el-button>
      </div>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope, xStore } from "@/js/mixins";

export default {
  mixins: [FormScope, xStore],
  name: "input-app-url",
  props: {
    gameList: {
      type: Array,
      required: true
    },
    value: {
      type: [Number],
      default: 0
    },
    label: {
      type: [String],
      default: "下载链接"
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
  computed: {
    displayText: function() {
      let game = null;
      this.gameList.forEach(item => {
        if (item.value === this.value) {
          game = item;
        }
      });
      if (game) {
        return game.url;
      } else {
        return "";
      }
    }
  },
  mounted() {}
};
</script>
