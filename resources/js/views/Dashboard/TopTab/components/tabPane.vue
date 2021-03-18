<template>

<div>
  <el-table :data="list" border fit highlight-current-row style="width: 100%">
    <el-table-column align="center" :label="options.head" min-width="65"  v-loading="loading"
    element-loading-text="请给我点时间！">
      <template slot-scope="scope">
        <span>{{scope.row.key}}</span>
      </template>
    </el-table-column>

    <el-table-column min-width="150px" align="center" label="数量">
      <template slot-scope="scope">
        <span>{{scope.row.doc_count}}</span>
      </template>
    </el-table-column>
  </el-table>
</div>

</template>

<script>
import { fetchList } from "@/js/elementAdmin/api/home";

export default {
  props: {
    type: {
      type: String,
      default: "VIEW"
    },
    options: {
      type: Object
    },
    list: {
      type: Array
    }
  },
  data() {
    return {
      listQuery: {
        page: 1,
        limit: 10,
        type: this.type,
        sort: "+id"
      },
      loading: false
    };
  },
  filters: {
    statusFilter(status) {
      const statusMap = {
        published: "success",
        draft: "info",
        deleted: "danger"
      };
      return statusMap[status];
    }
  },
  created() {
    // this.getList();
  },
  methods: {
    // getList() {
    //   this.loading = true;
    //   this.$emit("create"); // for test
    //   fetchList(this.listQuery).then(response => {
    //     this.list = response.data;
    //     this.loading = false;
    //   });
    // }
  }
};
</script>

