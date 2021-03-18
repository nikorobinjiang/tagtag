<template>
  <div class="input-block">
    <el-form-item :label="label" v-if="false">
      <el-radio-group v-model="curSmartInventory">
        <el-radio-button
        v-for="(item, key) in inventoryTypeMode"
        :key="key"
        :label="item.value">{{ item.text }}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
    <el-form-item :label="label" :prop="getFormScopedField('inventory_type')">
      <el-checkbox-group v-model="curInventoryType">
        <el-checkbox
        v-for="(item, key) in inventoryTypeList"
        v-if="item.value !== 0"
        :key="key"
        :label="item.value">{{ item.text }}
        </el-checkbox>
      </el-checkbox-group>
    </el-form-item>
  </div>
</template>

<script>
import { uniq } from "lodash";

import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-ad-inventory",
  props: {
    smartInventory: {
      type: [Number],
      required: true
    },
    inventoryType: {
      type: [Array],
      required: true
    },
    label: {
      type: [String],
      default: "投放位置"
    }
  },
  data() {
    return {
      curSmartInventory: this.smartInventory,
      curInventoryType:
        this.inventoryType === undefined || this.inventoryType === null
          ? []
          : this.inventoryType,
      inventoryTypeMode: dataToutiao.inventoryTypeMode,
      inventoryTypeList: dataToutiao.inventoryType
    };
  },
  watch: {
    curSmartInventory: {
      handler: function(val) {
        this.$emit("update:smartInventory", val);
      },
      immediate: true
    },
    curInventoryType: {
      handler: function(val) {
        this.$emit(
          "update:inventoryType",
          uniq(
            val.map(item => {
              if (item == "INVENTORY_TEXT_LINK") {
                return "INVENTORY_FEED";
              } else {
                return item;
              }
            })
          )
        );
      },
      immediate: true,
      deep: true
    }
  },
  computed: {},
  created() {
    this.curSmartInventory = 0;
  }
};
</script>
