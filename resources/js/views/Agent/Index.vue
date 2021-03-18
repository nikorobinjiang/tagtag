<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>合作代理商</el-breadcrumb-item>
          <el-breadcrumb-item>代理商管理</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-search">
      <el-form :inline="true" ref="search" :model="search" label-width="100px" size="mini">
        <el-form-item label="代理商名称">
          <el-input v-model="search.name" @keyup.enter.native="onSubmit"></el-input>
        </el-form-item>
        <el-form-item label="负责媒体">
          <el-select v-model="search.media_id" clearable filterable placeholder="请选择所属媒体">
            <el-option
              v-for="(item, key) in mediaList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label=" 负责游戏">
          <el-select v-model="search.game_id" clearable filterable placeholder="请选择游戏名称">
            <el-option
              v-for="(item, key) in gameList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="reloadList()">查询</el-button>
        </el-form-item>
      </el-form>
    </el-row>
    <el-row class="row-actions">
      <el-col>
        <el-button type="primary" size="mini" @click="handlerItem('create')">创建代理商</el-button>
      </el-col>
    </el-row>
    <el-row class="row-table">
      <el-table
        v-loading="loading"
        ref="agentTable"
        :data="list.data"
        tooltip-effect="dark"
        style="width: 100%">
        <el-table-column
          label="序号"
          width="60">
          <template slot-scope="scope">{{ scope.row.agent_id }}</template>
        </el-table-column>
        <el-table-column
          prop="agent_name"
          label="代理商名称"
          min-width="200">
        </el-table-column>
        <el-table-column
          prop="account"
          label="代理商账号"
          min-width="160"
          show-overflow-tooltip>
        </el-table-column>
        <el-table-column
          prop="media_names"
          label="负责媒体"
          min-width="160"
          show-overflow-tooltip>
        </el-table-column>
        <el-table-column
          fixed="right"
          label="操作"
          width="165">
          <template slot-scope="scope">
            <el-button type="text" size="small" @click="handlerItem('show', scope.row)">查看</el-button>
            <el-button type="text" size="small" @click="handlerItem('edit', scope.row)">编辑</el-button>
          </template>
        </el-table-column>
      </el-table>
      <el-pagination
        @size-change="handlePagerSizeChange"
        @current-change="handlePagerCurChange"
        :current-page.sync="list.current_page"
        :page-sizes="[20]"
        :page-size="list.per_page"
        layout="total, sizes, prev, pager, next, jumper"
        :total="list.total">
      </el-pagination>
    </el-row>

    <!-- 创建、修改 -->
    <el-dialog
      :visible.sync="dialogVisible"
      :title="dialogTitle"
      :before-close="handlerItemClose">
      <component
        :is="dialogRequestAction==='show'?'Show':'Form'"
        :visible.sync="dialogVisible"
        :requestAction="dialogRequestAction"
        :itemId="dialogId">
      </component>
    </el-dialog>
  </div>
</template>

<script>
import { getBasicData } from "@/js/api/common";
import { fetchList } from "@/js/api/agent";

import Form from "./Form";
import Show from "./Show";

export default {
  name: "gmp-agent-main-index",
  components: {
    Form,
    Show
  },
  props: {},
  data: function() {
    return {
      dialogTitle: "",
      dialogRequestAction: "",
      dialogId: null,
      dialogVisible: false,

      loading: true,
      mediaList: [],
      gameList: [],

      search: {
        name: "",
        media_id: "",
        game_id: ""
      },
      list: {
        current_page: 1,
        data: [],
        per_page: 20,
        total: 0
      }
    };
  },
  methods: {
    handlerItem: function(action, item) {
      this.dialogRequestAction = action;
      if (action === "create") {
        this.dialogTitle = "创建代理商";
        this.dialogId = null;
      } else if (["edit"].indexOf(action) !== -1) {
        this.dialogTitle = "编辑代理商";
        this.dialogId = item.agent_id;
      } else if (["show"].indexOf(action) !== -1) {
        this.dialogTitle = "查看代理商";
        this.dialogId = item.agent_id;
      }
      this.dialogVisible = true;
    },
    handlerItemClose: function() {
      this.dialogVisible = false;
    },
    handlePagerSizeChange(val) {
      console.log(`每页 ${val} 条`);
    },
    // 翻页
    handlePagerCurChange(val) {
      this.reloadList(val);
    },
    // 加载数据
    reloadList(page) {
      this.loading = true;
      if (page === undefined) {
        page = this.list.current_page;
      }

      fetchList(
        Object.assign({}, this.search, {
          page: page
        })
      )
        .then(response => {
          this.list = response.data.list;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    }
  },
  watch: {},
  computed: {},
  created() {
    getBasicData({
      mediaList: "",
      gameList: ""
    })
      .then(response => {
        this.mediaList = response.data.mediaList;
        this.gameList = response.data.gameList;
        this.reloadList();
      })
      .catch(error => {
        console.log(error);
      });
  },
  mounted() {}
};
</script>

