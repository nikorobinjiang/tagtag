<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>广告管理</el-breadcrumb-item>
          <el-breadcrumb-item>广告实时数据</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-search">
      <el-form :inline="true" ref="adRealtimeDataIndexSearch" :model="search" label-width="80px" size="mini">
        <el-form-item label="游戏名称">
          <el-select v-model="search.game_id" clearable filterable placeholder="请选择游戏名称">
            <el-option
              v-for="(item, key) in gameList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="媒体名称">
          <el-select v-model="search.media_id" clearable filterable placeholder="请选择媒体名称">
            <el-option
              v-for="(item, key) in mediaList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="媒体账号">
          <el-select v-model="search.promote_id" clearable filterable placeholder="请选择媒体账号">
            <el-option
              v-for="(item, key) in promoteList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="广告名称">
          <el-input v-model="search.name"></el-input>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onSubmit">查询</el-button>
        </el-form-item>
      </el-form>
    </el-row>
    <el-row class="row-tools" type="flex" justify="end">
      <el-button type="primary" size="mini" @click="handleDataColsBoxOpen"><i class="el-icon-setting"></i></el-button>
    </el-row>
    <el-row class="row-table" v-loading="loading">
      <el-table
        :data="list.data"
        style="width: 100%">
        <el-table-column
          prop="ad_id"
          label="ID"
          width="80">
        </el-table-column>
        <template  v-for="(column, key) in columnsResort">
          <el-table-column
            v-if="column.field === 'ad_name'"
            :key="column.field + key"
            :label="column.label"
            :min-width="column.width?column.width:80">
            <template slot-scope="scope">
              <el-button type="text" size="small" v-newtab="{url: scope.row.edit_url }">{{ scope.row[column.field] }}</el-button>
            </template>
          </el-table-column>
          <el-table-column
            v-else-if="typeof(column.cal) === 'function'"
            :key="column.field + key"
            :prop="column.field"
            :label="column.label"
            :min-width="column.width?column.width:80">
            <template slot-scope="scope">
              {{ column.cal(scope.row) }}
            </template>
          </el-table-column>
          <el-table-column
            v-else
            :key="column.field + key"
            :prop="column.field"
            :label="column.label"
            :min-width="column.width?column.width:80">
          </el-table-column>
        </template>
      </el-table>
      <el-pagination
        @size-change="handlePagerSizeChange"
        @current-change="handlePagerCurChange"
        :current-page.sync="list.current_page"
        :page-sizes="[20]"
        :page-size="list.per_page"
        layout="total, sizes, prev, pager, next, jumper"
        :total="list.total">
      </el-pagination>
    </el-row>
    <el-dialog
      title="数据项"
      :visible.sync="dataColsBoxVisible"
      width="50%">
      <el-collapse v-model="activeColsBoxNames">
        <el-collapse-item v-for="(item, key) in ColsBoxInfo" :key="key" :name="key">
          <template slot="title">
            {{ item.title }}
            <el-checkbox :indeterminate="item.isIndeterminate" v-model="item.checkAll" @change="handleColsBoxCheckAllChange(item)">全选</el-checkbox>
          </template>
          <div style="margin: 15px 0;"></div>
          <el-checkbox-group v-model="item.checked" @change="handleColsBoxCheckedChange(item)">
            <el-checkbox
              v-for="column in item.columns"
              :key="column.label"
              :label="column.field">
              {{ column.label }}
            </el-checkbox>
          </el-checkbox-group>
        </el-collapse-item>
      </el-collapse>
      <span slot="footer" class="dialog-footer">
        <el-button size="mini" v-if="false" @click="dataColsBoxVisible = false">取 消</el-button>
        <el-button size="mini" type="primary" @click="handleDataColsBoxClose">确 定</el-button>
      </span>
    </el-dialog>
  </div>
</template>

<script>
import * as Cookies from "js-cookie";

import { getBasicData, selMedia } from "@/js/api/common";
import { fetchList } from "@/js/api/adRealtimeDate";
import { mapStateGetSet } from "@/js/utils";
import { xStore } from "@/js/mixins";

export default {
  mixins: [xStore],
  name: "gmp-ad-realtime-data-index",
  components: {},
  props: {},
  data: function() {
    let ColsBoxInfo = {
      Distribute: {
        title: "媒体数据",
        isIndeterminate: false,
        checkAll: false,
        checked: [],
        columns: [
          {
            label: "总花费",
            field: "dw_dist.stat_cost",
            order: 8790,
            cal: item => {
              if (!item.dw_dist) {
                return "-";
              }
              let res = item.dw_dist.stat_cost;
              return isNaN(res) ? "-" : parseFloat(res).toFixed(2);
            }
          },
          { label: "展示数", field: "dw_dist.show", order: 8780 },
          { label: "素材点击数", field: "dw_dist.click", order: 8770 },
          {
            label: "素材点击率",
            field: "click/show",
            order: 8760,
            cal: item => {
              if (!item.dw_dist) {
                return "-";
              }
              let res = item.dw_dist.click / item.dw_dist.show;
              return isNaN(res) ? "-" : (res * 100).toFixed(2) + "%";
            }
          },
          {
            label: "平均点击单价",
            field: "stat_cost/click",
            order: 8750,
            cal: item => {
              if (!item.dw_dist || !item.dw_dist.click) {
                return "-";
              }
              let res = item.dw_dist.stat_cost / item.dw_dist.click;
              return isNaN(res) ? "-" : res.toFixed(2);
            }
          },
          {
            label: "平均千次展现费用（元）",
            field: "stat_cost/show*1000",
            order: 8740,
            cal: item => {
              if (!item.dw_dist || !item.dw_dist.show) {
                return "-";
              }
              let res =
                (item.dw_dist.stat_cost / item.dw_dist.show) * 1000;
              return isNaN(res) ? "-" : res.toFixed(2);
            }
          },
          {
            label: "转化数",
            field: "dw_dist.convert",
            order: 8730
          },
          {
            label: "转化率",
            field: "convert/click",
            order: 8720,
            cal: item => {
              if (!item.dw_dist || !item.dw_dist.show) {
                return "-";
              }
              let res = item.dw_dist.convert / item.dw_dist.click;
              return isNaN(res) ? "-" : (res * 100).toFixed(2) + "%";
            }
          },
          {
            label: "转化成本",
            field: "stat_cost/convert",
            order: 8710,
            cal: item => {
              if (item.dw_dist && !isNaN(item.dw_dist.convert_cost)) {
                return parseFloat(item.dw_dist.convert_cost).toFixed(2);
              }
              if (!item.dw_dist || !item.dw_dist.convert) {
                return "-";
              }
              let res = item.dw_dist.stat_cost / item.dw_dist.convert;
              return isNaN(res) ? "-" : res.toFixed(2);
            }
          }
        ]
      },
      Normal: {
        title: "基础数据",
        isIndeterminate: false,
        checkAll: false,
        checked: [],
        columns: [
          { label: "广告名称", field: "ad_name", order: 9999, width: 250 },
          { label: "访客数", field: "view_count", order: 1990 },
          { label: "独立IP数", field: "ip_count", order: 1980 },
          { label: "点击数", field: "click_count", order: 1970 }, // TODO
          { label: "页面加载完成次数", field: "load_count", order: 1960 }, // TODO
          { label: "点击下载次数", field: "down_count", order: 1950 },
          { label: "点击-下载", field: "click_down_rate", order: 1940 },
          { label: "下载完成次数", field: "finish_count", order: 1930 },
          { label: "下载-完成", field: "down_finish_rate", order: 1920 },
          { label: "新增", field: "dw.add_num", order: 1910 },
          { label: "活跃", field: "dw.active_num", order: 1900 },
          { label: "付费人数", field: "dw.pay_num", order: 1890 },
          { label: "付费", field: "dw.pay_total", order: 1880 }, // 实付 应付 single_a_play
          { label: "付费率", field: "dw.pay_rate", order: 1870 },
          { label: "ARPU", field: "dw.arpu", order: 1860 },
          { label: "ARPPU", field: "dw.arrpu", order: 1850 }
        ],
        cals: {
          "dw.pay_rate": item => {
            return (item.dw.pay_rate * 100).toFixed(2) + "%";
          },
          click_down_rate: item => {
            if (!item.ip_count) {
              return "-";
            }
            let res = item.down_count / item.ip_count;
            return isNaN(res) ? "-" : (res * 100).toFixed(2) + "%";
          },
          down_finish_rate: item => {
            if (!item.ip_count) {
              return "-";
            }
            let res = item.finish_count / item.down_count;
            return isNaN(res) ? "-" : (res * 100).toFixed(2) + "%";
          }
        }
      }
    };
    // 加载 cals
    ColsBoxInfo.Normal.columns.forEach(column => {
      if (ColsBoxInfo.Normal.cals[column.field]) {
        column.cal = ColsBoxInfo.Normal.cals[column.field];
      }
    });

    return {
      loading: true,
      gameList: [],
      mediaList: [],
      promoteList: [],
      dataColsBoxVisible: false,
      activeColsBoxNames: ["Distribute", "Normal"],
      search: {
        name: "",
        media_id: null,
        game_id: null,
        promote_id: null
      },
      columns: [].concat(ColsBoxInfo.Normal.columns),
      list: {
        current_page: 1,
        data: [],
        per_page: 20,
        total: 0
      },
      ColsBoxInfo
    };
  },
  methods: {
    handleDataColsBoxOpen() {
      this.dataColsBoxVisible = true;
    },
    // 数据项关闭
    handleDataColsBoxClose() {
      this.dataColsBoxVisible = false;
      // let columns = [];
      // Object.keys(this.ColsBoxInfo).forEach(segmentKey => {
      //   let segment = this.ColsBoxInfo[segmentKey];
      //   segment.checked.forEach(column => {
      //     columns.push(column);
      //   });
      // });
      // this.columns = columns;
    },
    handleColsBoxCheckAllChange(item) {
      item.checked = item.checkAll
        ? item.columns.map(column => {
            return column.field;
          })
        : [];
      item.isIndeterminate = false;
    },
    handleColsBoxCheckedChange(item) {
      let checkedCount = item.checked.length;
      item.checkAll = checkedCount === item.columns.length;
      item.isIndeterminate =
        checkedCount > 0 && checkedCount < item.columns.length;
    },
    // 加载数据
    reloadList(page) {
      this.loading = true;
      if (page === undefined) {
        page = this.list.current_page;
      }
      fetchList(
        Object.assign({}, this.search, {
          page: page
        })
      )
        .then(response => {
          this.list = response.data.list;
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
    onSubmit() {
      this.reloadList();
    },
    handlePagerSizeChange(val) {
      console.log(`每页 ${val} 条`);
    },
    // 翻页
    handlePagerCurChange(val) {
      this.reloadList(val);
    },
    // 媒体联动
    handleMediaIdChange: function(val, oldVal) {
      if (!val) {
        return;
      }
      selMedia({
        media_id: val
      })
        .then(response => {
          this.promoteList = response.data.promoteList;
        })
        .catch(error => {
          console.log(error);
        });
    }
  },
  watch: {
    "search.media_id": function(val, oldVal) {
      this.handleMediaIdChange(val, oldVal);
    }
  },
  computed: {
    columnsResort: function() {
      let columnsCookie = {};
      let columns = [];
      Object.keys(this.ColsBoxInfo).forEach(segmentKey => {
        columnsCookie[segmentKey] = {
          checked: []
        };
        let segment = this.ColsBoxInfo[segmentKey];
        segment.columns.forEach(column => {
          if (segment.checked.indexOf(column.field) !== -1) {
            columnsCookie[segmentKey].checked.push(column.field);
            columns.push(column);
          }
        });
      });
      Cookies.set("ad_realtime_data_columns", columnsCookie);
      return columns.sort(function(a, b) {
        let orderA = a.order ? a.order : 0;
        let orderB = b.order ? b.order : 0;
        return orderB - orderA;
      });
    }
  },
  created() {
    getBasicData({
      gameList: "",
      mediaList: ""
    }).then(response => {
      this.gameList = response.data.gameList;
      this.mediaList = response.data.mediaList;
      this.reloadList();
    });

    // columns cookie
    try {
      let columnsCookie = Cookies.getJSON("ad_realtime_data_columns");
      Object.keys(columnsCookie).forEach(segmentKey => {
        this.ColsBoxInfo[segmentKey].checked =
          columnsCookie[segmentKey].checked;
      });
    } catch (error) {
      // 默认 checked 处理
      this.ColsBoxInfo.Normal.checked.push(
        ...this.ColsBoxInfo.Normal.columns.map(column => {
          return column.field;
        })
      );
    }
  }
};
</script>


