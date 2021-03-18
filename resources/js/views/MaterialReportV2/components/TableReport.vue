<template>
<el-row  v-loading="loading">
  <el-col>
    <span style="font-size:14px;color:#DC143C;margin-bottom:10px;float:left"><i class="el-icon-info"></i>注:目前只展示今日头条和UC头条相关素材数据</span>
    <!-- 表头 -->
    <el-row>
      <el-col :span="24">
        <el-row class="tb-row text-bold">
          <el-col :span="5" style="font-size:14px">
            设计师
          </el-col>
          <el-col :span="2">
              <TableHeadSortable v-model="sort.annex_count" @beforeInput="handleTableHeadSortableBeforeInput('annex_count')">{{type.label}}数量</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.annex_used" @beforeInput="handleTableHeadSortableBeforeInput('annex_used')">广告使用{{type.label}}数量</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.annex_rate" @beforeInput="handleTableHeadSortableBeforeInput('annex_rate')">{{type.label}}使用率</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.cost" @beforeInput="handleTableHeadSortableBeforeInput('cost')">总花费</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.show" @beforeInput="handleTableHeadSortableBeforeInput('show')">素材展示数</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.click" @beforeInput="handleTableHeadSortableBeforeInput('click')">素材点击数</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.click_rate" @beforeInput="handleTableHeadSortableBeforeInput('click_rate')">素材点击率</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.convert" @beforeInput="handleTableHeadSortableBeforeInput('convert')">转化数</TableHeadSortable>
          </el-col>
          <el-col :span="1">
            <TableHeadSortable v-model="sort.convert_rate" @beforeInput="handleTableHeadSortableBeforeInput('convert_rate')">转化率</TableHeadSortable>
          </el-col>
          <el-col :span="1" style="position:relative;left:-8px">
            <TableHeadSortable v-model="sort.convert_cost" @beforeInput="handleTableHeadSortableBeforeInput('convert_cost')">转化成本</TableHeadSortable>
          </el-col>
          <el-col :span="1" style="position:relative;left:-8px">
            <TableHeadSortable v-model="sort.add_num" @beforeInput="handleTableHeadSortableBeforeInput('add_num')">新增用户数</TableHeadSortable>
          </el-col>
        </el-row>
      </el-col>
    </el-row>
    <!-- end 表头 -->
    <el-row style="font-size:14px;">
      <el-row v-for="item in calListData" :key="item.designer_id" :class="item.designer_id + type.value">
        <el-row :class="item.designer_id+'-tr'" class="tb-row">
          <el-col :span="5">
            <span class="button-channel outter-designer" @click="toggleCollapse(['designer', item.designer_id])">
              <i :class="getCollapse(['designer', item.designer_id])?'el-icon-remove-outline':'el-icon-circle-plus-outline'"></i>{{item.designer_name ? item.designer_name : item.designer_id}}
            </span>
          </el-col>
          <el-col :span="2"><el-button type="text" v-newtab="{url: genDetailUrl(item, 0)}">{{item.annex_count}}</el-button></el-col>
          <el-col :span="2"><el-button type="text" v-newtab="{url: genDetailUrl(item, 1)}">{{item.annex_used}}</el-button></el-col>
          <el-col :span="2">{{fixRateNumber(item.annex_rate)}}</el-col>
          <el-col :span="2">{{item.cost}}</el-col>
          <el-col :span="2">{{item.show}}</el-col>
          <el-col :span="2">{{item.click}}</el-col>
          <el-col :span="2">{{fixRateNumber(item.click_rate)}}</el-col>
          <el-col :span="2">{{item.convert}}</el-col>
          <el-col :span="1">{{fixRateNumber(item.convert_rate)}}</el-col>
          <el-col :span="1">{{item.convert_cost}}</el-col>
          <el-col :span="1">{{item.add_num}}</el-col>
        </el-row>
        <template v-for="(media, mediaKey) in item.children">
          <el-row
            :key="mediaKey"
            class="tb-row"
            v-show="getCollapse(['designer', item.designer_id])">
            <el-col :span="5" class="tab_1">
              <span class="button-channel" @click="toggleCollapse(['media', item.designer_id, media.media_id])">
                <i :class="getCollapse(['media', item.designer_id, media.media_id])?'el-icon-remove-outline':'el-icon-circle-plus-outline'"></i>
                {{ media.media_name }}
              </span>
            </el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">{{media.cost}}</el-col>
            <el-col :span="2">{{media.show}}</el-col>
            <el-col :span="2">{{media.click}}</el-col>
            <el-col :span="2">{{fixRateNumber(media.click_rate)}}</el-col>
            <el-col :span="2">{{media.convert}}</el-col>
            <el-col :span="1">{{fixRateNumber(media.convert_rate)}}</el-col>
            <el-col :span="1">{{media.convert_cost}}</el-col>
            <el-col :span="1">{{media.add_num}}</el-col>
          </el-row>

          <el-row
            v-for="ad in media.children"
            :key="ad.ad_id" 
            v-show="getCollapse(['designer', item.designer_id])
              && getCollapse(['media', item.designer_id, media.media_id])">
            <template v-if="showItem(ad)">
              <el-row class="tb-row">
                <el-col :span="5" class="tab_2">
                  <span class="button-channel" @click="toggleCollapse(['ad', , item.designer_id, media.media_id, ad.ad_id])">
                    <i :class="getCollapse(['ad', , item.designer_id, media.media_id, ad.ad_id])?'el-icon-remove-outline':'el-icon-circle-plus-outline'"></i>
                    {{ad.ad_name}}
                  </span>
                </el-col>
                <el-col :span="2">-</el-col>
                <el-col :span="2">-</el-col>
                <el-col :span="2">-</el-col>
                <el-col :span="2">{{ad.cost}}</el-col>
                <el-col :span="2">{{ad.show}}</el-col>
                <el-col :span="2">{{ad.click}}</el-col>
                <el-col :span="2">{{fixRateNumber(ad.click_rate)}}</el-col>
                <el-col :span="2">{{ad.convert}}</el-col>
                <el-col :span="1">{{fixRateNumber(ad.convert_rate)}}</el-col>
                <el-col :span="1">{{ad.convert_cost}}</el-col>
                <el-col :span="1">{{ad.add_num}}</el-col>
              </el-row>
              <el-row
                class="tb-row"
                v-for="(creative, creativeKey) in ad.children"
                :key="creative.id + '-' + creativeKey"
                v-show="getCollapse(['designer', item.designer_id])
                  && getCollapse(['media', item.designer_id, media.media_id])
                  && getCollapse(['ad', , item.designer_id, media.media_id, ad.ad_id])
                  && showItem(creative)">
                <el-col :span="5" class="tab_3">
                  <el-button
                    type="text"
                    @click="handleItem(creative.id, creativeKey, creative.creative_name)">
                    {{ ad.ad_name ? ad.ad_name : ''}}
                    （创意{{genCreatvieName(creative)}}）
                  </el-button>
                </el-col>
                <el-col :span="2">-</el-col>
                <el-col :span="2">-</el-col>
                <el-col :span="2">-</el-col>
                <el-col :span="2">{{creative.cost}}</el-col>
                <el-col :span="2">{{creative.show}}</el-col>
                <el-col :span="2">{{creative.click}}</el-col>
                <el-col :span="2">{{fixRateNumber(creative.click_rate)}}</el-col>
                <el-col :span="2">{{creative.convert}}</el-col>
                <el-col :span="1">{{fixRateNumber(creative.convert_rate)}}</el-col>
                <el-col :span="1">{{creative.convert_cost}}</el-col>
                <el-col :span="1">{{creative.add_num}}</el-col>
              </el-row>
            </template>
          </el-row>
        </template>
      </el-row>
    </el-row>
  </el-col>

  <el-col :span="24">
    <el-dialog
      :visible.sync="dialogItemVisible"
      :title="dialogItemTitle"
      :before-close="handlerItemClose">
      <div v-loading="dialog_loading">
        <el-row :gutter="10" v-if="type.value=='picture'">
          <el-col :span="8" v-for="(item,key) in dialog.materials.img" :key="key">
            <img style="width:100%" :src="item.preview"/>
          </el-col>
        </el-row>
        <el-row :gutter="10" v-if="type.value=='video'">
          <el-col :span="8" v-for="(item,key) in dialog.materials.video" :key="key">
            <video preload="none" controls :poster="item.video.preview" style="max-width:calc( 100% - 8px );max-height: 96%;display: block;margin: 0 auto;" >
              <source :src="item.video.file_path" type="video/mp4">
              Your browser does not support the video tag.
            </video>
          </el-col>
        </el-row>
        <span>游戏名称:{{dialog.game_name}}</span><br>
        <span>广告同步时间:{{dialog.ad_sync_time}}</span><br>
        <span>广告状态:{{dialog.ad_state}}</span>
      </div>
    </el-dialog>
  </el-col>
</el-row>
  
</template>
<script>
import { stringify } from "qs";
import { assign, cloneDeep, sortedUniqBy, keys, values, orderBy, pickBy } from "lodash";

import { fetchList, fetchCreativeDetail } from "@/js/api/materialReportV2";
import TableHeadSortable from "./TableHeadSortable";

import { xStore } from "@/js/mixins";
import { mapActions } from "vuex";

export default {
  mixins: [xStore],
  name:'TableReport',
  components:{ TableHeadSortable },
  props:{
    search:{
      type: Object
    },
    type:{
      type:Object,
      required:true
    }
  },
  data() {
    return {
      creativeStack: {},
      collapseStack: {},
      // 创意详情框
      dialogItemTitle: "",
      dialogItemId: null,
      dialogItemVisible: false,
      dialog_loading:false,
      dialog:{
        materials:{
          img:{},
          video:{}
        },
        game_name:'',
        ad_sync_time:'',
        ad_state:'',
        ad_name:''
      },
      sort: {
        annex_count: '',
        annex_used: '',
        annex_rate: '',
        cost: '',
        show: '',
        click: '',
        click_rate: '',
        convert: '',
        convert_rate: '',
        convert_cost: '',
        add_num: '',
      },
      loading: true,
      list: {
        current_page: 1,
        data: [],
        per_page: 20,
        total: 0
      },
      restDesigners:{}
    }
  },
  methods:{
    // 加载数据
    reloadList(page) {
      this.loading = true;
      if (page === undefined) {
        page = this.list.current_page;
      }
      fetchList(
        assign( {}, this.search, {
          tab_type: this.type.value
          // page: page
        })
      )
        .then(response => {
          this.list = response.data.list;
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    },
    handleItem: function(id,key,name) {
      // 获取创意信息
      this.dialog_loading=true;
      fetchCreativeDetail(
        {
          ad_material_id:id,
          type:this.type.value
        }
      )
      .then(response => {
        if(response.data.error) {
          this.$message(response.data.error);
        }
        this.dialog = response.data;
        this.dialog.materials = JSON.parse(this.dialog.materials);
        this.dialog_loading=false;

        this.dialogItemTitle = this.dialog.ad_name + "（创意" + (key + 1) + "）素材详情";
        this.dialogItemId = id;
        this.dialogItemVisible = true;
      })
      .catch(error => {
        console.log(error);
      });
    },
    handlerItemClose: function() {
      this.dialogItemVisible = false;

    },
    handlePagerSizeChange(val) {
      console.log(`每页 ${val} 条`);
    },
    handleTableHeadSortableBeforeInput(column) {
      if (column) {
        let sort = {};
        keys(this.sort).map(key => {
          if (key !== column) {
            this.$set(this.sort, key, '');
          }
        });
      }
    },
    fixRateNumber(val) {
      return Number(val) ? (Number(val) * 100).toFixed(2) + '%' :0;
    },
    showItem(item) {
      return (item.cost + item.show + item.add_num) > 0
    },
    toggleCollapse(slug) {
      const key = slug.join('_')
      let stack = this.collapseStack[key];
      if (stack === undefined) {
        stack = false;
      }
      stack = !stack;
      this.$set(this.collapseStack, key, stack);
    },
    getCollapse(slug) {
      const key = slug.join('_')
      let stack = this.collapseStack[key];
      if (stack === undefined) {
        stack = false;
      }
      return stack;
    },
    genCreatvieName(creative) {
      let stack = this.creativeStack[creative.ad_id];
      if (stack === undefined) {
        stack = [];
      }
      if (stack.indexOf(creative.id) === -1) {
        stack.push(creative.id);
        stack = sortedUniqBy(stack)
        this.creativeStack[creative.ad_id] = stack;
      }
      return stack.indexOf(creative.id) + 1;
    },
    genDetailUrl(item, used) {
      const query = assign({}, this.search, {
        designer_id: item.designer_id,
        type: this.type.value,
        used: used,
      });
      return '/material/designer_material_detail?' + stringify(query, { arrayFormat: 'indices' });
    },
    ...mapActions("reportStatistics",[
      'setStatisticsPicture','setStatisticsVideo'
    ])
  },
  watch: {
  },
  computed: {
    calListData: function() {
      if (this.list.data) {
        let list = cloneDeep(this.list.data)
        list.map(designer => {
          designer.children = designer.children.map(media => {
            if (media.children) {
              media.children = media.children.map(ad => {
                if (ad.children) {
                  ad.children = orderBy(ad.children, ['cost'], ['desc']);
                }
                return ad;
              });
              media.children = orderBy(media.children, ['cost'], ['desc']);
            }
            return media;
          })
          return designer;
        });
        const sort = pickBy(this.sort, item => {
          return ['asc', 'desc'].indexOf(item) !== -1;
        });
        if (keys(sort).length > 0) {
          // if(['asc', 'desc'].indexOf(this.sort.cost) !== -1){
            // var sort_type = this.sort.cost;
            // 广告花费排序
            // this.list.data.forEach(function(value){
            //   value.list_creatives_toutiao = orderBy(value.list_creatives_toutiao, ['cost'], ['desc']);
            //   value.list_creatives_uc = orderBy(value.list_creatives_uc, ['cost'], ['desc']);
            // });
          // }
          return orderBy(list, keys(sort), values(sort));
        } else {
          return list;
        }
      } else {
        return this.list.data
      }
    }
  },
  created() {
    this.reloadList();

  }
}
</script>
<style>
.text-bold{
  font-weight: bold;
}
.tb-row{
  border-bottom: 1px solid #ebeef5;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  padding: 14px 0;
  box-sizing: border-box;
  text-overflow: ellipsis;
  vertical-align: middle;
  position: relative;
  text-align: left;
  color: #606266;
}
.tb-row .el-col{
  padding: 10px 0;
}
.tb-row .el-col button{
  padding: 0;
}
.button-channel{
    
    cursor: pointer;
}
.text-blue{color:#409EFF}
.imgpreview{display: block;width: 90%}
.landpage-bottom{
    
  width: 90%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
} 
.landpage-bottom span{
  display:block;
}
.label_1{
  color:#999;
}
.am-avg-sm-3 img{
  max-width:100%;
  max-height:79%;
}
.am-u-sm-11 .am-avg-sm-3 li{
  height:230px;
}
td img,td video{
  width:170px;

}
td video{
  display:block;
}

.text-bottom{
  width: 96%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
.text-bottom span{
  display:block;
}
.tab_1{
  text-indent: 10px;
}
.tab_3{
  text-indent: 70px;
}.tab_2{
  text-indent: 20px;
}


</style>