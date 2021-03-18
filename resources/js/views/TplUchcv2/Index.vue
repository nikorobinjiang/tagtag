<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>广告模板</el-breadcrumb-item>
          <el-breadcrumb-item>UC头条</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-search">
      <el-form :inline="true" ref="search" :model="search" label-width="80px" size="mini">
        <el-form-item label="模板名称">
          <el-input v-model="search.name" @keyup.enter.native="onSubmit"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="reloadList()">查询</el-button>
        </el-form-item>
      </el-form>
    </el-row>
    <el-row class="row-actions">
      <el-col>
        <el-button type="primary" size="mini" v-newtab="{url: urlCreate}">创建模板</el-button>
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
          <template slot-scope="scope">{{ scope.row.id }}</template>
        </el-table-column>
        <el-table-column
          prop="title"
          label="模板名称"
          min-width="200">
        </el-table-column>
        <el-table-column
          prop="created_at"
          label="创建时间"
          min-width="160"
          show-overflow-tooltip>
        </el-table-column>
        <el-table-column
          fixed="right"
          label="操作"
          width="165">
          <template slot-scope="scope">
            <el-button type="text" size="small" v-newtab="{url: editItemUrl(scope.row.id)}">编辑</el-button>
            <el-button type="text" size="small" @click="handleDestory(scope.row)">删除</el-button>
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
import {
  fetchList,
  createItem,
  editItemUrl,
  destroyItem
} from "@/js/api/uchcv2Tpl";

import Form from "./Form";

export default {
  name: "gmp-tpl-uchcv2-tpl-index",
  components: {
    Form
  },
  props: {},
  data: function() {
    return {
      editItemUrl,

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
        this.dialogTitle = "创建模板";
        this.dialogId = null;
      } else if (["edit"].indexOf(action) !== -1) {
        this.dialogTitle = "编辑模板";
        this.dialogId = item.agent_id;
      } else if (["show"].indexOf(action) !== -1) {
        this.dialogTitle = "查看模板";
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
    // 删除
    handleDestory(item) {
      this.$confirm("是否确认删除该模版", "", {
        confirmButtonText: "确定",
        cancelButtonText: "取消",
        type: "warning"
      })
        .then(() => {
          this.loading = true;
          destroyItem(item.id)
            .then(response => {
              this.$message({
                message: response.data.message,
                center: true
              });
              this.reloadList();
              this.loading = false;
            })
            .catch(error => {
              this.loading = false;
              console.log(error);
            });
        })
        .catch(() => {});
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
  computed: {
    urlCreate: function() {
      return createItem();
    }
  },
  created() {
    this.reloadList();
  },
  mounted() {}
};
</script>

