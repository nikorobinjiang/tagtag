<template>
  <el-row>
    <el-col :span="12">
      <el-form-item v-if="['edit', 'show'].indexOf(requestAction) !== -1" label="广告名称">
        <el-input disabled v-model="curVal.ad_name"></el-input>
      </el-form-item>
    </el-col>

    <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 12 : 24">
      <el-form-item label="所属媒体" prop="media_id">
        <el-select :disabled="['edit', 'show'].indexOf(requestAction) !== -1" v-model="curVal.media_id" filterable>
          <el-option
            v-for="item in xStore.data.mediaList"
            :key="item.value"
            :title="item.label"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
    </el-col>

    <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 12 : 24">
      <el-form-item  label="代理商" prop="agent_id">
        <el-select :disabled="['edit', 'show'].indexOf(requestAction) !== -1" v-model="curVal.agent_id" filterable>
          <el-option
            v-for="item in agentList"
            :key="item.value"
            :title="item.label"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
    </el-col>

    <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 12 : 24">
      <el-form-item label="媒体账号" prop="promote_id">
        <el-select :disabled="['edit', 'show'].indexOf(requestAction) !== -1" v-model="curVal.promote_id" filterable>
          <el-option
            v-for="item in promoteList"
            :key="item.value"
            :title="item.label"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
    </el-col>

  </el-row>
</template>

<script>
import { mapGetters } from "vuex";

import { selMedia } from "@/js/api/common";
import { xLoading, xStore } from "@/js/mixins";
import { mapStateGetSet } from "@/js/utils";

import { Injector } from "../mixins";

export default {
  mixins: [xLoading, xStore, Injector],
  name: "form-normal-init",
  components: {},
  props: {
    value: {
      type: [Object],
      required: true
    }
  },
  data() {
    return {
      curVal:
        this.value === undefined || this.value === null
          ? {
              distribute: null,
              ad_name: null,
              media_id: null,
              agent_id: null,
              promote_id: null
            }
          : this.value,
      agentList: [],
      promoteList: []
    };
  },
  methods: {
    // 所属媒体联动
    handleMediaIdChange: function(val, oldVal) {
      if (!val) {
        return;
      }
      this.xloading = true;
      this.agentList = [];
      this.promoteList = [];
      selMedia({
        media_id: val
      })
        .then(response => {
          this.agentList = response.data.agentList;
          this.promoteList = response.data.promoteList;
          this.xloading = false;
        })
        .catch(error => {
          this.xloading = false;
          console.log(error);
        });
      if (oldVal !== null) {
        this.curVal.agent_id = null;
        this.curVal.promote_id = null;
        this.curVal.position_id = null;
        this.curVal.settlement = "";
      }
    },
    // 所属代理商联动
    handleAgentIdChange: function(val, oldVal) {
      if (!val) {
        return;
      }
      this.xloading = true;
      this.promoteList = [];
      selMedia({
        media_id: this.curVal.media_id,
        agent_id: val
      })
        .then(response => {
          this.promoteList = response.data.promoteList;
          this.xloading = false;
        })
        .catch(error => {
          this.xloading = false;
          console.log(error);
        });
      if (oldVal !== null) {
        this.curVal.promote_id = null;
        this.curVal.position_id = null;
        this.curVal.settlement = "";
      }
    },
    // 媒体账号联动
    handlePromoteIdChange: function(val, oldVal) {
      if (!val) {
        return;
      }
      let promote = this.promoteList.find(item => {
        return item.value == val;
      });
      if (promote) {
        this.curVal.distribute = promote.distribute;
      }
    }
  },
  watch: {
    value: {
      handler: function(val) {
        this.curVal =
          val === undefined || val === null
            ? {
                distribute: null,
                ad_name: null,
                media_id: null,
                agent_id: null,
                promote_id: null,
                positionList: [],
                settlementList: []
              }
            : val;
      },
      deep: true
    },
    curVal: {
      handler: function() {
        this.$emit("input", this.curVal);
      },
      deep: true
    },
    "curVal.media_id": function(val, oldVal) {
      this.handleMediaIdChange(val, oldVal);
    },
    "curVal.agent_id": function(val, oldVal) {
      this.handleAgentIdChange(val, oldVal);
    },
    "curVal.agent_id": function(val, oldVal) {
      this.handleAgentIdChange(val, oldVal);
    },
    "curVal.promote_id": function(val, oldVal) {
      this.handlePromoteIdChange(val, oldVal);
    }
  },
  computed: {},
  created() {
    this.handleMediaIdChange(this.curVal.media_id, null);
  }
};
</script>
