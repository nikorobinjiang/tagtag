<template>
    <div class="input-block">
        <el-form-item :label="label">
            <el-radio-group v-model="retargetingType">
                <el-radio-button
                v-for="(item, key) in customWordsType"
                :key="key"
                :label="item.value">
                {{ item.text }}
                </el-radio-button>
            </el-radio-group>
        </el-form-item>
        <el-form-item label="" v-show="retargetingType !== 0">
            <el-select v-model="retargetingTags" multiple placeholder="请选择">
                <el-option
                v-for="item in retargetingTagsType"
                :key="item.value"
                :label="item.label"
                :value="item.value">
                </el-option>
            </el-select>
        </el-form-item>
    </div>
</template>

<script>
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "input-custom-words",
  props: {
    state: {},
    label: {
      type: [String],
      default: "自定义人群包"
    }
  },
  data() {
    return {
      customWordsType:
        this.state.typeOptions.customWordsType === undefined ||
        this.state.typeOptions.customWordsType === null
          ? []
          : this.state.typeOptions.customWordsType,
      retargetingTagsType:
        this.state.typeOptions.retargetingTagsType === undefined ||
        this.state.typeOptions.retargetingTagsType === null
          ? []
          : this.state.typeOptions.retargetingTagsType
    };
  },
  computed: {
    retargetingType: {
      get() {
        return this.state.form.retargetingType;
      },
      set(val) {
        this.$store.dispatch(
          this.state.getActionName("form.retargetingType"),
          val
        );
      }
    },
    retargetingTags: {
      get() {
        return this.state.form.retargetingTags;
      },
      set(val) {
        this.$store.dispatch(
          this.state.getActionName("form.retargetingTags"),
          val
        );
      }
    }
  }
};
</script>
