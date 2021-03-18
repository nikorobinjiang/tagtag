<template>
<div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>投放媒体</el-breadcrumb-item>
          <el-breadcrumb-item>媒体账号管理</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-search">
      <el-form :inline="true" ref="adRealtimeDataIndexSearch" :model="search" label-width="120px" size="mini">
        <el-form-item label="媒体名称">
          <el-select v-model="search.search_media" clearable filterable placeholder="请选择媒体名称">
            <el-option
              v-for="(item, key) in mediaList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="代理商">
          <el-select v-model="search.search_agent" clearable filterable placeholder="请选择代理商">
            <el-option
              v-for="(item, key) in agentList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="分发媒体类型">
          <el-select v-model="search.search_distribute" clearable filterable placeholder="请选择分发媒体类型">
            <el-option
              v-for="(item, key) in distributeList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="账号">
          <el-input v-model="search.search_account"></el-input>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="reloadList()">查询</el-button>
        </el-form-item>
      </el-form>
    </el-row>

    <el-row class="row-actions">
      <el-col>
        <el-button type="primary" size="mini" @click="handlerItem('create')">创建媒体账号</el-button>
      </el-col>
    </el-row>

    
    <el-row>
      <el-row>
        <el-col>
          <el-tabs v-model="activeName" @tab-click="handleClick">
            <el-tab-pane label="账号列表" name="list">
              <span slot="label">
                账号列表
                <i class="el-icon-refresh" @click="reloadList('tabList')"></i>
              </span>
              <TablePromoteList ref="tabList" :search="search"></TablePromoteList>
            </el-tab-pane>
            <el-tab-pane label="回收站" name="trashed">
              <span slot="label">
                回收站
                <i class="el-icon-refresh" @click="reloadList('tabTrashed')"></i>
              </span>
              <table-promote-trashed ref="tabTrashed" :search="search"></table-promote-trashed>
            </el-tab-pane>
          </el-tabs>
        </el-col>
      </el-row>
      
    </el-row>

    <!-- 创建、修改 -->
    <el-dialog
      :visible.sync="dialogItemVisible"
      :title="dialogItemTitle"
      :before-close="handlerItemClose">
      <FormPromote
        v-if="dialogItemVisible"
        @reloadList="reloadList"
        :visible.sync="dialogItemVisible"
        :requestAction="dialogItemRequestAction"
        :mediaId="dialogMediaId"
        :itemId="dialogItemId"
        @submitSucc="reloadList()">
      </FormPromote>
    </el-dialog>
</div>
</template>

<script>
import * as Cookies from "js-cookie";

import { getBasicData } from "@/js/api/common";
import { createItem, fetchList } from "@/js/api/promote";
import FormPromote from "./Form";
import TablePromoteList from "./components/TablePromoteList";
import TablePromoteTrashed from "./components/TablePromoteTrashed";

export default {
  name: "gmp-ad-promote-index",
  components: {
    FormPromote,
    TablePromoteList,
    TablePromoteTrashed
  },
  props: {},
  data: function() {
    return {
      activeName: 'list',

      dialogItemTitle: "",
      dialogItemRequestAction: "",
      dialogMediaId: null,
      dialogItemId: null,
      dialogItemVisible: false,

      mediaList: [],
      agentList: [],
      distributeList: [],

      urlCreate: createItem(),
      dataColsBoxVisible: false,
      search: {
        search_media: null,
        search_agent: null,
        search_distribute: ""
      }
      
    };
  },
  methods: {
    handleClick(tab, event) {
      // console.log(tab, event);
    },
    handlerItem: function(action, item) {
      this.dialogItemRequestAction = action;
      if (action === "create") {
        this.dialogItemTitle = "创建媒体账号";
        this.dialogItemId = null;
      } else if (action === "edit") {
        this.dialogItemTitle = "编辑媒体账号";
        this.dialogItemId = item.promote_id;
      }
      this.dialogItemVisible = true;
    },
    handlerItemClose: function() {
      this.dialogItemVisible = false;
    },
    reloadList(item) {
      if (item && this.$refs[item]) {
        this.$refs[item].reloadList();
      } else {
        ["tabList",  "tabTrashed"].map(item => {
          this.$refs[item].reloadList();
        });
      }
    },
  },
  watch: {},
  computed: {},
  created() {
    // 媒体跳转过来的 打开编辑或者新建框
    let scatOpenPromote = Cookies.getJSON("scatOpenPromote");
    if (scatOpenPromote) {
      this.dialogItemRequestAction = scatOpenPromote.action;
      if (scatOpenPromote.action === "edit") {
        this.dialogItemTitle = "编辑媒体账号";
        this.dialogItemId = scatOpenPromote.promote_id;
        this.dialogItemVisible = true;
      } else if (scatOpenPromote.action === "create") {
        this.dialogItemTitle = "创建媒体账号";
        this.dialogMediaId = scatOpenPromote.media_id;
        this.dialogItemVisible = true;
      }
      Cookies.remove("scatOpenPromote");
    }

    this.mediaList = [];
    this.agentList = [];
    this.search.search_media = null;
    this.search.search_agent = null;
    getBasicData({
      mediaList: {},
      agentList: {},
      distributeList: {}
    })
      .then(response => {
        this.mediaList = response.data.mediaList;
        this.agentList = response.data.agentList;
        this.distributeList = response.data.distributeList;
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>

<style lang="scss" scoped>
.row-breadcrumb,
.row-search,
.row-tools,
.row-table {
  padding: 10px;
}
</style>
