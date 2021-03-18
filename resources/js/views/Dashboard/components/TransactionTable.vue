<template>
<div>
  {{title}}
  <el-table :data="list" style="width: 100%;padding-top: 15px;">
    <el-table-column :label=attr1>
      <template slot-scope="scope">
        {{scope.row.key}}
      </template>
    </el-table-column>
    <el-table-column :label=attr2 align="center">
      <template slot-scope="scope">
        {{scope.row.doc_count}}
      </template>
    </el-table-column>
  </el-table>
</div>
</template>

<script>
import CountTo from "vue-count-to";
import { mapStateGetSet } from "@/js/utils";
import { xStore } from "@/js/mixins";

export default {
  filters: {
    statusFilter(status) {
      const statusMap = {
        success: "success",
        pending: "danger"
      };
      return statusMap[status];
    },
    orderNoFilter(str) {
      return str.substring(0, 30);
    }
  },
  computed: {
    title: {
      get: function() {
        return this.$store.state.realtimeData.title;
      },
      set: function(title) {
        this.$store.state.realtimeData.title = title;
      }
    },
    attr1: {
      get: function() {
        return this.$store.state.realtimeData.attr1;
      },
      set: function(attr1) {
        this.$store.state.realtimeData.attr1 = attr1;
      }
    },
    attr2: {
      get: function() {
        return this.$store.state.realtimeData.attr2;
      },
      set: function(attr2) {
        this.$store.state.realtimeData.attr2 = attr2;
      }
    },
    list: {
      get: function() {
        return this.$store.state.realtimeData.datas;
      },
      set: function(datas) {
        this.$store.state.realtimeData.datas = datas;
      }
    }
  }
};
</script>
