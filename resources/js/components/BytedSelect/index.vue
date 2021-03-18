<template>
  <el-row>
    <el-col :span="6">
      <BytedSelectPanelModuler
        title="省份"
        :list="list"
        :maxLevel="maxLevel"
        :level="1"
        :parent="0"
        v-model="curVal"
        @transferChange="transferLv1Change"
        @transInfoClick="transferLv1InfoClick">
      </BytedSelectPanelModuler>
    </el-col>
    <el-col :span="6">
      <BytedSelectPanelModuler
        title="城市"
        :list="list"
        :maxLevel="maxLevel"
        :level="2"
        :parent="parentLv2"
        v-model="curVal"
        @transferChange="transferLevelTwoChange">
      </BytedSelectPanelModuler>
    </el-col>
    <el-col :span="4">
      <BytedSelectPanelResult
        title="已选"
        :list="list"
        v-model="curVal">
      </BytedSelectPanelResult>
    </el-col>
  </el-row>
</template>

<script>
import { values, remove, difference, xor } from "lodash";
import dataToutiao from "@/js/store/data/Toutiao";

import BytedSelectPanelModuler from "./moduler";
import BytedSelectPanelResult from "./result";

export default {
  name: "byted-select",
  components: {
    BytedSelectPanelModuler,
    BytedSelectPanelResult
  },
  props: {
    list: {
      type: Object,
      required: true
    },
    value: {
      type: Array,
      required: true
    },
    maxLevel: {
      type: Number,
      default: 2
    }
  },
  data() {
    return {
      // list: dataToutiao.cityOptions,
      // items: [],
      parentLv2: null,
      itemsOne: [],
      itemsTwo: [],
      defaultProps: {
        children: "children",
        label: "label"
      }
    };
  },
  methods: {
    transferLv1Change: function(val, oldVal) {
      // console.log('transferLv1Change');
      const diffAdd = difference(val, oldVal);
      const diffDel = difference(oldVal, val);
      if (diffAdd.length > 0) {
        // this.items.push(diffAdd[0].id)
        this.parentLv2 = diffAdd[0].id;
      }

      // // 一级选项联动
      // const diffXor = xor(oldVal, val);
      // diffXor.forEach(id => {
      //   if (this.items.indexOf(id) === -1) {
      //     this.items.push(id);
      //   } else {
      //     this.items.splice(this.items.indexOf(id), 1);
      //   }
      // });
    },
    transferLv1InfoClick: function(parent) {
      // console.log("transferLv1InfoClick");
      this.parentLv2 = parent;
    },
    transferLevelTwoChange: function(val, oldVal) {
      // const diffAdd = difference(val, oldVal);
      // const diffDel = difference(oldVal, val);
      // if (diffAdd.length > 0) {
      //   const opItem = diffAdd[0];
      //   const opParent = dataToutiao.cityOptions[opItem.parent];
      //   if (this.itemsOne.indexOf(opParent.id) === -1) {
      //     this.itemsOne.push(opParent.id);
      //   }
      // } else if (diffDel.length > 0) {
      //   diffDel.forEach(opItem => {
      //     this.itemsOne = this.itemsOne.filter(itemId => {
      //       return opItem.parent !== itemId;
      //     });
      //   });
      // }
    }
  },
  computed: {
    curVal: {
      get() {
        return this.value
      },
      set(payload) {
        this.$emit('input', payload)
      }
    }
  },
  watch: {
    // curVal: {
    //   handler: function(val, oldVal) {
    //     this.$emit("input", this.curVal);
    //   },
    //   immediate: true,
    //   deep: true
    // }
  }
};
</script>
