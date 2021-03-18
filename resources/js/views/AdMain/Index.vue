<template>
  <div>
    <el-row class="row-breadcrumb">
      <el-col>
        <el-breadcrumb separator-class="el-icon-arrow-right">
          <el-breadcrumb-item>首页</el-breadcrumb-item>
          <el-breadcrumb-item>广告管理</el-breadcrumb-item>
          <el-breadcrumb-item>广告管理</el-breadcrumb-item>
        </el-breadcrumb>
      </el-col>
    </el-row>
    <el-row class="row-search">
      <el-form :inline="true" ref="search" :model="search" label-width="80px" size="mini">
        <el-form-item label="广告名称">
          <el-input v-model="search.name" clearable @keyup.enter.native="reloadList"></el-input>
        </el-form-item>
        <el-form-item label="所属媒体">
          <el-select @change="handleStatus" v-model="search.media_id" clearable filterable placeholder="请选择所属媒体">
            <el-option
              v-for="(item, key) in xStore.data.mediaList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="媒体账号">
          <el-select @change="handleStatus" v-model="search.promote_id" clearable filterable placeholder="请选择媒体账号">
            <el-option
              v-for="(item, key) in promoteList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="结算方式">
          <el-select v-model="search.settlement" multiple clearable filterable placeholder="请选择结算方式">
            <el-option
              v-for="(item, key) in xStore.data.settlementList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="游戏名称">
          <el-select v-model="search.game_id" clearable filterable placeholder="请选择游戏名称">
            <el-option
              v-for="(item, key) in xStore.data.gameList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="广告位">
          <el-select v-model="search.position_id" clearable filterable placeholder="请选择广告位">
            <el-option
              v-for="(item, key) in positionList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="代理商" v-if="zoneStore === ''">
          <el-select v-model="search.agent_id" clearable filterable placeholder="请选择代理商">
            <el-option
              v-for="(item, key) in xStore.data.agentList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value"></el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="广告状态">
          <el-select v-model="search.status" clearable filterable placeholder="请选择广告状态">
            <el-option-group
              v-for="group in distributeStatus"
              :key="group.label"
              :label="group.label">
              <el-option
                v-for="item in group.options"
                :key="group.value + item.value"
                :label="item.label"
                :value="group.value + '__' + item.value">
              </el-option>
            </el-option-group>
          </el-select>
        </el-form-item>
        <el-form-item label="创建时间">
          <el-date-picker
            v-model="search.date"
            type="daterange"
            align="right"
            unlink-panels
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            :default-time="['00:00:00', '23:59:59']"
            value-format="yyyy-MM-dd HH:mm:ss"
            :picker-options="datePickerOptions">
          </el-date-picker>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="reloadList">查询</el-button>
        </el-form-item>
      </el-form>
    </el-row>
    <el-row class="row-actions">
      <el-button type="primary" size="mini" ref="adMainIndexCreate" @click="handlerItem('create')">创建广告</el-button>
      <el-button type="primary" size="mini" @click="handlerItemAdsDesk('create')">创建AdsDesk广告</el-button>
    </el-row>
    <el-row class="row-table">
      <el-tabs v-model="activeTab">
        <el-tab-pane name="no">
          <span slot="label">
            母包未更新
            <i class="el-icon-refresh" @click="reloadList('tabNo')"></i>
          </span>
          <TableAdMainNo
            ref="tabNo"
            :search="search"
            type="no">
          </TableAdMainNo>
        </el-tab-pane>
        <el-tab-pane name="yes">
          <span slot="label">
            母包更新
            <i class="el-icon-refresh" @click="reloadList('tabYes')"></i>
          </span>
          <TableAdMainYes
            ref="tabYes"
            :search="search"
            type="yes">
          </TableAdMainYes>
        </el-tab-pane>
        <el-tab-pane name="sync">
          <span slot="label">
            同步广告
            <i class="el-icon-refresh" @click="reloadList('tabSync')"></i>
          </span>
          <TableAdMainSync
            ref="tabSync"
            :search="search"
            type="sync">
          </TableAdMainSync>
        </el-tab-pane>
        <el-tab-pane name="trashed">
          <span slot="label">
            回收站
            <i class="el-icon-refresh" @click="reloadList('tabTrashed')"></i>
          </span>
          <TableAdMainTrashed
            ref="tabTrashed"
            :search="search"
            type="trashed">
          </TableAdMainTrashed>
        </el-tab-pane>
      </el-tabs>
    </el-row>

    <!-- 创建、修改 -->
    <el-dialog
      :visible.sync="dialogVisible"
      :title="dialogTitle"
      :before-close="handlerItemClose">
      <FormCreate
        v-if="dialogVisible && dialogDist === 'mix'"
        @reloadList="reloadList"
        :visible.sync="dialogVisible"
        :requestAction="dialogRequestAction"
        :itemId="dialogId">
      </FormCreate>
      <FormCreateAdsDesk
        v-if="dialogVisible && dialogDist === 'AdsDesk'"
        @reloadList="reloadList"
        :visible.sync="dialogVisible"
        :requestAction="dialogRequestAction"
        :itemId="dialogId">
      </FormCreateAdsDesk>
    </el-dialog>
  </div>
</template>

<script>
import * as Cookies from "js-cookie";

import { mapGetters } from "vuex";
import { mapStateGetSet } from "@/js/utils";
import { xStore, adIndex } from "@/js/mixins";
import { getBasicData, selMedia } from "@/js/api/common";
import { createItem } from "@/js/api/advertising";

import FormCreate from "./FormCreate";
import FormCreateAdsDesk from "./FormCreateAdsDesk";
import TableAdMainNo from "./components/TableAdMainNo";
import TableAdMainYes from "./components/TableAdMainYes";
import TableAdMainSync from "./components/TableAdMainSync";
import TableAdMainTrashed from "./components/TableAdMainTrashed";

export default {
  mixins: [xStore, adIndex],
  name: "gmp-ad-main-index",
  components: {
    FormCreate,
    FormCreateAdsDesk,
    TableAdMainNo,
    TableAdMainYes,
    TableAdMainSync,
    TableAdMainTrashed
  },
  props: {
    distributePre: {
      type: [String],
      default: "Normal"
    } // 上一次创建的广告分发类型
  },
  data: function() {
    return {
      dialogTitle: "",
      dialogRequestAction: "",
      dialogId: null,
      dialogVisible: false,
      dialogDist: 'mix',

      positionList: [],
      promoteList: [],
      distributeStatus: [],
      search: {
        name: "",
        media_id: null,
        promote_id: null,
        settlement: [],
        game_id: null,
        position_id: null,
        agent_id: null,
        status: null,
        date: []
      },
      activeTab: "no",
      datePickerOptions: {},
      multipleSelection: []
    };
  },
  methods: {
    handlerItem: function(action, item) {
      this.dialogDist = 'mix';
      this.dialogRequestAction = action;
      if (action === "create") {
        this.dialogTitle = "创建广告";
        this.dialogId = null;
      } else if (action === "edit") {
        this.dialogTitle = "编辑广告";
        this.dialogId = item.ad_id;
      }
      this.dialogVisible = true;
    },
    handlerItemAdsDesk: function(action, item) {
      this.dialogDist = 'AdsDesk';
      this.dialogRequestAction = action;
      if (action === "create") {
        this.dialogTitle = "创建AdsDesk广告";
        this.dialogId = null;
      } else if (action === "edit") {
        this.dialogTitle = "编辑AdsDesk广告";
        this.dialogId = item.ad_id;
      }
      this.dialogVisible = true;
    },
    handlerItemClose: function() {
      this.dialogVisible = false;
    },
    reloadList(item) {
      if (item && this.$refs[item]) {
        this.activeTab = item.replace("tab", "").toLowerCase();
        this.$refs[item].reloadList();
      } else {
        ["tabNo", "tabYes", "tabSync", "tabTrashed"].map(item => {
          this.$refs[item].reloadList();
        });
      }
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
          this.positionList = response.data.positionList;
        })
        .catch(error => {
          console.log(error);
        });
    },
    handleStatus: function() {
      getBasicData({
        distributeStatus: {
          media_id: this.search.media_id,
          promote_id: this.search.promote_id
        }
      })
        .then(response => {
          this.distributeStatus = response.data.distributeStatus;
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
    ...mapGetters(["zoneStore"]),
    createItemUrl() {
      return createItem();
    }
  },
  created() {
    this.xStoreLoadExtraConfig();
    // 媒体跳转过来的 打开编辑或者新建框
    let scatOpenAdvertising = Cookies.getJSON("scatOpenAdvertising");
    if (scatOpenAdvertising && scatOpenAdvertising.action === "list") {
      this.search.position_id = scatOpenAdvertising.position_id;
      this.search.media_id = scatOpenAdvertising.media_id;
      Cookies.remove("scatOpenAdvertising");
    }
  },
  mounted() {
    if (this.distributePre !== "Normal") {
      this.activeTab = "sync";
    }
  }
};
</script>


