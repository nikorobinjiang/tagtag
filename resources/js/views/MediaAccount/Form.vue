<template>
  <el-form
    v-loading="loading"
    :model="form"
    :rules="rules"
    size="mini"
    ref="form"
    label-width="100px">

    <el-form-item label="账号名称" prop="promote_name">
      <el-input v-model="form.promote_name"></el-input>
    </el-form-item>

    <el-row>
      <el-col :span=12>
        <el-form-item label="所属媒体" prop="media_id">
          <el-select :disabled="form.ad_count > 0" filterable v-model="form.media_id">
            <el-option v-for="(item, key) in mediaList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span=12>
        <el-form-item label="代理商" prop="agent_id">
          <el-select  :disabled="form.ad_count > 0" filterable v-model="form.agent_id">
            <el-option v-for="(item, key) in agentList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span=12>
        <el-form-item label="公司名称" prop="company_name">
          <el-select v-model="form.company_name" filterable clearable>
            <el-option v-for="(item, key) in companyList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.label">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span=12>
        <el-form-item label="后台地址" prop="manage_url">
          <el-input v-model="form.manage_url"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span=12>
        <el-form-item label="账号" prop="promote_account">
          <el-input v-model="form.promote_account" :disabled="form.distribute !== 'Normal' && form.has_api > 0"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span=12>
        <el-form-item label="密码" prop="promote_password">
          <el-input v-model="form.promote_password"></el-input>
        </el-form-item>
      </el-col>
    </el-row>
    <el-row>
      <el-col :span="12">
        <el-form-item label="代理使用" prop="used_by">
            <el-radio v-model="form.used_by" label="agency">是</el-radio>
            <el-radio v-model="form.used_by" label="scat">否</el-radio>
        </el-form-item>
      </el-col>
      <el-col :span=12>
        <el-form-item label="监测地址" prop="trace_settings_id">
          <el-select v-model="form.trace_settings_id" filterable>
            <el-option
              v-for="(item, key) in traceSettinglList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span=12>
        <el-form-item label="落地页域名" prop="ad_site_id">
          <el-select v-model="form.ad_site_id" filterable>
            <el-option
              v-for="(item, key) in adSiteList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
      <el-col :span=12>
        <el-form-item label="游戏域名" prop="ad_url_id">
          <el-select v-model="form.ad_url_id" filterable>
            <el-option
              v-for="(item, key) in adUrlList"
              :key="key"
              :title="item.label"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span=12>
        <el-form-item label="分发媒体类型" prop="distribute">
          <el-select v-model="form.distribute" filterable :disabled="requestAction === 'edit'">
            <el-option
              v-for="(item, key) in distributeList"
              :key="key"
            :title="item.label"
            :label="item.label"
            :value="item.value"></el-option>
          </el-select>
          <el-row type="flex" justify="start">
            <el-col :span="12">
              <el-button type="text" v-if="(form.media_id == 2 || form.distribute === 'Toutiao') && requestAction === 'edit'" target="_blank" v-newtab="toutiaoAuthUrl" >前往授权</el-button>
              <el-button type="text" v-else-if="form.distribute === 'Uchc'" @click="handleCheckUchcAccount">验证</el-button>
              <el-button type="text" v-else-if="form.distribute === 'Uchcv2'" @click="handleCheckUchcv2Account">验证</el-button>
            </el-col>
            <el-col :span="12">
              <span v-if="form.has_api == 1" style="color:#67C23A;display: block;">[已授权]</span>
              <span v-else-if="form.has_api == 2" style="color:#67C23A;display: block;">[授权失效]</span>
            </el-col>
          </el-row>
        </el-form-item>
      </el-col>
      <el-col :span=12>
        <el-form-item label="KEY" prop="promote_key">
          <el-input v-model="form.promote_key"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-row>
      <el-col :span=12>
        <el-form-item label="APIKEY" prop="promote_apikey" v-if="['Uchc', 'Uchcv2'].indexOf(form.distribute) !== -1">
          <el-input v-model="form.promote_apikey"></el-input>
        </el-form-item>
      </el-col>
    </el-row>

    <el-form-item>
      <el-button @click="cancelForm">取消</el-button>
      <el-button type="primary" :disabled="submitDisabled" @click="submitForm">确认</el-button>
    </el-form-item>

  </el-form>
</template>

<script>
import { pick } from "lodash";

import {
  getBasicData,
  getToutiaoData,
  checkUchcAccount,
  checkUchcv2Account
} from "@/js/api/common";
import { editItem, storeItem, updateItem } from "@/js/api/promote";
import { renewObject } from "@/js/utils";

export default {
  name: "gmp-promote-form",
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    requestAction: {
      type: [String],
      required: true
    },
    itemId: {
      default: null
    },
    mediaId: {
      default: null
    }
  },
  data: function() {
    return {
      settlementList: [],
      mediaList: [],
      agentList: [],
      companyList: [
        {
          value: "",
          label: ""
        }
      ],
      traceSettinglList: [],
      adSiteList: [],
      adUrlList: [],
      distributeList: [
        {
          value: "Normal",
          label: "普通账号"
        },
        {
          value: "Toutiao",
          label: "今日头条"
        },
        {
          value: "Uchc",
          label: "UC头条"
        },
        {
          value: "Uchcv2",
          label: "UC头条 2.0"
        }
      ],
      toutiaoAuthUrl: "",

      submitDisabled: false,
      initForm: {
        promote_name: "",
        media_id: "",
        agent_id: "",
        company_name: "",
        company_info: "",
        manage_url: "",
        promote_account: "",
        promote_password: "",
        promote_apikey: "",
        used_by: "scat",
        trace_settings_id: "",
        ad_site_id: "",
        ad_url_id: "",
        distribute: "",
        promote_key: "",
        promote_apikey: "",
        has_api: 0
      },
      form: {},
      rules: {
        promote_name: [
          { required: true, message: "请输入账号名称", trigger: "blur" }
        ],
        media_id: [
          { required: true, message: "请选择所属媒体", trigger: "blur" }
        ],
        agent_id: [
          { required: true, message: "请选择代理商", trigger: "blur" }
        ],
        promote_account: [
          { required: true, message: "请输入账号", trigger: "blur" }
        ],
        promote_password: [
          { required: true, message: "请输入密码", trigger: "blur" }
        ],
        ad_site_id: [
          { required: true, message: "请选择落地页地址", trigger: "blur" }
        ],
        ad_url_id: [{ required: true, message: "请选择域名", trigger: "blur" }],
        distribute: [
          { required: true, message: "请选择分发媒体类型", trigger: "blur" }
        ]
      }
    };
  },
  methods: {
    // 媒体联动
    handleMediaIdChange: function(val, oldVal) {
      if (!val) {
        return;
      }
      this.agentList = [];
      this.traceSettinglList = [];
      this.companyList = [];
      // this.form.agent_id='';
      // this.form.company_name='';
      getBasicData({
        agentList: {
          media_id: this.form.media_id
        },
        traceSettinglList: {
          media_id: this.form.media_id
        },
        mediaDetail: {
          media_id: this.form.media_id
        }
      })
        .then(response => {
          this.agentList = response.data.agentList;
          this.traceSettinglList = response.data.traceSettinglList;
          this.form.promote_key = response.data.mediaDetail.media_key;
          this.form.manage_url = response.data.mediaDetail.manage_url;
        })
        .catch(error => {
          console.log(error);
        });
    },
    handleAgentIdChange(val) {
      if (!val) {
        return;
      }
      this.companyList = [];
      // this.form.company_name='';
      getBasicData({
        companyList: {
          agent_id: this.form.agent_id
        }
      })
        .then(response => {
          this.companyList = response.data.companyList;
        })
        .catch(error => {
          console.log(error);
        });
    },
    handleCheckUchcAccount() {
      checkUchcAccount(
        pick(this.form, [
          "promote_account",
          "promote_password",
          "promote_apikey"
        ])
      ).then(response => {
        if (response.data.result == "success") {
          this.$message(response.data.message);
        } else {
          this.$message.warning(response.data.message);
        }
      });
    },
    handleCheckUchcv2Account() {
      checkUchcv2Account(
        pick(this.form, [
          "promote_account",
          "promote_password",
          "promote_apikey"
        ])
      ).then(response => {
        if (response.data.result == "success") {
          this.$message(response.data.message);
        } else {
          this.$message.warning(response.data.message);
        }
      });
    },
    reloadItem() {
      this.loading = true;
      this.$set(this.$data, "form", renewObject(this.initForm));
      // 加载编辑数据
      if (this.itemId) {
        editItem(this.itemId)
          .then(response => {
            let it = response.data.it;
            this.form = Object.assign({}, this.form, it);
            this.loading = false;
            return it;
          })
          .then(it => {
            return getToutiaoData({
              authUrl: {
                promote_id: it.promote_id
              }
            });
          })
          .then(response => {
            this.toutiaoAuthUrl = response.data.authUrl;
          });
      } else {
        this.loading = false;
      }
    },
    cancelForm() {
      this.$emit("update:visible", false);
    },
    submitForm(btn) {
      this.$refs.form.validate(valid => {
        if (valid) {
          this.submitDisabled = true;
          let form = Object.assign({}, this.form);
          this.companyList.map(item => {
            if (item.label === form.company_name) {
              form.company_info = item.value;
            }
          });
          (this.requestAction === "edit"
            ? updateItem(this.itemId, form)
            : storeItem(form)
          )
            .then(response => {
              if (response.data.result == "error") {
                this.$alert(response.data.message, "", {
                  confirmButtonText: "确定"
                });
              } else {
                this.$message("保存成功");

                this.$emit("submitSucc");
                this.$emit("update:visible", false);
              }
              this.submitDisabled = false;
            })
            .catch(error => {
              console.log("error", error);
              this.submitDisabled = false;
            });
        } else {
          this.$message({
            message: "请检查表单是否填写完整",
            center: true
          });
        }
      });
    }
  },
  watch: {
    visible: function(val, oldVal) {
      if (!val) {
        this.$set(this.$data, "form", renewObject(this.initForm));
      } else {
        this.reloadItem();
      }
    },
    itemId: {
      handler: function() {
        this.reloadItem();
      },
      immediate: true
    },
    "form.media_id": function(val, oldVal) {
      this.handleMediaIdChange(val, oldVal);
    },
    "form.agent_id": function(val, oldVal) {
      this.handleAgentIdChange(val, oldVal);
    }
  },
  computed: {},
  mounted() {
    this.$set(this.$data, "form", renewObject(this.initForm));
    // 媒体跳转创建
    if (this.mediaId) {
      this.form.media_id = this.mediaId;
    }
    getBasicData({
      mediaList: {},
      adSiteList: {},
      adUrlList: {}
    })
      .then(response => {
        this.mediaList = response.data.mediaList;
        this.adSiteList = response.data.adSiteList;
        this.adUrlList = response.data.adUrlList;
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>
