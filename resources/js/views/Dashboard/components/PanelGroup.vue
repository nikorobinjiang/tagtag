<template>
  <el-row v-loading="loading" class="panel-group" :gutter="40">
    <el-col :xs="12" :sm="12" :lg="4" class="card-panel-col">
      <div class='card-panel' @click="handleSetLineChartData('newVisitis')">
        <div class="card-panel-icon-wrapper icon-people">
          <svg-icon icon-class="people" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">浏览量(PV)</div>
          <count-to class="card-panel-num" :startVal="0" :endVal="data?data.pv:0" :duration="2600"></count-to>
        </div>
      </div>
    </el-col>
	
    <el-col :xs="12" :sm="12" :lg="4" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('messages')">
        <div class="card-panel-icon-wrapper icon-message">
          <svg-icon icon-class="international" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">ip数</div>
          <count-to class="card-panel-num" :startVal="0" :endVal="data?data.ips:0" :duration="3000"></count-to>
        </div>
      </div>
    </el-col>

    <el-col :xs="12" :sm="12" :lg="4" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('purchases')">
        <div class="card-panel-icon-wrapper icon-money">
          <svg-icon icon-class="star" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">加载完成数</div>
          <count-to class="card-panel-num" :startVal="0" :endVal="data?data.loads:0" :duration="3200"></count-to>
        </div>
      </div>
    </el-col>
	
    <el-col :xs="12" :sm="12" :lg="4" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('shoppings')">
        <div class="card-panel-icon-wrapper icon-shoppingCard">
          <svg-icon icon-class="star" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">点击数</div>
          <count-to class="card-panel-num" :startVal="0" :endVal="data?data.clicks:0" :duration="3600"></count-to>
        </div>
      </div>
    </el-col>

    <el-col :xs="12" :sm="12" :lg="4" class="card-panel-col">
      <div class="card-panel" @click="handleSetLineChartData('shoppings')">
        <div class="card-panel-icon-wrapper icon-downs">
          <svg-icon icon-class="star" class-name="card-panel-icon" />
        </div>
        <div class="card-panel-description">
          <div class="card-panel-text">下载数</div>
          <count-to class="card-panel-num" :startVal="0" :endVal="data?data.downs:0" :duration="3600"></count-to>
        </div>
      </div>
    </el-col>

  </el-row>
</template>

<script>
import CountTo from "vue-count-to";

import { countTo } from "@/js/api/home";
import { xStore } from "@/js/mixins";

export default {
  name: "panel-group",
  mixins: [xStore],
  components: {
    CountTo
  },
  props: {
    title: {
      type: String
    },
    data: {
      type: Object,
      default: () => {
        return {
          pv: 0,
          ips: 0,
          loads: 0,
          clicks: 0,
          downs: 0
        };
      }
    }
  },
  data: function() {
    return {
      loading: true
    };
  },
  methods: {
    handleSetLineChartData(type) {
      this.$emit("handleSetLineChartData", type);
    }
  },
  watch: {
    data: function(val) {
      if (val) {
        this.loading = false;
      }
    }
  },
  created: function() {
    // `this` 指向 vm 实例
    //console.log(this.$store.state.realtimeData.pv)
  }
};
</script>

<style rel="stylesheet/scss" lang="scss" scoped>
.panel-group {
  margin-top: 18px;
  .card-panel-col {
    margin-bottom: 32px;
  }
  .card-panel {
    height: 88px;
    cursor: pointer;
    font-size: 12px;
    position: relative;
    overflow: hidden;
    color: #666;
    background: #fff;
    box-shadow: 4px 4px 40px rgba(0, 0, 0, 0.05);
    border-color: rgba(0, 0, 0, 0.05);
    &:hover {
      .card-panel-icon-wrapper {
        color: #fff;
      }
      .icon-people {
        background: #40c9c6;
      }
      .icon-message {
        background: #36a3f7;
      }
      .icon-money {
        background: #f4516c;
      }
      .icon-shoppingCard {
        background: #34bfa3;
      }
      .icon-downs {
        background: #1fc235;
      }
    }
    .icon-people {
      color: #40c9c6;
    }
    .icon-message {
      color: #36a3f7;
    }
    .icon-money {
      color: #f4516c;
    }
    .icon-shoppingCard {
      color: #34bfa3;
    }
    .icon-downs {
      color: #1fc235;
    }
    .card-panel-icon-wrapper {
      float: left;
      margin: 14px 0 0 14px;
      padding: 0px;
      transition: all 0.38s ease-out;
      border-radius: 6px;
    }
    .card-panel-icon {
      float: left;
      font-size: 48px;
    }
    .card-panel-description {
      float: right;
      font-weight: bold;
      margin: 10px;
      margin-left: 0px;
      .card-panel-text {
        line-height: 18px;
        color: rgba(0, 0, 0, 0.45);
        font-size: 16px;
        margin-bottom: 12px;
      }
      .card-panel-num {
        font-size: 20px;
      }
    }
  }
}
</style>
