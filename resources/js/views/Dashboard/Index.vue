<template>
  <div class="dashboard-editor-container">
    <PanelGroup
      :data="panelData"></PanelGroup>
    <div>
      <LineChart title="访问趋势图"
        :chartData="trendData">
      </LineChart>
    </div>
    <div>
      <div class="lfloat">
        <TopTab :data="topData">
        </TopTab>
      </div>
      <div class="rfloat">
        <GeoMap title="地域分布"
          :data="mapData">
        </GeoMap>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</template>

<script>
import { fetchData } from "@/js/api/home";
import { mapStateGetSet } from "@/js/utils";
import { xStore } from "@/js/mixins";

import PanelGroup from "./components/PanelGroup";
import LineChart from "./components/LineChart";

import GeoMap from "./components/GeoMap";
import TopTab from "./TopTab";

export default {
  name: "dashboard-index",
  components: { PanelGroup, LineChart, GeoMap, TopTab },
  data() {
    return {
      panelData: null,
      trendData: null,
      topData: null,
      mapData: null
    };
  },
  methods: {
    reloadList() {
      fetchData({
        panelData: {},
        trendData: {},
        topData: {},
        mapData: {},
      }).then(response => {
        this.panelData = response.data.panelData;
        this.trendData = response.data.trendData;
        this.topData = response.data.topData;
        this.mapData = response.data.mapData;
      });
    }
  },
  created() {
    this.reloadList();
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.dashboard-editor-container {
  padding: 0px 32px;
  .chart-wrapper {
    padding: 16px 16px 0;
    margin-bottom: 32px;
  }
}

.lfloat {
  width: 50%;
  float: left;
}
.rfloat {
  width: 50%;
  float: right;
}
.clear {
  clear: both;
}
.buttons {
  margin: 10px;
}
</style>