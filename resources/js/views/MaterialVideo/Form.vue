<template>
  <el-form
    v-loading="loading"
    :model="form"
    :rules="rules"
    size="mini"
    ref="form"
    label-width="100px">

    <el-row>
      <el-col :span="12">
        <el-form-item label="素材名称" prop="name">
          <el-input v-model="form.name" placeholder="输入素材名称" :disabled="isShow"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="12">
        <el-form-item label="游戏名称" prop="game_id">
          <el-select v-model="form.game_id" filterable :disabled="isShow">
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
    </el-row>

    <el-form-item label="素材标签" prop="tags">
      <InputTags
        :isShow="isShow"
        v-model="form.tags">
      </InputTags>
    </el-form-item>

    <el-form-item label="上传视频" prop="annex_videos">
      <InputUpload
        :isShow="isShow"
        type="video"
        :handleCheckCanRemove="checkCanRemoveUpload"
        v-model="form.annex_videos">
      </InputUpload>
    </el-form-item>

    <el-form-item label="备注">
      <el-input
        :disabled="isShow"
        type="textarea"
        v-model="form.remark"
        placeholder="输入备注信息">
      </el-input>
    </el-form-item>

    <el-form-item>
      <el-button @click="cancelForm">取消</el-button>
      <el-button v-if="!isShow" type="primary" :disabled="submitDisabled" @click="submitForm">确认</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { omit } from "lodash";

import {
  getBasicData,
  getUploadUrl,
  checkDestroyMaterial
} from "@/js/api/common";
import { editItem, storeItem, updateItem } from "@/js/api/materialVideo";
import { renewObject } from "@/js/utils";

import InputTags from "@/js/components/InputTags";
import InputUpload from "@/js/components/InputUpload";

export default {
  name: "gmp-material-video-form",
  components: {
    InputTags,
    InputUpload
  },
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
    isShow: {
      default: false
    }
  },
  data: function() {
    let checkCanRemoveUpload = annexId => {
      return checkDestroyMaterial(annexId, "material_annex").then(response => {
        if (response.data.result !== "success") {
          this.$message({
            message: response.data.message,
            type: "warning",
            center: true
          });
          return false;
        } else {
          return true;
        }
      });
    };
    return {
      loading: true,
      gameList: [],

      checkCanRemoveUpload,

      submitDisabled: false,
      initForm: {
        name: "",
        game_id: "",
        tags: "",
        annex_videos: [],
        material_annex_ids: [],
        remark: ""
      },
      form: {},
      rules: {
        name: [{ required: true, message: "请输入素材名称", trigger: "blur" }],
        game_id: [{ required: true, message: "请选择游戏", trigger: "blur" }],
        annex_videos: [
          { required: true, message: "请上传视频", trigger: "blur" }
        ]
      }
    };
  },
  methods: {
    cancelForm() {
      this.$emit("update:visible", false);
    },
    submitForm(btn) {
      this.$refs.form.validate(valid => {
        if (valid) {
          this.submitDisabled = true;
          (this.requestAction === "edit"
            ? updateItem(this.itemId, omit(this.form, ["annex_videos"]))
            : storeItem(omit(this.form, ["annex_videos"]))
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
            type: "warning",
            center: true
          });
        }
      });
    }
  },
  watch: {
    visible: {
      handler: function(val) {
        this.loading = true;
        // 加载编辑数据
        if (val && this.itemId) {
          editItem(this.itemId).then(response => {
            this.$set(
              this.$data,
              "form",
              Object.assign({}, this.form, response.data.it)
            );
            this.loading = false;
          });
        } else {
          this.$set(this.$data, "form", renewObject(this.initForm));
          this.loading = false;
        }
      },
      immediate: true
    },
    "form.annex_videos": function() {
      this.form.material_annex_ids = this.form.annex_videos.map(item => {
        return item.annex_id;
      });
    }
  },
  computed: {},
  created() {
    this.$set(this.$data, "form", renewObject(this.initForm));
    getBasicData({
      gameList: {}
    })
      .then(response => {
        this.gameList = response.data.gameList;
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>