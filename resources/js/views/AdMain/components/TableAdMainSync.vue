<template>
  <el-row class="row-table" v-loading="loading">
    <el-table
      ref="tabNo"
      :data="data"
      tooltip-effect="dark"
      style="width: 100%"
      @selection-change="handleSelectionChange">
      <el-table-column
        v-if="['yes'].indexOf(type) !== -1"
        type="selection"
        width="55">
      </el-table-column>
      <el-table-column
        label="序号"
        width="60">
        <template slot-scope="scope">{{ scope.row.ad_id }}</template>
      </el-table-column>
      <el-table-column
        prop="ad_name"
        label="广告名称"
        min-width="200">
      </el-table-column>
      <el-table-column
        prop="media_name"
        label="所属媒体"
        min-width="120"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        prop="promote_account"
        label="媒体账号"
        min-width="160"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        v-if="false"
        prop="position_name"
        label="广告位"
        width="120"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        prop="settlement"
        label="结算方式"
        width="80"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        v-if="false"
        prop="game_name"
        label="游戏名称"
        width="120"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        prop="game_type"
        label="游戏类型"
        width="80"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        prop="agent_name"
        label="代理商"
        width="120"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        label="同步状态"
        width="120">
        <template slot-scope="scope">
          {{ scope.row.distribute_sync_text}}
          <el-tooltip
            v-if="scope.row.distribute_sync === 3"
            class="item" effect="dark" placement="top">
            <div slot="content" v-html="$scat.filters.formatDistributeMsg(scope.row.distribute_msg)">
            </div>
            <i class="el-icon-warning"></i>
          </el-tooltip>
        </template>
      </el-table-column>
      <el-table-column
        prop="add_time"
        label="创建时间"
        width="160"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        prop="pkg_status_text"
        label="打包状态"
        width="80"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        v-if="false"
        prop="mother_pkg_status_text"
        label="母包是否更新"
        width="120"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        fixed="right"
        label="操作"
        width="225">
        <template slot-scope="scope">
          <el-button type="text" size="small" v-newtab="{url: scope.row.show_url}">查看</el-button>
          <el-button v-if="['AdsDesk'].indexOf(scope.row.distribute) === -1" type="text" size="small" v-newtab="{url: scope.row.edit_url}">编辑</el-button>
          <el-button type="text" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
          <el-button type="text" size="small" v-if="scope.row.down_addr" v-clipboard="{ text: scope.row.down_addr, hlScope: 'TableAdMainSync', hlName: scope.row.ad_id }">复制链接</el-button>
          <el-button type="text" size="small" v-if="getDistributeSyncOpName(scope.row)" @click="handleReSyncDist(scope.$index, scope.row)">{{ getDistributeSyncOpName(scope.row) }}</el-button>
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
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { fetchList } from "@/js/api/advertising";
import { FormScope, adIndex } from "@/js/mixins";
import { TableHandleDelete } from "./mixins";

export default {
  mixins: [FormScope, adIndex, TableHandleDelete],
  name: "table-ad-main-sync",
  props: {
    search: {},
    type: {
      type: [String],
      default: ""
    }
  },
  data() {
    return {
      loading: true,
      list: {
        current_page: 1,
        data: [],
        per_page: 20,
        total: 0
      }
    };
  },
  methods: {
    getDistributeSyncOpName(row) {
      // 同步状态(distribute_sync)：0物料待完善, 1可同步, 2同步中, 3同步失败
      switch (row.distribute_sync) {
        case 1:
          return "同步广告";
          break;
        case 3:
          return "重新同步";
          break;
        case 4:
          return "重新同步";
          break;
        default:
          break;
      }
    },
    handleReSyncDist(index, row) {
      this.loading = true;
      axios
        .get(row.re_sync_dist)
        .then(response => {
          if (response.data.result == "success") {
            this.$message.success(response.data.message);
          } else {
            this.$message.error(response.data.message);
          }
          this.loading = false;
          this.reloadList();
        })
        .catch(error => {
          this.loading = false;
          console.log(error);
          this.reloadList();
        });
    },
    // table 条目选择
    toggleSelection(rows) {
      if (rows) {
        rows.forEach(row => {
          this.$refs.tabNo.toggleRowSelection(row);
        });
      } else {
        this.$refs.tabNo.clearSelection();
      }
    },
    handleSelectionChange(val) {
      this.multipleSelection = val;
    },
    // 加载数据
    reloadList(page) {
      this.loading = true;
      if (page === undefined) {
        page = this.list.current_page;
      }
      fetchList(
        Object.assign({}, this.search, {
          tab_type: this.type,
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
    },
    handlePagerSizeChange(val) {
      console.log(`每页 ${val} 条`);
    },
    // 翻页
    handlePagerCurChange(val) {
      this.reloadList(val);
    }
  },
  watch: {},
  computed: {
    data: function() {
      return this.list.data ? this.list.data : [];
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

