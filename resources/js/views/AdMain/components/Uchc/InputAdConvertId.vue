<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('adgroup.adconvertId')">
      <el-select
        v-model="curVal"
        filterable>
        <el-option
          v-for="(item, key) in options"
          :key="key"
          v-if="(appType === 'iOS' && item.platform === 1)
            || (appType !== 'iOS' && item.platform === 2)"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>
  </div>
</template>

<script>
import { FormScope, xStore } from "@/js/mixins";
import { getUchcData } from "@/js/api/common";

export default {
  mixins: [FormScope, xStore],
  name: "input-adconvert-id",
  props: {
    appType: {
      type: String,
      required: true
    },
    promoteId: {
      required: true
    },
    value: {
      type: [Number],
      required: true
    },
    label: {
      type: [String],
      default: "转化名称"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null || this.value === 0
          ? null
          : this.value,
      options: []
    };
  },
  methods: {},
  watch: {
    curVal: function(val, oldVal) {
      this.$emit("input", val ? val : 0);
    }
  },
  computed: {},
  mounted() {
    getUchcData({
      promoteId: this.promoteId,
      adConvertList: {}
    }).then(response => {
      this.options = response.data.adConvertList;
    });
  }
};
</script>
