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
              <TableHeadSortable v-model="sort.material_count" @beforeInput="handleTableHeadSortableBeforeInput('material_count')">{{type.label}}数量</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.used_count" @beforeInput="handleTableHeadSortableBeforeInput('used_count')">广告使用{{type.label}}数量</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.use_rate" @beforeInput="handleTableHeadSortableBeforeInput('use_rate')">{{type.label}}使用率</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.total_cost" @beforeInput="handleTableHeadSortableBeforeInput('total_cost')">总花费</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.total_show" @beforeInput="handleTableHeadSortableBeforeInput('total_show')">素材展示数</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.total_click" @beforeInput="handleTableHeadSortableBeforeInput('total_click')">素材点击数</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.click_rate" @beforeInput="handleTableHeadSortableBeforeInput('click_rate')">素材点击率</TableHeadSortable>
          </el-col>
          <el-col :span="2">
            <TableHeadSortable v-model="sort.total_convert" @beforeInput="handleTableHeadSortableBeforeInput('total_convert')">转化数</TableHeadSortable>
          </el-col>
          <el-col :span="1">
            <TableHeadSortable v-model="sort.convert_rate" @beforeInput="handleTableHeadSortableBeforeInput('convert_rate')">转化率</TableHeadSortable>
          </el-col>
          <el-col :span="1" style="position:relative;left:-8px">
            <TableHeadSortable v-model="sort.convert_cost" @beforeInput="handleTableHeadSortableBeforeInput('convert_cost')">转化成本</TableHeadSortable>
          </el-col>
          <el-col :span="1" style="position:relative;left:-8px">
            <TableHeadSortable v-model="sort.add_num_of_cur_designer" @beforeInput="handleTableHeadSortableBeforeInput('add_num_of_cur_designer')">新增用户数</TableHeadSortable>
          </el-col>
        </el-row>
      </el-col>
    </el-row>
<!-- end 表头 -->

    <el-row style="font-size:14px;">
      <el-row v-for="(item) in calListData" :key="item.designer_id" :class="item.designer_id + type.value">
        <el-row :class="item.designer_id+'-tr'" class="tb-row">
          <el-col :span="5">
            <span class="button-channel outter-designer" @click="toggleCollapse(['designer', item.designer_id])">
              <i :class="getCollapse(['designer', item.designer_id])?'el-icon-remove-outline':'el-icon-circle-plus-outline'"></i>{{item.designer_name ? item.designer_name : item.designer_id}}
            </span>
          </el-col>
          <el-col :span="2"><el-button type="text" v-newtab="{url: genDetailUrl(item, 0)}">{{item.material_count}}</el-button></el-col>
          <el-col :span="2"><el-button type="text" v-newtab="{url: genDetailUrl(item, 1)}">{{item.used_count}}</el-button></el-col>
          <el-col :span="2">{{item.use_rate>0 ? (Number(item.use_rate)*100).toFixed(2) +'%' :0}}</el-col>
          <el-col :span="2">{{Number(item.total_cost).toFixed(2)}}</el-col>
          <el-col :span="2">{{Number(item.total_show)}}</el-col>
          <el-col :span="2">{{Number(item.total_click)}}</el-col>
          <el-col :span="2">{{Number(item.click_rate) ? (Number(item.click_rate) * 100).toFixed(2) + '%' :0}}</el-col>
          <el-col :span="2">{{Number(item.total_convert)}}</el-col>
          <el-col :span="1">{{Number(item.convert_rate) ? (Number(item.convert_rate) *100).toFixed(2) + '%' : 0}}</el-col>
          <el-col :span="1">{{Number(item.convert_cost) ? Number(item.convert_cost).toFixed(2) : 0}}</el-col>
          <el-col :span="1">{{Number(item.add_num_of_cur_designer)}}</el-col>
        </el-row>
        <el-row class="tb-row" v-show="getCollapse(['designer', item.designer_id])" v-if="item.list_creatives_toutiao.length!=0">
          <el-col :span="5" class="tab_1">
            <span class="button-channel" @click="toggleCollapse(['media', 'Toutiao', item.designer_id])">
              <i :class="getCollapse(['media', 'Toutiao', item.designer_id])?'el-icon-remove-outline':'el-icon-circle-plus-outline'"></i>
              今日头条
            </span>
          </el-col>
          <el-col :span="2">-</el-col>
          <el-col :span="2">-</el-col>
          <el-col :span="2">-</el-col>
          <el-col :span="2">{{Number(item.total_toutiao_cost).toFixed(2)}}</el-col>
          <el-col :span="2">{{item.total_toutiao_show ?item.total_toutiao_show :0}}</el-col>
          <el-col :span="2">{{item.total_toutiao_click ?item.total_toutiao_click :0}}</el-col>
          <el-col :span="2">{{item.total_toutiao_show>0 ? (Number(item.total_toutiao_click)/Number(item.total_toutiao_show) *100) .toFixed(2) +'%' :0}}</el-col>
          <el-col :span="2">{{item.total_toutiao_convert ?item.total_toutiao_convert :0}}</el-col>
          <el-col :span="1">{{item.total_toutiao_click>0 ? (Number(item.total_toutiao_convert)/Number(item.total_toutiao_click) *100).toFixed(2) +'%' : 0}}</el-col>
          <el-col :span="1">{{item.total_toutiao_convert>0 ? (Number(item.total_toutiao_cost)/Number(item.total_toutiao_convert)).toFixed(2) : 0}}</el-col>
          <el-col :span="1">{{item.add_num_of_toutiao}}</el-col>
        </el-row>
        <el-row v-for="ad_item in item.list_creatives_toutiao" 
          :key="ad_item.ad_id" 
          v-show="getCollapse(['designer', item.designer_id]) && getCollapse(['media', 'Toutiao', item.designer_id])"
          >
          <el-row class="tb-row" v-if="ad_item.cost > 0 || ad_item.show > 0 || ad_item.add_num > 0 ">
            <el-col :span="5" class="tab_2">
              <span class="button-channel" @click="toggleCollapse([ad_item.ad_id, item.designer_id])">
                <i :class="getCollapse([ad_item.ad_id, item.designer_id])?'el-icon-remove-outline':'el-icon-circle-plus-outline'"></i>
                {{ad_item.ad_name}}
              </span>
            </el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">{{Number(ad_item.cost).toFixed(2)}}</el-col>
            <el-col :span="2">{{ad_item.show}}</el-col>
            <el-col :span="2">{{ad_item.click}}</el-col>
            <el-col :span="2">{{ad_item.show >0 ? (ad_item.click / ad_item.show * 100).toFixed(2) +'%' : 0}}</el-col>
            <el-col :span="2">{{ad_item.convert}}</el-col>
            <el-col :span="1">{{ad_item.click>0 ? (Number(ad_item.convert)/Number(ad_item.click) *100).toFixed(2) +'%' : 0}}</el-col>
            <el-col :span="1">{{ad_item.convert>0 ? (Number(ad_item.cost)/Number(ad_item.convert)).toFixed(2) : 0}}</el-col>
            <el-col :span="1">{{ad_item.add_num}}</el-col>
          </el-row>
          <el-row class="tb-row" v-for="(creative_item, key1) in ad_item.list"
              :key="creative_item.ad_material_id"
              v-show="getCollapse(['designer', item.designer_id]) && getCollapse(['media', 'Toutiao', item.designer_id]) && getCollapse([ad_item.ad_id, item.designer_id])">
          
            <el-col :span="5" class="tab_3">
              <el-button
                type="text"
                @click="handleItem(creative_item.ad_material_id, key1, creative_item.creative_name)">
                {{ creative_item.ad_name ? creative_item.ad_name : ''}}
                （创意{{genCreatvieName(creative_item)}}）
                <!-- ({{creative_item.creative_name ? creative_item.creative_name :creative_item.ad_material_id}}) -->
              </el-button>
            </el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">{{Number(creative_item.cost).toFixed(2)}}</el-col>
            <el-col :span="2">{{creative_item.show ? creative_item.show : 0}}</el-col>
            <el-col :span="2">{{creative_item.click ? creative_item.click :0}}</el-col>
            <el-col :span="2">{{creative_item.show>0 ? (creative_item.click/creative_item.show *100).toFixed(2) +'%' :0}}</el-col>
            <el-col :span="2">{{creative_item.convert ?creative_item.convert :0}}</el-col>
            <el-col :span="1">{{creative_item.click>0 ? (creative_item.convert/creative_item.click *100).toFixed(2)+'%' :0}}</el-col>
            <el-col :span="1">{{creative_item.convert>0 ? (creative_item.cost/creative_item.convert).toFixed(2) :0}}</el-col>
            <el-col :span="1">{{ad_item.click>0 ? Math.round(creative_item.click/ad_item.click* creative_item.plan_add_num) : 0}}</el-col>

          </el-row>
        </el-row>
        
        
        <el-row class="tb-row" v-show="getCollapse(['designer', item.designer_id])" v-if="item.list_creatives_uc.length!=0">
          <el-col :span="5" class="tab_1">
            <span class="button-channel" @click="toggleCollapse(['media', 'Uchc', item.designer_id])">
              <i :class="getCollapse(['media', 'Uchc', item.designer_id])?'el-icon-remove-outline':'el-icon-circle-plus-outline'"></i>
              UC头条
            </span>
          </el-col>
          <el-col :span="2">-</el-col>
          <el-col :span="2">-</el-col>
          <el-col :span="2">-</el-col>
          <el-col :span="2">{{Number(item.total_uc_cost).toFixed(2)}}</el-col>
          <el-col :span="2">{{item.total_uc_show ?item.total_uc_show:0}}</el-col>
          <el-col :span="2">{{item.total_uc_click?item.total_uc_click:0}}</el-col>
          <el-col :span="2">{{item.total_uc_show>0 ? (Number(item.total_uc_click)/Number(item.total_uc_show) *100) .toFixed(2) +'%' :0}}</el-col>
          <el-col :span="2">{{item.total_uc_convert?item.total_uc_convert:0}}</el-col>
          <el-col :span="1">{{item.total_uc_click>0 ? (Number(item.total_uc_convert)/Number(item.total_uc_click) *100).toFixed(2) +'%' : 0}}</el-col>
          <el-col :span="1">{{item.total_uc_convert>0 ? (Number(item.total_uc_cost)/Number(item.total_uc_convert)).toFixed(2) : 0}}</el-col>
          <el-col :span="1">{{item.add_num_of_uc}}</el-col>
        </el-row>

        <el-row v-for="ad_item in item.list_creatives_uc"
          :key="ad_item.ad_id"
          v-show="getCollapse(['designer', item.designer_id]) && getCollapse(['media', 'Uchc', item.designer_id])"
         >
          <el-row class="tb-row"  v-if="ad_item.cost > 0 || ad_item.show > 0 || ad_item.add_num > 0 ">
            <el-col :span="5" class="tab_2">
              <span class="button-channel" @click="toggleCollapse([ad_item.ad_id, item.designer_id])">
                <i :class="getCollapse([ad_item.ad_id, item.designer_id])?'el-icon-remove-outline':'el-icon-circle-plus-outline'"></i>
                {{ad_item.ad_name}}
              </span>
            </el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">{{Number(ad_item.cost).toFixed(2)}}</el-col>
            <el-col :span="2">{{ad_item.show}}</el-col>
            <el-col :span="2">{{ad_item.click}}</el-col>
            <el-col :span="2">{{ad_item.show >0 ? (ad_item.click / ad_item.show *100).toFixed(2) +'%' : 0}}</el-col>
            <el-col :span="2">{{ad_item.convert}}</el-col>
            <el-col :span="1">{{ad_item.click>0 ? (Number(ad_item.convert)/Number(ad_item.click) *100).toFixed(2) +'%' : 0}}</el-col>
            <el-col :span="1">{{ad_item.convert>0 ? (Number(ad_item.cost)/Number(ad_item.convert)).toFixed(2) : 0}}</el-col>
            <el-col :span="1">{{ad_item.add_num}}</el-col>
          </el-row>
          <el-row class="tb-row"
          v-for="(creative_item, key1) in ad_item.list"
          :key="creative_item.ad_material_id"
          v-show="getCollapse(['designer', item.designer_id]) && getCollapse(['media', 'Uchc', item.designer_id]) && getCollapse([ad_item.ad_id, item.designer_id])">
            <el-col :span="5" class="tab_3">
              <el-button
                type="text"
                @click="handleItem(creative_item.ad_material_id, key1, creative_item.creative_name)">
                {{ creative_item.ad_name ? creative_item.ad_name : ''}}
                （创意{{genCreatvieName(creative_item)}}）
                <!-- ({{creative_item.creative_name ? creative_item.creative_name :creative_item.ad_material_id}}) -->
              </el-button>
            </el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">-</el-col>
            <el-col :span="2">{{Number(creative_item.cost).toFixed(2)}}</el-col>
            <el-col :span="2">{{creative_item.show ? creative_item.show : 0}}</el-col>
            <el-col :span="2">{{creative_item.click ? creative_item.click :0}}</el-col>
            <el-col :span="2">{{creative_item.show>0 ? (creative_item.click/creative_item.show *100).toFixed(2) +'%' :0}}</el-col>
            <el-col :span="2">{{creative_item.convert ?creative_item.convert :0}}</el-col>
            <el-col :span="1">{{creative_item.click>0 ? (creative_item.convert/creative_item.click *100).toFixed(2)+'%' :0}}</el-col>
            <el-col :span="1">{{creative_item.convert>0 ? (creative_item.cost/creative_item.convert).toFixed(2) :0}}</el-col>
            <el-col :span="1">{{creative_item.plan_add_num }}</el-col>

          </el-row>
        </el-row>
      </el-row>
      <el-row v-for="(item,key) in restDesigners" :key="key">
        <el-row class="tb-row">
          <el-col :span="5">
            <span class="button-channel outter-designer" >
              <i :class="getCollapse(['designer', item.designer_id])?'el-icon-remove-outline':'el-icon-circle-plus-outline'"></i>{{item.designer_name ? item.designer_name : item.designer_id}}
            </span>
          </el-col>
          <el-col :span="2"><el-button type="text" v-newtab="{url: genDetailUrl(item, 0)}">{{item.material_count}}</el-button></el-col>
          <el-col :span="2"><el-button type="text" v-newtab="{url: genDetailUrl(item, 1)}">{{item.used_count}}</el-button></el-col>
          <el-col :span="2">0</el-col>
          <el-col :span="2">0.00</el-col>
          <el-col :span="2">0</el-col>
          <el-col :span="2">0</el-col>
          <el-col :span="2">0.00%</el-col>
          <el-col :span="2">0</el-col>
          <el-col :span="1">0.00%</el-col>
          <el-col :span="1">0.00</el-col>
          <el-col :span="1">0</el-col>
        </el-row>
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
              <source :src="item.video.file_url" type="video/mp4">
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
import { assign, sortedUniqBy, keys, values, orderBy, pickBy } from "lodash";

import { fetchList,fetchCreativeDetail } from "@/js/api/materialReport";
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
        material_count: '',
        used_count: '',
        use_rate: '',
        total_cost: '',
        total_show: '',
        total_click: '',
        click_rate: '',
        total_convert: '',
        convert_rate: '',
        convert_cost: '',
        add_num_of_cur_designer: '',
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
        Object.assign({}, this.search, {
          tab_type:this.type.value
          // page: page
        })
      )
        .then(response => {
          this.list = response.data.list;
          // 标题统计数据
          // if(this.type.value == 'picture'){
          //   this.setStatisticsPicture(response.data.statistics.picture);
          // }else if(this.type.value == 'video'){
          //   this.setStatisticsVideo(response.data.statistics.video);
          // }
          // 未使用到的设计师
          this.restDesigners = response.data.list.restDesigners;

          if (response.data.ids) {
            this.ids = response.data.ids;
          }
          if (response.data.excel_url) {
            this.excel_url = response.data.excel_url;
          }
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
      if (stack.indexOf(creative.ad_material_id) === -1) {
        stack.push(creative.ad_material_id);
        stack = sortedUniqBy(stack)
        this.creativeStack[creative.ad_id] = stack;
      }
      return stack.indexOf(creative.ad_material_id) + 1;
    },
    genDetailUrl(item, used) {
      const query = assign({
        designer_id: item.designer_id,
        type: this.type.value,
        used: used,
      }, this.search);
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
      this.list.data.forEach(function(value){
        value.list_creatives_toutiao = orderBy(value.list_creatives_toutiao, ['cost'], ['desc']);
        value.list_creatives_uc = orderBy(value.list_creatives_uc, ['cost'], ['desc']);
        // 头条创意按花费排序
        value.list_creatives_toutiao = value.list_creatives_toutiao.map(toutiao_value => {
          toutiao_value.list=orderBy(toutiao_value.list,['cost'], ['desc']);
          return toutiao_value;
        });
      });
      const sort = pickBy(this.sort, item => {
        return ['asc', 'desc'].indexOf(item) !== -1;
      });
      if (keys(sort).length > 0) {

        // if(['asc', 'desc'].indexOf(this.sort.total_cost) !== -1){
          // var sort_type = this.sort.total_cost;
          // 广告花费排序
          // this.list.data.forEach(function(value){
          //   value.list_creatives_toutiao = orderBy(value.list_creatives_toutiao, ['cost'], ['desc']);
          //   value.list_creatives_uc = orderBy(value.list_creatives_uc, ['cost'], ['desc']);
          // });
        // }
        
        return orderBy(this.list.data, keys(sort), values(sort));
      } else {
        return this.list.data;
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