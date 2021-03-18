<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-radio-group v-model="audienceTargeting">
        <el-radio-button
          v-for="(item, key) in audienceTargetingList"
          :key="key"
          :label="item.value">
        {{ item.label }}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
    <el-form-item label="" v-show="audienceTargeting === 'LIMIT'">
      <el-card class="box-card">
        <div slot="header" class="clearfix">
          <span>
            选定人群
            <template v-if="audienceList.length === 0">（人群包待添加）</template>
          </span>
        </div>
        <div class="audience-sel-box-content">
          <el-tabs v-model="activeLimitTabs">
            <el-tab-pane label="定向" name="include">
              <el-checkbox-group v-model="curRetargetingTagsInclude">
                <el-checkbox
                  v-for="item in audienceList"
                  :key="item.value"
                  :disabled="curRetargetingTagsExclude.indexOf(item.value) !== -1"
                  :label="item.value">
                  {{ item.label }}
                </el-checkbox>
              </el-checkbox-group>
            </el-tab-pane>
            <el-tab-pane label="排除" name="exclude">
              <el-checkbox-group v-model="curRetargetingTagsExclude">
                <el-checkbox
                  v-for="item in audienceList"
                  :key="item.value"
                  :disabled="curRetargetingTagsInclude.indexOf(item.value) !== -1"
                  :label="item.value">
                  {{ item.label }}
                </el-checkbox>
              </el-checkbox-group>
            </el-tab-pane>
          </el-tabs>
        </div>
      </el-card>
    </el-form-item>
  </div>
</template>

<script>
import { getToutiaoData } from "@/js/api/common";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-audience-info",
  props: {
    promoteId: {
      required: true
    },
    retargetingTagsInclude: {
      type: [Array],
      required: true
    },
    retargetingTagsExclude: {
      type: [Array],
      required: true
    },
    label: {
      type: [String],
      default: "人群包"
    }
  },
  data() {
    return {
      curRetargetingTagsInclude: this.retargetingTagsInclude
        ? this.retargetingTagsInclude
        : [],
      curRetargetingTagsExclude: this.retargetingTagsExclude
        ? this.retargetingTagsExclude
        : [],
      audienceList: [],
      activeLimitTabs: "include",
      audienceTargeting: "NOLIMIT",
      audienceTargetingList: [
        { label: "不限", value: "NOLIMIT" },
        { label: "定向或排除人群包", value: "LIMIT" }
      ]
    };
  },
  computed: {},
  watch: {
    audienceTargeting: {
      handler: function(val) {
        if (val === 'NOLIMIT') {
          this.curRetargetingTagsInclude = [];
          this.curRetargetingTagsExclude = [];
        }
      }
    },
    curRetargetingTagsInclude: {
      handler: function(val) {
        if (val.length > 0) {
          this.audienceTargeting = "LIMIT";
        }
        this.$emit(
          "update:retargetingTagsInclude",
          this.curRetargetingTagsInclude
        );
      },
      immediate: true
    },
    curRetargetingTagsExclude: {
      handler: function(val) {
        if (val.length > 0) {
          this.audienceTargeting = "LIMIT";
        }
        this.$emit(
          "update:retargetingTagsExclude",
          this.curRetargetingTagsExclude
        );
      },
      immediate: true
    }
  },
  created() {
    getToutiaoData({
      promoteId: this.promoteId,
      audienceList: {}
    }).then(response => {
      this.audienceList = response.data.audienceList;
    });
  }
};
</script>

<style lang="scss" scoped>
.audience-sel-box-content {
  padding: 0px 10px;
}
</style>

