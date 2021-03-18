<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('ad_tag')">
      <el-radio-group v-model="curVal.ad_tag_type" @change="handleTypeChange">
        <el-radio-button
        v-for="(item, key) in adTagsType"
        v-if="item.show"
        :key="key"
        :label="item.value">{{ item.text }}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
    <el-form-item label="" v-if="curVal.ad_tag_type === 1">
      <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全部</el-checkbox>
      <div style="margin: 15px 0;"></div>
      <el-checkbox-group v-model="curVal.ad_tag" @change="handleCheckedChange">
        <el-checkbox
        v-for="(item, key) in adTagsOptions"
        :key="key"
        :label="item.value">{{ item.label }}
        </el-checkbox>
      </el-checkbox-group>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-ad-tags",
  props: {
    value: {
      type: [Object],
      required: true
    },
    label: {
      type: [String],
      default: "兴趣分类"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null
          ? {
              ad_tag_type: null,
              ad_tag: []
            }
          : this.value,
      checkAll: false,
      isIndeterminate: this.value.ad_tag.length > 0 ? true : false,
      adTagsType:
        dataToutiao.adTagsType === undefined || dataToutiao.adTagsType === null
          ? []
          : dataToutiao.adTagsType,
      adTagsOptions:
        dataToutiao.adTagsOptions === undefined ||
        dataToutiao.adTagsOptions === null
          ? []
          : dataToutiao.adTagsOptions.map(item => {
              return {
                value: item.value,
                label: item.label
              };
            })
    };
  },
  methods: {
    handleTypeChange(val) {
      if (val === 0) {
        this.curVal.ad_tag = this.adTagsOptions.map(item => {
          return item.value;
        });
      } else {
        this.curVal.ad_tag = [];
      }
    },
    handleCheckAllChange(val) {
      this.curVal.ad_tag = val
        ? this.adTagsOptions.map(item => {
            return item.value;
          })
        : [];
      this.isIndeterminate = false;
    },
    handleCheckedChange(value) {
      let checkedCount = value.length;
      this.checkAll = checkedCount === this.adTagsOptions.length;
      this.isIndeterminate =
        checkedCount > 0 && checkedCount < this.adTagsOptions.length;
    }
  },
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
