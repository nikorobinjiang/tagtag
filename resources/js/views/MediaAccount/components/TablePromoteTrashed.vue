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
              <el-button type="text" size="small" @click="handlerItem('restore', scope.row)">恢复</el-button>
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
</template>
<script>
import { fetchList, destroyItem } from "@/js/api/promote";

export default {
  name: "table-promote-trashed",
  components: {},
  props: {
    search: {}
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
    handlerItem(action, item) {
      if (action === "restore") {
        this.$confirm("确定恢复使用该媒体账号吗？")
          .then(_ => {
            destroyItem(item.promote_id, { _action: "restore" })
              .then(response => {
                if (response.data.result == "error") {
                  this.$alert(response.data.message, "", {
                    confirmButtonText: "确定"
                  });
                } else {
                  this.$alert("媒体账号恢复成功", "", {
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
