<template>
  <div class="material-card">
    <el-card>
      <template slot="header" v-if="showHeader" class="clearfix">
        <div class="header" v-if="$slots.header || header">
          <slot name="header">{{ header }}</slot>
        </div>
        <div class="header" v-else>
          <span>{{ material.name }}</span>
          <el-button
            type="text"
            v-if="!formItemDisabled && actionDel"
            @click="actionDel">
            <span class="el-icon-delete"></span>
          </el-button>
          <el-button
            type="text"
            v-if="!formItemDisabled && downloadInfo"
            v-download="downloadInfo">
            <span class="el-icon-download"></span>
          </el-button>
        </div>
      </template>

      <el-row>
        <el-col>
          <img v-if="type==='video'" :src="material.preview" :title="material.name" v-preview="{file_url: downloadInfo.url}" class="image">
          <img v-else :src="material.preview" :title="material.name" v-preview class="image">
        </el-col>
      </el-row>

      <el-row>
        <el-col>
          <div class="bottom" v-if="$slots.bottom || bottom">
            <slot name="bottom">{{ bottom }}</slot>
          </div>
          <div class="bottom" v-else>
            <span :title="material.designer_name" class="bottom-text">{{ material.designer_name }}</span>
          </div>
        </el-col>
      </el-row>
    </el-card>
  </div>
</template>

<script>
import { xStore } from "@/js/mixins";
import { Injector } from "../../mixins";

export default {
  mixins: [xStore, Injector],
  name: "material-card",
  props: {
    type: {
      // video img
      type: String,
      required: true
    },
    material: {},
    showHeader: {
      type: Boolean,
      default: true
    },
    header: {},
    bottom: {},
    downloadInfo: {}, // 下载
    actionDel: {}, // 删除
    actionSel: {} // 选择
  },
  data() {
    return {};
  },
  methods: {},
  computed: {},
  mounted() {}
};
</script>

<style lang="scss" scoped>
@import "~@/sass/mixins/mixins";

.material-card {
  @include b(card) {
    margin: 5px;
    padding: 0px;
    @include e(header) {
      color: #fff;
      padding: 2px;
      background: rgba(0, 0, 0, 0.25);
      // padding: 0 5px;
      button {
        padding: 3px 0px;
        float: right;
        .el-icon-delete,
        .el-icon-download {
          font-size: 17px;
          margin-right: 5px;
          color: #fff;
        }
      }
    }
    @include e(body) {
      padding: 0px 0px;
      .image {
        width: 100%;
        height: 150px;
        display: block;
      }
      .bottom {
        font-size: 14px;
        padding: 2px 4px;
        height: 30px;
        > span {
          display: inline-block;
          overflow: hidden;
          text-overflow: ellipsis;
          white-space: nowrap;
        }
        &-text {
          &-lt {
            width: 35%;
            float: left;
            text-align: left;
          }
          &-rt {
            width: 65%;
            float: right;
            text-align: right;
          }
        }
      }
    }
    .clearfix:before,
    .clearfix:after {
      display: table;
      content: "";
    }
    .clearfix:after {
      clear: both;
    }
  }
}
</style>
