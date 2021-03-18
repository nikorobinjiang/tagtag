<template>
  <div class="input-block">
    <el-form-item label="开始日期" :prop="getFormScopedField('campaign.' + prop[0])">
      <el-date-picker
        v-model="curVal.startDate"
        :editable="false"
        :clearable="false"
        type="date"
        placeholder="选择日期"
        value-format="yyyy-MM-dd 00:00:00"
        :picker-options="pickerStartDateOptions">
      </el-date-picker>
    </el-form-item>
    <el-form-item label="结束日期" :prop="getFormScopedField('campaign.' + prop[1])">
      <el-radio-group v-model="endDateSelectVal" size="small">
        <el-radio label="nolimit">不限</el-radio>
        <el-radio label="limit">
          <el-date-picker
            :disabled="endDateSelectVal === 'nolimit'"
            v-model="curVal.endDate"
            :editable="false"
            :clearable="false"
            type="date"
            placeholder="选择日期"
            value-format="yyyy-MM-dd 23:59:59"
            :picker-options="pickerEndDateOptions">
          </el-date-picker>
        </el-radio>
      </el-radio-group>
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
    prop: {
      type: Array,
      default: _ => ["startDate", "endDate"]
    },
    startDate: {
      type: [String],
      required: true
    },
    endDate: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: ""
    }
  },
  data() {
    return {
      curVal: {
        startDate: this.startDate
          ? this.startDate
          : moment().format("YYYY-MM-DD 00:00:00"),
        // : moment().format("YYYY-MM-DD H:mm:ss"),
        endDate: this.endDate ? this.endDate : "2099-01-01 23:59:59"
      },
      pickerStartDateOptions: {
        disabledDate: time => {
          return (
            time.getTime() > this.curEndDate.getTime() ||
            time.getTime() < (new Date().getTime() - 24000 * 3600)
          );
        }
      },
      pickerEndDateOptions: {
        disabledDate: time => {
          return time.getTime() < this.curStartDate.getTime();
        }
      }
    };
  },
  methods: {},
  watch: {
    curVal: {
      handler: function(val, oldVal) {
        this.$emit("update:startDate", val.startDate);
        this.$emit("update:endDate", val.endDate);
      },
      immediate: true,
      deep: true
    }
  },
  computed: {
    curStartDate: {
      get: function() {
        return new Date(this.curVal.startDate);
      },
      set: function(val) {
        this.curVal.startDate = val;
      }
    },
    curEndDate: {
      get: function() {
        return new Date(this.curVal.endDate);
      },
      set: function(val) {
        this.curVal.endDate = val;
      }
    },
    endDateSelectVal: {
      get: function() {
        return this.curVal.endDate === "2099-01-01 23:59:59"
          ? "nolimit"
          : "limit";
      },
      set: function(val) {
        this.curVal.endDate =
          val === "nolimit"
            ? "2099-01-01 23:59:59"
            : moment(this.startDate)
                .add(1, "day")
                .format("YYYY-MM-DD 23:59:59");
      }
    }
  },
  created() {}
};
</script>
