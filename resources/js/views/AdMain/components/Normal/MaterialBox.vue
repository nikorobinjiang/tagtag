<template>
  <div
    v-if="isMounted"
    class="input-block">
    <InputIsMultiMaterials
      :formScope="formScope"
      :styleIds="style_ids"
      v-model="curVal.is_multi_materials">
    </InputIsMultiMaterials>

    <InputPosition
      :formScope="formScope"
      :disabled="true"
      v-model="curVal.position_id"
      :options="positionList">
    </InputPosition>

    <InputStyles
      id="creative-styles"
      :formScope="formScope"
      :disabled="material_ids.length > 0"
      v-model="style_ids"
      :options="styleList"
      :isMultiMaterials="curVal.is_multi_materials"
      :materialInfo="curVal.material_info">
    </InputStyles>

    <div v-if="selectmode === 'customize'">
      <StyleBox
        :formScope="formScope"
        v-for="(item) in style_ids"
        :key="item"
        v-model="style_box"
        :promoteId="promoteId"
        :styleId="item"
        :styleList="styleList"
        :gameId="gameId"
        :isMultiMaterials="curVal.is_multi_materials">
      </StyleBox>
    </div>
    <div v-else-if="selectmode === 'procedural'">
      <StyleBoxProcedural
        :formScope="formScope"
        v-model="style_box"
        :promoteId="promoteId"
        :styleIds="style_ids"
        :gameId="gameId">
      </StyleBoxProcedural>
    </div>
  </div>
</template>

<script>
import { flatten, values } from 'lodash';
import { selMedia, selPosition } from "@/js/api/common";
import { FormScope, xStore } from "@/js/mixins";
import dataToutiao from "@/js/store/data/Toutiao";
import { Injector } from "../../mixins";

import InputIsMultiMaterials from "@/js/views/AdMain/components/Normal/InputIsMultiMaterials";
import InputPosition from "@/js/views/AdMain/components/Normal/InputPosition";
import InputStyles from "@/js/views/AdMain/components/Normal/InputStyles";
import StyleBox from "@/js/views/AdMain/components/Normal/StyleBox";
import StyleBoxProcedural from "@/js/views/AdMain/components/Normal/StyleBoxProcedural";

export default {
  mixins: [FormScope, xStore, Injector],
  name: "material-box",
  components: {
    InputIsMultiMaterials,
    InputPosition,
    InputStyles,
    StyleBox,
    StyleBoxProcedural
  },
  props: {
    value: {
      type: [Object],
      required: true
    },
    selectmode: {
      type: [String],
      default: "customize"
    },
    promoteId: {
      type: [Number],
      required: true
    },
    mediaId: {
      type: [Number],
      required: true
    },
    gameId: {
      type: [Number],
      required: true
    }
  },
  data() {
    return {
      isMounted: false,
      curVal:
        this.value === undefined || this.value === null
          ? {
              is_multi_materials: true,
              style_ids: [],
              position_id: null,
              material_info: {},
              procedural_content: {}
            }
          : this.value,
      positionList: [],
      styleList: []
    };
  },
  methods: {
    handlePositionIdChange: function(val, oldVal) {
      if (val !== oldVal || this.styleList.length <= 0) {
        this.positionList.forEach((position, idx) => {
          if (position.value === val) {
            this.$emit("changePosition", position);
          }
        });
        selPosition({
          position_id: val
        })
          .then(response => {
            this.styleList = response.data.styleList;
            if (oldVal) {
              this.style_ids = [];
            }
          })
          .catch(error => {
            console.log(error);
          });
      }
    }
  },
  watch: {
    curVal: {
      handler: function(val) {
        this.$emit("input", val);
      },
      deep: true
    },
    "curVal.position_id": {
      handler: function(val, oldVal) {
        this.handlePositionIdChange(val, oldVal);
      },
      immediate: true
    },
    selectmode: {
      handler: function() {
        // 根据创意生成方式，重载 style_ids
        let getIds = content => {
          return Object.keys(content)
            .filter(id => {
              return !isNaN(id);
            })
            .map(id => {
              return parseInt(id);
            });
        };
        if (this.selectmode === "procedural") {
          let tmpStyleIds = getIds(this.curVal.procedural_content);
          if (_.xor(this.style_ids, tmpStyleIds).length > 0) {
            this.style_ids = tmpStyleIds;
          }
        } else if (this.selectmode === "customize") {
          let tmpStyleIds = getIds(this.curVal.material_info);
          if (_.xor(this.style_ids, tmpStyleIds).length > 0) {
            this.style_ids = tmpStyleIds;
          }
        }
      },
      immediate: true
    }
  },
  computed: {
    material_ids: function() {
      if (this.style_box && this.style_box.material_info) {
        const ids = flatten(values(this.style_box.material_info)).filter(item => {
          return item.id;
        }).map(item => {
          return item.id;
        });
        return values(ids);
      } else {
        return [];
      }
    },
    style_ids: {
      get: function() {
        return this.curVal.style_ids ? this.curVal.style_ids : [];
      },
      set: function(val) {
        this.curVal.style_ids = val;
        // 删除广告素材
        Object.keys(this.curVal.material_info).forEach(key => {
          if (
            this.selectmode === "customize" &&
            !isNaN(key) &&
            val.indexOf(parseInt(key)) === -1
          ) {
            Vue.delete(this.curVal.material_info, key);
          }
        });
        // 删除广告元素材
        Object.keys(this.curVal.procedural_content).forEach(key => {
          if (
            this.selectmode === "procedural" &&
            !isNaN(key) &&
            val.indexOf(parseInt(key)) === -1
          ) {
            Vue.delete(this.curVal.procedural_content, key);
          }
        });
      }
    },
    style_box: {
      get: function() {
        return {
          material_info: this.curVal.material_info,
          procedural_content: this.curVal.procedural_content
        };
      },
      set: function(val) {
        this.curVal.material_info = val.material_info;
        this.curVal.procedural_content = val.procedural_content;
      }
    },
    position: function() {
      let res = null;
      this.positionList.forEach((item, idx) => {
        if (item.value === curVal.position_id) {
          res = item;
        }
      });
      this.$emit("transferPosition", res);
      console.log(res);
      return res;
    }
  },
  created() {
    // 加载广告位样式
    selMedia({
      media_id: this.mediaId
    })
      .then(response => {
        this.positionList = response.data.positionList;
      })
      .then(_ => {
        this.handlePositionIdChange(this.curVal.position_id);
        this.isMounted = true;
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>
