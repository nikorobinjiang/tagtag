<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('ad_group.adConvertId')">
      <el-select
        v-model="curVal"
        filterable>
        <el-option
          v-for="(item, key) in options"
          :key="key"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>
  </div>
</template>

<script>
import { head } from 'lodash'
import { FormScope, xStore } from "@/js/mixins";
import { getUchcv2Data } from "@/js/api/common";

export default {
  mixins: [FormScope, xStore],
  name: "InputAdConvertId",
  props: {
    promoteId: {
      required: true
    },
    value: {
      type: Number,
      required: true
    },
    label: {
      type: [String],
      default: "转化名称"
    }
  },
  data() {
    return {
      options: []
    };
  },
  methods: {},
  watch: {
    curVal: function(val, oldVal) {
      this.$emit("input", val ? val : 0);
    }
  },
  computed: {
    curVal: {
      get() {
        if (this.options.map(item => item.value).indexOf(this.value) === -1) {
          const item = head(this.options)
          if (item) {
            this.$emit('input', item.value)
            return item.value
          } else {
            return null
          }
        }
        return this.value
      },
      set(payload) {
        this.$emit('input', payload)
      }
    }
  },
  mounted() {
    getUchcv2Data({
      promoteId: this.promoteId,
      adConvertList: {}
    }).then(response => {
      this.options = response.data.adConvertList;
    });
  }
};
</script>
