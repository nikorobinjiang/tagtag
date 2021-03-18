<template>
  <el-form
  v-loading="loading"
  :model="form"
  :rules="rules"
  ref="PageForm"
  size="mini"
  label-width="100px">
    <el-form-item label="落地页域名" prop="site_url">
      <el-input v-model="form.site_url" :disabled="this.requestAction=='edit'"></el-input>
    </el-form-item>
    <el-row>
      <el-col :span="18">
        <el-form-item label="ICP备案号">
          <el-input v-model="form.icp"></el-input>
        </el-form-item>
      </el-col>
      <el-col :span="6">
        （例：沪ICP备 1602365号）
      </el-col>
    </el-row>

    <el-form-item>
      <el-button @click="cancelForm">取消</el-button>
      <el-button type="primary" :disabled="submitDisabled" @click="submitForm">确认</el-button>
    </el-form-item>
  </el-form>
</template>

<script>
import { getBasicData } from "@/js/api/common";
import { renewObject } from "@/js/utils";
import { editPage, storePage, updatePage } from "@/js/api/domain";

export default {
  name: "page-domain-form",
  components: {},
  props: {
    visible: {
      type: Boolean,
      default: false
    },
    requestAction: { // create edit show
      type: [String],
      required: true
    },
    itemId: {
      default: null
    }
  },
  data: function() {
    return {
      loading: false,
      submitDisabled: false,
      initForm: {
        site_url: "",
        icp: ""
      },
      form: {
        site_url: "",
        icp: ""
      },
      rules: {
        site_url: [
          { required: true, message: "请输入落地页域名", trigger: "blur" }
        ]
      }
    };
  },
  watch: {
    visible: {
      handler: function() {
        this.loading = true;
        this.$set(this.$data, "form", renewObject(this.initForm));
        // 加载编辑数据
        if (this.itemId) {
          editPage(this.itemId).then(response => {
            this.form = Object.assign({}, this.form, response.data.it);
            this.loading = false;
          });
        } else {
          this.loading = false;
        }
      },
      immediate: true
    }
  },
  methods: {
    cancelForm() {
      this.$emit("update:visible", false);
    },
    submitForm(btn) {
      this.$refs.PageForm.validate(valid => {
        if (valid) {
          this.submitDisabled = true;
          (this.requestAction === "edit"
            ? updatePage(this.itemId, this.form)
            : storePage(this.form)
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
          console.log("error submit!!");
          return false;
        }
      });
    }
  },
  mounted() {}
};
</script>