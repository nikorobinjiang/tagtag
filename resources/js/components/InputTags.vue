<template>
  <div class="input-block">
    <el-input
      class="input-new-tag"
      v-if="inputVisible && !isShow"
      v-model="inputValue"
      size="small"
      :placeholder="placeholder"
      @keyup.enter.native="handleInputConfirm"
      @blur="handleInputConfirm">
      <template slot="append">
        <label @click="handleInputConfirm">添加</label>
      </template>
    </el-input>
    <div class="input-keywords-result">
      <el-tag
        v-for="tag in curVal"
        :key="tag"
        :closable="!isShow"
        :disable-transitions="false"
        @close="handleClose(tag)">
        {{ tag }}
      </el-tag>
    </div>
  </div>
</template>

<script>
export default {
  name: "input-tags",
  props: {
    value: {
      type: [Array, String],
      required: true
    },
    placeholder: {
      type: String,
      default: "空格分隔，最多二十个，每个标签不超过10个字符"
    },
    isShow: {
      default: false
    }
  },
  data() {
    return {
      curVal: [],
      inputValue: ""
    };
  },
  methods: {
    handleClose(tag) {
      this.curVal.splice(this.curVal.indexOf(tag), 1);
    },
    handleInputConfirm() {
      let hasOutLimit = false;
      let inputValue = this.inputValue
        .split(" ")
        .map(item => {
          return _.trim(item);
        })
        .filter(item => {
          if (item.length > 10) {
            hasOutLimit = true;
          }
          return item.length <= 10 && item.length > 0;
        });
      if (inputValue.length > 0) {
        this.curVal.push(...inputValue);
        this.curVal = _.uniq(this.curVal);
        this.curVal = this.curVal.slice(0, 20);
      }
      this.inputValue = "";
    },
    loadValue() {
      if (this.value) {
        if (this.curValType === "string") {
          this.curVal = this.value.split(",");
        } else {
          this.curVal = this.value;
        }
      } else {
        this.curVal = [];
      }
    }
  },
  watch: {
    value: function(val, oldVal) {
      this.loadValue();
    },
    curVal: function(val, oldVal) {
      const res =
        this.curValType === "string" ? this.curVal.join(",") : this.curVal;
      if (this.value !== res) {
        this.$emit("input", res);
      }
    }
  },
  computed: {
    curValType: function() {
      return typeof this.value === "string" ? "string" : "array";
    },
    inputVisible: function() {
      if (this.curVal.length >= 20) {
        return false;
      }
      return true;
    }
  },
  created() {
    this.loadValue();
  }
};
</script>

<style>
.input-keywords-result {
  padding-top: 10px;
}
.el-tag + .el-tag {
  margin-left: 10px;
}
.button-new-tag {
  margin-left: 10px;
  height: 32px;
  line-height: 30px;
  padding-top: 0;
  padding-bottom: 0;
}
</style>
