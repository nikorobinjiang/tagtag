<template>
  <div>
    <el-row>
      <el-col>
        <el-button
          class="right"
          type="primary"
          size="mini"
          @click="handleRelActionPromote({action: 'create', media_id: mediaId})"
          v-newtab="{url: '/media/account'}">
          创建媒体账号
        </el-button>
      </el-col>
    </el-row>
    <el-row>
      <el-col>
        <el-table :data="list.data" style="width: 100%">
          <el-table-column
            prop="promote_id"
            label="序号">
          </el-table-column>
          <el-table-column
            prop="promote_account"
            label="账号">
          </el-table-column>
          <el-table-column
            prop="manage_url"
            label="后台">
          </el-table-column>
          <el-table-column
            label="操作">
            <template slot-scope="scope">
              <el-button
                type="text"
                size="small"
                @click="handleRelActionPromote({action: 'edit', promote_id: scope.row.promote_id})"
                v-newtab="{url: '/media/account'}">
                编辑
              </el-button>
            </template>
          </el-table-column>
        </el-table>
      </el-col>
    </el-row>
    <el-row>
      <el-col>
        <el-pagination
          @size-change="handlePagerSizeChange"
          @current-change="reloadList"
          :current-page.sync="list.current_page"
          :page-sizes="[20]"
          :page-size="list.per_page"
          layout="total, sizes, prev, pager, next, jumper"
          :total="list.total">
        </el-pagination>
      </el-col>
    </el-row>
  </div>
</template>

<script>
import * as Cookies from "js-cookie";

import { fetchPromoteList } from "@/js/api/media";

export default {
  name: "Promote",
  components: {},
  props: {
    mediaId: {
      type: Number,
      default: null
    }
  },
  data: function() {
    return {
      search: {},
      list: {
        current_page: 1,
        data: [],
        per_page: 20,
        total: 0
      }
    };
  },
  methods: {
    reloadList(page) {
      this.loading = true;
      if (page === undefined) {
        page = this.list.current_page;
      }
      fetchPromoteList(
        Object.assign(
          {},
          { id: this.mediaId },
          {
            page: page
          }
        )
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
    handleRelActionPromote(info) {
      Cookies.set("scatOpenPromote", info);
    }
  },
  watch: {
    mediaId: function() {
      this.reloadList();
    }
  },
  mounted() {
    this.reloadList();
  }
};
</script>
