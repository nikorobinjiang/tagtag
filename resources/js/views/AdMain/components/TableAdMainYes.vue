<template>
  <div v-loading="loading">
    <el-row class="row-table-tools" type="flex" justify="end">
      <el-button v-if="false" type="primary" size="mini" @click="handleBatchUpdate(true)">全部更新</el-button>
      <el-button type="primary" size="mini" @click="handleBatchUpdate(false)">批量更新</el-button>
      <el-button v-if="false" class="tool-item" type="primary" size="mini" v-newtab="{ url: excel_url }">导出Excel</el-button>
      <el-button class="tool-item" type="primary" size="mini" @click="handleExeclBtn">导出Excel</el-button>
    </el-row>
    <el-row>
      <el-table
        ref="tabNo"
        :data="data"
        tooltip-effect="dark"
        style="width: 100%"
        @selection-change="handleSelectionChange">
        <el-table-column
          type="selection"
          align="center"
          width="30">
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
            <el-button type="text" size="small" v-newtab="{url: scope.row.show_url}">查看</el-button>
            <el-button v-if="['AdsDesk'].indexOf(scope.row.distribute) === -1" type="text" size="small" v-newtab="{url: scope.row.edit_url}">编辑</el-button>
            <el-button type="text" @click="handleDelete(scope.$index, scope.row)">删除</el-button>
            <el-dropdown>
              <el-button type="text" size="small" class="el-dropdown-link" :disabled="scope.row.pkg_status_text !== '打包完成'">
                更多操作
              </el-button>
              <el-dropdown-menu slot="dropdown">
                <el-dropdown-item v-if="xStore.data.gameTypeH5 === scope.row.game_type"><el-button type="text" v-newtab="scope.row.shell_url" target="_blank">打开链接</el-button></el-dropdown-item>
                <el-dropdown-item v-else><el-button type="text" v-newtab="scope.row.apk_addr" target="_blank">下载</el-button></el-dropdown-item>

                <el-dropdown-item><el-button type="text" size="small" v-clipboard="{ text: scope.row.copy_link_content }">复制链接</el-button></el-dropdown-item>

                <el-dropdown-item v-if="xStore.data.gameTypeH5 === scope.row.game_type"><el-button type="text" v-newtab="scope.row.apk_addr" target="_blank">壳包地址获取</el-button></el-dropdown-item>

                <el-dropdown-item v-if="scope.row.distribute!='Normal'">
                  <el-button type="text" size="small" @click="copyAd(scope.$index, scope.row)">复制广告</el-button>
                </el-dropdown-item> 
                
                <el-dropdown-item v-if="['AdsDesk'].indexOf(scope.row.distribute) === -1">
                  <el-button type="text" size="small" @click="handleStatusSwitch(scope.$index, scope.row)">{{ scope.row.status?'暂停广告':'开始投放' }}</el-button>
                </el-dropdown-item>
                
                <el-dropdown-item><el-button type="text" size="small" @click="handleRePkg(scope.$index, scope.row)">重新打包</el-button></el-dropdown-item>
                <el-dropdown-item v-if="scope.row.bi_url"><el-button type="text" v-newtab="scope.row.bi_url" target="_blank">查看数据</el-button></el-dropdown-item>
              </el-dropdown-menu>
            </el-dropdown>
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
import { uniq, cloneDeep, assign, get } from "lodash";
import queryString from 'query-string'

import dataToutiao from "@/js/store/data/Toutiao";
import { fetchList, batchRepkg } from "@/js/api/advertising";
import { xStore, FormScope, adIndex } from "@/js/mixins";
import { TableHandleDelete } from "./mixins";

export default {
  mixins: [xStore, FormScope, adIndex, TableHandleDelete],
  name: "table-ad-main-yes",
  props: {
    search: {}
  },
  data() {
    return {
      loading: true,
      ids: [],
      excel_url: "",
      excel_info: {},
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
    // 复制广告
    copyAd(index, row) {
      axios
        .get(row.copy_ad)
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

      batchRepkg(
        batchIds.join(","),
        Object.assign({}, this.search, {
          tab_type: "yes"
        })
      )
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
          tab_type: "yes",
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
          if (response.data.excel_info) {
            this.excel_info = response.data.excel_info;
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
    handleExeclBtn() {
      if (this.multipleSelection.length === 0) {
        this.$message.warning("请选择需要导出的广告");
        return;
      }
      const ids = this.multipleSelection.map(item => {
        return item.ad_id;
      }).join(',')
      const query = assign(
        cloneDeep(get(this.excel_info, 'query')),
        { ids }
      )
      window.open(this.excel_info.url + '?' + queryString.stringify(query))
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

