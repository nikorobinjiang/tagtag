<template>
  <div>
    <el-row v-loading="loading">
      <el-table
        ref="tabGroup"
        :data="data"
        tooltip-effect="dark"
        style="width: 100%">
        <el-table-column
          prop="site_id"
          label="序号"
          width="60">
        </el-table-column>
        <el-table-column
          prop="site_url"
          label="域名"
          min-width="160"
          show-overflow-tooltip>
        </el-table-column>
        <el-table-column
          prop="icp"
          label="备案号"
          min-width="160"
          show-overflow-tooltip>
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
      <FormPage
        :visible.sync="dialogVisible"
        :requestAction="dialogRequestAction"
        :itemId="dialogId">
      </FormPage>
    </el-dialog>
  </div>
</template>

<script>
// 落地页域名
import { xStore } from "@/js/mixins";
import { fetchPageList } from "@/js/api/domain";

import FormPage from "../FormPage";
import dataToutiao from "@/js/store/data/Toutiao";

export default {
  mixins: [xStore],
  name: "table-page",
  components: {
    FormPage
  },
  props: {
    dataSearchName: String
  },
  data() {
    return {
      dialogTitle: "",
      dialogRequestAction: "",
      dialogId: null,
      dialogVisible: false,

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
    handlerItem: function(action, item) {
      this.dialogRequestAction = action;
      if (action === "create") {
        this.dialogTitle = "创建落地页域名";
        this.dialogId = null;
      } else if (action === "edit") {
        this.dialogTitle = "编辑落地页域名";
        this.dialogId = item.site_id;
      }
      this.dialogVisible = true;
    },
    handlerItemClose: function() {
      this.dialogVisible = false;
    },
    handlePagerSizeChange(val) {
      console.log(`每页 ${val} 条`);
    },
    // 翻页
    handlePagerCurChange(val) {
      this.reloadList(val);
    },
    // 删除
    handleDestory(item) {
      this.loading = true;
      axios
        .delete(item.destroy_url)
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
    },
    // 加载数据
    reloadList(page) {
      this.loading = true;
      if (page === undefined) {
        page = this.list.current_page;
      }
      var type = "PAGE";
      fetchPageList({
        page: page,
        type: type,
        search_domain: this.dataSearchName
      })
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
    dialogVisible: function(val) {
      if (!val) {
        this.reloadList();
      }
    }
  },
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

