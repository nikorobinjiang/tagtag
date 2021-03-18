<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-radio-group v-model="curVal.district" >
        <el-radio-button
          v-for="(item, key) in districtType"
          v-if="item.show"
          :key="key"
          :label="key">{{ item.text }}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>

    <el-form-item label="" v-show="curVal.district === 'CITY'">
      <el-radio-group v-model="curVal.location_type" v-show="curVal.district === 'CITY'">
        <el-radio
          v-for="(item, key) in locationType"
          v-if="item.show"
          :key="key"
          :label="key" >{{ item.text }}
        </el-radio>
      </el-radio-group>
    </el-form-item>

    <el-form-item label="" v-show="curVal.district === 'CITY'">
      <template v-if="false">
        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
        <div style="margin: 15px 0;"></div>
        <el-checkbox-group v-if="false" v-model="curVal.city" @change="handleCheckedCitiesChange">
          <el-checkbox
            v-for="(item, key) in cityOptions"
            :label="item.id"
            :key="key">{{item.name}}
          </el-checkbox>
        </el-checkbox-group>
      </template>
      <BytedSelect
        :list="cityOptions"
        v-model="curVal.city"></BytedSelect>
    </el-form-item>
  </div>
</template>

<script>
import { values } from "lodash";
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

import BytedSelect from "@/js/components/BytedSelect";

export default {
  mixins: [FormScope],
  name: "input-district",
  components: {
    BytedSelect
  },
  props: {
    value: {},
    label: {
      type: [String],
      default: "地域"
    }
  },
  data() {
    let curVal =
      this.value === undefined || this.value === null
        ? {
            district: null,
            location_type: null,
            city: []
          }
        : this.value;
    return {
      curVal,
      checkAll: false,
      dataFilter: "",
      isIndeterminate: curVal.city.length > 0 ? true : false,
      districtType:
        dataToutiao.districtType === undefined ||
        dataToutiao.districtType === null
          ? []
          : dataToutiao.districtType,
      locationType:
        dataToutiao.locationType === undefined ||
        dataToutiao.locationType === null
          ? []
          : dataToutiao.locationType,
      cityOptions:
        dataToutiao.cityOptions === undefined ||
        dataToutiao.cityOptions === null
          ? {}
          : dataToutiao.cityOptions
    };
  },
  methods: {
    handleCheckAllChange(val) {
      this.curVal.city = val
        ? this.cityOptions.map(item => {
            return item.id;
          })
        : [];
      this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
      let checkedCount = value.length;
      this.checkAll = checkedCount === this.cityOptions.length;
      this.isIndeterminate =
        checkedCount > 0 && checkedCount < this.cityOptions.length;
      this.curVal.city = this.curVal.city.filter(item => {
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
  }
};
</script>
