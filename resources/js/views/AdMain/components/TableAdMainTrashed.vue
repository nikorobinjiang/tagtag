<template>
<div v-loading="loading">
  <el-row v-if="false" class="row-table-tools" type="flex" justify="end">
    <el-dropdown class="tool-item" size="mini" trigger="click" @command="handleBatchCommand">
      <el-button type="primary" size="mini">
        批量修改<i class="el-icon-arrow-down el-icon--right"></i>
      </el-button>
      <el-dropdown-menu slot="dropdown">
        <el-dropdown-item command="startup">启动</el-dropdown-item>
        <el-dropdown-item command="pause">暂停</el-dropdown-item>
        <el-dropdown-item command="bid">修改出价</el-dropdown-item>
        <el-dropdown-item command="budget">修改预算</el-dropdown-item>
        <el-dropdown-item command="schedule">修改日期和时段</el-dropdown-item>
      </el-dropdown-menu>
    </el-dropdown>
    <el-button class="tool-item" type="primary" size="mini" v-newtab="{ url: excel_url }">导出Excel</el-button>
  </el-row>
  <DialogBatch
    ref="dialogBatch"
    :list="multipleSelection"
    :action="batchAction"
    @reloadList="reloadList()">
  </DialogBatch>
  <el-row>
    <el-table
      ref="tabTrashed"
      :data="data"
      tooltip-effect="dark"
      style="width: 100%"
      @selection-change="handleSelectionChange">
      <el-table-column
        type="selection"
        :selectable="handleTableSelectable"
        align="center"
        width="32">
      </el-table-column>
      <el-table-column
        label="序号"
        align="center"
        min-width="65">
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
        label="广告状态"
        width="120">
        <template slot-scope="scope">
          {{ scope.row.status_name}}
          <el-tooltip
            v-if="scope.row.dist_audit_reject_reason"
            class="item" effect="dark" placement="top">
            <div slot="content" v-html="$scat.filters.formatDistributeMsg(scope.row.dist_audit_reject_reason)">
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
        prop="mother_pkg_status_text"
        label="母包是否更新"
        width="120"
        show-overflow-tooltip>
      </el-table-column>
      <el-table-column
        fixed="right"
        label="操作"
        width="165">
        <template slot-scope="scope">
          <el-button type="text" size="small" v-newtab="{url: scope.row.show_url + '?_action=trashed'}">查看</el-button>
          <el-button type="text" @click="handleRestore(scope.$index, scope.row)">恢复</el-button>
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
import { uniq } from "lodash";

import { batchSetStatus } from "@/js/api/distribute";
import { fetchList, batchRepkg } from "@/js/api/advertising";
import { xStore, FormScope, adIndex } from "@/js/mixins";
import { TableHandleDelete } from "./mixins";

import dataToutiao from "@/js/store/data/Toutiao";

import DialogBatch from "./DialogBatch";

export default {
  mixins: [xStore, FormScope, adIndex, TableHandleDelete],
  name: "table-ad-main-trashed",
  components: {
    DialogBatch
  },
  props: {
    search: {}
  },
  data() {
    return {
      loading: true,
      ids: [],
      excel_url: "",
      list: {
        current_page: 1,
        data: [],
        per_page: 20,
        total: 0
      },
      multipleSelection: [],
      batchAction: ""
    };
  },
  methods: {
    // '暂停广告':'开始投放'
    handleStatusSwitch(index, row) {
      // scope.row.re_pack_url
      axios
        .get(row.status_switch)
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
    // 重新打包
    handleRePkg(index, row) {
      axios
        .get(row.re_pack_url)
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
    handleClick(index, row) {
      console.log(index, row);
    },
    handleEdit(index, row) {
      console.log(index, row);
    },
    handleDelete(index, row) {
      console.log(index, row);
    },
    // 批量更新
    handleBatchUpdate(isAll = false) {
      let batchIds = [];
      let actionMsg = "";
      if (isAll) {
        batchIds = this.ids;
        actionMsg = "全部";
        this.$message.success(actionMsg + "更新");
      } else {
        batchIds = this.multipleSelection.map(item => {
          return item.ad_id;
        });
        actionMsg = "批量";
        if (batchIds.length > 0) {
          this.$message.success(actionMsg + "更新");
        } else {
          this.$message.warning("您还没有勾选条目");
          return;
        }
      }

      batchRepkg(batchIds.join(","))
        .then(response => {
          if (response.data.result == "success") {
            this.$message.success(actionMsg + response.data.message);
            this.reloadList();
          } else {
            this.$message.error(actionMsg + response.data.message);
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    // table 切换第二、第三行的选中状态
    toggleSelection(rows) {
      if (rows) {
        rows.forEach(row => {
          this.$refs.tabTrashed.toggleRowSelection(row);
        });
      } else {
        this.$refs.tabTrashed.clearSelection();
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
          tab_type: "trashed",
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
    },
    // 翻页
    handlePagerCurChange(val) {
      this.reloadList(val);
    },
    handleTableSelectable(row, index) {
      if (['Toutiao', 'Uchc'].indexOf(row.distribute) === -1) {
        return false;
      } else if (this.multipleSelection.length > 0) {
        return row.distribute === this.multipleSelection[0].distribute;
      } else {
        return true;
      }
    },
    handleBatchCommand(command) {
      if (this.multipleSelection.length === 0) {
        this.$message.warning("未选中广告");
        return;
      }
      const promote_ids = uniq(
        this.multipleSelection.map(item => {
          return item.promote_id;
        })
      );
      const distributes = uniq(
        this.multipleSelection.map(item => {
          return item.distribute;
        })
      );
      if (["bid", "budget", "schedule"].indexOf(command) !== -1) {
        this.batchAction = command;
        this.$refs.dialogBatch.open(command);
      } else if (["startup", "pause"].indexOf(command) !== -1) {
        batchSetStatus(
          this.multipleSelection.map(item => {
            return item.ad_id;
          }),
          command === "startup" ? 1 : 0
        ).then(response => {
          if (response.data.result === "success") {
            this.$message.success(response.data.message);
          } else {
            this.$message.warning(response.data.message);
          }
        });
      }
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

