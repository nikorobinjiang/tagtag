<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('style_ids')">
      <template v-if="calOptions.length > 0">
        <el-checkbox-group
          :disabled="disabled"
          v-model="curVal"
          :max="isMultiMaterials ? 999 : 1">
          <el-checkbox
            v-for="(item, key) in calOptions"
            :disabled="!item.show"
            :key="key"
            :label="item.value">{{ item.label }}
          </el-checkbox>
        </el-checkbox-group>
      </template>
      <template v-else>无</template>
    </el-form-item>
  </div>
</template>

<script>
import { xor } from "lodash";
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-styles",
  props: {
    value: {
      type: [Array],
      default: _ => []
    },
    options: {
      type: [Array],
      default: _ => []
    },
    isMultiMaterials: {
      type: [Boolean],
      default: false
    },
    materialInfo: {
      type: [Object]
    },
    disabled: {
      type: Boolean,
      default: false
    },
    label: {
      type: [String],
      default: "素材样式"
    }
  },
  data() {
    return {};
  },
  watch: {},
  methods: {},
  computed: {
    curVal: {
      get: function() {
        return this.value === undefined || this.value === null
          ? []
          : this.value;
      },
      set: function(val) {
        let diff = xor(this.value, val);
        let needConfirm = false;
        diff.forEach(id => {
          if (this.materialInfo[id]) {
            needConfirm = true;
          }
        });
        if (val.length < this.value.length && needConfirm) {
          this.$confirm("取消勾选将会清除该样式素材", "", {
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            type: "warning"
          })
            .then(_ => {
              this.$emit("input", val);
            })
            .catch(_ => {});
        } else {
          this.$emit("input", val);
        }
      }
    },
    calOptions: function() {
      return this.options.filter(item => {
        return item.show
      });
    }
  },
  mounted() {}
};
</script>
