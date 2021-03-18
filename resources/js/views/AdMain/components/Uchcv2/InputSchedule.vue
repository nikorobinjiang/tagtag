<template>
  <div class="input-block">
    <el-form-item label="排期方式">
      <el-radio-group v-model="stCal">
        <el-radio-button label="full">从今天开始长期投放</el-radio-button>
        <el-radio-button label="range">设置开始和结束日期</el-radio-button>
      </el-radio-group>
    </el-form-item>
    <el-form-item v-if="stCal === 'range'" label="设置日期">
      <el-date-picker
        v-model="curVal"
        :picker-options="pickerOptions"
        type="daterange"
        range-separator="至"
        start-placeholder="开始日期"
        end-placeholder="结束日期"
        format="yyyy 年 MM 月 dd 日">
      </el-date-picker>
    </el-form-item>
  </div>
</template>

<script>
import moment from "moment";
import { FormScope } from "@/js/mixins";

const startOfToday = moment().startOf('day').valueOf()

export default {
  mixins: [FormScope],
  name: "InputSchedule",
  props: {
    startDate: {
      type: Number,
      required: true
    },
    endDate: {
      type: Number,
      required: true
    },
    label: {
      type: [String],
      default: ""
    }
  },
  data() {
    return {
      pickerOptions: {
        disabledDate: time => {
          return time.getTime() < startOfToday;
        }
      },
    };
  },
  methods: {},
  watch: {
  },
  computed: {
    stCal: {
      get() {
        return this.endDate === 20990101 ? 'full' : 'range'
      },
      set(payload) {
        if (payload === 'full') {
          this.$emit('update:endDate', 20990101)
        } else {
          this.$emit('update:endDate', parseInt(moment().add(1, 'months').format("YYYYMMDD")))
        }
      }
    },
    curVal: {
      get() {
        if (isNaN(this.startDate) || this.startDate.toString().length < 8) {
          this.$emit('update:startDate', parseInt(moment().add(1, 'days').format("YYYYMMDD")))
        }
        if (isNaN(this.endDate) || this.endDate.toString().length < 8) {
          this.$emit('update:endDate', parseInt(moment().add(1, 'months').format("YYYYMMDD")))
        }
        return [moment(this.startDate.toString(), "YYYYMMDD"), moment(this.endDate.toString(), "YYYYMMDD")]
      },
      set(payload) {
        if (payload && payload.length === 2) {
          this.$emit('update:startDate', parseInt(moment(payload[0]).format("YYYYMMDD")))
          this.$emit('update:endDate', parseInt(moment(payload[1]).format("YYYYMMDD")))
        }
      }
    }
  },
  created() {}
};
</script>
