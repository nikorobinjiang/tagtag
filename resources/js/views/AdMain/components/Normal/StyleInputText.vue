<template>
  <div class="input-block style-input-text">
    <el-input
      v-for="(text, textKey) in textList"
      :key="textKey"
      :id="genInputId(text, textKey)"
      :disabled="isDisabled(text)"
      v-model="text.value"
      :minlength="isNaN(text.min_length)?'':parseInt(text.min_length)"
      :maxlength="isNaN(text.max_length)?'':parseInt(text.max_length)"
      :placeholder="genInputPlaceHolder(text)"
      @change="transferText(styleKey, text.name, text, textKey)">
      <template slot="prepend">{{ text.name }}</template>
      <template slot="append">
        <label v-if="countLength(text) !== ''">{{ countLength(text) }}</label>
        <label
          v-if="hasMounted && countWordPackage(genInputId(text, textKey)) === 0 && ['Uchc', 'Uchcv2'].indexOf(formScope) !== -1"
          @click="handleWorkPackageListToggle(genInputId(text, textKey))">
          插入词包
        </label>
      </template>
    </el-input>
    <el-table
      v-loading="wordPackageListLoading"
      class="word-package-list" v-if="wordPackageListShow && (countWordPackage(focusInputIdNow) === 0)"
      :data="wordPackageList"
      height="250"
      style="width: 100%">
      <el-table-column
        prop="packageName"
        label="包名">
      </el-table-column>
      <el-table-column
        prop="defaultWord"
        label="默认词">
      </el-table-column>
      <el-table-column
        prop="converageRate"
        label="覆盖量">
      </el-table-column>
      <el-table-column
        prop="packageTypeDesc"
        label="类型">
      </el-table-column>
      <el-table-column
        label="操作">
        <template slot-scope="scope">
          <el-button type="text" size="small" @click="handleInsertAtCursor(scope.row)">添加</el-button>
          <el-button type="text" size="small" @click="handleClickShowWordPackageInfo(scope.row)">查看</el-button>
        </template>
      </el-table-column>
    </el-table>
    <el-dialog
      title="所有词"
      :visible.sync="dialogWorkPackageItemVisible"
      width="30%">
      <div class="word-package-item" v-if="dialogWorkPackageItem">
        <template v-if="dialogWorkPackageItem.detailWords && dialogWorkPackageItem.detailWords.length > 0">
          <label v-for="(item, key) in dialogWorkPackageItem.detailWords" :key="key">{{ item }}<br /></label>
        </template>
        <template v-else>
          接口内容暂未开放
        </template>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import insertTextAtCursor from "insert-text-at-cursor";

import { getUchcData, getUchcv2Data } from "@/js/api/common";
import { renewObject } from "@/js/utils";
import { FormScope, xStore } from "@/js/mixins";
import { Injector } from "../../mixins";

var regexBraces = /\{(.+?)\}/g; // {} 花括号, 大括号

export default {
  mixins: [FormScope, xStore, Injector],
  name: "style-input-text",
  components: {},
  props: {
    promoteId: {
      type: [Number],
      required: true
    },
    creativeId: {
      default: ""
    },
    styleKey: {},
    textList: {}
  },
  data() {
    return {
      hasMounted: false,
      focusInputIdNow: "",
      wordPackageList: [],
      wordPackageListShow: false,
      wordPackageListLoading: false,
      dialogWorkPackageItem: null,
      dialogWorkPackageItemVisible: false
    };
  },
  methods: {
    transferText: function(idx, field, value, textKey) {
      const el = document.getElementById(this.genInputId(value, textKey));
      const packages = value.value ? value.value.match(regexBraces) : null;
      if (el && el.__uchc_creative_text_package) {
        const elPackage = el.__uchc_creative_text_package;
        if (elPackage && elPackage.id) {
          this.$set(value, "wildcardIds", elPackage.id);
        }
      }
      if ((packages === null || packages.length === 0) && value.wildcardIds) {
        this.$delete(value, "wildcardIds");
      }
      this.$emit("transferText", idx, field, value);
    },
    handleWorkPackageListToggle: function(inputId) {
      this.focusInputIdNow = inputId;
      this.wordPackageListShow = !this.wordPackageListShow;
      if (this.wordPackageList.length <= 0) {
        this.wordPackageListLoading = true;
        if ('Uchc' === this.formScope) {
          getUchcData({
            promoteId: this.promoteId,
            wordPackageList: {}
          }).then(response => {
            this.wordPackageList = response.data.wordPackageList;
            this.wordPackageListLoading = false;
          });
        } else if ('Uchcv2' === this.formScope) {
          getUchcv2Data({
            promoteId: this.promoteId,
            wordPackageList: {}
          }).then(response => {
            this.wordPackageList = response.data.wordPackageList;
            this.wordPackageListLoading = false;
          });
        }
      }
    },
    handleInsertAtCursor: function(row) {
      if (this.countWordPackage(this.focusInputIdNow) === 0) {
        let el = document.getElementById(this.focusInputIdNow);
        el.__uchc_creative_text_package = row;
        insertTextAtCursor(el, `{${row.packageName}}`);
      }
    },
    handleClickShowWordPackageInfo: function(item) {
      this.dialogWorkPackageItem = item;
      this.dialogWorkPackageItemVisible = true;
    },
    countWordPackage(elId) {
      const el = document.getElementById(elId);
      let res;
      if (el) {
        res = el.value.match(regexBraces);
      }
      if (res) {
        return res.length;
      } else {
        return 0;
      }
    },
    genInputId(item, key) {
      return '__uchc_creative_text_' +  [this.styleKey, key, item.name].join("__");
    },
    genInputPlaceHolder(text) {
      return `标题字符范围为${
        isNaN(text.min_length) ? "0" : parseInt(text.min_length)
      } - ${isNaN(text.max_length) ? "无限" : parseInt(text.max_length)}`;
    },
    countLength(item) {
      let res = [];
      let max_length = isNaN(item.max_length)
        ? null
        : parseInt(item.max_length);

      if (item.value) {
        res.push(item.value.length);
      } else {
        res.push(0);
      }
      if (max_length) {
        res.push(max_length);
      }
      return res.join("/");
    },
    enableWordPackage(text, textKey) {
      return (
        this.formScope === "Uchc" &&
        text.name === "title" &&
        this.countWordPackage(this.genInputId(text, textKey)) === 0
      );
    },
    isDisabled(text) {
      if (
        this.formScope === "Uchc" &&
        this.creativeId &&
        this.creativeId.length &&
        text.name === "app_name"
      ) {
        return true;
      } else {
        return false;
      }
    }
  },
  computed: {},
  mounted() {
    this.hasMounted = true
  }
};
</script>

<style lang="scss" scoped>
.style-input-text {
  .word-package-item {
    max-height: 320px;
  }
  input:invalid {
    border-color: #f00;
    padding-left: 5px;
  }

  input:valid {
    border-color: #26b72b;
    padding-left: 5px;
  }
}
</style>
