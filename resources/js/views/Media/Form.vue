<template>
  <el-form
    v-loading="loading"
    :model="form"
    :rules="rules"
    size="mini"
    ref="form"
    label-width="100px">
    <el-form-item label="媒体名称" prop="media_name">
      <el-input v-model="form.media_name"></el-input>
    </el-form-item>

    <el-form-item label="后台地址" prop="manage_url">
      <el-input v-model="form.manage_url"></el-input>
    </el-form-item>

    <el-form-item label="key" prop="media_key">
      <el-input v-model="form.media_key"></el-input>
    </el-form-item>

    <el-form-item label="结算方式" prop="settlement">
      <el-select v-model="form.settlement" multiple>
        <el-option
          v-for="item in settlementList"
          :key="item.value"
          :title="item.label"
          :label="item.label"
          :value="item.value">
        </el-option>
      </el-select>
    </el-form-item>

    <el-form-item label="备注">
      <el-input v-model="form.remark"></el-input>
    </el-form-item>

    <el-form-item>
      <el-button @click="cancelForm">取消</el-button>
      <el-button type="primary" :disabled="submitDisabled" @click="submitForm">确认</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { editItem, storeItem, updateItem } from "@/js/api/media";
import { renewObject } from "@/js/utils";
import { getBasicData } from "@/js/api/common";

export default {
  name: "gmp-media-form",
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
    }
  },
  data: function() {
    return {
      settlementList: [],

      submitDisabled: false,
      initForm: {
        media_name: "",
        manage_url: "",
        settlement: [],
        remark: "",
        media_key: ""
      },
      form: {},
      rules: {
        media_name: [
          { required: true, message: "请输入媒体名称", trigger: "blur" }
        ]
      }
    };
  },
  methods: {
    reloadItem() {
      this.loading = true;
      this.$set(this.$data, "form", renewObject(this.initForm));
      // 加载编辑数据
      if (this.itemId) {
        editItem(this.itemId).then(response => {
          let it = response.data.it;
          // it.settlement = it.settlement.filter(item => {
          //   return item.length > 0;
          // });
          this.form = Object.assign({}, this.form, it);
          this.loading = false;
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
          (this.requestAction === "edit"
            ? updateItem(this.itemId, this.form)
            : storeItem(this.form)
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
    }
  },
  computed: {},
  mounted() {
    this.$set(this.$data, "form", renewObject(this.initForm));
    this.mediaList = [];
    getBasicData({
      settlementList: "",
      mediaList: {
        distribute: "Toutiao"
      }
    })
      .then(response => {
        this.mediaList = response.data.mediaList;
        this.settlementList = response.data.settlementList;
      })
      .catch(error => {
        console.log(error);
      });
  }
};
</script>