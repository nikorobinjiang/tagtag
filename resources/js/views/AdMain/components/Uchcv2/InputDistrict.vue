<template>
  <div class="input-block" v-if="!loading">
    <el-form-item :label="label">
      <el-radio-group v-model="allRegionCal" >
        <el-radio-button
          v-for="(item, key) in districtTypeCal"
          :key="key"
          :label="item.value">{{ item.label }}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>

    <el-form-item label="" v-show="allRegion === '0'">
      <template v-if="false">
        <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
        <div style="margin: 15px 0;"></div>
        <el-checkbox-group v-model="regionCal" @change="handleCheckedCitiesChange">
          <el-checkbox
            v-for="(item, key) in provinceList"
            :key="key"
            :label="item.value">{{item.label}}
          </el-checkbox>
        </el-checkbox-group>
      </template>
      <BytedSelect
        :list="provinceList"
        v-model="regionCal"/>
    </el-form-item>
  </div>
</template>

<script>
import { getUchcv2Data } from "@/js/api/common";
import { FormScope } from "@/js/mixins";

import BytedSelect from "@/js/components/BytedSelect";

export default {
  mixins: [FormScope],
  name: "InputDistrict",
  components: {
    BytedSelect
  },
  props: {
    allRegion: {
      type: String,
      required: true
    },
    region: {
      type: Array,
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
    allRegionCal: {
      get() {
        if (['0', '1'].indexOf(this.allRegion) === -1) {
        this.$emit("update:allRegion", '1');
        }
        return this.allRegion
      },
      set(payload) {
        this.$emit("update:allRegion", payload);
      }
    },
    regionCal: {
      get() {
        return this.region
      },
      set(payload) {
        this.$emit("update:region", payload);
      }
    },
    districtTypeCal: function() {
      return this.districtType.filter(item => item.show)
    }
  },
  methods: {
    handleCheckAllChange(val) {
      this.regionCal = val
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
      this.regionCal = this.regionCal.filter(item => {
        return !isNaN(item);
      });
    }
  },
  watch: {
  },
  created() {
    getUchcv2Data({
      provinceList: ""
    }).then(response => {
      this.provinceList = response.data.provinceList;
      this.loading = false;
    });
  }
};
</script>
