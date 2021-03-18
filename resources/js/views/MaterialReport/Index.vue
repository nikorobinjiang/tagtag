<template>
    <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>游戏素材</el-breadcrumb-item>
          <el-breadcrumb-item>素材报表</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
      <el-row class="row-search">
      <el-form :inline="true" ref="adRealtimeDataIndexSearch" :model="search" label-width="80px" size="mini">
        <el-form-item label="时间">
          <el-date-picker 
            :default_value="init_date"
            v-model="search.search_date"
            type="daterange"
            align="right"
            unlink-panels
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            :default-time="['00:00:00', '23:59:59']"
            value-format="yyyy-MM-dd HH:mm:ss">
          </el-date-picker>
        </el-form-item>
        <el-form-item label="所属媒体">
          <el-select v-model="search.media_id" clearable filterable placeholder="请选择所属媒体">
            <el-option
              v-for="(item, key) in mediaList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        
        <el-form-item label="设计师">
          <el-select v-model="search.search_designer" clearable filterable >
            <el-option
              v-for="(item, key) in designerList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <!-- <el-form-item label="广告名称">
            <el-input v-model="search.search_name"></el-input>
        </el-form-item> -->
        <el-form-item>
          <el-button type="primary" @click="reloadList()">查询</el-button>
        </el-form-item>
      </el-form>
    </el-row>
    <el-row>
      <el-col><h3 style="text-align:center;color:#606266">素材报表
          <i class="el-icon-question" @click="handleReportStatistic()"></i>  
        </h3>
      </el-col>
    </el-row>
    <el-row>
      <el-col>
        <el-tabs v-model="activeName" @tab-click="handleClick">
          <el-tab-pane label="图片素材" name="picture">
            <Mtable ref="picture" :search="search" :type="typePicture"></Mtable>
          </el-tab-pane>
          <el-tab-pane label="视频素材" name="video">
            <Mtable ref="video" :search="search" :type="typeVideo"></Mtable>
          </el-tab-pane>
        </el-tabs>
      </el-col>
    </el-row>
    <el-dialog
      v-if="dialogStatisticVisible"
      :visible.sync="dialogStatisticVisible"
      title="素材报表说明"
      :before-close="handlerItemClose">
      <div v-loading="dialogStatistic_loading">
        <h4>图片/视频数量：</h4><span>设计师上传广告系统素材库的图片/视频数量;</span>
        <h4>广告使用图片/视频数量：</h4><span>被分发媒体广告关联的素材数量;</span>
        <h4>图片/视频使用率：</h4><span>广告使用的图片/视频数量占设计师上传广告系统的图片/视频数量的百分比;</span>
        <h4>总花费：</h4><span>媒体后台同步的广告消耗费用（未返点）;</span>
        <h4>素材展示数：</h4><span>从媒体后台同步的素材展示数据;</span>
        <h4>素材点击数：</h4><span>从媒体后台同步的素材点击数量;</span>
        <h4>素材点击率：</h4><span>从媒体后台同步的素材点击率;</span>
        <h4>转化数：</h4><span>媒体监测到的激活转化数量；数据从媒体后台同步;</span>
        <h4>转化率：</h4><span>计算公式为转化数/素材点击数；数据从媒体后台同步;</span>
        <h4>转化成本：</h4><span>计算公式为总花费/转化数；数据从媒体后台同步;</span>
        <h4>新增用户数：</h4><span>通过通行证进入游戏的用户数；由于今日的部分广告无法区分是哪个广告创意来的新增数，<br>
        故而针对这种广告采取一种估算，估算的公式为：该广告创意点击数/该广告点击数*该广告新增数，并向上取整;</span>
        <br>
        <span>注：视频封面图片不计入计算;</span>
      </div>
    </el-dialog>
    </div>
</template>
<script>
import moment from "moment";

import { getBasicData } from "@/js/api/common";
import { xStore, adIndex } from "@/js/mixins";
import Mtable from "./components/TableReport";
import { fetchList,fetchCreativeDetail } from "@/js/api/materialReport";
import { mapState } from 'vuex';


export default {
  mixins: [xStore, adIndex],
  name:'gmp-material-report-index',
  components:{Mtable},
  props:{},
  data:function() {
    const end = moment().format("YYYY-MM-DD 23:59:59");
    const start = moment(this.startDate)
      .subtract(1, 'months')
      .format("YYYY-MM-DD 00:00:00");
    let init_date = [
      start,
      end
    ];
    return {
      activeName: 'picture',
      designerList:[],
      init_date,
      search:{
        search_designer:'',
        media_id: null,
        search_date:init_date,
      },
      mediaList:[],
      typePicture:{
        label:'图片',
        value:"picture"
      },
      typeVideo:{
        label:'视频',
        value:"video"
      },
      
      pickerOptions2: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          }
        }]
      },
      // 弹框
      dialogItemTitle: "",
      dialogStatisticVisible: false,
      dialogStatistic_loading:false,
      
    }
  },
  methods: {
    
    // 加载数据
    reloadList(item) {
      if (item && this.$refs[item]) {
        this.$refs[item].reloadList();
      } else {
        ["picture",  "video"].map(item => {
          this.$refs[item].reloadList();
        });
      }
    },
    // 弹框详情
    handleReportStatistic(){
      this.dialogStatisticVisible = true;
      // this.dialogStatistic_loading=true;

    },
    handlerItemClose: function() {
      this.dialogStatisticVisible = false;

    },
    handlePagerSizeChange(val) {
      console.log(`每页 ${val} 条`);
    },
    handleClick(tab, event) {
    }
  },
  watch: {
    
  },
  computed: {
    ...mapState('reportStatistics',{
      reportDetail: state=>state.statistics,
    })
  },
  created() {
    this.xStoreLoadExtraConfig();

    this.designerList = [];
    this.mediaList = [];
    this.search.search_designer = null;

    getBasicData({
      reportDesignerList: "",
      mediaList : {distribute : "Toutiao,Uchc"}
    })
      .then(response => {
        this.designerList = response.data.reportDesignerList;
        this.mediaList = response.data.mediaList;
      })
      .catch(error => {
        console.log(error);
      });
  },
  mounted(){
  }

}
</script>
<style>
.text-blue{color:#409EFF}

</style>
