<template>
  <div class="input-block ban-select">
    <el-form-item :label="label">
      <el-radio-group v-model="scheduleTimeVal">
        <el-radio-button
          v-for="(item, key) in scheduleTimeType"
          :key="key"
          :label="item.value">{{ item.text}}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
    <el-form-item
    v-show="scheduleTimeVal !== 'full'">
      <table
        cellspacing="0" cellspadding="0"
        class="table-bordered table-time-sel">
        <tr>
          <th rowspan="2" colspan="5">星期/时间</th>
          <th colspan="24">00:00-12:00</th>
          <th colspan="24">12:00-24:00</th>
        </tr>
        <tr>
          <th
            colspan="2"
            class="text-normal"
            v-for="(val, key) in halfDayHours"
            v-if="(key) < 24"
            :key="(key)">{{ (key) }}
          </th>
        </tr>
        <tr
          v-for="(week, weekKey) in weekdayNames"
          :key="weekKey">
          <th colspan="5" class="week-title">{{ week }}</th>
          <td
            v-for="halfHour in halfDayHours"
            :key="weekKey * 48 + halfHour"
            :class="{chosen: getChosen(weekKey, halfHour)}"
            @mousedown="handleSelDown(weekKey, halfHour)"
            @mouseover="handleSelOver(weekKey, halfHour)"
            @mouseup="handleSelUp(weekKey, halfHour)">
          </td>
        </tr>
        <tr>
          <th colspan="53">
            <span
              class="cancel_chosen text-normal"
              @click="handleCancelChosen">取消选择
            </span>
          </th>
        </tr>
      </table>
    </el-form-item>
  </div>
</template>
<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-week-time",
  props: {
    value: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "投放时段"
    }
  },
  data() {
    let curVal =
      this.value === undefined ||
      this.value === null ||
      this.value.length < 48 * 7
        ? _.repeat("0", 48 * 7)
        : this.value;
    return {
      positionMouseDown: null,
      halfDayHours: _.range(48),
      scheduleTimeVal: "full",
      // 事实值
      curVal: curVal,
      // 预览值
      preVal: curVal,
      scheduleTimeType: {
        NOLIMIT: {
          text: "全天投放",
          value: "full"
        },
        LIMIT: {
          text: "选择时间段",
          value: "ranges"
        }
      },
      weekdayNames: [
        "星期一",
        "星期二",
        "星期三",
        "星期四",
        "星期五",
        "星期六",
        "星期七"
      ]
    };
  },
  methods: {
    getChosen(weekDay, halfHour) {
      if (this.positionMouseDown && this.preVal) {
        return this.preVal[weekDay * 48 + halfHour] === "1";
      } else {
        return this.curVal[weekDay * 48 + halfHour] === "1";
      }
    },
    calChosen(newX, newY) {
      if (this.positionMouseDown) {
        let oldX = this.positionMouseDown[0];
        let oldY = this.positionMouseDown[1];
        let curVal = this.curVal.split("");

        let startX = oldX <= newX ? oldX : newX;
        let startY = oldY <= newY ? oldY : newY;
        let endX = oldX > newX ? oldX : newX;
        let endY = oldY > newY ? oldY : newY;

        _.range(startX, endX + 1).map(x => {
          _.range(startY, endY + 1).map(y => {
            let position = x * 48 + y;
            curVal[position] = curVal[position] === "0" ? "1" : "0";
          });
        });

        return curVal.join("");
      }
    },
    handleSelDown(weekDay, halfHour) {
      this.positionMouseDown = [weekDay, halfHour];
    },
    handleSelOver(weekDay, halfHour) {
      this.preVal = this.calChosen(weekDay, halfHour);
    },
    handleSelUp(weekDay, halfHour) {
      this.curVal = this.calChosen(weekDay, halfHour);
      this.positionMouseDown = null;
    },
    handleCancelChosen() {
      this.curVal = _.repeat("0", 48 * 7);
    }
  },
  watch: {
    scheduleTimeVal(val) {
      if (val === "full") {
        this.curVal = _.repeat("1", 48 * 7);
      }
      this.$emit("input", this.curVal);
    },
    curVal(val) {
      this.$emit("input", this.curVal);
    }
  },
  computed: {},
  created() {
    if (this.curVal.indexOf(0) !== -1) {
      this.scheduleTimeVal = "ranges";
    }
  },
  mounted() {}
};
</script>

<style>
.table-bordered td,
.table-bordered th {
  border-left: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
  text-align: center;
}
.table-bordered td:last-child,.table-bordered th:last-child{
  border-right: 1px solid #ddd;
}
.table-bordered tr:first-child th{
  border-top: 1px solid #ddd;
}
.cancel_chosen {
  float: right;
  cursor: pointer;
}
.text-normal {
  font-weight: normal;
}
.chosen {
  background: #ff0000;
}
.table-bordered td {
  width: 1.5%;
}
.table-bordered .week-title {
  width: 8%;
}
.table-bordered {
  width: 90%;
}
</style>
