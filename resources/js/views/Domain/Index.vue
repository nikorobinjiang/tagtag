<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>域名管理</el-breadcrumb-item>
          <el-breadcrumb-item>域名列表</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-search">
      <el-form :inline="true" :model="search" label-width="100px" size="mini" @submit.native.prevent>
        <el-form-item label="域名">
          <el-input v-model="search.name" @keyup.enter.native="reloadList()"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="reloadList()">查询</el-button>
        </el-form-item>
      </el-form>
    </el-row>
    <el-row class="row-table">
      <el-tabs v-model="activeTab">
        <el-tab-pane label="落地页域名" name="page">
          <TablePage :data-search-name="search.name"
            ref="tablePage">
          </TablePage>
        </el-tab-pane>
        <el-tab-pane label="游戏域名" name="game">
          <TableGame :data-search-name="search.name"
            ref="tableGame">
          </TableGame>
        </el-tab-pane>
        <el-tab-pane label="反劫持域名" name="down_site">
          <TableDown :data-search-name="search.name"
            ref="tableDownSite">
          </TableDown>
        </el-tab-pane>
      </el-tabs>
    </el-row>
  </div>
</template>

<script>
import { mapStateGetSet } from "@/js/utils";
import { xStore } from "@/js/mixins";

import TablePage from "./components/TablePage";
import TableGame from "./components/TableGame";
import TableDown from "./components/TableDown";

export default {
  mixins: [xStore],
  name: "gmp-domian-main-index",
  components: {
    TablePage,
    TableGame,
    TableDown
  },
  props: {},
  data: function() {
    return {
      activeTab: "page",
      search: {
        name: ""
      }
    };
  },
  methods: {
    reloadList: function() {
      ["tablePage", "tableGame", "tableDownSite"].map(item => {
        console.log(this.search.name);
        this.$refs[item].reloadList();
      });
    }
  },
  watch: {},
  computed: {},
  created() {
    this.xStoreLoadExtraConfig();
  },
  mounted() {}
};
</script>
