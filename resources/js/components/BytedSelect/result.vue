<template>
  <div class="byted-select-moduler">
    <div class="byted-select-moduler-header">
      <span class="byted-select-moduler-header-title">
        {{ title }}
        <el-button type="text" @click="handleRemoveAll()">清空全部</el-button>
      </span>
    </div>
    <div class="byted-select-moduler-container"
      :xs="12">
      <el-col class="byted-select-moduler-list"
        v-for="(id, key) in value" :key="key"
        v-if="list[id]">
        <div class="byted-select-moduler-item" >
          {{ list[id].name }}
          <!-- {{ item.id }} -->
          <el-button type="text" icon="el-icon-close" @click="handleRemove(id)"></el-button>
        </div>
      </el-col>
    </div>
  </div>
</template>

<script>
import { xor, uniq, values, difference } from "lodash";

export default {
  name: "byted-select-result",
  props: {
    title: {
      type: String,
      default: ""
    },
    list: {
      type: Object,
      required: true
    },
    value: {
      type: Array,
      default: _ => []
    }
  },
  data() {
    return {
      listArr: values(this.list),
      curVal: []
    };
  },
  methods: {
    handleRemove: function(id) {
      this.curVal.splice(this.curVal.indexOf(id), 1);
    },
    handleRemoveAll: function() {
      this.curVal = [];
    },
    transferChange: function(val, oldVal) {
      this.$emit("transferChange", val, oldVal);
    },
    transInfoClick: function(item) {
      this.$emit("transInfoClick", item.id);
    }
  },
  watch: {
    value: {
      handler: function(val, oldVal) {
        this.curVal = val;
      },
      immediate: true
    },
    curVal: function(val, oldVal) {
      const diffAdd = difference(val, oldVal);
      const diffDel = difference(oldVal, val);
      const diffXor = xor(val, oldVal);

      if (diffXor.length > 0) {
        this.$emit(
          "transferChange",
          this.listArr.filter(item => val.indexOf(item.id) !== -1),
          this.listArr.filter(item => oldVal.indexOf(item.id) !== -1)
        );
        this.$emit("input", uniq(val));
      }
    }
  },
  computed: {}
};
</script>

<style lang="scss" scoped>
@import "index.scss";
</style>
