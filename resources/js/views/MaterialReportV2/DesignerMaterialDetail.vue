<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>素材详情页</el-breadcrumb-item>
          <el-breadcrumb-item>{{detailData.designer_name}}</el-breadcrumb-item>

        </el-breadcrumb>
      </el-col>
    </el-row>
    
    <div v-if="detailData.type=='video'">
      <el-row v-loading="loading" :gutter="20" v-for="(item,key) in detailData.list" :key="key">
        <el-col :span=24>
          <h4 class="text-blue">|{{key}}</h4>
        </el-col>
        <el-col :span="4" style=" margin-bottom: 2rem;" v-for="(itemInner,keyInner) in item" :key="keyInner">
          <scat-card size="small" :file-name="itemInner.file_name" :info="['游戏名称:'+itemInner.game_name,'关联广告数:'+itemInner.used_ad_count]">
                <template slot="content">
                  <video preload="none" controls :poster="itemInner.preview" style="max-width:calc( 100% - 8px );max-height: 96%;display: block;margin: 0 auto;" >
                    <source :src="itemInner.file_url" type="video/mp4">
                    Your browser does not support the video tag.
                  </video>
                </template>
            </scat-card>
        </el-col>
      </el-row>
    </div>
    <div v-else>
      <el-row v-loading="loading" :gutter="20" v-for="(item,key) in detailData.list" :key="key">
        <el-col :span=24>
          <h4 class="text-blue">|{{key}}</h4>
        </el-col>
        <el-col :span="4" style=" margin-bottom: 2rem;" v-for="(itemInner,keyInner) in item" :key="keyInner">
          <scat-card size="small" :file-name="itemInner.file_name" :info="['游戏名称:'+itemInner.game_name,'关联广告数:'+itemInner.used_ad_count]">
                <template slot="content">
                  <img :src="itemInner.file_url" :alt="itemInner.file_url" v-preview="{file_type: 'image'}">
                </template>
            </scat-card>
        </el-col>
      </el-row>
    </div>
    
  </div>
</template>
<script>
import { getBasicData } from "@/js/api/common";
import { xStore, adIndex } from "@/js/mixins";
import { fetchMaterialList } from "@/js/api/materialReportV2";
import ScatCard from "@/js/components/ScatCard/index";

export default {
  mixins: [xStore, adIndex],
  name:'gmp-material-report-v2-detail',
  components:{ScatCard},
  props:{
    detailData:{
      required:true,
      type:Object
    }
  },
  data:function(){
    return {
      loading:false,
    }
  },
  methods: {
    // 加载数据
    reloadList(page) {
      this.loading = true;
      
      fetchMaterialList(
        Object.assign({}, {
          designer_id:this.detailData.designer_id,
          type:this.detailData.type
        })
      )
        .then(response => {
          this.detailData.list = response.data.list;
          
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
  },
  watch: {
    
  },
  computed: {
    
  },
  created() {
    if(this.detailData.code==400){
      this.$confirm("不存在的设计师");
    }
    // this.reloadList();
  }

}
</script>
<style>
.text-blue{color:#409EFF}

</style>
