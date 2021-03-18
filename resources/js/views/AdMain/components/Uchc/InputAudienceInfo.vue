<template>
  <div class="input-block">
    <el-form-item :label="label">
      <el-radio-group v-model="curAudienceTargeting">
        <el-radio-button
          v-for="(item, key) in audienceTargetingList"
          :key="key"
          :label="item.value">
        {{ item.label }}
        </el-radio-button>
      </el-radio-group>
    </el-form-item>
    <el-form-item label="" v-show="curAudienceTargeting !== '-1'">
      <el-checkbox-group v-model="curAudience">
        <el-checkbox
          v-for="item in audienceList"
          :key="item.value"
          :label="item.value">
          {{ item.label }}
        </el-checkbox>
      </el-checkbox-group>
    </el-form-item>
  </div>
</template>

<script>
import { getUchcData } from "@/js/api/common";
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-audience-info",
  props: {
    promoteId: {
      required: true
    },
    audience: {
      type: [Array],
      required: true
    },
    audienceTargeting: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "自定义人群包"
    }
  },
  data() {
    return {
      curAudience: this.audience,
      curAudienceTargeting: this.audienceTargeting
        ? this.audienceTargeting
        : "-1",
      audienceList: [],
      audienceTargetingList: [
        { label: "不限", value: "-1" },
        { label: "定向用户群", value: "1" },
        { label: "排除用户群", value: "2" }
      ]
    };
  },
  computed: {},
  watch: {
    curAudience: {
      handler: function() {
        this.$emit("update:audience", this.curAudience);
      },
      immediate: true
    },
    curAudienceTargeting: {
      handler: function(val) {
        if (val === "-1") {
          this.curAudience = [];
        }
        this.$emit("update:audienceTargeting", this.curAudienceTargeting);
      },
      immediate: true
    }
  },
  created() {
    getUchcData({
      promoteId: this.promoteId,
      audienceList: {}
    }).then(response => {
      this.audienceList = response.data.audienceList;
    });
  }
};
</script>
