<template>
    <el-row  class="row-table" v-loading="loading">
        <el-table
          :data="list.data"
          style="width: 100%">
          <el-table-column
            prop="promote_id"
            label="序号">
          </el-table-column>
          <el-table-column
            prop="promote_name"
            label="账号名称">
          </el-table-column>
          <el-table-column
            prop="promote_account"
            label="账号">
            <template slot-scope="scope">
              {{ scope.row.promote_account}}
              <el-tooltip
                v-if="scope.row.distribute !== 'Normal' && scope.row.has_api === 2"
                class="item" effect="dark" placement="top">
                <div slot="content" v-html="'授权失效'">
                </div>
                <i class="el-icon-warning"></i>
              </el-tooltip>
            </template>
          </el-table-column>
          <el-table-column
            prop="media.media_name"
            label="媒体">
          </el-table-column>
          <el-table-column
            prop="agent.agent_name"
            label="代理商">
          </el-table-column>
          <el-table-column
            fixed="right"
            label="操作"
            width="165">
            <template slot-scope="scope">
              <el-button type="text" size="small" @click="handlerItem('edit', scope.row)">编辑</el-button>
              <el-button type="text" size="small" @click="handlerItem('delete', scope.row)">删除</el-button>
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

        <!-- 修改 -->
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
            :itemId="dialogItemId">
        </FormPromote>
        </el-dialog>
      </el-row>
</template>
<script>
import { createItem, fetchList, destroyItem } from "@/js/api/promote";
import FormPromote from "../Form";

export default {
  name: "table-promote-list",
  components: {
    FormPromote
  },
  props: {
    search: {}
  },
  data() {
    return {
      loading: true,

      // 对话框
      dialogItemTitle: "",
      dialogItemRequestAction: "",
      dialogMediaId: null,
      dialogItemId: null,
      dialogItemVisible: false,

      list: {
        current_page: 1,
        data: [],
        per_page: 20,
        total: 0
      }
    };
  },
  methods: {
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

    handlerItem: function(action, item) {
      this.dialogItemRequestAction = action;
      if (action === "create") {
        this.dialogItemTitle = "创建媒体账号";
        this.dialogItemId = null;
        this.dialogItemVisible = true;
      } else if (action === "edit") {
        this.dialogItemTitle = "编辑媒体账号";
        this.dialogItemId = item.promote_id;
        this.dialogItemVisible = true;
      } else if (action === "delete") {
        this.$confirm(
          "确定删除该媒体账号吗？删除成功后，可从回收站恢复该媒体账号。"
        )
          .then(_ => {
            destroyItem(item.promote_id)
              .then(response => {
                if (response.data.result == "error") {
                  this.$alert(response.data.message, "", {
                    confirmButtonText: "确定"
                  });
                } else {
                  this.$alert("媒体账号删除成功", "", {
                    confirmButtonText: "确定",
                    callback: _ => {
                      location.reload();
                    }
                  });
                  setTimeout(function() {
                    location.reload();
                  }, 2000);
                }
              })
              .catch(error => {
                console.log("error", error);
              });
          })
          .catch(_ => {});
      }
    },
    handlerItemClose: function() {
      this.dialogItemVisible = false;
    },

    handlePagerSizeChange(val) {
      console.log(`每页 ${val} 条`);
    }
  },
  watch: {},
  computed: {},
  created() {
    this.reloadList();
  }
};
</script>
