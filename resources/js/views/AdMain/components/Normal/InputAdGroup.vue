<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('ad_group_id')">
      <el-select
        :disabled="isDisabled"
        v-model="curVal"
        filterable>
        <el-option
          v-for="(item, key) in options"
          :key="key"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope, xStore } from "@/js/mixins";
import { getBasicData } from "@/js/api/common";

export default {
  mixins: [FormScope, xStore],
  name: "input-ad-group",
  props: {
    value: {
      type: [Number],
      default: null
    },
    promoteId: {
      type: [Number],
      default: null
    },
    distribute: {
      type: [String],
      required: true
    },
    label: {
      type: [String],
      default: "广告组"
    }
  },
  data() {
    return {
      isDisabled: this.value ? true : false,
      curVal:
        this.value === undefined || this.value === null || this.value === 0
          ? null
          : this.value,
      options: []
    };
  },
  methods: {
    // 媒体账号联动
    handlePromoteChange: function(val, oldVal) {
      if (!val) {
        return;
      }
      this.options = [];
      getBasicData({
        adGroupList: {
          promote_id: this.promoteId,
          hasCampaign: true
        }
      })
        .then(response => {
          this.options = response.data.adGroupList;
        })
        .catch(error => {
          console.log(error);
        });
    }
  },
  watch: {
    curVal: function(val, oldVal) {
      this.$emit("input", val ? val : 0);
    },
    promoteId: {
      handler: function(val, oldVal) {
        this.handlePromoteChange(val, oldVal);
      },
      immediate: true
    }
  },
  computed: {
    curItem: function() {
      let res = {
        value: null,
        label: ""
      };
      this.options.forEach(item => {
        if (item.value === this.curVal) {
          res = item;
        }
      });
      return res;
    }
  },
  mounted() {}
};
</script>
