<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('ad_category')">
      <el-input
        placeholder="请选择"
        v-model="adCategoryDisplay"
        @focus="handleShowMenuFocus">
      </el-input>
    </el-form-item>

    <el-form-item label="" v-show="showMenu">
      <el-row>
        <el-col :xs="10" :sm="8" :lg="5" class="left-dropdown-cont">
          <el-radio-group v-model="level_1">
            <el-radio-button
              v-for="(item, key) in levelOne()"
              :key="key"
              :label="item.id">{{ item.value }}
            </el-radio-button>
          </el-radio-group>
        </el-col>

        <el-col :xs="14" :sm="16" :lg="15" class="second-dropdown-cont">
          <el-row
            v-for="(item, key) in levelTwo()"
            :key="key">
            <el-col :xs="11" :sm="8" :lg="4" v-show="level_1 != ''">
              <el-radio-group v-model="level_2">
                <el-radio-button
                  :label="item.id">{{ item.value }}
                </el-radio-button>
              </el-radio-group>
            </el-col>
            <el-col :xs="11" :sm="16" :lg="20" v-show="level_1 != ''">
              <el-radio-group v-model="level_3">
                <el-radio-button
                  v-for="(itemThree, keyThree) in levelThree(item.id)"
                  :key="keyThree"
                  :label="itemThree.id">{{ itemThree.value }}
                </el-radio-button>
              </el-radio-group>
            </el-col>
          </el-row>
        </el-col>
      </el-row>
    </el-form-item>
  </div>
</template>

<script>
import { values } from "lodash";

import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-ad-category",
  props: {
    value: {
      type: [Number],
      default: null
    },
    label: {
      type: [String],
      default: "创意分类"
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null ? null : this.value,
      level_1: null,
      level_2: null,
      level_3: null,
      showMenu: false,
      industryCategory: dataToutiao.industryCategory
    };
  },
  methods: {
    handleInput(event) {
      this.$emit("input", curVal);
    },
    handleChange(event) {
      this.$emit("change", curVal);
    },
    levelOne() {
      return this.industryCategory["1"];
    },
    levelTwo() {
      let res = values(this.industryCategory["2"]).filter(item => {
        return item.parent === this.level_1;
      });
      return res;
    },
    levelThree(level_now) {
      let res = values(this.industryCategory["3"]).filter(item => {
        return item.parent === level_now;
      });
      return res;
    },
    // field 需要提取的字段
    calLevel(field) {
      let res = [];
      let calId = this.curVal;
      let calLevel = 3;
      do {
        Object.keys(this.industryCategory[calLevel.toString()]).forEach(key => {
          let item = this.industryCategory[calLevel.toString()][key];
          if (item.id == calId) {
            calId = item.parent;
            res[calLevel - 1] = field ? item[field] : item;
          }
        });
        calLevel--;
      } while (calLevel > 0);
      return res;
    },
    handleShowMenuFocus() {
      this.showMenu = true;
    }
  },
  watch: {
    current_level: {
      handler: function(val, oldVal) {
        console.log(val, oldVal);
      },
      deep: true
    },
    level_1: function(val, oldVal) {
      if (val !== 0 && this.levelTwo().length <= 0) {
        this.curVal = val;
        this.showMenu = false;
      } else {
        this.level_2 = 0;
        this.level_3 = 0;
      }
    },
    level_2: function(val, oldVal) {
      if (val !== 0 && this.levelThree(val).length <= 0) {
        this.curVal = val;
        this.showMenu = false;
      } else {
        this.level_3 = 0;
      }
    },
    level_3: function(val, oldVal) {
      if (val !== 0) {
        this.curVal = val;
        this.showMenu = false;
      }
    },
    showMenu: function(val, oldVal) {
      this.$emit("input", this.curVal);
      this.$emit("change", this.curVal);
    }
  },
  computed: {
    adCategoryDisplay: {
      get: function() {
        return this.calLevel("value").join(",");
      },
      set: function(val) {}
    }
  }
};
</script>

<style lang="scss">
@import "~@/sass/mixins/mixins";

.left-dropdown-cont,
.second-dropdown-cont {
  float: left;
  border: 1px solid #dcdfe6;
  // padding: 18px;
  height: 500px;
  background-color: #fff;
  overflow-y: scroll;

  @include b(radio-group) {
    @include b(radio-button) {
      @include e(inner) {
        border: none;
        border-radius: 0;
        font-size: 14px;
      }
    }
  }
}

.left-dropdown-cont {
  @include b(radio-group) {
    @include b(radio-button) {
      display: block;
      text-align: center;
    }
  }
}
</style>