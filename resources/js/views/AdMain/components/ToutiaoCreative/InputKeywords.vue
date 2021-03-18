<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('ad_keywords')">
      <el-input
        class="input-new-tag"
        v-if="inputVisible"
        v-model="inputValue"
        :placeholder="placeholder"
        @keyup.enter.native="handleInputConfirm"
        @blur="handleInputConfirm">
        <el-button slot="append" @click="handleInputConfirm">添加</el-button>
      </el-input>
      <div class="input-keywords-result">
        <el-tag
          v-for="tag in curVal"
          :key="tag"
          :closable="!formItemDisabled"
          :disable-transitions="false"
          @close="handleClose(tag)">
          {{ tag }}
        </el-tag>
      </div>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope } from "@/js/mixins";
import { Injector } from "../../mixins";

export default {
  mixins: [FormScope, Injector],
  name: "input-keywords",
  props: {
    value: {
      type: [Array],
      required: true
    },
    placeholder: {
      type: String,
      default: "空格分隔，最多二十个，每个标签不超过10个字符"
    },
    label: {
      type: [String],
      default: "创意标签"
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? [] : this.value,
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
        console.log(inputValue);
        this.curVal.push(...inputValue);
        this.curVal = _.uniq(this.curVal);
        this.curVal = this.curVal.slice(0, 20);
      }
      this.inputValue = "";
    }
  },
  watch: {
    curVal: function(val, oldVal) {
      this.$emit("input", this.curVal);
      this.$emit("change", this.curVal);
    }
  },
  computed: {
    inputVisible: function() {
      if (this.curVal.length >= 20) {
        return false;
      }
      return true;
    }
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
