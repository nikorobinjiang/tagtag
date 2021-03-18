<template>
  <div class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('generalizeType')">
      <el-radio-group v-model="curVal" size="small">
        <el-radio v-for="item in optionsCal"
          :key="item.value"
          :label="item.value">{{ item.label}}
        </el-radio>
      </el-radio-group>
    </el-form-item>
  </div>
</template>

<script>
import { head } from 'lodash'
import { FormScope } from "@/js/mixins";

export default {
  mixins: [FormScope],
  name: "InputObjectiveType",
  props: {
    value: {
      type: Number,
      required: true
    },
    appType: {
      type: String,
      default: 'Android'
    },
    label: {
      type: String,
      default: "推广对象"
    }
  },
  data() {
    return {
      options: [{
        label: "推广网站",
        value: 1,
        scope: ['H5', 'Android', 'iOS']
      }, {
        label: "推广iOS应用",
        value: 2,
        scope: ['iOS']
      // }, {
      //   label: "Android App",
      //   value: 4,
      //   scope: ['Android']
      }]
    };
  },
  computed: {
    optionsCal: function() {
      return this.options.filter(item => item.scope.indexOf(this.appType) !== -1)
    },
    curVal: {
      get() {
        if (this.optionsCal.map(item => item.value).indexOf(this.value) === -1) {
          this.$emit("input", 1);
        }
        return this.value
      },
      set(payload) {
        this.$emit("input", payload);
      }
    }
  },
  methods: {},
  watch: {
  }
};
</script>
