<template>
  <div class="byted-select-moduler">
    <div class="byted-select-moduler-header">
      <el-checkbox class="byted-select-moduler-header-checkall"
        v-if="curList.length > 1"
        v-model="checkall">
      </el-checkbox>
      <span class="byted-select-moduler-header-title">
        {{ title }}
      </span>
    </div>
    <el-checkbox-group class="byted-select-moduler-container"
      :xs="12"
      v-model="curVal">
      <el-col class="byted-select-moduler-list"
        v-for="(item, key) in curList" :key="key">
        <el-checkbox class="byted-select-moduler-item"
          :key="item.id"
          :label="item.id">
          <label>{{ '' }}</label>
        </el-checkbox>
        <div :class="['byted-select-moduler-info', item.has_child_lv?'byted-select-moduler-info-has-child':'']"
          @click="transInfoClick(item)">
          <label>{{ item ? item.name : ''}}</label>
          <i v-if="item.has_child_lv" class="el-icon-arrow-right"></i>
        </div>
      </el-col>
    </el-checkbox-group>
  </div>
</template>

<script>
import { xor, uniq, values, difference } from "lodash";

export default {
  name: "byted-select-moduler",
  props: {
    title: {
      type: String,
      default: ""
    },
    list: {
      type: Object,
      required: true
    },
    level: {
      type: Number,
      default: 1
    },
    maxLevel: {
      type: Number,
      default: 1
    },
    parent: {
      type: Number,
      default: 0
    },
    value: {
      type: Array,
      default: _ => []
    }
  },
  data() {
    return {
      listArr: values(this.list),
      curVal: []
    };
  },
  methods: {
    reloadValue() {
      // 当前父
      const parent = this.list[this.parent];
      // 当前父选择的子
      const selected = this.value.filter(id => {
        return this.list[id] && this.list[id].parent === this.parent;
      });
      // 如果对当前父的子没有选择，则当前值为当前父的所有子
      // 否则为当前父选择了的子
      if (
        parent &&
        this.value.indexOf(this.parent) !== -1 &&
        selected.length === 0
      ) {
        // console.log("reloadValue 1", [
        //   this.parent,
        //   this.value,
        //   this.value.indexOf(this.parent) !== -1,
        //   selected,
        //   parent.children
        // ]);
        this.curVal = [...parent.children];
      } else {
        // console.log("reloadValue 2", [this.parent, this.value, selected]);
        this.curVal = selected;
      }
    },
    transferChange: function(val, oldVal) {
      this.$emit("transferChange", val, oldVal);
    },
    transInfoClick: function(item) {
      this.$emit("transInfoClick", item.id);
    }
  },
  watch: {
    list: function(val, oldVal) {
      this.listArr = values(val);
    },
    value: {
      handler: function(val, oldVal) {
        // // 上一级
        // let parentLv = this.level === 0 ? 0 : this.level - 1;
        // let parents = val.filter(id => {
        //   return this.list[id].level === parentLv;
        // });
        // // 当前
        // let mys = val.filter(id => {
        //   return this.list[id].parent === this.parent;
        // });
        // if (parents.indexOf(this.parent) === -1) {
        this.reloadValue();
        // }
      },
      immediate: true
    },
    parent: {
      handler: function(val, oldVal) {
        if (!this.isLoading) {
          // 当前父没有被选择
          if (this.value.indexOf(val) === -1) {
            // console.log("watch parent no", [
            //   this.parent,
            //   this.value,
            //   val,
            //   this.value.filter(id => {
            //     return this.list[id].parent === val;
            //   })
            // ]);
            this.curVal = this.value.filter(id => {
              return this.list[id] && this.list[id].parent === val;
            });
          } else {
            this.curVal = values(this.list)
              .filter(item => {
                return item.parent === val;
              })
              .map(item => item.id);
          }
        }
      },
      immediate: true
    },
    curVal: function(val, oldVal) {
      const diffAdd = difference(val, oldVal);
      const diffDel = difference(oldVal, val);
      const diffXor = xor(val, oldVal);

      if (diffXor.length > 0) {
        this.$emit(
          "transferChange",
          this.listArr.filter(item => val.indexOf(item.id) !== -1),
          this.listArr.filter(item => oldVal.indexOf(item.id) !== -1)
        );

        let res = [];
        const children =
          this.parent === 0
            ? values(this.list)
                .filter(item => item.parent === 0)
                .map(item => item.id)
            : this.list[this.parent]
              ? this.list[this.parent].children
              : [];
        if (val.length < children.length) {
          // 未全选 处理
          // console.log("not all", [this.parent, val, this.value]);
          res.push(...val);
          res.push(
            ...this.value.filter(
              id =>
                this.list[id] &&
                this.list[id].parent !== this.parent &&
                id !== this.parent
            )
          );
        } else {
          // 全选 处理
          res = this.value.filter(id => children.indexOf(id) === -1);
          if (this.parent === 0) {
            res.push(...children);
          } else {
            res.push(this.parent);
          }
          // console.log("all", [this.parent, val, children]);
        }
        // 若是值增加，则去除对应的子
        if (diffAdd.length > 0) {
          let effectIds = [];
          diffAdd.forEach(id => {
            const addItem = this.list[id];
            // console.log(addItem, addItem.parent, this.parent);
            effectIds.push(...addItem.children);
          });
          // console.log("remove childrent", this.parent, diffAdd, effectIds);
          res = res.filter(id => effectIds.indexOf(id) === -1);
        }
        // 若是值减少，则去除对应的父
        // if (diffDel.length > 0) {
        //   let delIds = [];
        //   diffDel.forEach(id => {
        //     const delItem = this.list[id];
        //     if (
        //       delItem &&
        //       delItem.level === this.level + 1 &&
        //       delItem.children.length > 0
        //     ) {
        //       delIds.push(...delItem.children);
        //     }
        //   });
        //   res = res.filter(id => delIds.indexOf(id) !== -1);
        // }
        // console.log("curVal Result:", [this.parent, res]);
        this.$emit("input", uniq(res));
      }
    }
  },
  computed: {
    curList: function() {
      return this.listArr
        .filter(
          item => item.parent === this.parent && item.level === this.level
        )
        .map(item => {
          item.has_child_lv = false;
          this.listArr.forEach(el => {
            if (
              el.parent === item.id &&
              el.level === this.level + 1 &&
              el.level <= this.maxLevel
            ) {
              item.has_child_lv = true;
            }
          });
          return item;
        });
    },
    checkall: {
      get: function() {
        if (this.curVal) {
          return this.curVal.length === this.curList.length;
        } else {
          return false;
        }
      },
      set: function(val) {
        if (val) {
          this.curVal = this.listArr
            .filter(item => {
              return item.parent === this.parent && item.level === this.level;
            })
            .map(item => item.id);
        } else {
          this.curVal = [];
        }
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import "index.scss";
</style>

