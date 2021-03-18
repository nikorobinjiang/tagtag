<template>
  <div v-loading="loading">
    <el-row class="row-actions">
      <el-col>
        <el-button type="primary" size="mini" @click="handlerItem('create')">新增反劫持域名</el-button>
      </el-col>
    </el-row>
    <el-row>
      <el-table
        ref="tabGroup"
        :data="data"
        tooltip-effect="dark"
        style="width: 100%">
        <el-table-column
          prop="id"
          label="序号"
          width="60">
        </el-table-column>
        <el-table-column
          prop="url"
          label="域名"
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
      <FormDown
        :visible.sync="dialogVisible"
        :requestAction="dialogRequestAction"
        :itemId="dialogId">
      </FormDown>
    </el-dialog>
  </div>
</template>

<script>
// 反劫持域名
import { xStore } from "@/js/mixins";
import { fetchDownList, urlCreateDown } from "@/js/api/domain";
import FormDown from "../FormDownSite";

import dataToutiao from "@/js/store/data/Toutiao";

export default {
  mixins: [xStore],
  name: "table-down-site",
  components: {
    FormDown
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
      urlCreateDown,
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
      }
    };
  },
  methods: {
    handlerItem: function(action, item) {
      this.dialogRequestAction = action;
      if (action === "create") {
        this.dialogTitle = "创建反劫持域名";
        this.dialogId = null;
      } else if (action === "edit") {
        this.dialogTitle = "编辑反劫持域名";
        this.dialogId = item.id;
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
      var type = "DOWN";
      fetchDownList({
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

