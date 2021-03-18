<template>
  <div class="input-block" v-if="!loading">
    <el-form-item :label="label">
      <el-radio-group v-model="curVal.all_region" >
        <el-radio-button
          v-for="(item, key) in districtType"
          v-if="item.show"
          :key="key"
          :label="item.value">{{ item.label }}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>

    <el-form-item label="" v-show="curVal.all_region === '0'">
      <template v-if="false">
        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
        <div style="margin: 15px 0;"></div>
        <el-checkbox-group v-model="curVal.region" @change="handleCheckedCitiesChange">
          <el-checkbox
            v-for="(item, key) in provinceList"
            :key="key"
            :label="item.value">{{item.label}}
          </el-checkbox>
        </el-checkbox-group>
      </template>
      <BytedSelect
        :list="provinceList"
        v-model="curVal.region"></BytedSelect>
    </el-form-item>
  </div>
</template>

<script>
import { getUchcData } from "@/js/api/common";
import { FormScope } from "@/js/mixins";

import BytedSelect from "@/js/components/BytedSelect";

export default {
  mixins: [FormScope],
  name: "input-district",
  components: {
    BytedSelect
  },
  props: {
    value: {
      type: [Object],
      required: true
    },
    label: {
      type: [String],
      default: "投放地域"
    }
  },
  data() {
    return {
      loading: true,
      isIndeterminate: false,
      curVal:
        this.value === undefined || this.value === null
          ? {
              all_region: "1", // 投放地域定向
              region: [] // 地域id集合
            }
          : this.value,
      checkAll: false,
      dataFilter: "",
      districtType: [
        {
          label: "不限",
          value: "1",
          show: true
        },
        {
          label: "省市",
          value: "0",
          show: true
        }
      ],
      provinceList: {}
    };
  },
  computed: {
  },
  methods: {
    handleCheckAllChange(val) {
      this.curVal.region = val
        ? this.provinceList.map(item => {
            return item.value;
          })
        : [];
      this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
      let checkedCount = value.length;
      this.checkAll = checkedCount === this.provinceList.length;
      this.isIndeterminate =
        checkedCount > 0 && checkedCount < this.provinceList.length;
      this.curVal.region = this.curVal.region.filter(item => {
        return !isNaN(item);
      });
    }
  },
  watch: {
    curVal: {
      handler: function(val, oldVal) {
        this.$emit("input", this.curVal);
      },
      immediate: true,
      deep: true
    }
  },
  created() {
    getUchcData({
      provinceList: ""
    }).then(response => {
      this.provinceList = response.data.provinceList;
      this.loading = false;
    });
  }
};
</script>
