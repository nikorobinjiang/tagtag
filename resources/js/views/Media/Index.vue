<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>投放媒体</el-breadcrumb-item>
          <el-breadcrumb-item>媒体管理</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    
    <el-row class="row-search">
      <el-form :inline="true" ref="adRealtimeDataIndexSearch" :model="search" label-width="80px" size="mini">
        <el-form-item label="媒体名称">
          <el-select v-model="search.media_id" clearable filterable placeholder="请选择媒体名称">
            <el-option
              v-for="(item, key) in mediaList"
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
        <el-button type="primary" size="mini" @click="handlerItem('create')">创建媒体</el-button>
      </el-col>
    </el-row>
    <el-row class="row-table" v-loading="loading">
      <el-row>
        <el-table
          :data="list.data"
          style="width: 100%">
          <el-table-column
            prop="media_id"
            label="序号">
          </el-table-column>
          <el-table-column
            prop="media_name"
            label="媒体名称">
          </el-table-column>
          <el-table-column
            prop="manage_url"
            label="后台地址">
          </el-table-column>
          <el-table-column
            prop="settlement_count"
            label="结算方式">
          </el-table-column>
          <el-table-column
            prop="position_count"
            label="广告位">
          </el-table-column>
          <el-table-column
            fixed="right"
            label="操作"
            width="165">
            <template slot-scope="scope">
              <el-button type="text" size="small" @click="handlerItem('edit', scope.row)">编辑</el-button>
              <el-button type="text" size="small" @click="handlerItemDetail( scope.row)">详情</el-button>
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
        :visible.sync="dialogItemVisible"
        :requestAction="dialogItemRequestAction"
        :itemId="dialogItemId"
        @submitSucc="reloadList()">
      </Form>
    </el-dialog>

    <!-- 详情 -->
    <el-dialog
      :visible.sync="dialogItemDetailVisible"
      :title="dialogItemDetailTitle"
      :before-close="handlerItemDetailClose">
      <DetailForm
        :visible.sync="dialogItemDetailVisible"
        :requestAction="dialogItemDetailRequestAction"
        :itemId="dialogItemDetailId">
      </DetailForm>
    </el-dialog>

  </div>
</template>

<script>
import { getBasicData } from "@/js/api/common";
import { createItem, fetchList } from "@/js/api/media";
import Form from "./Form";
import DetailForm from "./Show";
import ScatCard from "@/js/components/ScatCard/index";

export default {
  mixins: [],
  name: "gmp-ad-media-management-index",
  components: { Form,DetailForm,ScatCard },
  props: {},
  data: function() {
    return {
      // 创建修改框
      dialogItemTitle: "",
      dialogItemRequestAction: "",
      dialogItemId: null,
      dialogItemVisible: false,

// 详情框
      dialogItemDetailTitle: "",
      dialogItemDetailRequestAction: "",
      dialogItemDetailId: null,
      dialogItemDetailVisible: false,

      mediaList: [],

      loading: true,
      urlCreate: createItem(),
      dataColsBoxVisible: false,
      search: {
        media_id: null
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
    // 创建修改框
    handlerItem: function(action, item) {
      this.dialogItemRequestAction = action;
      if (action === "create") {
        this.dialogItemTitle = "创建媒体";
        this.dialogItemId = null;
      } else if (action === "edit") {
        this.dialogItemTitle = "编辑媒体";
        this.dialogItemId = item.media_id;
      }
      this.dialogItemVisible = true;
    },
    handlerItemClose: function() {
      this.dialogItemVisible = false;

    },
    // 详情框
    handlerItemDetail: function(item) {
     
      this.dialogItemDetailTitle = "媒体详情";
      this.dialogItemDetailId = item.media_id;
      this.dialogItemDetailVisible = true;
    },
    handlerItemDetailClose: function() {
      this.dialogItemDetailVisible = false;
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
    this.mediaList = [];
    this.search.media_id = null;
    getBasicData({
      mediaList: ""
    })
      .then(response => {
        this.mediaList = response.data.mediaList;
        this.reloadList();
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>
