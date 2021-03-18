<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-radio-group v-model="curVal.schedule_type">
        <el-radio-button label="SCHEDULE_FROM_NOW">从现在开始一直投放</el-radio-button>
        <el-radio-button label="SCHEDULE_START_END">
          <template v-if="onlyEndTime">
            设置结束日期
          </template>
          <template v-else>
            设置开始和结束日期
          </template>
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
    <el-form-item label="" v-show="curVal.schedule_type !== 'SCHEDULE_FROM_NOW'">
      <el-date-picker
        v-if="onlyEndTime"
        v-model="curVal.end_time"
        type="date"
        placeholder="结束日期"
        value-format="yyyy-MM-dd 23:59:59">
      </el-date-picker>
      <el-date-picker
        v-else
        v-model="date"
        type="daterange"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        :default-time="['00:00:00', '23:59:59']"
        value-format="yyyy-MM-dd HH:mm:ss">
      </el-date-picker>
    </el-form-item>
  </div>
</template>

<script>
import moment from "moment";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-schedule",
  props: {
    scheduleType: {
      type: [String],
      required: true
    },
    startTime: {
      type: [String],
      required: true
    },
    endTime: {
      type: [String],
      required: true
    },
    onlyEndTime: {
      type: Boolean,
      default: false
    },
    label: {
      type: [String],
      default: "投放时间"
    }
  },
  data() {
    return {
      curVal: {
        schedule_type: this.scheduleType
          ? this.scheduleType
          : "SCHEDULE_FROM_NOW",
        start_time: this.startTime
          ? this.startTime
          : moment().format("YYYY-MM-DD 00:00:00"),
        end_time: this.endTime
          ? this.endTime
          : moment(this.startDate)
              .add(1, "day")
              .format("YYYY-MM-DD 23:59:59")
      }
    };
  },
  watch: {
    curVal: {
      handler: function(val, oldVal) {
        this.$emit("update:scheduleType", val.schedule_type);
        this.$emit("update:startTime", val.start_time);
        this.$emit("update:endTime", val.end_time);
      },
      immediate: true,
      deep: true
    }
  },
  computed: {
    date: {
      get: function() {
        return [
          new Date(this.curVal.start_time),
          new Date(this.curVal.end_time)
        ];
      },
      set: function(val) {
        this.curVal.start_time = val[0];
        this.curVal.end_time = val[1];
      }
    }
  }
};
</script>
