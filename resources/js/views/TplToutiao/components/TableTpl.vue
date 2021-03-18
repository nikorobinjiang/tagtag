<template>
  <div v-loading="loading">
    <el-row class="row-search">
      <el-form :inline="true" :model="search" label-width="120px" size="mini" @submit.native.prevent>
        <el-form-item label="模板名称">
          <el-input v-model="search.name" @keyup.enter.native="reloadList()"></el-input>
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
    <el-row>
      <el-table
        ref="tabGroup"
        :data="data"
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
          min-width="160"
          show-overflow-tooltip>
        </el-table-column>
        <el-table-column
          prop="created_at"
          label="创建时间"
          width="160"
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
  </div>
</template>

<script>
import { xStore } from "@/js/mixins";
import {
  fetchList,
  createItem,
  editItemUrl,
  destroyItem
} from "@/js/api/toutiaoTpl";

import dataToutiao from "@/js/store/data/Toutiao";

export default {
  mixins: [xStore],
  name: "table-tpl",
  props: {},
  data() {
    return {
      editItemUrl,

      loading: true,
      promoteList: [],
      search: {
        name: "",
        media_id: null,
        promote_id: null
      },
      list: {
        current_page: 1,
        data: [],
        per_page: 20,
        total: 0
      },
      multipleSelection: []
    };
  },
  methods: {
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
    data: function() {
      return this.list.data ? this.list.data : [];
    },
    urlCreate: function() {
      return createItem();
    }
  },
  created() {
    this.reloadList();
  }
};
</script>

<style lang="scss" scoped>
.el-dropdown-link {
  padding-left: 10px;
}
</style>
