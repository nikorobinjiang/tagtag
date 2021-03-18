<template>
  <div v-if="!xloading">
    <el-row v-if="['edit', 'show'].indexOf(requestAction) !== -1 && ['iOS', 'WXMP'].indexOf(appType) === -1">
      <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 12 : 24">
        <el-form-item label="apk壳地址"> {{ appType }}
          <el-input disabled v-model="downAddr">
            <el-button slot="append" v-clipboard="{ text: downAddr }">复制</el-button>
          </el-input>
        </el-form-item>
      </el-col>

      <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 12 : 24">
        <el-form-item label="游戏域名">
          <el-input disabled v-model="gameDomainUrl"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span="12">
        <el-form-item label="投放游戏" prop="game_id">
          <el-select v-model="curVal.game_id" filterable>
            <el-option
              v-for="(item, key) in gameList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>

      <el-col :span="12">
        <InputPosition
          v-model="curVal.position_id"
          :options="positionList"
          :disabled="['edit'].indexOf(requestAction) !== -1">
        </InputPosition>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span="['edit', 'show'].indexOf(requestAction) === -1 ? 6 : (['WXMP'].indexOf(appType) === -1 ? 6 : 12)">
        <el-form-item label="结算方式" prop="settlement">
          <el-select v-model="curVal.settlement">
            <el-option
              v-for="(item, key) in settlementList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col v-if="['WXMP'].indexOf(appType) === -1" :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 6 : 6">
        <el-form-item label="是否有水印" prop="has_watermark">
          <el-select v-model="curVal.has_watermark" disabled>
            <el-option
              v-for="(item, key) in watermarkList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>

      <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 12 : 12" v-if="appType === 'iOS'">
        <InputAppId
          :gameList="gameList"
          v-model="curVal.game_id">
        </InputAppId>
      </el-col>

      <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 12 : 24"
        v-if="['edit', 'show'].indexOf(requestAction) !== -1 && ['iOS', 'WXMP'].indexOf(appType) === -1">
        <el-form-item label="apk地址">
          <el-input v-model="curVal.apk_addr">
            <el-button slot="append" v-clipboard="{ text: curVal.apk_addr }">复制</el-button>
          </el-input>
        </el-form-item>
      </el-col>

      <el-col :span="['edit', 'show'].indexOf(requestAction) !== -1 ? 12 : 24"
        v-if="['edit', 'show'].indexOf(requestAction) !== -1 && appType === 'WXMP'">
        <el-form-item label="追踪参数">
          <el-input disabled v-model="curVal.wxmp_tracking">
            <el-button slot="append" v-clipboard="{ text: curVal.wxmp_tracking }">复制</el-button>
          </el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <template v-if="appType !== 'WXMP'">
      <el-row v-if="requestAction !== 'create'">
        <el-col :span="24">
          <InputAppBundleID
            :gameList="gameList"
            v-model="curVal.game_id">
          </InputAppBundleID>
        </el-col>
      </el-row>

      <el-row v-if="appType === 'iOS'">
        <el-col :span="24">
          <InputAppUrl
            :gameList="gameList"
            v-model="curVal.game_id">
          </InputAppUrl>
        </el-col>
        <el-col :span="24" v-if="['edit', 'show'].indexOf(requestAction) !== -1">
          <InputAppTrackUrl
            v-model="curVal.track_url">
          </InputAppTrackUrl>
        </el-col>
      </el-row>

      <el-row>
        <el-col :span="12">
          <InputIsMultiMaterials
            :styleIds="curVal.style_ids"
            v-model="is_multi_materials">
          </InputIsMultiMaterials>
        </el-col>
        <el-col :span="12" v-if="appType !== 'iOS'">
          <InputApkDomain
            v-model="curVal.apk_domain">
          </InputApkDomain>
        </el-col>
      </el-row>

      <InputStyles
        v-if="curVal.position_id"
        v-model="curVal.style_ids"
        :options="curVal.styleList"
        :isMultiMaterials="is_multi_materials"
        :materialInfo="curVal.material_info"
        :disabled="material_ids.length > 0">
      </InputStyles>

      <el-form-item
        v-if="['show', 'edit'].indexOf(requestAction) !== -1 && curVal.style_ids.length > 0"
        lable="">
        <el-button
          class="clearfix"
          type="primary"
          size="mini"
          @click="handleDownloadAdMateralClick">下载全部素材
        </el-button>
      </el-form-item>

      <StyleBox
        :formScope="curVal.distribute"
        v-for="(item) in curVal.style_ids"
        :key="item"
        v-model="style_box"
        :promoteId="curVal.promote_id"
        :styleId="item"
        :styleList="curVal.styleList"
        :gameId="curVal.game_id"
        :isMultiMaterials="is_multi_materials">
      </StyleBox>

      <el-form-item label="落地页"
        v-if="couldLandingPage">
        <el-radio-group v-model="has_landing_page">
          <el-radio
            v-for="(item, key) in curVal.hasLandingPageList"
            :key="key"
            :label="item.value">{{ item.label}}
          </el-radio>
        </el-radio-group>
      </el-form-item>

      <LandingPageBox
        v-if="has_landing_page"
        v-model="landing_box"
        :gameId="curVal.game_id"
        :lpCompanyFull="curVal.lp_company_full"
        :shellUrl="curVal.shell_url"
        :adUrl="curVal.ad_url">
      </LandingPageBox>

      <el-form-item v-if="['edit', 'show'].indexOf(requestAction) !== -1 && appType !== 'iOS'" label="安装包历史">
        <el-row
          v-for="(item, key) in downHistories"
          :key="key">
          <span>
            <a class="width-80-nowrap" target="_blank" :href=" item.down_url?item.down_url:'javascript:void:;'">
              {{ item.down_url?basename(item.down_url):'无' }}
            </a>
            <span v-if="key > 0 && !formItemDisabled" @click="delDownHistory(key, item)">删除</span>
          </span>
        </el-row>
      </el-form-item>
    </template>
  </div>
</template>

<script>
import { flatten, values } from 'lodash';
import { getBasicRelData, selMedia, selPosition } from "@/js/api/common";
import { removeDwonHistory, downloadAdMaterial } from "@/js/api/advertising";
import { downloadFile, downloadBlob, basename } from "@/js/utils";
import { xLoading, xStore } from "@/js/mixins";
import { Injector } from "../mixins";

import InputAppId from "./Normal/InputAppId";
import InputAppUrl from "./Normal/InputAppUrl";
import InputAppBundleID from "./Normal/InputAppBundleID";
import InputAppTrackUrl from "./Normal/InputAppTrackUrl";
import InputIsMultiMaterials from "./Normal/InputIsMultiMaterials";
import InputPosition from "@/js/views/AdMain/components/Normal/InputPosition";
import InputStyles from "@/js/views/AdMain/components/Normal/InputStyles";
import InputApkDomain from "./Normal/InputApkDomain";
import StyleBox from "./Normal/StyleBox";
import LandingPageBox from "./Normal/LandingPageBox";

export default {
  mixins: [xLoading, xStore, Injector],
  name: "form-normal-main",
  components: {
    InputAppId,
    InputAppUrl,
    InputAppBundleID,
    InputAppTrackUrl,
    InputIsMultiMaterials,
    InputPosition,
    InputStyles,
    InputApkDomain,
    StyleBox,
    LandingPageBox
  },
  props: {
    value: {
      type: [Object],
      required: true
    },
    mediaId: {},
    downAddr: {},
    gameDomainUrl: {},
    gameList: {}
  },
  data() {
    return {
      positionList: [],
      settlementList: [],
      downHistories: [],
      curVal:
        this.value === undefined || this.value === null
          ? {
              game_id: null,
              position_id: null,
              settlement: "",
              wxmp_tracking: "",
              apk_addr: "",
              is_multi_materials: false,
              apk_domain: "",
              style_ids: [],
              material_info: {},
              has_landing_page: null,
              template_id: null,
              template_info: {},
              shell_url: "",
              ad_url: "",
              lp_company_full: "",

              track_url: "",
              styleList: [],
              hasLandingPageList: []
            }
          : this.value
    };
  },
  methods: {
    basename: function(str) {
      return basename(str);
    },
    handleDownloadAdMateralClick: function() {
      let use_blob = false;
      let filename = this.curVal.ad_name + ".zip";
      downloadAdMaterial({
        ad_id: this.curVal.ad_id,
        material_info: this.curVal.material_info,
        template_id: this.curVal.template_id
      })
        .then(response => {
          if (use_blob) {
            window.data = response.data;
            if (response.data instanceof Blob && response.data.size > 0) {
              downloadBlob(response.data, filename);
            } else {
              this.$message.warning("素材为空");
            }
          } else {
            if (response.data.download_url) {
              downloadFile(response.data.download_url, filename);
            } else {
              this.$message.warning("素材为空");
            }
          }
        })
        .catch(error => {
          console.log(error);
        });
    },
    handlePositionIdChange: function(val, oldVal) {
      if (val && (val !== oldVal || this.curVal.styleList.length <= 0)) {
        selPosition({
          position_id: val
        })
          .then(response => {
            this.curVal.styleList = response.data.styleList;
            if (oldVal) {
              this.curVal.style_ids = [];
            }
          })
          .catch(error => {
            console.log(error);
          });
      }
    },
    delDownHistory: function(key, item) {
      this.xloading = true;
      removeDwonHistory(this.curVal.ad_id, item.id)
        .then(response => {
          let data = response.data;
          if (data.result == "success") {
            this.$message.success(data.message);
            Vue.delete(this.downHistories, key);
            this.$forceUpdate();
          } else {
            this.$message.error(data.message);
          }
          this.xloading = false;
        })
        .catch(error => {
          console.log(error);
          this.xloading = false;
        });
    },
    clearMaterialInfo() {
      this.curVal.material_info = {};
      this.curVal.style_ids = [];
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
    // "curVal.game_id": function() {
    //   let game = null;
    //   this.gameList.forEach(item => {
    //     if (item.value === this.curVal.game_id) {
    //       game = item;
    //     }
    //   });
    //   if (game) {
    //     if (game.app_type === "iOS") {
    //       this.has_landing_page = 0;
    //     }
    //     this.curVal.app_type = game.app_type;
    //   }
    // },
    mediaId: {
      handler: function(val, oldVal) {
        if (val) {
          // this.xloading = true;
          selMedia({
            media_id: val
          })
            .then(response => {
              let settlementList = [];
              for (let key in response.data.settlementList) {
                if (!this.curVal.settlement) {
                  this.curVal.settlement = response.data.settlementList[key];
                }
                settlementList.push({
                  label: response.data.settlementList[key],
                  value: response.data.settlementList[key]
                });
              }
              this.settlementList = settlementList;

              if (response.data.positionList) {
                this.positionList = response.data.positionList;
                if (this.positionList.length > 0 && oldVal !== undefined) {
                  this.curVal.position_id = this.positionList[0].value;
                }
              }
              // this.xloading = false;
            })
            .catch(error => {
              // this.xloading = false;
              console.log(error);
            });
        }
      },
      immediate: true
    }
  },
  computed: {
    watermarkList() {
      return this.xStore.data.watermarkList;
    },
    style_box: {
      get: function() {
        return {
          material_info: this.curVal.material_info
        };
      },
      set: function(val) {
        this.curVal.material_info = val.material_info;
      }
    },
    material_ids: function() {
      if (this.curVal.material_info) {
        const ids = flatten(values(this.curVal.material_info)).filter(item => {
          return item.id;
        }).map(item => {
          return item.id;
        });
        return values(ids);
      } else {
        return [];
      }
    },
    landing_box: {
      get: function() {
        return {
          template_id: this.curVal.template_id,
          template_info: this.curVal.template_info
        };
      },
      set: function(val) {
        this.curVal.template_id = val.template_id;
        this.curVal.template_info = val.template_info;
      }
    },
    is_multi_materials: {
      get() {
        return this.curVal.is_multi_materials;
      },
      set(val) {
        let needReset = false;
        if (this.curVal.style_ids.length > 1) {
          needReset = true;
        } else {
          for (const key in this.curVal.style_ids) {
            const id = this.curVal.style_ids[key];
            if (
              this.curVal.material_info.hasOwnProperty(id) &&
              this.curVal.material_info[id].length > 1
            ) {
              needReset = true;
            }
          }
        }
        if (needReset) {
          this.$confirm("该操作将会清空所有的素材样式", "", {
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            type: "warning"
          })
            .then(_ => {
              this.curVal.template_id = 0;
              this.curVal.template_info = {};
              this.curVal.is_multi_materials = val;
              this.curVal.material_info = {};
              this.curVal.style_ids = [];
            })
            .catch(_ => {});
        } else {
          this.curVal.is_multi_materials = val;
        }
      }
    },
    appType: function() {
      let res = null;
      this.gameList.forEach(item => {
        if (item.value === this.curVal.game_id) {
          res = item.app_type;
        }
      });
      if (!res) {
        res = this.curVal.app_type;
      }
      return res;
    },
    couldLandingPage: function() {
      // if (this.appType === 'iOS') {
      if (this.curVal.distribute === "Toutiao") {
        this.has_landing_page = 0;
        return false;
      }
      // }
      return true;
    },
    // style_ids: {
    //   get() {
    //     return this.curVal.style_ids ? this.curVal.style_ids : [];
    //   },
    //   set(val) {
    //     let diff = _.xor(this.style_ids, val);
    //     let needConfirm = false;
    //     diff.forEach(id => {
    //       if (this.curVal.material_info[id]) {
    //         needConfirm = true;
    //       }
    //     });
    //     if (val.length < this.style_ids.length && needConfirm) {
    //       this.$confirm("取消勾选将会清除该样式素材", "", {
    //         confirmButtonText: "确定",
    //         cancelButtonText: "取消",
    //         type: "warning"
    //       })
    //         .then(_ => {
    //           this.curVal.style_ids = val;

    //           Object.keys(this.curVal.material_info).forEach(key => {
    //             if (val.indexOf(parseInt(key)) === -1) {
    //               Vue.delete(this.curVal.material_info, key);
    //             }
    //           });
    //         })
    //         .catch(_ => {});
    //     } else {
    //       this.curVal.style_ids = val;
    //     }
    //   }
    // },
    has_landing_page: {
      get() {
        return this.curVal.has_landing_page;
      },
      set(val) {
        if (val === 0 && this.curVal.template_id > 0) {
          this.$confirm("该操作将清空选择的素材", "", {
            confirmButtonText: "确定",
            cancelButtonText: "取消",
            type: "warning"
          })
            .then(_ => {
              this.curVal.template_id = 0;
              this.curVal.template_info = {};
              this.curVal.has_landing_page = val;
            })
            .catch(_ => {});
        } else {
          this.curVal.has_landing_page = val;
        }
      }
    }
  },
  created() {
    getBasicRelData({
      downHistories: {
        ad_id: this.value.ad_id
      }
    }).then(response => {
      this.downHistories = response.data.downHistories;
    });
  }
};
</script>
