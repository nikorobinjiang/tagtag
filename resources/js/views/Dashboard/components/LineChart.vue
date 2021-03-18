<template>
  <div v-loading="!chartData">
    <div :ref="className" :class="className" :style="{height:height,width:width}"></div>
  </div>
</template>

<script>
import echarts from "echarts";
require("echarts/theme/macarons"); // echarts theme

export default {
  props: {
    title: String,
    className: {
      type: String,
      default: "chart"
    },
    width: {
      type: String,
      default: "100%"
    },
    height: {
      type: String,
      default: "350px"
    },
    autoResize: {
      type: Boolean,
      default: true
    },
    chartData: {
      type: Object
    }
  },
  data() {
    return {
      loading: true,
      chart: null
    };
  },
  mounted() {
    this.initChart();
  },
  beforeDestroy() {
    if (!this.chart) {
      return;
    }
    if (this.autoResize) {
      window.removeEventListener("resize", this.__resizeHanlder);
    }

    const sidebarElm = document.getElementsByClassName("sidebar-container")[0];
    sidebarElm.removeEventListener("transitionend", this.__resizeHanlder);

    this.chart.dispose();
    this.chart = null;
  },
  watch: {
    chartData: {
      deep: true,
      handler(val) {
        this.loading = false;
        this.setOptions(val);
      }
    }
  },
  created() {},
  methods: {
    setOptions({ trendTime, trendCount } = {}) {
      this.chart.setOption({
        title: {
          show: true, //显示折线图
          text: "24小时访问趋势图", //标题文字
          //link: 'http://echarts.baidu.com/option.html#title.link', //主标题超文本链接,
          //subtext: '熟悉title的配置项', //副标题
          left: 50, //配置title的位置
          padding: [5, 20, 5, 10] //title的padding值
        },
        xAxis: {
          data: trendTime,
          boundaryGap: false,
          axisTick: {
            show: false
          }
        },
        grid: {
          left: 10,
          right: 10,
          bottom: 20,
          top: 30,
          containLabel: true
        },
        tooltip: {
          trigger: "axis",
          axisPointer: {
            type: "cross"
          },
          padding: [5, 10]
        },
        yAxis: {
          axisTick: {
            show: false
          }
        },
        legend: {
          data: ["访问量"]
        },
        series: [
          {
            name: "访问量",
            smooth: true,
            type: "line",
            itemStyle: {
              normal: {
                color: "#3888fa",
                lineStyle: {
                  color: "#3888fa",
                  width: 2
                },
                areaStyle: {
                  color: "#f3f8ff"
                }
              }
            },
            data: trendCount,
            animationDuration: 2800,
            animationEasing: "quadraticOut"
          }
        ]
      });
    },
    initChart() {
      this.chart = echarts.init(this.$refs[this.className], "macarons");
      if (!this.loading) {
        this.setOptions(this.chartData);
      }
    }
  }
};
</script>
