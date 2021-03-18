<template>
  <div v-loading="loading" class="tab-container">
    <el-tabs style='margin-top:15px;' v-model="activeName" type="border-card">
      <el-tab-pane v-for="item in tabMapOptions" :label="item.label" :key='item.key' :name="item.key">
        <keep-alive>
          <tab-pane v-if='activeName==item.key' :type='item.key' :options="item" :list="getItemData(item)" @create='showCreatedTimes'></tab-pane>
        </keep-alive>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import { has } from 'lodash';
import tabPane from "./components/tabPane";

export default {
  name: "top-tab",
  components: { tabPane },
  props: {
    title: {
      type: String
    },
    data: {
      type: Object,
      default: () => {}
    }
  },
  data() {
    return {
      loading: true,
      tabMapOptions: [
        { label: "Top10点击", key: "CLICK", head: "广告编号" },
        { label: "Top10下载", key: "DOWN", head: "广告编号" },
        { label: "Top10浏览", key: "VIEW", head: "广告编号" },
        { label: "Top10Ip", key: "IP", head: "IP" }
      ],
      activeName: "VIEW",
      createdTimes: 0
    };
  },
  methods: {
    showCreatedTimes() {
      this.createdTimes = this.createdTimes + 1;
    },
    getItemData(item) {
      return has(this.data, item.key) ? this.data[item.key] : [];
    }
  },
  watch: {
    data: function(val) {
      if (val) {
        this.loading = false;
      }
    }
  }
};
</script>

<style scoped>
.tab-container {
  margin: 30px;
}
</style>
