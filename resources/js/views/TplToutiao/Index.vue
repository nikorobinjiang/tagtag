<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>广告模板</el-breadcrumb-item>
          <el-breadcrumb-item>今日头条</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-table">
      <el-tabs v-model="activeTab">
        <el-tab-pane label="广告组" name="group">
          <TableGroup
            ref="tableGroup">
          </TableGroup>
        </el-tab-pane>
        <el-tab-pane label="广告模板" name="tpl">
          <TableTpl
            ref="tableTpl">
          </TableTpl>
        </el-tab-pane>
      </el-tabs>
    </el-row>
  </div>
</template>

<script>
import { mapStateGetSet } from "@/js/utils";

import TableGroup from "./components/TableGroup";
import TableTpl from "./components/TableTpl";

export default {
  mixins: [],
  name: "gmp-tpl-toutiao-index",
  components: {
    TableGroup,
    TableTpl
  },
  props: {},
  data: function() {
    return {
      activeTab: "group"
    };
  },
  methods: {},
  watch: {},
  computed: {},
  created() {},
  mounted() {}
};
</script>
