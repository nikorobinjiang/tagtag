<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-row>
        <el-col>
          <el-radio-group v-model="curUserTargeting" >
            <el-radio-button
              v-for="(item, key) in targetingType"
              v-if="item.show"
              :key="key"
              :label="item.value">{{ item.text }}
            </el-radio-button>
          </el-radio-group>
        </el-col>
      </el-row>
      <el-row v-show="curUserTargeting === '1'">
        <el-col :span="12">
          <el-card class="box-card">
            <div slot="header" class="clearfix">
              <span>兴趣分类</span>
            </div>
            <el-checkbox v-if="false" :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">全选</el-checkbox>
            <el-checkbox-group v-model="curInterest" @change="handleCheckedCitiesChange">
              <el-checkbox
                v-for="(item, key) in interestList"
                :key="key"
                :label="item.value">{{item.label}}
              </el-checkbox>
            </el-checkbox-group>
          </el-card>
        </el-col>
        <el-col :span="12">
          <el-card class="box-card">
            <div slot="header" class="clearfix">
              <span>兴趣关键词{{ tipsWord }}</span>
            </div>
            <el-input
              type="textarea"
              placeholder="换行分隔，最多1000个"
              :rows="6"
              v-model="curWordText"
              @change="handleWordsChange"
              @keyup.enter.native="handleWordsChange(curWordText)">
            </el-input>
          </el-card>
        </el-col>
      </el-row>
    </el-form-item>
    <el-form-item label="">
      <el-checkbox v-model="curIntelliTargeting">同时开启用户智能定向</el-checkbox>
    </el-form-item>
  </div>
</template>

<script>
import { uniq, xor } from "lodash";
import { getUchcData } from "@/js/api/common";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-user-target",
  components: {},
  props: {
    promoteId: {
      default: null
    },
    userTargeting: {
      type: [String],
      required: true
    },
    interest: {
      type: [Array],
      required: true
    },
    word: {
      type: [Array],
      required: true
    },
    intelliTargeting: {
      type: Number,
      required: true
    },
    label: {
      type: [String],
      default: "兴趣与行为定向"
    }
  },
  data() {
    return {
      curUserTargeting:
        this.userTargeting === undefined ||
        this.userTargeting === null ||
        ["-1", "1"].indexOf(this.userTargeting) === -1
          ? "-1"
          : this.userTargeting, // 兴趣与行为自定义
      curInterest:
        this.interest === undefined || this.interest === null
          ? []
          : this.interest, // 兴趣定向
      curWord: this.word === undefined || this.word === null ? [] : this.word, // 关键关键词定向
      curWordText:
        this.word === undefined || this.word === null
          ? ""
          : this.word.join("\n"),
      curIntelliTargeting:
        this.intelliTargeting === undefined || this.intelliTargeting === null
          ? false
          : (this.intelliTargeting == 500 ? true : false), // 开启用户智能定向
      checkAll: false,
      dataFilter: "",
      targetingType: [
        {
          text: "不限",
          value: "-1",
          show: true
        },
        {
          text: "自定义",
          value: "1",
          show: true
        }
      ],
      interestList: []
    };
  },
  computed: {
    isIndeterminate: function() {
      return (
        this.curInterest.length > 0 &&
        this.interestList.length > 0 &&
        this.curInterest.length < this.interestList.length
      );
    },
    tipsWord: function() {
      return `(已添加${this.curWord.length}个，还可以添加${1000 -
        this.curWord.length}个)`;
    }
  },
  methods: {
    handleCheckAllChange(val) {
      this.curInterest = val
        ? this.interestList.map(item => {
            return item.value;
          })
        : [];
    },
    handleCheckedCitiesChange(val) {
      let checkedCount = val.length;
      this.checkAll = checkedCount === this.interestList.length;
      this.curInterest = this.curInterest.filter(item => {
        return !isNaN(item);
      });
    },
    handleWordsChange(val) {
      let words = uniq(
        val.split("\n").filter(item => {
          return item.length > 0;
        })
      );
      if (xor(words, this.curWord).length > 0) {
        this.$set(this.$data, "curWord", words.slice(0, 1000));
      }
      this.curWordText = this.curWord.join("\n") + "\n";
    }
  },
  watch: {
    curUserTargeting: {
      handler: function(val, oldVal) {
        this.$emit("update:userTargeting", val);
      },
      immediate: true
    },
    curInterest: {
      handler: function(val, oldVal) {
        this.$emit("update:interest", val);
      },
      immediate: true
    },
    curWord: {
      handler: function(val, oldVal) {
        this.$emit("update:word", val);
      },
      immediate: true
    },
    // curWordText: {
    //   handler: function(val, oldVal) {
    //     if (val !== oldVal) {
    //       this.handleWordsChange(val);
    //     }
    //   },
    //   immediate: true
    // },
    curIntelliTargeting: {
      handler: function(val, oldVal) {
        this.$emit("update:intelliTargeting", val ? 500 : 0);
      },
      immediate: true
    }
  },
  created() {
    getUchcData({
      promoteId: this.promoteId,
      interestList: {}
    }).then(response => {
      this.interestList = response.data.interestList;
    });
  }
};
</script>
