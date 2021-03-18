<template>
  <el-form
    v-loading="loading"
    :model="form"
    :rules="rules"
    :disabled="requestAction==='show'"
    ref="agentForm"
    label-width="100px"
    size="mini">
    代理商基础信息
    <el-row>
      <el-col :span="12">
        <el-form-item label="代理商名称" prop="agent_name">
          <el-input v-model="form.agent_name"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="联系人" prop="linkman">
          <el-input v-model="form.linkman"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span="12">
        <el-form-item label="联系电话" prop="linkman_phone">
              <el-input v-model="form.linkman_phone"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="邮箱地址" prop="email">
              <el-input v-model="form.email"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-form-item label="落地页公司" prop="companys">
      <el-row v-for="(company, key) in form.companys" :key="key">
        <el-col :span="7">
          <el-input
            placeholder="请输入公司名称"
            prefix-icon="el-icon-info"
            v-model="company.name">
          </el-input>
        </el-col>
        <el-col :span="7">
          <el-input
            placeholder="请输入公司地址(非必填)"
            prefix-icon="el-icon-location-outline"
            v-model="company.addr">
          </el-input>
        </el-col>
        <el-col :span="7">
          <el-input
            placeholder="请输入公司电话(非必填)"
            prefix-icon="el-icon-phone"
            v-model="company.tel">
          </el-input>
        </el-col>
        <el-col :span="1">
          <el-button size="mini" type="primary" icon="el-icon-plus" @click="handleAddCompany" circle v-if="key === 0"></el-button>
          <el-button size="mini" type="danger" icon="el-icon-minus" @click="handleDelCompany(key, company)" circle v-else></el-button>
        </el-col>
      </el-row>
    </el-form-item>

    <h4>代理商负责信息</h4>
    <el-row>
      <el-col :span="12">
        <el-form-item label="代理商账号" prop="account">
          <el-input v-model="form.account"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="代理商密码" prop="password">
          <el-input v-model="form.password"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span="12">
        <el-form-item label="负责媒体" prop="media_ids">
          <el-select v-model="form.media_ids" multiple filterable @change="handleMediaIdChange">
            <el-option
              v-for="item in mediaList"
              :key="item.value"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="负责媒体账号" prop="promote_ids" v-if="false">
          <el-select v-model="form.promote_ids" multiple filterable>
            <el-option
              v-for="(item, key) in promoteList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="负责游戏" prop="game_ids">
          <el-select v-model="form.game_ids" multiple filterable>
          <el-option
            v-for="item in gameList"
            :key="item.value"
            :title="item.label"
            :label="item.label"
            :value="item.value"></el-option>
          </el-select>
        </el-form-item>
      </el-col>
    </el-row>

    <el-form-item label="创建广告" prop="can_create_ad">
      <el-radio v-model="form.can_create_ad" :label="1" :disabled="diableCreateAd">是</el-radio>
      <el-radio v-model="form.can_create_ad" :label="0">否</el-radio>
    </el-form-item>

    <el-form-item label="查看数据项" prop="data_items">
      <el-checkbox-group v-model="form.data_items">
        <el-checkbox
          v-for="item in agentDataFieldList"
          :key="item.value"
          :label="item.value">{{item.label}}
        </el-checkbox>
      </el-checkbox-group>
    </el-form-item>

    <el-form-item v-if="requestAction !== 'show'">
      <el-button @click="cancelForm">取消</el-button>
      <el-button type="primary" :disabled="submitDisabled" @click="submitForm">确认</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { xor } from "lodash";
import { getBasicData } from "@/js/api/common";
import { editItem, storeItem, updateItem } from "@/js/api/agent";
import { renewObject } from "@/js/utils";

export default {
  name: "gmp-agent-main-form",
  components: {},
  props: {
    requestAction: {
      type: [String],
      required: true
    },
    itemId: {
      default: null
    }
  },
  data: function() {
    return {
      loading: true,
      settlementList: [],
      mediaList: [],
      gameList: [],
      agentDataFieldList: [],
      submitDisabled: false,
      formInit: {
        agent_name: "",
        linkman: "",
        linkman_phone: "",
        email: "",
        companys: [],
        account: "",
        password: "",
        media_ids: [],
        game_ids: [],
        promote_ids: [],
        can_create_ad: null,
        data_items: []
      },
      form: {},
      rules: {
        agent_name: [
          { required: true, message: "请输入代理商名称", trigger: "blur" }
        ],
        linkman_phone: [
          { required: true, message: "请输入联系电话", trigger: "blur" }
        ],
        companys: [
          {
            type: "array",
            required: true,
            message: "请输入落地页公司",
            trigger: "blur"
          },
          {
            validator: (rule, value, callback) => {
              let hasEmpty = false;
              value.forEach(item => {
                if (!item.name || item.name.length <= 0) {
                  hasEmpty = true;
                }
              });

              if (hasEmpty) {
                callback(new Error("请完整填写落地页公司名称"));
              } else {
                callback();
              }
            },
            trigger: "blur"
          }
        ],
        account: [
          { required: true, message: "请输入代理商账号", trigger: "blur" }
        ],
        password: [
          { required: true, message: "请输入代理商密码", trigger: "blur" }
        ],
        media_ids: [
          {
            type: "array",
            required: true,
            message: "请选择负责媒体",
            trigger: "blur"
          }
        ],
        game_ids: [
          {
            type: "array",
            required: true,
            message: "请选择负责游戏",
            trigger: "blur"
          }
        ]
      }
      // promoteList: []
    };
  },
  methods: {
    cancelForm() {
      this.$emit("update:visible", false);
    },
    submitForm(btn) {
      if (this.form.media_ids.length > 0) {
        this.form.media_ids = this.form.media_ids.filter(id => {
          return id > 0;
        });
      }
      if (this.form.game_ids.length > 0) {
        this.form.game_ids = this.form.game_ids.filter(id => {
          return id > 0;
        });
      }
      if (this.form.promote_ids.length > 0) {
        this.form.promote_ids = this.form.promote_ids.filter(id => {
          return id > 0;
        });
      }
      if (this.form.data_items.length > 0) {
        this.form.data_items = this.form.data_items.filter(id => {
          let res = false;
          this.agentDataFieldList.forEach(item => {
            if (item.value === id) {
              res = true;
            }
          });
          return res;
        });
      }
      this.$refs.agentForm.validate(valid => {
        if (valid) {
          this.submitDisabled = true;
          (this.requestAction === "edit"
            ? updateItem(this.itemId, this.form)
            : storeItem(this.form)
          )
            .then(response => {
              if (response.data.result == "error") {
                this.$alert(response.data.message, "", {
                  confirmButtonText: "确定"
                });
                this.submitDisabled = false;
              } else {
                this.$alert("保存成功", "", {
                  confirmButtonText: "确定",
                  callback: _ => {
                    location.reload();
                  }
                });
                setTimeout(function() {
                  location.reload();
                }, 2000);
              }
            })
            .catch(error => {
              console.log("error", error);
              this.submitDisabled = false;
            });
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    cancelForm() {
      this.$emit("update:visible", false);
    },
    handleAddCompany() {
      this.form.companys.push({
        name: "",
        addr: "",
        tel: ""
      });
    },
    handleDelCompany(key, company) {
      this.form.companys.splice(key, 1);
    },
    // 负责媒体联动
    handleMediaIdChange: function() {
      let val = this.form.media_ids;
      let selectedMedia = this.mediaList.filter(item => {
        return this.form.media_ids.indexOf(item.value) !== -1;
      });
      let canCreateAd = false;
      selectedMedia.map(item => {
        if (item.has_promote_api && item.has_promote_api === true) {
          canCreateAd = true;
        }
      });
      // 只选中媒体对接过第三方平台时 可以选创建广告
      if (canCreateAd) {
        this.form.can_create_ad = 1;
      } else {
        this.form.can_create_ad = 0;
      }
    }
  },
  watch: {
    itemId: {
      handler: function() {
        this.loading = true;
        this.$set(this.$data, "form", renewObject(this.formInit));
        if (this.form.companys.length <= 0) {
          this.handleAddCompany();
        }
        // 加载编辑数据
        if (this.itemId) {
          editItem(this.itemId).then(response => {
            let it = response.data.it;
            it.media_ids = it.media_ids.map(item => {
              if (item != "") {
                return parseInt(item);
              }
            });
            it.game_ids = it.game_ids.map(item => {
              if (item != "") {
                return parseInt(item);
              }
            });
            it.promote_ids = it.promote_ids.map(item => {
              if (item != "") {
                return parseInt(item);
              }
            });
            this.$set(this.$data, "form", it);
            if (this.form.companys.length <= 0) {
              this.handleAddCompany();
            }
            this.loading = false;
          });
        } else {
          this.loading = false;
        }
      },
      immediate: true
    }
  },
  computed: {
    diableCreateAd: function() {
      let selectedMedia = this.mediaList.filter(item => {
        return this.form.media_ids.indexOf(item.value) !== -1;
      });

      let canCreateAd = false;
      selectedMedia.map(item => {
        if (item.has_promote_api && item.has_promote_api === true) {
          canCreateAd = true;
        }
      });
      return !canCreateAd;
    }
  },
  created() {
    this.loading = true;
    getBasicData({
      settlementList: "",
      agentDataFieldList: "",
      mediaList: { has_promote_api: "show" },
      gameList: ""
    })
      .then(response => {
        this.settlementList = response.data.settlementList;
        this.agentDataFieldList = response.data.agentDataFieldList;
        this.mediaList = response.data.mediaList;
        this.gameList = response.data.gameList;
        this.loading = false;
      })
      .catch(() => {
        this.loading = false;
      });
  }
};
</script>
