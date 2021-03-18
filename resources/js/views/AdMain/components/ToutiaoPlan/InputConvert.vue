<template>
  <div v-loading="loading" class="input-block">
    <el-form-item :label="label" :prop="getFormScopedField('convert_id')">
      <el-select
        v-model="curVal"
        style="width: 360px"
        filterable>
        <el-option
          v-for="(item, key) in options"
          :key="key"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
      <el-button v-if="!formItemDisabled" type="primary" icon="el-icon-search" @click="reloadOptions"></el-button>
    </el-form-item>
  </div>
</template>

<script>
import dataToutiao from "@/js/store/data/Toutiao";
import { FormScope, xStore } from "@/js/mixins";
import { getBasicData } from "@/js/api/common";
import { Injector } from "../../mixins";

export default {
  mixins: [FormScope, xStore, Injector],
  name: "input-convert",
  props: {
    value: {
      type: [String],
      default: null
    },
    distribute: {
      type: [String],
      required: true
    },
    promoteId: {
      type: [Number],
      required: true
    },
    gameId: {
      type: [Number],
      required: true
    },
    label: {
      type: [String],
      default: "转化元素"
    }
  },
  data() {
    return {
      loading: false,
      curVal:
        this.value === undefined || this.value === null || this.value === 0
          ? null
          : this.value,
      options: []
    };
  },
  methods: {
    reloadOptions: function() {
      this.loading = true;
      this.options = [];
      getBasicData({
        convertList: {
          distribute: this.distribute,
          promote_id: this.promoteId,
          game_id: this.gameId,
        }
      })
        .then(response => {
          this.options = response.data.convertList;
          if (this.options.length > 0) {
          }
          this.loading = false;
        })
        .catch(error => {
          this.loading = false;
          console.log(error);
        });
    }
  },
  watch: {
    curVal: function(val, oldVal) {
      this.$emit("input", val ? val : 0);
    },
    gameId: function() {
      this.curVal = null;
      this.reloadOptions();
    }
  },
  computed: {},
  mounted() {
    this.reloadOptions();
  }
};
</script>
