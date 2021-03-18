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
      :prop="getFormScopedField(prop)"
      v-show="scheduleTimeVal === 'ranges'">
      <table
        cellspacing="0" cellspadding="0"
        class="table-bordered table-time-sel">
        <tr>
          <th rowspan="2" colspan="5">星期/时间</th>
          <th colspan="12">00:00-12:00</th>
          <th colspan="12">12:00-24:00</th>
        </tr>
        <tr>
          <th
            class="text-normal"
            v-for="(val, key) in daySegmentsLT24"
            :key="(key)">{{ (key) }}
          </th>
        </tr>
        <tr
          v-for="(week, weekKey) in weekdayNames"
          :key="weekKey">
          <th colspan="5" class="week-title">{{ week }}</th>
          <td
            v-for="fullHour in daySegments"
            :key="weekKey * 24 + fullHour"
            :class="{chosen: getChosen(weekKey, fullHour)}"
            @mousedown="handleSelDown(weekKey, fullHour)"
            @mouseover="handleSelOver(weekKey, fullHour)"
            @mouseup="handleSelUp(weekKey, fullHour)">
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
import { cloneDeep, repeat, range } from "lodash"
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-week-time",
  props: {
    prop: {
      type: String,
      default: "campaign.schedule_time"
    },
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
    return {
      positionMouseDown: null, // 当前鼠标落点
      daySegments: range(24).reverse(), // 一天分片数
      preVal: cloneDeep(this.curVal), // 预览值
      scheduleTimeType: [
        {
          text: "全天投放",
          value: "full"
        },
        {
          text: "选择时间段",
          value: "ranges"
        }
      ],
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
    getChosen(weekDay, fullHour) {
      if (this.positionMouseDown && this.preVal) {
        return this.preVal[weekDay * 24 + fullHour] === "1";
      } else {
        return this.curVal[weekDay * 24 + fullHour] === "1";
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

        range(startX, endX + 1).map(x => {
          range(startY, endY + 1).map(y => {
            let position = x * 24 + y;
            curVal[position] = curVal[position] === "0" ? "1" : "0";
          });
        });

        return curVal.join("");
      }
    },
    handleSelDown(weekDay, fullHour) {
      this.positionMouseDown = [weekDay, fullHour];
    },
    handleSelOver(weekDay, fullHour) {
      this.preVal = this.calChosen(weekDay, fullHour);
    },
    handleSelUp(weekDay, fullHour) {
      this.curVal = this.calChosen(weekDay, fullHour);
      this.positionMouseDown = null;
    },
    handleCancelChosen() {
      this.curVal = repeat("0", 24 * 7);
    }
  },
  watch: {
  },
  computed: {
    curVal: {
      get() {
        if (!this.value || this.value.length !== 24 * 7) {
          this.$emit("input", repeat("0", 24 * 7));
        }
        return this.value
      },
      set(payload) {
        this.$emit("input", payload);
      }
    },
    daySegmentsLT24() {
      return this.daySegments.filter((val, key) => key < 24)
    },
    scheduleTimeVal: {
      get: function() {
        let res = "full";
        if (this.curVal.indexOf("0") !== -1) {
          res = "ranges";
        }
        return res;
      },
      set: function(val) {
        if (val === "full") {
          this.$emit("input", repeat("1", 24 * 7));
        } else {
          this.$emit("input", repeat("0", 24 * 7));
        }
      }
    }
  },
  created() {},
  mounted() {}
};
</script>

<style lang="scss" scoped>
.table-bordered {
  width: 100%;
  td,
  th {
    border-left: 1px solid #ddd;
    border-bottom: 1px solid #ddd;
    text-align: center;
    &:last-child {
      border-right: 1px solid #ddd;
    }
  }
  tr:first-child th {
    border-top: 1px solid #ddd;
  }
  td {
    width: 3%;
    min-width: 15px;
    max-width: 30px;
  }
  .week-title {
    width: 8%;
    min-width: 60px;
    max-width: 60px;
  }
}

.table-time-sel {
  .text-normal {
    font-weight: normal;
  }
  .chosen {
    background: #ff0000;
  }
  .cancel_chosen {
    float: right;
    padding-right: 15px;
    cursor: pointer;
  }
}
</style>
