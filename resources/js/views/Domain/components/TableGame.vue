<template>
  <el-row v-loading="loading">
    <el-table
      ref="tabGroup"
      :data="data"
      tooltip-effect="dark"
      style="width: 100%">
      <el-table-column
        prop="ad_url_site_id"
        label="序号"
        width="60">
      </el-table-column>
      <el-table-column
        prop="ad_url_site"
        label="域名"
        min-width="160"
        show-overflow-tooltip>
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
import { xStore } from "@/js/mixins";
import { fetchGameList } from '@/js/api/domain'

import dataToutiao from "@/js/store/data/Toutiao";

export default {
  mixins: [xStore],
  name: "table-game",
  props: {
    'dataSearchName':String
  },
  data() {
    return {
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
      var type='GAME';
      fetchGameList({page:page,type:type,search_domain:this.dataSearchName})
        .then(response => {
          this.list = response.data.list;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
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

