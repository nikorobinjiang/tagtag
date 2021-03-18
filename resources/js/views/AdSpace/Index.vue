<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>广告管理</el-breadcrumb-item>
          <el-breadcrumb-item>广告位管理</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-search">
      <el-form :inline="true" ref="adRealtimeDataIndexSearch" :model="search" label-width="80px" size="mini">
        <el-form-item label="广告位名称">
          <el-input v-model="search.search_name"/>
        
        </el-form-item>
        <el-form-item label="所属媒体">
            <el-select v-model="search.search_media_id" clearable filterable placeholder="请选择媒体名称">
            <el-option
              v-for="(item, key) in mediaList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        
        <el-form-item label="结算方式">
          <el-select v-model="search.search_settlement" multiple clearable placeholder="请选择结算方式">
            <el-option
              v-for="item in settlementList"
              :key="item.value"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        
        <el-form-item>
          <el-button type="primary" @click="reloadList()">查询</el-button>
        </el-form-item>
      </el-form>
    </el-row>
    <el-row class="row-actions">
      <el-col>
        <el-button type="primary" size="mini" @click="handlerItem('create')">创建广告位</el-button>
      </el-col>
    </el-row>
    <el-row class="row-table" v-loading="loading">
      <el-row>
        <el-table
          :data="list.data"
          style="width: 100%">
          <el-table-column
            prop="position_id"
            label="序号">
          </el-table-column>
          <el-table-column
            prop="name"
            label="广告位名称">
          </el-table-column>
          <el-table-column
            prop="media_name"
            label="所属媒体">
          </el-table-column>
          <el-table-column
            prop="settlement"
            label="结算方式">
          </el-table-column>
          
          
          <el-table-column
            fixed="right"
            label="操作"
            width="165">
            <template slot-scope="scope">
              <el-button type="text" size="small" @click="handlerItem('edit', scope.row)">编辑</el-button>
            </template>
          </el-table-column>
        </el-table>
        <el-pagination
          @size-change="handlePagerSizeChange"
          @current-change="reloadList"
          :current-page.sync="list.current_page"
          :page-sizes="[20]"
          :page-size="list.per_page"
          layout="total, sizes, prev, pager, next, jumper"
          :total="list.total">
        </el-pagination>
      </el-row>
    </el-row>

    <!-- 创建、修改 -->
    <el-dialog
      :visible.sync="dialogItemVisible"
      :title="dialogItemTitle"
      :before-close="handlerItemClose">
      <Form
        v-if="dialogItemVisible"
        :visible.sync="dialogItemVisible"
        :requestAction="dialogItemRequestAction"
        :mediaId="dialogMediaId"
        :itemId="dialogItemId">
      </Form>
    </el-dialog>
  </div>
</template>

<script>
import * as Cookies from "js-cookie";

import { getBasicData } from "@/js/api/common";
import { createItem, fetchList } from "@/js/api/adSpace";
import Form from "./Form";

export default {
  mixins: [],
  name: "gmp-ad-space-index",
  components: { Form },
  props: {},
  data: function() {
    return {
      dialogItemTitle: "",
      dialogItemRequestAction: "",
      dialogItemId: null,
      dialogMediaId: null,
      dialogItemVisible: false,

      mediaList: [],
      settlementList: [],

      loading: true,
      urlCreate: createItem(),
      dataColsBoxVisible: false,
      search: {
        search_name: "",
        search_media_id: "",
        search_settlement: []
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
      this.dialogItemRequestAction = action;
      if (action === "create") {
        this.dialogItemTitle = "创建广告位";
        this.dialogItemId = null;
      } else if (action === "edit") {
        this.dialogItemTitle = "编辑广告位";
        this.dialogItemId = item.position_id;
      }
      this.dialogItemVisible = true;
    },
    handlerItemClose: function() {
      this.dialogItemVisible = false;
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
          if (response.data.ids) {
            this.ids = response.data.ids;
          }
          if (response.data.excel_url) {
            this.excel_url = response.data.excel_url;
          }
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    handlePagerSizeChange(val) {
      console.log(`每页 ${val} 条`);
    }
  },
  watch: {},
  computed: {},
  created() {
    // 媒体跳转过来的 打开编辑或者新建框
    let scatOpenAdSpace = Cookies.getJSON("scatOpenAdSpace");
    if (scatOpenAdSpace) {
      this.dialogItemRequestAction = scatOpenAdSpace.action;
      if (scatOpenAdSpace.action === "edit") {
        this.dialogItemTitle = "编辑广告位";
        this.dialogItemId = scatOpenAdSpace.position_id;
        this.dialogItemVisible = true;
      } else if (scatOpenAdSpace.action === "create") {
        this.dialogItemTitle = "创建广告位";
        this.dialogMediaId = scatOpenAdSpace.media_id;
        this.dialogItemVisible = true;
      }
      Cookies.remove("scatOpenAdSpace");
    }

    this.mediaList = [];
    this.settlementList = [];
    this.search.media_id = null;
    (this.search.search_name = ""), (this.search.search_settlement = []);
    getBasicData({
      mediaList: "",
      settlementList: ""
    })
      .then(response => {
        this.mediaList = response.data.mediaList;
        this.settlementList = response.data.settlementList;

        this.reloadList();
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>
