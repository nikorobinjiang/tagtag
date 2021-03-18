<template>
<div v-loading="loading">
  <div id="geomap" ref="geomap"></div>
</div>
</template>

<script>
export default {
  props: {
    title: {
      type: String
    },
    data: {
      type: Object,
      default: () => {}
    },
  },
  data() {
    return {
      loading: true,
      map: null
    };
  },
  methods: {
    loadMap() {
      this.map = new BMap.Map(this.$refs.geomap); // 创建Map实例

      let point = new BMap.Point(116.418261, 39.921984);
      this.map.centerAndZoom(point, 4); // 初始化地图，设置中心点坐标和地图级别
      //map.enableScrollWheelZoom(); // 允许滚轮缩放

      if (!isSupportCanvas()) {
        alert(
          "热力图目前只支持有canvas支持的浏览器,您所使用的浏览器不能使用热力图功能~"
        );
      }
      //详细的参数,可以查看heatmap.js的文档 https://github.com/pa7/heatmap.js/blob/master/README.md
      //参数说明如下:
      /* visible 热力图是否显示,默认为true
       * opacity 热力的透明度,1-100
       * radius 势力图的每个点的半径大小
       * gradient  {JSON} 热力图的渐变区间 . gradient如下所示
       *	{
        .2:'rgb(0, 255, 255)',
        .5:'rgb(0, 110, 255)',
        .8:'rgb(100, 0, 255)'
        }
        其中 key 表示插值的位置, 0~1.
        value 为颜色值. 
       */
      let heatmapOverlay = new BMapLib.HeatmapOverlay({ radius: 20 });
      this.map.addOverlay(heatmapOverlay);
      heatmapOverlay.setDataSet({ data: this.data.points, max: this.data.avg });
      //是否显示热力图
      function openHeatmap() {
        heatmapOverlay.show();
      }

      function closeHeatmap() {
        heatmapOverlay.hide();
      }
      openHeatmap();
      function setGradient() {
        /*格式如下所示:
        {
            0:'rgb(102, 255, 0)',
          .5:'rgb(255, 170, 0)',
            1:'rgb(255, 0, 0)'
        }*/
        let gradient = {};
        let colors = document.querySelectorAll("input[type='color']");
        colors = [].slice.call(colors, 0);
        colors.forEach(function(ele) {
          gradient[ele.getAttribute("data-key")] = ele.value;
        });
        heatmapOverlay.setOptions({ gradient: gradient });
      }
      //判断浏览区是否支持canvas
      function isSupportCanvas() {
        let elem = document.createElement("canvas");
        return !!(elem.getContext && elem.getContext("2d"));
      }
    }
  },
  watch: {
    data: function() {
      if (!this.map && this.data) {
        this.loadMap();
        this.loading = false;
      }
    }
  },
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
  }
};
</script>

<style lang="css" scoped>
#geomap {
  height: 590px;
  width: 100%;
  margin-top: 30px;
}
</style>