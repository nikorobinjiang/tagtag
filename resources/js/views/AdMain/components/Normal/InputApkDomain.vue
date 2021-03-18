<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('ad_tag')">
      <el-select
        v-model="curVal"
        @input="handleInput"
        @change="handleChange"
        filterable
        class="select-apk-domain">
        <el-option
          v-for="(item, key) in domainDownList"
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
import { getBasicData } from "@/js/api/common";
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope, xStore } from "@/js/mixins";

export default {
  mixins: [FormScope, xStore],
  name: "input-apk-domain",
  props: {
    value: {
      type: [String],
      default: ""
    },
    label: {
      type: [String],
      default: "apk域名"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? "0.0" : this.value,
      domainDownList: []
    };
  },
  methods: {
    handleInput(event) {
      this.$emit("input", event);
    },
    handleChange(event) {
      this.$emit("change", event);
    }
  },
  watch: {},
  computed: {},
  mounted() {
    // 加载反劫持域名 apk域名 列表
    getBasicData({
      domainDownList: ""
    })
      .then(response => {
        this.$set(this.$data, "domainDownList", []);
        this.domainDownList = response.data.domainDownList;
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>
