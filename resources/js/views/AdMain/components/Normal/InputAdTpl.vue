<template>
  <div class="input-block">
    <el-form-item
    :label="label"
    :prop="getFormScopedField('ad_tpl_id')">
      <el-select
        v-model="curVal"
        filterable
        @change="handleEmitChnage">
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
import { FormScope, xStore } from "@/js/mixins";
import { getBasicData } from "@/js/api/common";

export default {
  mixins: [FormScope, xStore],
  name: "input-ad-tpl",
  props: {
    value: {
      type: [Number],
      default: null
    },
    distribute: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "广告模板"
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
  methods: {
    handleEmitChnage() {
      this.$emit('change', this.curVal)
    }
  },
  watch: {
    curVal: function(val, oldVal) {
      this.$emit("input", val ? val : 0);
    }
  },
  computed: {},
  mounted() {
    getBasicData({
      adTplList: {
        distribute: this.distribute
      }
    })
      .then(response => {
        this.options = response.data.adTplList;
        if (this.options.length > 0) {
        }
        this.$forceUpdate();
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>
