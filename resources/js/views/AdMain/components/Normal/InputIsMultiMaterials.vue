<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('is_multi_materials')">
      <el-radio-group
        :disabled="disabled"
        v-model="curVal">
        <el-radio
          v-for="(item, key) in options"
          v-if="item.show"
          :key="key"
          :label="item.value">{{ item.label }}
        </el-radio>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-is-multi-materials",
  props: {
    styleIds: {},
    value: {
      type: [Boolean],
      default: false
    },
    label: {
      type: [String],
      default: "支持多组素材"
    }
  },
  data() {
    return {
      curVal: this.value === undefined || this.value === null ? true : this.value,
      options: [
        {
          label: "是",
          value: true,
          show: this.formScope === "Uchc" ? false : true
        },
        {
          label: "否",
          value: false,
          show: true
        }
      ]
    };
  },
  methods: {},
  watch: {
    curVal: {
      handler: function() {
        this.$emit("input", this.curVal);
      },
      deep: true
    }
  },
  computed: {
    disabled: function() {
      let res = this.styleIds.length > 1 ? true : false;
      // if (
      //   this.options.filter(item => {
      //     return item.show;
      //   }).length <= 1
      // ) {
      //   res = true;
      // }
      return res;
    }
  },
  mounted() {
    if (this.formScope === "Uchc") {
      this.curVal = false;
    }
  }
};
</script>
