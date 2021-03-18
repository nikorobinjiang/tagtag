<template>
<el-dialog
  :visible.sync="visible"
  :title="getTitle()"
  :before-close="handlerDialogClose">
  <el-form
    ref="adMainDialogBatchForm"
    :model="form"
    :rules="rules"
    label-width="100px"
    size="mini">

    <el-form-item label="已选择广告">
      <template v-if="action === 'bid'">
        (UC头条广告批量修改出价只能够修改同一阶段的广告)
      </template>
      <el-checkbox-group v-model="selectedList">
        <el-row 
          v-for="(item, key) in relInfoList"
          :key="key">
          <el-checkbox
            :label="item.ad_id">
            {{ stringifyItem(item) }}
          </el-checkbox>
        </el-row>
      </el-checkbox-group>
    </el-form-item>

    <el-form-item v-if="action === 'bid'" label="出价更改为">
      <el-input-number v-model="form.bid" size="mini" controls-position="right" :min="0"></el-input-number>元
    </el-form-item>

    <el-form-item v-else-if="action === 'budget'" label="日预算更改为">
      <el-input-number v-model="form.budget" size="mini" controls-position="right" :min="0"></el-input-number>元
    </el-form-item>

    <template v-else-if="action === 'schedule'">
      <template v-if="distribute === 'Toutiao'">
        <InputScheduleToutiao
          :scheduleType.sync="form.scheduleType"
          :startTime.sync="form.startDate"
          :endTime.sync="form.endDate"
          :onlyEndTime="true">
        </InputScheduleToutiao>
        <InputWeektimeToutiao
          v-model="form.weekTime">
        </InputWeektimeToutiao>
      </template>
      <template v-else-if="distribute === 'Uchc'">
        <InputScheduleUchc
          :startDate.sync="form.startDate"
          :endDate.sync="form.endDate">
        </InputScheduleUchc>
        <InputWeektimeUchc
          v-model="form.weekTime">
        </InputWeektimeUchc>
      </template>
    </template>
  </el-form>

  <span slot="footer">
    <el-button size="mini" @click="visible = false">取 消</el-button>
    <el-button size="mini" type="primary" @click="handleSubmit">确 定</el-button>
  </span>
</el-dialog>
</template>

<script>
import { batchGetData, batchSetData } from "@/js/api/distribute";
import { xLoading, xStore } from "@/js/mixins";
import { renewObject } from "@/js/utils";

import InputScheduleToutiao from "./ToutiaoPlan/InputSchedule";
import InputWeektimeToutiao from "./ToutiaoPlan/InputWeektime";

import InputScheduleUchc from "./Uchc/InputSchedule";
import InputWeektimeUchc from "./Uchc/InputWeektime";

import { Injector } from "../mixins";

export default {
  mixins: [xLoading, xStore, Injector],
  name: "dialog-batch",
  components: {
    InputScheduleToutiao,
    InputWeektimeToutiao,
    InputScheduleUchc,
    InputWeektimeUchc
  },
  props: {
    list: {
      type: Array,
      default: []
    },
    action: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      visible: false,
      promote_id: "",
      distribute: "",
      relInfoList: [],
      selectedList: [],
      formInit: {
        action: this.action,
        bid: 0,
        budget: 0,
        scheduleType: "",
        startDate: "",
        endDate: "",
        weekTime: ""
      },
      form: {},
      rules: {}
    };
  },
  methods: {
    handleSubmit() {
      this.$refs.adMainDialogBatchForm.validate(valid => {
        if (valid) {
          batchSetData(this.selectedList, this.form).then(response => {
            if (response.data.result) {
              this.$message.success(response.data.message);
              this.visible = false;
              this.$emit("reloadList");
            } else {
              this.$message.warning(response.data.message);
            }
          });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    getTitle() {
      let title = "修改";
      switch (this.form.action) {
        case "bid":
          title += "出价";
          break;
        case "budget":
          title += "预算";
          break;
        case "schedule":
          title += "日期和时段";
          break;
      }
      return title;
    },
    stringifyItem(item) {
      let res = [item.ad_name];
      if (this.action === "bid") {
        let strBid = `原出价:${item.bid}`;
        let strStage = "";
        if (this.distribute === "Uchc") {
          if (item.bidStage === 1) {
            strStage = "   优化目标:第一阶段";
          } else if (item.bidStage === 2) {
            strStage = "   优化目标:第二阶段";
          } else {
            strStage = "   优化目标:同步中";
          }
        }
        res.push(`(${strBid}${strStage})`);
      } else if (this.action === "budget") {
        if (item.budget_mode === "INFINITE") {
          res.push(`(原日预算:不限)`);
        } else {
          res.push(`(原日预算:${item.budget})`);
        }
      }
      return res.join(" ");
    },
    handlerDialogClose() {
      this.visible = false;
    },
    open(action) {
      this.$set(this.$data, "form", renewObject(this.formInit));
      if (action) {
        this.form.action = action;
      }
      this.selectedList = this.list.map(item => {
        return item.ad_id;
      });
      batchGetData({
        adRelInfolList: {
          action: this.form.action,
          ad_ids: this.list.map(item => {
            return item.ad_id;
          })
        }
      }).then(response => {
        if (response.data.result === "success") {
          this.promote_id = response.data.promote_id;
          this.distribute = response.data.distribute;
          this.relInfoList = response.data.adRelInfolList;
          this.selectedList = this.relInfoList.map(item => {
            return item.ad_id;
          });
          this.visible = true;
        } else {
          this.$message({
            message: response.data.message,
            type: "warning"
          });
        }
      });
    },
    close() {
      this.visible = false;
    },
    toggle() {
      this.visible = !this.visible;
    }
  },
  watch: {

  },
  computed: {},
  created() {}
};
</script>

<style lang="scss" scoped>
.foot {
  padding-top: 10px;
}
</style>

