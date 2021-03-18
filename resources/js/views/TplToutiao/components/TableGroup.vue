<template>
  <div v-loading="loading">
    <el-row class="row-search">
      <el-form :inline="true" :model="search" label-width="120px" size="mini" @submit.native.prevent>
        <el-form-item label="广告组名称">
          <el-input v-model="search.name" @keyup.enter.native="reloadList()"></el-input>
        </el-form-item>
        <el-form-item label="所属媒体">
          <el-select v-model="search.media_id" clearable filterable placeholder="请选择所属媒体">
            <el-option
              v-for="(item, key) in mediaList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="媒体账号">
          <el-select v-model="search.promote_id" clearable filterable placeholder="请选择媒体账号">
            <el-option
              v-for="(item, key) in promoteList"
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
        <el-button type="primary" size="mini" @click="handlerItem('create')">创建广告组</el-button>
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
          prop="campaign_name"
          label="广告组名称"
          min-width="200">
        </el-table-column>
        <el-table-column
          prop="media_name"
          label="媒体名称"
          min-width="160"
          show-overflow-tooltip>
        </el-table-column>
        <el-table-column
          prop="promote_account"
          label="媒体账号"
          min-width="160"
          show-overflow-tooltip>
        </el-table-column>
        <el-table-column
          prop="status_name"
          label="广告组状态"
          width="120"
          show-overflow-tooltip>
        </el-table-column>
        <el-table-column
          label="同步状态"
          width="120">
          <template slot-scope="scope">
            {{ scope.row.distribute_sync_text}}
            <el-tooltip
              v-if="scope.row.distribute_msg_text"
              class="item" effect="dark" placement="top">
              <div slot="content" v-html="$scat.filters.formatDistributeMsg(scope.row.distribute_msg_text)">
              </div>
              <i class="el-icon-warning"></i>
            </el-tooltip>
          </template>
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
            <el-button type="text" size="small" @click="handlerItem('edit', scope.row)">编辑</el-button>
            <el-button type="text" size="small" @click="handleSync(scope.index, scope.row)">
              {{ scope.row.distribute_sync==3  ? '重新同步' :'' }}
              {{ scope.row.distribute_sync==2 ? '重新同步':''}}
              {{ scope.row.distribute_sync == 1 ? '同步广告组' :'' }}
            </el-button>
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
      <FormGroup
        formName="广告组"
        :visible.sync="dialogVisible"
        :requestAction="dialogRequestAction"
        :itemId="dialogId"
        @reloadList="reloadList()">
      </FormGroup>
    </el-dialog>
  </div>
</template>

<script>
import { xStore } from "@/js/mixins";
import dataToutiao from "@/js/store/data/Toutiao";
import { getBasicData } from "@/js/api/common";
import { fetchList, syncItem } from "@/js/api/toutiaoGroup";

import FormGroup from "../FormGroup";

export default {
  mixins: [xStore],
  name: "table-group",
  components: {
    FormGroup
  },
  props: {},
  data() {
    return {
      dialogTitle: "",
      dialogRequestAction: "",
      dialogId: null,
      dialogVisible: false,

      loading: true,
      mediaList: [],
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
    handlerItem: function(action, item) {
      this.dialogRequestAction = action;
      if (action === "create") {
        this.dialogTitle = "创建广告组";
        this.dialogId = null;
      } else if (action === "edit") {
        this.dialogTitle = "编辑广告组";
        this.dialogId = item.id;
      }
      this.dialogVisible = true;
    },
    handlerItemClose: function() {
      this.dialogVisible = false;
    },
    // 同步广告组
    handleSync(index, row) {
      syncItem({
        group_id: row.id
      })
        .then(response => {
          if (response.data.result == "success") {
            this.$message.success(response.data.message);
            this.reloadList();
          } else {
            this.$message.error(response.data.message);
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    // 媒体联动
    handleMediaIdChange: function(val, oldVal) {
      if (!val) {
        return;
      }
      this.promoteList = [];
      this.search.promote_id = null;
      getBasicData({
        promoteList: {
          media_id: this.search.media_id,
          distribute: "Toutiao"
        }
      })
        .then(response => {
          this.promoteList = response.data.promoteList;
        })
        .catch(error => {
          console.log(error);
        });
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
  watch: {
    "search.media_id": function(val, oldVal) {
      this.handleMediaIdChange(val, oldVal);
    }
  },
  computed: {
    data: function() {
      return this.list.data ? this.list.data : [];
    }
  },
  created() {
    this.mediaList = [];
    this.search.media_id = null;
    getBasicData({
      mediaList: {
        distribute: "Toutiao"
      }
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

<style lang="scss" scoped>
.el-dropdown-link {
  padding-left: 10px;
}
</style>
